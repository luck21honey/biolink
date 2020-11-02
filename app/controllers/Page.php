<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Title;

class Page extends Controller {

    public function index() {

        $url = isset($this->params[0]) ? Database::clean_string($this->params[0]) : false;

        /* If the custom page url is set then try to get data from the database */
        $page = $url ? $this->database->query("
            SELECT
                `pages`.*,
                `pages_categories`.`url` AS `pages_category_url`,
                `pages_categories`.`title` AS `pages_category_title`
            FROM `pages`
            LEFT JOIN `pages_categories` ON `pages_categories`.`pages_category_id` = `pages`.`pages_category_id`
            WHERE
                `pages`.`url` = '{$url}' 
        ")->fetch_object() ?? false : false;

        /* Redirect if the page does not exist */
        if(!$page) {
            redirect('pages');
        }

        /* Delete Modal */
        $view = new \Altum\Views\View('admin/pages/page_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Add a new view to the page */
        $this->database->query("UPDATE `pages` SET `total_views` = `total_views`+1 WHERE `page_id` = {$page->page_id}");

        /* Prepare the View */
        $data = [
            'page'  => $page
        ];

        $view = new \Altum\Views\View('page/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

        /* Set a custom title */
        Title::set($page->title);

    }

}


