<?php

namespace Altum\Controllers;

use Altum\Middlewares\Authentication;

class Plan extends Controller {

    public function index() {

        if(!$this->settings->payment->is_enabled) {
            redirect();
        }

        $type = isset($this->params[0]) && in_array($this->params[0], ['renew', 'upgrade', 'new']) ? $this->params[0] : 'new';

        /* If the user is not logged in when trying to upgrade or renew, make sure to redirect them */
        if(in_array($type, ['renew', 'upgrade']) && !Authentication::check()) {
            redirect('plan/new');
        }

        /* Plans View */
        $data = [
            'simple_user_plan_settings' =>  require APP_PATH . 'includes/simple_user_plan_settings.php'
        ];

        $view = new \Altum\Views\View('partials/plans', (array) $this);

        $this->add_view_content('plans', $view->run($data));


        /* Prepare the View */
        $data = [
            'type' => $type
        ];

        $view = new \Altum\Views\View('plan/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
