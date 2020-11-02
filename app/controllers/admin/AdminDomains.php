<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Csrf;
use Altum\Middlewares\Authentication;
use Altum\Response;

class AdminDomains extends Controller {

    public function index() {

        Authentication::guard('admin');

        /* Main View */
        $view = new \Altum\Views\View('admin/domains/index', (array) $this);

        $this->add_view_content('content', $view->run());

    }


    public function read() {

        Authentication::guard('admin');

        $datatable = new \Altum\DataTable();
        $datatable->set_accepted_columns(['domain_id', 'type', 'host', 'date', 'links', 'email', 'name', 'is_enabled']);
        $datatable->process($_POST);

        $result = Database::$database->query("
            SELECT
                `domains`.*,
                `users`.`email`,
                COUNT(`links`.`domain_id`) AS `links`,
                (SELECT COUNT(*) FROM `domains`) AS `total_before_filter`,
                (SELECT COUNT(*) FROM `domains` LEFT JOIN `users` ON `domains` . `user_id` = `users` . `user_id` WHERE `users`.`name` LIKE '%{$datatable->get_search()}%' OR `users`.`email` LIKE '%{$datatable->get_search()}%' OR `domains`.`host` LIKE '%{$datatable->get_search()}%') AS `total_after_filter`
            FROM
                `domains`
            LEFT JOIN
                `links` ON `domains`.`domain_id` = `links`.`domain_id`
            LEFT JOIN
                `users` ON `domains`.`user_id` = `users`.`user_id`
            WHERE 
                `users`.`name` LIKE '%{$datatable->get_search()}%' 
                OR `users`.`email` LIKE '%{$datatable->get_search()}%' 
                OR`domains`.`host` LIKE '%{$datatable->get_search()}%'
            GROUP BY
                `domain_id`
            ORDER BY
                `domains`.`type` DESC,
                `domain_id` ASC,
                " . $datatable->get_order() . "
            LIMIT
                {$datatable->get_start()}, {$datatable->get_length()}
        ");

        $total_before_filter = 0;
        $total_after_filter = 0;

        $data = [];

        while($row = $result->fetch_object()):

            /* Type */
            $row->type =
                $row->type == 1 ?
                    '<span class="badge badge-pill badge-success" data-toggle="tooltip" title="' . $this->language->admin_domains->display->type_global . '"><i class="fa fa-fw fa-globe"></i></span>' :
                    '<span class="badge badge-pill badge-secondary" data-toggle="tooltip" title="' . $this->language->admin_domains->display->type_user . '"><i class="fa fa-fw fa-user"></i></span>';

            /* Email */
            $row->email = '<a href="' . url('admin/user-view/' . $row->user_id) . '"> ' . $row->email . '</a>';

            /* host */
            $host_prepend = '<img src="https://external-content.duckduckgo.com/ip3/' . $row->host . '.ico" class="img-fluid icon-favicon mr-1" />';
            $row->host = $host_prepend . '<a href="' . url('admin/domain-update/' . $row->domain_id) . '" class="align-middle">' . $row->host . '</a>';

            /* Links */
            $row->links = '<i class="fa fa-fw fa-link text-muted"></i> ' . nr($row->links);

            /* is_enabled badge */
            $row->is_enabled = $row->is_enabled ? '<span class="badge badge-pill badge-success"><i class="fa fa-fw fa-check"></i> ' . $this->language->global->active . '</span>' : '<span class="badge badge-pill badge-warning"><i class="fa fa-fw fa-eye-slash"></i> ' . $this->language->global->disabled . '</span>';

            $row->date = '<span data-toggle="tooltip" title="' . \Altum\Date::get($row->date, 1) . '">' . \Altum\Date::get($row->date, 2) . '</span>';
            $row->actions = include_view(THEME_PATH . 'views/admin/partials/admin_domain_dropdown_button.php', ['id' => $row->domain_id]);

            $data[] = $row;
            $total_before_filter = $row->total_before_filter;
            $total_after_filter = $row->total_after_filter;

        endwhile;

        Response::simple_json([
            'data' => $data,
            'draw' => $datatable->get_draw(),
            'recordsTotal' => $total_before_filter,
            'recordsFiltered' =>  $total_after_filter
        ]);

    }

    public function delete() {

        Authentication::guard();

        $domain_id = (isset($this->params[0])) ? (int) $this->params[0] : false;

        if(!Csrf::check()) {
            $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
        }

        if(empty($_SESSION['error'])) {

            /* Delete the domain */
            $this->database->query("DELETE FROM `domains` WHERE `domain_id` = {$domain_id}");

            /* Delete all the links using that domain */
            $this->database->query("DELETE FROM `links` WHERE `domain_id` = {$domain_id}");

            redirect('admin/domains');

        }

        die();
    }

}
