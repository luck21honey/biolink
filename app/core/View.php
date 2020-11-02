<?php

namespace Altum\Views;

use Altum\Traits\Paramsable;

class View {
    use Paramsable;

    public $view;
    public $view_path;

    public function __construct($view, Array $params = []) {

        $this->view = $view;
        $this->view_path = THEME_PATH . 'views/' . $view . '.php';

        $this->add_params($params);

    }

    public function run($data = []) {

        $data = (object) $data;

        ob_start();

        require $this->view_path;

        return ob_get_clean();
    }

}
