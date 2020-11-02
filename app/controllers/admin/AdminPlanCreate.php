<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Date;
use Altum\Middlewares\Csrf;
use Altum\Middlewares\Authentication;

class AdminPlanCreate extends Controller {

    public function index() {

        Authentication::guard('admin');

        if(in_array($this->settings->license->type, ['SPECIAL','Extended License'])) {
            /* Get the available taxes from the system */
            $taxes = [];

            $result = $this->database->query("SELECT `tax_id`, `internal_name`, `name`, `description` FROM `taxes`");

            while ($row = $result->fetch_object()) {
                $taxes[] = $row;
            }
        }

        if(!empty($_POST)) {
            /* Filter some the variables */
            $_POST['name'] = Database::clean_string($_POST['name']);
            $_POST['monthly_price'] = (float) $_POST['monthly_price'];
            $_POST['annual_price'] = (float) $_POST['annual_price'];
            $_POST['lifetime_price'] = (float) $_POST['lifetime_price'];

            $_POST['settings'] = json_encode([
                'additional_global_domains' => (bool) isset($_POST['additional_global_domains']),
                'custom_url'            => (bool) isset($_POST['custom_url']),
                'deep_links'            => (bool) isset($_POST['deep_links']),
                'no_ads'                => (bool) isset($_POST['no_ads']),
                'removable_branding'    => (bool) isset($_POST['removable_branding']),
                'custom_branding'       => (bool) isset($_POST['custom_branding']),
                'custom_colored_links'  => (bool) isset($_POST['custom_colored_links']),
                'statistics'            => (bool) isset($_POST['statistics']),
                'google_analytics'      => (bool) isset($_POST['google_analytics']),
                'facebook_pixel'        => (bool) isset($_POST['facebook_pixel']),
                'custom_backgrounds'    => (bool) isset($_POST['custom_backgrounds']),
                'verified'              => (bool) isset($_POST['verified']),
                'scheduling'            => (bool) isset($_POST['scheduling']),
                'seo'                   => (bool) isset($_POST['seo']),
                'utm'                   => (bool) isset($_POST['utm']),
                'socials'               => (bool) isset($_POST['socials']),
                'music_embeds'          => (bool) isset($_POST['music_embeds']),
                'video_embeds'          => (bool) isset($_POST['video_embeds']),
                'fonts'                 => (bool) isset($_POST['fonts']),
                'password'              => (bool) isset($_POST['password']),
                'sensitive_content'     => (bool) isset($_POST['sensitive_content']),
                'leap_link'             => (bool) isset($_POST['leap_link']),
                'projects_limit'        => (int) $_POST['projects_limit'],
                'biolinks_limit'        => (int) $_POST['biolinks_limit'],
                'links_limit'           => (int) $_POST['links_limit'],
                'domains_limit'         => (int) $_POST['domains_limit'],
            ]);
            $_POST['status'] = (int) $_POST['status'];
            $_POST['taxes_ids'] = json_encode(array_keys($_POST['taxes_ids'] ?? []));

            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            if(empty($_SESSION['error'])) {
                /* Update the database */
                $stmt = Database::$database->prepare("INSERT INTO plans (`name`, `monthly_price`, `annual_price`, `lifetime_price`, `settings`, `taxes_ids`, `status`, `date`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('ssssssss', $_POST['name'], $_POST['monthly_price'], $_POST['annual_price'], $_POST['lifetime_price'], $_POST['settings'], $_POST['taxes_ids'], $_POST['status'], Date::$date);
                $stmt->execute();
                $stmt->close();

                /* Set a nice success message */
                $_SESSION['success'][] = $this->language->global->success_message->basic;

                redirect('admin/plans');
            }
        }


        /* Main View */
        $data = [
            'taxes' => $taxes ?? null
        ];

        $view = new \Altum\Views\View('admin/plan-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
