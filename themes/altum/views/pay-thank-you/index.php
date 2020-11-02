<?php defined('ALTUMCODE') || die() ?>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<div class="container">

    <?php display_notifications() ?>

    <div class="d-flex flex-column align-items-center justify-content-center">
        <img src="<?= SITE_URL . ASSETS_URL_PATH . 'images/thank_you.svg' ?>" class="col-10 col-md-6 col-lg-4 mb-4" alt="<?= $this->language->pay_thank_you->header ?>" />

        <h1><?= $this->language->pay_thank_you->header ?></h1>

        <?php if($data->plan_id == 'trial'): ?>

            <p class="text-muted"><?= $this->language->pay_thank_you->plan_trial_start ?></p>

        <?php elseif(is_numeric($data->plan_id)): ?>

            <?php if($_GET['payment_processor'] == 'stripe'): ?>

                <p class="text-muted"><?= $this->language->pay_thank_you->plan_custom_will_start ?></p>

            <?php elseif($_GET['payment_processor'] == 'paypal'): ?>

                <?php if($_GET['payment_type'] == 'one_time'): ?>
                    <p class="text-muted"><?= $this->language->pay_thank_you->plan_custom_start ?></p>

                <?php elseif($_GET['payment_type'] == 'recurring'): ?>
                    <p class="text-muted"><?= $this->language->pay_thank_you->plan_custom_will_start ?></p>
                <?php endif ?>

            <?php elseif($_GET['payment_processor'] == 'offline_payment'): ?>

                <p class="text-muted"><?= $this->language->pay_thank_you->plan_custom_pending ?></p>

            <?php endif ?>

        <?php endif ?>

        <a href="<?= url('dashboard') ?>" class="btn btn-outline-primary mt-4"><?= $this->language->pay_thank_you->button ?></a>

    </div>


</div>

<?php ob_start() ?>
<script>
    let current_url = new URL(window.location.href);

    /* Here you could add your thank you page affiliate tracker code and use the already ready variables from below */
    let plan_id = current_url.searchParams.get('plan_id');

    /* The payment gateway name (ex: stripe) */
    let payment_processor = current_url.searchParams.get('payment_processor');

    /* The payment frequency (monthly, annual, lifetime) */
    let payment_frequency = current_url.searchParams.get('payment_frequency');

    /* The payment type (one_time, recurring) */
    let payment_type = current_url.searchParams.get('payment_type');

    /* Discount code, if any */
    let code = current_url.searchParams.get('code');

    /* Paid amount */
    let total_amount = current_url.searchParams.get('total_amount');

    /* Unique random identifier for this transaction */
    let unique_transaction_identifier = current_url.searchParams.get('unique_transaction_identifier');

    /* User id of the current logged in user */
    let user_id = current_url.searchParams.get('user_id');

</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
