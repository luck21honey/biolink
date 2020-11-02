<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;
use Altum\Models\User;

class AccountDelete extends Controller {

    public function index() {

        Authentication::guard();

        if(!empty($_POST)) {

            /* Check for any errors */
            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            if(!password_verify($_POST['current_password'], $this->user->password)) {
                $_SESSION['error'][] = $this->language->account->error_message->invalid_current_password;
            }

            if(empty($_SESSION['error'])) {

                /* Delete the user */
                (new User(['settings' => $this->settings]))->delete($this->user->user_id);

                Authentication::logout();

            }

        }

        /* Establish the account sidebar menu view */
        $menu = new \Altum\Views\View('partials/account_header', (array) $this);
        $this->add_view_content('account_header', $menu->run());

        /* Prepare the View */
        $view = new \Altum\Views\View('account-delete/index', (array) $this);

        $this->add_view_content('content', $view->run([]));

    }

}
