<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;
use Altum\Models\User;
use Altum\Response;

class Domains extends Controller {

    public function index() {

        Authentication::guard();

        if(!$this->settings->links->domains_is_enabled) {
            redirect('dashboard');
        }

        /* Create Modal */
        $view = new \Altum\Views\View('domains/domain_create_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Update Modal */
        $view = new \Altum\Views\View('domains/domain_update_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Delete Modal */
        $view = new \Altum\Views\View('domains/domain_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Prepare the paginator */
        $total_rows = Database::$database->query("SELECT COUNT(*) AS `total` FROM `domains` WHERE `user_id` = {$this->user->user_id} AND `type` = 0")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, 25, $_GET['page'] ?? 1, url('domains?page=%d')));

        /* Get the domains list for the user */
        $domains = [];
        $domains_result = Database::$database->query("SELECT * FROM `domains` WHERE `user_id` = {$this->user->user_id} AND `type` = 0 LIMIT {$paginator->getSqlOffset()}, {$paginator->getItemsPerPage()}");
        while($row = $domains_result->fetch_object()) $domains[] = $row;

        /* Prepare the pagination view */
        $pagination = (new \Altum\Views\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Establish the account header view */
        $menu = new \Altum\Views\View('partials/account_header', (array) $this);
        $this->add_view_content('account_header', $menu->run());

        /* Prepare the View */
        $data = [
            'domains'       => $domains,
            'total_domains' => $total_rows,
            'pagination'    => $pagination
        ];

        $view = new \Altum\Views\View('domains/index', (array) $this);

        $this->add_view_content('content', $view->run($data));
    }

    /* Ajax method */
    public function create() {
        Authentication::guard();

        if(!$this->settings->links->domains_is_enabled) {
            die();
        }

        $_POST['scheme'] = isset($_POST['scheme']) && in_array($_POST['scheme'], ['http://', 'https://']) ? Database::clean_string($_POST['scheme']) : 'https://';
        $_POST['host'] = trim(Database::clean_string($_POST['host']));
        $_POST['custom_index_url'] = trim(Database::clean_string($_POST['custom_index_url']));

        /* Make sure that the user didn't exceed the limit */
        $user_total_domains = Database::$database->query("SELECT COUNT(*) AS `total` FROM `domains` WHERE `user_id` = {$this->user->user_id} AND `type` = 0")->fetch_object()->total;
        if($this->user->plan_settings->domains_limit != -1 && $user_total_domains >= $this->user->plan_settings->domains_limit) {
            Response::json($this->language->domain_create_modal->error_message->domains_limit, 'error');
        }

        if(empty($errors)) {

            /* Define some needed variables */
            $type = 0;

            /* Add the row to the database */
            $stmt = Database::$database->prepare("INSERT INTO `domains` (`user_id`, `scheme`, `host`, `custom_index_url`, `type`, `date`) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('ssssss', $this->user->user_id, $_POST['scheme'], $_POST['host'], $_POST['custom_index_url'], $type, \Altum\Date::$date);
            $stmt->execute();
            $stmt->close();

            Response::json($this->language->domain_create_modal->success_message, 'success');

        }
    }

    /* Ajax method */
    public function update() {
        Authentication::guard();

        if(!$this->settings->links->domains_is_enabled) {
            die();
        }

        $_POST['domain_id'] = (int) $_POST['domain_id'];
        $_POST['scheme'] = isset($_POST['scheme']) && in_array($_POST['scheme'], ['http://', 'https://']) ? Database::clean_string($_POST['scheme']) : 'https://';
        $_POST['host'] = trim(Database::clean_string($_POST['host']));
        $_POST['custom_index_url'] = trim(Database::clean_string($_POST['custom_index_url']));

        if(empty($errors)) {

            /* Insert to database */
            $stmt = Database::$database->prepare("UPDATE `domains` SET `scheme` = ?, `host` = ?, `custom_index_url` = ? WHERE `domain_id` = ? AND `user_id` = ?");
            $stmt->bind_param('sssss', $_POST['scheme'], $_POST['host'], $_POST['custom_index_url'], $_POST['domain_id'], $this->user->user_id);
            $stmt->execute();
            $stmt->close();

            Response::json($this->language->domain_update_modal->success_message, 'success');

        }
    }

    /* Ajax method */
    public function delete() {
        Authentication::guard();

        if(!$this->settings->links->domains_is_enabled) {
            die();
        }

        if(!empty($_POST) && (Csrf::check('token') || Csrf::check('global_token'))) {

            $_POST['domain_id'] = (int) $_POST['domain_id'];

            /* Check for possible errors */
            if(!Database::exists('domain_id', 'domains', ['domain_id' => $_POST['domain_id']])) {
                die();
            }

            /* Delete from database */
            $stmt = Database::$database->prepare("DELETE FROM `domains` WHERE `domain_id` = ? AND `user_id` = ?");
            $stmt->bind_param('ss', $_POST['domain_id'], $this->user->user_id);
            $stmt->execute();
            $stmt->close();

            $stmt = Database::$database->prepare("DELETE FROM `links` WHERE `domain_id` = ? AND `user_id` = ?");
            $stmt->bind_param('ss', $_POST['domain_id'], $this->user->user_id);
            $stmt->execute();
            $stmt->close();

            Response::json($this->language->domain_delete_modal->success_message, 'success');

        }

    }

}
