<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Title;

class Pages extends Controller {

    public function index() {

        /* Check if the category url is set */
        $pages_category_url = isset($this->params[0]) ? Database::clean_string($this->params[0]) : false;

        /* If the category url is set, get it*/
        if($pages_category_url) {

            /* Pages category index */
            $pages_category = $pages_category_url ? Database::get('*', 'pages_categories', ['url' => $pages_category_url]) : false;

            /* Redirect to pages if the category is not found */
            if(!$pages_category) {
                redirect('pages');
            }

            /* Get the pages for this category */
            $pages_result = $this->database->query("SELECT `url`, `title`, `description`, `total_views`, `type` FROM `pages` WHERE `pages_category_id` = {$pages_category->pages_category_id} ORDER BY `total_views` DESC");

            /* Delete Modal */
            $view = new \Altum\Views\View('admin/pages/pages_category_delete_modal', (array) $this);
            \Altum\Event::add_content($view->run(), 'modals');

            /* Prepare the View */
            $data = [
                'pages_category' => $pages_category,
                'pages_result' => $pages_result
            ];

            $view = new \Altum\Views\View('pages/pages_category', (array) $this);

            /* Set a custom title */
            Title::set($pages_category->title);

        } else {

            /* Pages index */

            /* Get the popular pages */
            $popular_pages_result = $this->database->query("SELECT `url`, `title`, `description`, `total_views`, `type` FROM `pages` ORDER BY `total_views` DESC LIMIT 6");

            /* Get all the pages categories */
            $pages_categories_result = $this->database->query("
                SELECT 
                    `pages_categories`.`url`,
                    `pages_categories`.`title`,
                    `pages_categories`.`icon`,
                    COUNT(`pages`.`page_id`) AS `total_pages`
                FROM `pages_categories`
                LEFT JOIN `pages` ON `pages`.`pages_category_id` = `pages_categories`.`pages_category_id`
                GROUP BY `pages_categories`.`pages_category_id`
                ORDER BY `pages_categories`.`order` ASC
            ");

            /* Prepare the View */
            $data = [
                'popular_pages_result' => $popular_pages_result,
                'pages_categories_result' => $pages_categories_result
            ];

            $view = new \Altum\Views\View('pages/index', (array) $this);
        }

        $this->add_view_content('content', $view->run($data));



    }

}
