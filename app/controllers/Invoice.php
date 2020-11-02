<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Authentication;
use Altum\Models\Plan;

class Invoice extends Controller {

    public function index() {

        Authentication::guard();

        $id = isset($this->params[0]) ? (int) $this->params[0] : false;

        /* Make sure the campaign exists and is accessible to the user */
        if(!$payment = Database::get('*', 'payments', ['id' => $id])) {
            redirect('dashboard');
        }

        if($payment->user_id != $this->user->user_id && !Authentication::is_admin()) {
            redirect('dashboard');
        }

        /* Try to see if we get details from the billing */
        $payment->billing = json_decode($payment->billing);

        /* Get the plan details */
        $payment->plan = (new Plan(['settings' => $this->settings]))->get_plan_by_id($payment->plan_id);

        /* Check for potential taxes */
        $payment_taxes = (new \Altum\Models\Plan())->get_plan_taxes_by_taxes_ids($payment->taxes_ids);

        /* Calculate the price if a discount was used */
        $payment->price = $payment->discount_amount ? $payment->base_amount - $payment->discount_amount : $payment->base_amount;

        /* Calculate taxes */
        if(!empty($payment_taxes)) {

            /* Check for the inclusives */
            $inclusive_taxes_array = [];

            foreach($payment_taxes as $key => $row) {

                if($row->type == 'exclusive') {
                    continue;
                }

                $inclusive_tax = number_format($payment->price - ($payment->price / (1 + $row->value / 100)), 2);

                $inclusive_taxes_array[] = $inclusive_tax;
                $payment_taxes[$key]->amount = $inclusive_tax;

            }

            $inclusive_taxes = array_sum($inclusive_taxes_array);

            $price_without_inclusive_taxes = $payment->price - $inclusive_taxes;

            /* Check for the exclusives */
            foreach($payment_taxes as $key => $row) {

                if($row->type == 'inclusive') {
                    continue;
                }

                $exclusive_tax = number_format($row->value_type == 'percentage' ? $price_without_inclusive_taxes * ($row->value / 100) : $row->value, 2);

                $payment_taxes[$key]->amount = $exclusive_tax;

            }

        }

        /* Prepare the View */
        $data = [
            'payment' => $payment,
            'payment_taxes' => $payment_taxes
        ];

        $view = new \Altum\Views\View('invoice/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }


}
