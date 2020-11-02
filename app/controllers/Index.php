<?php

namespace Altum\Controllers;


use Altum\Database\Database;

class Index extends Controller {

    public function index() {

        /* Check if the current link accessed is actually the original url or not ( multi domain use ) */
        $original_url_host = parse_url(url())['host'];
        $request_url_host = Database::clean_string($_SERVER['HTTP_HOST']);

        if($original_url_host != $request_url_host) {

            /* Make sure the custom domain is attached */
            $domain = Database::get(['domain_id', 'custom_index_url'], 'domains', ['host' => $request_url_host]);

            /* Redirect if custom index is set */
            if(!empty($domain->custom_index_url)) {
                header('Location: ' . $domain->custom_index_url);
                die();
            }

            $is_custom_domain = true;

        }


        /* Custom index redirect if set */
        if(!empty($this->settings->index_url)) {
            header('Location: ' . $this->settings->index_url);
            die();
        }

        /* Check if the current link accessed is actually the original url or not ( multi domain use ) */
        $original_url_host = parse_url(url())['host'];
        $request_url_host = Database::clean_string($_SERVER['HTTP_HOST']);

        if($original_url_host != $request_url_host) {
            $is_custom_domain = true;
        }

        /* Plans View */
        $data = [
            'simple_user_plan_settings' =>  require APP_PATH . 'includes/simple_user_plan_settings.php'
        ];

        $view = new \Altum\Views\View('partials/plans', (array) $this);

        $this->add_view_content('plans', $view->run($data));


        /* Main View */
        $data = [
            'is_custom_domain' => $is_custom_domain ?? false
        ];

        $view = new \Altum\Views\View('index/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
