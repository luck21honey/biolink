<?php

namespace Altum\Models;

use Altum\Database\Database;
use Altum\Logger;

class User extends Model {

    public function get($user_id) {

        $data = Database::get('*', 'users', ['user_id' => $user_id]);

        if($data) {

            /* Parse the users plan settings */
            $data->plan_settings = json_decode($data->plan_settings);

            /* Parse billing details if existing */
            $data->billing = json_decode($data->billing);

        }

        return $data;
    }

    public function delete($user_id) {

        /* Cancel his active subscriptions if active */
        $this->cancel_subscription($user_id);

        /* Get all the available biolinks and iterate over them to delete the stored images */
        $result = Database::$database->query("SELECT `settings` FROM `links` WHERE `user_id` = {$user_id} AND `type` = 'biolink' AND `subtype` = 'base'");

        while($row = $result->fetch_object()) {

            $row->settings = json_decode($row->settings);

            /* Delete current avatar */
            if(!empty($row->settings->image) && file_exists(UPLOADS_PATH . 'avatars/' . $row->settings->image)) {
                unlink(UPLOADS_PATH . 'avatars/' . $row->settings->image);
            }

            /* Delete current background */
            if(is_string($row->settings->background) && !empty($row->settings->background) && file_exists(UPLOADS_PATH . 'backgrounds/' . $row->settings->background)) {
                unlink(UPLOADS_PATH . 'backgrounds/' . $row->settings->background);
            }

        }

        /* Delete the record from the database */
        Database::$database->query("DELETE FROM `users` WHERE `user_id` = {$user_id}");

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $user_id);

    }

    public function update_last_activity($user_id) {

        Database::update('users', ['last_activity' => \Altum\Date::$date], ['user_id' => $user_id]);

    }

    /*
    * Function to update a user with more details on a login action
    */
    public function login_aftermath_update($user_id, $ip, $country, $user_agent) {

        $stmt = Database::$database->prepare("UPDATE `users` SET `ip` = ?, `country` = ?, `last_user_agent` = ?, `total_logins` = `total_logins` + 1 WHERE `user_id` = {$user_id}");
        $stmt->bind_param('sss', $ip, $country, $user_agent);
        $stmt->execute();
        $stmt->close();

        Logger::users($user_id, 'login.success');
    }

    /*
     * Needs to have access to the Settings and the User variable, or pass in the user_id variable
     */
    public function cancel_subscription($user_id = false) {

        if(!isset($this->settings)) {
            throw new \Exception('Model needs to have access to the "settings" variable.');
        }

        if(!isset($this->user) && !$user_id) {
            throw new \Exception('Model needs to have access to the "user" variable or pass in the $user_in.');
        }

        if($user_id) {
            $this->user = Database::get(['user_id', 'payment_subscription_id'], 'users', ['user_id' => $user_id]);
        }

        if(empty($this->user->payment_subscription_id)) {
            return true;
        }

        $data = explode('###', $this->user->payment_subscription_id);
        $type = strtolower($data[0]);
        $subscription_id = $data[1];

        switch($type) {
            case 'stripe':

                /* Initiate Stripe */
                \Stripe\Stripe::setApiKey($this->settings->stripe->secret_key);

                /* Cancel the Stripe Subscription */
                $subscription = \Stripe\Subscription::retrieve($subscription_id);
                $subscription->cancel();

                break;

            case 'paypal':

                /* Initiate paypal */
                $paypal = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($this->settings->paypal->client_id, $this->settings->paypal->secret));
                $paypal->setConfig(['mode' => $this->settings->paypal->mode]);

                /* Create an Agreement State Descriptor, explaining the reason to suspend. */
                $agreement_state_descriptior = new \PayPal\Api\AgreementStateDescriptor();
                $agreement_state_descriptior->setNote('Suspending the agreement');

                /* Get details about the executed agreement */
                $agreement = \PayPal\Api\Agreement::get($subscription_id, $paypal);

                /* Suspend */
                $agreement->suspend($agreement_state_descriptior, $paypal);


                break;
        }

        Database::$database->query("UPDATE `users` SET `payment_subscription_id` = '' WHERE `user_id` = {$this->user->user_id}");

    }

}
