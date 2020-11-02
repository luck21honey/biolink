<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Authentication;
use Altum\Models\Domain;
use Altum\Title;

class Project extends Controller {

    public function index() {

        Authentication::guard();

        $project_id = isset($this->params[0]) ? (int) $this->params[0] : false;

        /* Make sure the project exists and is accessible to the user */
        if(!$project = Database::get('*', 'projects', ['project_id' => $project_id, 'user_id' => $this->user->user_id])) {
            redirect('dashboard');
        }

        /* Prepare the paginator */
        $total_rows = Database::$database->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `project_id` = {$project->project_id} AND `user_id` = {$this->user->user_id} AND (`subtype` = 'base' OR `subtype` = '')")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, 25, $_GET['page'] ?? 1, url('project/' . $project->project_id . '?page=%d')));

        /* Get the links list for the project */
        $links_result = Database::$database->query("
            SELECT 
                `links`.*, `domains`.`scheme`, `domains`.`host`
            FROM 
                `links`
            LEFT JOIN 
                `domains` ON `links`.`domain_id` = `domains`.`domain_id`
            WHERE 
                `links`.`project_id` = {$project->project_id} AND 
                `links`.`user_id` = {$this->user->user_id} AND 
                (`links`.`subtype` = 'base' OR `links`.`subtype` = '')
            ORDER BY
                `links`.`type`
            LIMIT 
                {$paginator->getSqlOffset()}, {$paginator->getItemsPerPage()}
        ");

        /* Iterate over the links */
        $links = [];

        while($row = $links_result->fetch_object()) {
            $row->full_url = $row->domain_id ? $row->scheme . $row->host . '/' . $row->url : url($row->url);

            $links[] = $row;
        }

        /* Prepare the pagination view */
        $pagination = (new \Altum\Views\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Get statistics */
        if(count($links)) {
            $links_chart = [];
            $start_date_query = (new \DateTime())->modify('-30 day')->format('Y-m-d H:i:s');
            $end_date_query = (new \DateTime())->modify('+1 day')->format('Y-m-d H:i:s');

            $track_links_result = Database::$database->query("
                SELECT
                    `count`,
                    DATE_FORMAT(`date`, '%Y-%m-%d') AS `formatted_date`
                FROM
                    `track_links`
                WHERE
                    `project_id` = {$project->project_id}
                    AND (`date` BETWEEN '{$start_date_query}' AND '{$end_date_query}')
                ORDER BY
                    `formatted_date`
            ");

            /* Generate the raw chart data and save logs for later usage */
            while($row = $track_links_result->fetch_object()) {
                $logs[] = $row;

                $row->formatted_date = \Altum\Date::get($row->formatted_date, 4);

                /* Handle if the date key is not already set */
                if (!array_key_exists($row->formatted_date, $links_chart)) {
                    $links_chart[$row->formatted_date] = [
                        'impressions' => 0,
                        'uniques' => 0,
                    ];
                }

                /* Distribute the data from the database row */
                $links_chart[$row->formatted_date]['uniques']++;
                $links_chart[$row->formatted_date]['impressions'] += $row->count;
            }

            $links_chart = get_chart_data($links_chart);
        }

        /* Create Link Modal */
        $domains = (new Domain())->get_domains($this->user);

        $data = [
            'project' => $project,
            'domains' => $domains
        ];

        $view = new \Altum\Views\View('project/create_link_modals', (array) $this);

        \Altum\Event::add_content($view->run($data), 'modals');

        /* Update Project Modal */
        $view = new \Altum\Views\View('project/project_update_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Delete Project Modal */
        $view = new \Altum\Views\View('project/project_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Prepare the View */
        $data = [
            'project'           => $project,

            'links'             => $links,
            'pagination'        => $pagination,
            'links_chart'       => $links_chart ?? false
        ];

        $view = new \Altum\Views\View('project/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

        /* Set a custom title */
        Title::set(sprintf($this->language->project->title, $project->name));

    }

}
