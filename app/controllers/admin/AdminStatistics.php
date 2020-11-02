<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Authentication;

class AdminStatistics extends Controller {
    public $type;
    public $date;

    public function index() {

        Authentication::guard('admin');

        $this->type = (isset($this->params[0])) && in_array($this->params[0], ['payments', 'growth', 'links']) ? Database::clean_string($this->params[0]) : 'growth';

        $start_date = isset($_GET['start_date']) ? Database::clean_string($_GET['start_date']) : (new \DateTime())->modify('-30 day')->format('Y-m-d');
        $end_date = isset($_GET['end_date']) ? Database::clean_string($_GET['end_date']) : (new \DateTime())->format('Y-m-d');

        $this->date = \Altum\Date::get_start_end_dates($start_date, $end_date);

        /* Process only data that is needed for that specific page */
        $type_data = $this->{$this->type}();

        /* Main View */
        $data = [
            'type' => $this->type,
            'date' => $this->date
        ];
        $data = array_merge($data, $type_data);

        $view = new \Altum\Views\View('admin/statistics/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    protected function payments() {

        $payments_chart = [];
        $result = $this->database->query("SELECT COUNT(*) AS `total_payments`, DATE_FORMAT(`date`, '%Y-%m-%d') AS `formatted_date`, TRUNCATE(SUM(`total_amount`), 2) AS `total_amount` FROM `payments` WHERE `date` BETWEEN '{$this->date->start_date_query}' AND '{$this->date->end_date_query}' GROUP BY `formatted_date`");
        while($row = $result->fetch_object()) {

            $row->formatted_date = \Altum\Date::get($row->formatted_date, 2);

            $payments_chart[$row->formatted_date] = [
                'total_amount' => $row->total_amount,
                'total_payments' => $row->total_payments
            ];

        }

        $payments_chart = get_chart_data($payments_chart);

        return [
            'payments_chart' => $payments_chart
        ];

    }

    protected function growth() {

        /* Users */
        $users_chart = [];
        $result = $this->database->query("
            SELECT
                 COUNT(*) AS `total`,
                 DATE_FORMAT(`date`, '%Y-%m-%d') AS `formatted_date`
            FROM
                 `users`
            WHERE
                `date` BETWEEN '{$this->date->start_date_query}' AND '{$this->date->end_date_query}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {

            $row->formatted_date = \Altum\Date::get($row->formatted_date, 2);

            $users_chart[$row->formatted_date] = [
                'users' => $row->total
            ];
        }

        $users_chart = get_chart_data($users_chart);

        /* Projects */
        $projects_chart = [];
        $result = $this->database->query("
            SELECT
                 COUNT(*) AS `total`,
                 DATE_FORMAT(`date`, '%Y-%m-%d') AS `formatted_date`
            FROM
                 `projects`
            WHERE
                `date` BETWEEN '{$this->date->start_date_query}' AND '{$this->date->end_date_query}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {

            $row->formatted_date = \Altum\Date::get($row->formatted_date, 2);

            $projects_chart[$row->formatted_date] = [
                'projects' => $row->total
            ];
        }

        $projects_chart = get_chart_data($projects_chart);

        /* Links */
        $links_chart = [];
        $result = $this->database->query("
            SELECT
                 COUNT(*) AS `total`,
                 DATE_FORMAT(`date`, '%Y-%m-%d') AS `formatted_date`
            FROM
                 `links`
            WHERE
                `date` BETWEEN '{$this->date->start_date_query}' AND '{$this->date->end_date_query}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {

            $row->formatted_date = \Altum\Date::get($row->formatted_date, 2);

            $links_chart[$row->formatted_date] = [
                'links' => $row->total
            ];
        }

        $links_chart = get_chart_data($links_chart);

        /* Users logs */
        $users_logs_chart = [];
        $result = $this->database->query("
            SELECT
                 COUNT(*) AS `total`,
                 DATE_FORMAT(`date`, '%Y-%m-%d') AS `formatted_date`
            FROM
                 `users_logs`
            WHERE
                `date` BETWEEN '{$this->date->start_date_query}' AND '{$this->date->end_date_query}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {

            $row->formatted_date = \Altum\Date::get($row->formatted_date, 2);

            $users_logs_chart[$row->formatted_date] = [
                'users_logs' => $row->total
            ];
        }

        $users_logs_chart = get_chart_data($users_logs_chart);

        /* Redeemed codes */
        if(in_array($this->settings->license->type, ['SPECIAL', 'Extended License'])) {
            $redeemed_codes_chart = [];
            $result = $this->database->query("
                SELECT
                     COUNT(*) AS `total`,
                     DATE_FORMAT(`date`, '%Y-%m-%d') AS `formatted_date`
                FROM
                     `redeemed_codes`
                WHERE
                    `date` BETWEEN '{$this->date->start_date_query}' AND '{$this->date->end_date_query}'
                GROUP BY
                    `formatted_date`
                ORDER BY
                    `formatted_date`
            ");
            while ($row = $result->fetch_object()) {

                $row->formatted_date = \Altum\Date::get($row->formatted_date, 2);

                $redeemed_codes_chart[$row->formatted_date] = [
                    'redeemed_codes' => $row->total
                ];
            }

            $redeemed_codes_chart = get_chart_data($redeemed_codes_chart);
        }

        return [
            'users_chart' => $users_chart,
            'projects_chart' => $projects_chart,
            'links_chart' => $links_chart,
            'users_logs_chart' => $users_logs_chart,
            'redeemed_codes_chart' => $redeemed_codes_chart ?? null
        ];
    }
    protected function links() {

        $track_links_chart = [];
        $result = $this->database->query("
            SELECT
                 COUNT(*) AS `total`,
                 DATE_FORMAT(`date`, '%Y-%m-%d') AS `formatted_date`
            FROM
                 `track_links`
            WHERE
                `date` BETWEEN '{$this->date->start_date_query}' AND '{$this->date->end_date_query}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {

            $row->formatted_date = \Altum\Date::get($row->formatted_date, 2);

            $track_links_chart[$row->formatted_date] = [
                'track_links' => $row->total
            ];
        }

        $track_links_chart = get_chart_data($track_links_chart);


        return [
            'track_links_chart'   => $track_links_chart
        ];
    }

}
