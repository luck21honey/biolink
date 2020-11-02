<?php

namespace Altum\Controllers;

use Altum\Middlewares\Authentication;

class PayThankYou extends Controller {

    public function index() {

        Authentication::guard();

        if(!$this->settings->payment->is_enabled) {
            redirect();
        }

        $plan_id = $_GET['plan_id'] ?? null;

        /* Make sure it is either the trial / free plan or normal plans */
        switch($plan_id) {

            case 'free':

                /* Get the current settings for the free plan */
                $plan = $this->settings->plan_free;

                break;

            case 'trial':

                /* Get the current settings for the trial plan */
                $plan = $this->settings->plan_trial;

                break;

            default:

                $plan_id = (int) $plan_id;

                /* Check if plan exists */
                if(!$plan = (new \Altum\Models\Plan(['settings' => $this->settings]))->get_plan_by_id($plan_id)) {
                    redirect('plan');
                }

                break;
        }

        /* Make sure the plan is enabled */
        if(!$plan->status) {
            redirect('plan');
        }

        /* Extra safety */
        $thank_you_url_parameters_raw = array_filter($_GET, function($key) {
            return !in_array($key, ['altum', 'unique_transaction_identifier']);
        }, ARRAY_FILTER_USE_KEY);


        $thank_you_url_parameters = '';
        foreach($thank_you_url_parameters_raw as $key => $value) {
            $thank_you_url_parameters .= '&' . $key . '=' . $value;
        }

        $unique_transaction_identifier = md5(\Altum\Date::get('', 4) . $thank_you_url_parameters);

        if($_GET['unique_transaction_identifier'] != $unique_transaction_identifier) {
            redirect('plan');
        }

        /* Prepare the View */
        $data = [
            'plan_id'    => $plan_id,
            'plan'       => $plan,
        ];

        $view = new \Altum\Views\View('pay-thank-you/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
