<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Authentication;
use Altum\Models\Domain;
use Altum\Title;

class Link extends Controller {
    public $link;

    public function index() {

        Authentication::guard();

        $link_id = isset($this->params[0]) ? (int) $this->params[0] : false;
        $method = isset($this->params[1]) && in_array($this->params[1], ['settings', 'statistics']) ? $this->params[1] : 'settings';

        /* Make sure the link exists and is accessible to the user */
        if(!$this->link = Database::get('*', 'links', ['link_id' => $link_id, 'user_id' => $this->user->user_id])) {
            redirect('dashboard');
        }

        $this->link->settings = json_decode($this->link->settings);

        /* Get the current domain if needed */
        $this->link->domain = $this->link->domain_id ? (new Domain())->get_domain($this->link->domain_id) : null;

        /* Determine the actual full url */
        $this->link->full_url = $this->link->domain ? $this->link->domain->url . $this->link->url : url($this->link->url);

        /* Handle code for different parts of the page */
        switch($method) {
            case 'settings':

                if($this->link->type == 'biolink') {
                    /* Get the links available for the biolink */
                    $link_links_result = $this->database->query("SELECT * FROM `links` WHERE `biolink_id` = {$this->link->link_id} ORDER BY `order` ASC");

                    $biolink_link_types = require APP_PATH . 'includes/biolink_link_types.php';

                    /* Add the modals for creating the links inside the biolink */
                    foreach($biolink_link_types as $key) {
                        $data = ['link' => $this->link];
                        $view = new \Altum\Views\View('link/settings/create_' . $key . '_modal.settings.biolink.method', (array) $this);
                        \Altum\Event::add_content($view->run($data), 'modals');
                    }

                    if($this->link->subtype != 'base') {
                        redirect('link/' . $this->link->biolink_id);
                    }
                }

                /* Get the available domains to use */
                $domains = (new Domain())->get_domains($this->user);

                /* Prepare variables for the view */
                $data = [
                    'link'              => $this->link,
                    'method'            => $method,
                    'link_links_result' => $link_links_result ?? null,
                    'domains'           => $domains
                ];

                break;


            case 'statistics':

                $type = isset($this->params[2]) && in_array($this->params[2], ['lastactivity', 'referrers', 'countries', 'operatingsystems', 'browsers', 'devices', 'browserlanguages']) ? Database::clean_string($this->params[2]) : 'lastactivity';
                $start_date = isset($_GET['start_date']) ? Database::clean_string($_GET['start_date']) : null;
                $end_date = isset($_GET['end_date']) ? Database::clean_string($_GET['end_date']) : null;

                $date = \Altum\Date::get_start_end_dates($start_date, $end_date);

                /* Get data needed for statistics from the database */
                $logs = [];
                $logs_chart = [];

                $logs_result = Database::$database->query("
                    SELECT
                        COUNT(`count`) AS `uniques`,
						SUM(`count`) AS `impressions`,
                        DATE_FORMAT(`date`, '%Y-%m-%d') AS `formatted_date`
                    FROM
                         `track_links`
                    WHERE
                        `link_id` = {$this->link->link_id}
                        AND (`date` BETWEEN '{$date->start_date_query}' AND '{$date->end_date_query}')
                    GROUP BY
                        `formatted_date`
                    ORDER BY
                        `formatted_date`
                ");

                /* Generate the raw chart data and save logs for later usage */
                while($row = $logs_result->fetch_object()) {
                    $logs[] = $row;

                    $row->formatted_date = \Altum\Date::get($row->formatted_date, 4);

                    $logs_chart[$row->formatted_date] = [
                        'impressions'        => $row->impressions,
                        'uniques'            => $row->uniques,
                    ];
                }

                $logs_chart = get_chart_data($logs_chart);

                /* Get data based on what statistics are needed */
                switch($type) {
                    case 'lastactivity':

                        $result = Database::$database->query("
                            SELECT
                                `dynamic_id`,
                                `referrer`,
                                `country_code`,
                                `os_name`,
                                `browser_name`,
                                `browser_language`,
                                `device_type`,
                                `last_date`
                            FROM
                                `track_links`
                            WHERE
                                `link_id` = {$this->link->link_id}
                                AND (`date` BETWEEN '{$date->start_date_query}' AND '{$date->end_date_query}')
                            ORDER BY
                                `last_date` DESC
                            LIMIT 25
                        ");

                    break;

                    case 'referrers':
                    case 'countries':
                    case 'operatingsystems':
                    case 'browsers':
                    case 'devices':
                    case 'browserlanguages':

                        $columns = [
                            'referrers' => 'referrer',
                            'countries' => 'country_code',
                            'operatingsystems' => 'os_name',
                            'browsers' => 'browser_name',
                            'devices' => 'device_type',
                            'browserlanguages' => 'browser_language'
                        ];

                        $result = Database::$database->query("
                            SELECT
                                `{$columns[$type]}`,
                                COUNT({$columns[$type]}) AS `total`
                            FROM
                                 `track_links`
                            WHERE
                                `link_id` = {$this->link->link_id}
                                AND (`date` BETWEEN '{$date->start_date_query}' AND '{$date->end_date_query}')
                            GROUP BY
                                `{$columns[$type]}`
                            ORDER BY
                                `total` DESC
                            LIMIT 250
                        ");

                        break;
                }

                $statistics_rows = [];

                while($row = $result->fetch_object()) {
                    $statistics_rows[] = $row;
                }

                /* Prepare the statistics method View */
                $data = [
                    'rows' => $statistics_rows
                ];

                $view = new \Altum\Views\View('link/statistics/' . $type . '.statistics.method', (array) $this);
                $this->add_view_content('statistics.method', $view->run($data));

                /* Prepare variables for the view */
                $data = [
                    'link'              => $this->link,
                    'method'            => $method,
                    'type'              => $type,
                    'date'              => $date,
                    'logs'              => $logs,
                    'logs_chart'        => $logs_chart
                ];

                break;
        }

        /* Prepare the method View */
        $view = new \Altum\Views\View('link/' . $method . '.method', (array) $this);
        $this->add_view_content('method', $view->run($data));

        /* Prepare the View */
        $data = [
            'link'      => $this->link,
            'method'    => $method
        ];

        $view = new \Altum\Views\View('link/index', (array) $this);
        $this->add_view_content('content', $view->run($data));

        /* Set a custom title */
        Title::set(sprintf($this->language->link->title, $this->link->url));

    }

}
