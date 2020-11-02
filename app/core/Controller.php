<?php

namespace Altum\Controllers;

use Altum\Models\Page;
use Altum\Routing\Router;
use Altum\Traits\Paramsable;

class Controller {
    use Paramsable;

    public $views = [];

    public function __construct(Array $params = []) {

        $this->add_params($params);

    }

    public function add_view_content($name, $data) {

        $this->views[$name] = $data;

    }

    public function run() {

        if(Router::$path == 'link') {
            $wrapper = new \Altum\Views\View('link-path/wrapper', (array) $this);
        }

        if(Router::$path == '') {
            /* Get the top menu custom pages */
            $pages = (new Page(['database' => $this->database]))->get_pages('top');

            /* Establish the menu view */
            $menu = new \Altum\Views\View('partials/menu', (array) $this);
            $this->add_view_content('menu', $menu->run([ 'pages' => $pages ]));

            /* Get the footer */
            $pages = (new Page(['database' => $this->database]))->get_pages('bottom');

            /* Establish the footer view */
            $footer = new \Altum\Views\View('partials/footer', (array) $this);
            $this->add_view_content('footer', $footer->run([ 'pages' => $pages ]));

            $wrapper = new \Altum\Views\View(Router::$controller_settings['wrapper'], (array) $this);
        }


        if(Router::$path == 'admin') {
            /* Establish the side menu view */
            $sidebar = new \Altum\Views\View('admin/partials/admin_sidebar', (array) $this);
            $this->add_view_content('admin_sidebar', $sidebar->run());

            /* Establish the top menu view */
            $menu = new \Altum\Views\View('admin/partials/admin_menu', (array) $this);
            $this->add_view_content('admin_menu', $menu->run());

            /* Establish the footer view */
            $footer = new \Altum\Views\View('admin/partials/footer', (array) $this);
            $this->add_view_content('footer', $footer->run());

            $wrapper = new \Altum\Views\View('admin/wrapper', (array) $this);
        }

        echo $wrapper->run();
    }


}
