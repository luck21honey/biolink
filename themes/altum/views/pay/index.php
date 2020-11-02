<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<script>
    'use strict';

    /* Declare some used variables inside javascript */
    window.altum.plan_id = $('input[name="plan_id"]').val();
    window.altum.monthly_price = $('input[name="monthly_price"]').val();
    window.altum.annual_price = $('input[name="annual_price"]').val();
    window.altum.lifetime_price = $('input[name="lifetime_price"]').val();
    window.altum.discount = null;

    window.altum.payment_type_one_time_enabled = <?= json_encode((bool) in_array($this->settings->payment->type, ['one_time', 'both'])) ?>;
    window.altum.payment_type_recurring_enabled = <?= json_encode((bool) in_array($this->settings->payment->type, ['recurring', 'both'])) ?>;

    window.altum.taxes = <?= json_encode($data->plan_taxes ? $data->plan_taxes : null) ?>;
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<div class="container">
    <?php display_notifications() ?>

    <nav aria-label="breadcrumb">
        <small>
            <ol class="custom-breadcrumbs">
                <li><a href="<?= url() ?>"><?= $this->language->index->breadcrumb ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
                <li><a href="<?= url('plan') ?>"><?= $this->language->plan->breadcrumb ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
                <li class="active" aria-current="page"><?= sprintf($this->language->pay->breadcrumb, $data->plan->name) ?></li>
            </ol>
        </small>
    </nav>

    <?php if($data->plan_id == 'trial'): ?>
        <h1 class="h3"><?= sprintf($this->language->pay->trial->header, $data->plan->name) ?></h1>
        <div class="text-muted mb-5"><?= $this->language->pay->trial->subheader ?></div>

        <form action="<?= 'pay/' . $data->plan_id ?>" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <div class="row">
                <div class="col-12 col-xl-8 order-1 order-xl-0">

                    <?php if($this->user->plan_id != 'free' && !$this->user->plan_is_expired): ?>
                        <div class="alert alert-info" role="alert">
                            <?= $this->language->pay->trial->other_plan_not_expired ?>
                        </div>
                    <?php endif ?>

                    <div class="mt-4">
                        <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary"><?= sprintf($this->language->pay->trial->trial_start, $data->plan->days) ?></button>
                    </div>

                    <div class="mt-3 text-muted text-center">
                        <small>
                            <?= sprintf(
                                $this->language->pay->accept,
                                '<a href="' . $this->settings->terms_and_conditions_url . '">' . $this->language->global->terms_and_conditions . '</a>',
                                '<a href="' . $this->settings->privacy_policy_url . '">' . $this->language->global->privacy_policy . '</a>'
                            ) ?>
                        </small>
                    </div>

                </div>

                <div class="mb-5 col-12 col-xl-4 order-0 order-xl-1">
                    <div class="">
                        <div class="">
                            <h2 class="h4 mb-4 text-muted"><?= $this->language->pay->plan_details ?></h2>

                            <?= (new \Altum\Views\View('partials/plan_features', ['settings' => $this->settings]))->run(['plan_settings' => $data->plan->settings]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-xl-8">



                </div>
            </div>

        </form>


    <?php elseif(is_numeric($data->plan_id)): ?>

    <?php
    /* Check for extra savings on the prices */
    $annual_price_savings = number_format($data->plan->annual_price - ($data->plan->monthly_price * 12), 2);

    ?>

        <h1 class="h3"><?= sprintf($this->language->pay->custom_plan->header, $data->plan->name) ?></h1>
        <div class="text-muted mb-5"><?= $this->language->pay->custom_plan->subheader ?></div>

        <form action="<?= 'pay/' . $data->plan_id ?>" method="post" enctype="multipart/form-data" role="form">
            <input type="hidden" name="plan_id" value="<?= $data->plan_id ?>" />
            <input type="hidden" name="monthly_price" value="<?= $data->plan->monthly_price ?>" />
            <input type="hidden" name="annual_price" value="<?= $data->plan->annual_price ?>" />
            <input type="hidden" name="lifetime_price" value="<?= $data->plan->lifetime_price ?>" />
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <div class="row">
                <div class="col-12 col-xl-8">

                    <h2 class="h5 mb-4 text-muted"><i class="fa fa-fw fa-sm fa-box-open mr-1"></i> <?= $this->language->pay->custom_plan->payment_frequency ?></h2>

                    <div>
                        <div class="row d-flex align-items-stretch">

                            <?php if($data->plan->monthly_price): ?>
                                <label class="col-12 my-2 custom-radio-box">
                                    <input type="radio" id="monthly_price" name="payment_frequency" value="monthly" class="custom-control-input" required="required">

                                    <div class="card">
                                        <div class="card-body d-flex align-items-center justify-content-between">
                                            <div class="card-title mb-0"><?= $this->language->pay->custom_plan->monthly ?></div>

                                            <div class="">
                                                <div class="d-flex align-items-center">
                                                    <span id="monthly_price_amount" class="custom-radio-box-main-text"><?= $data->plan->monthly_price ?></span>
                                                    <span class="ml-1"><?= $this->settings->payment->currency ?></span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </label>
                            <?php endif ?>

                            <?php if($data->plan->annual_price): ?>
                                <label class="col-12 my-2 custom-radio-box">
                                    <input type="radio" id="annual_price" name="payment_frequency" value="annual" class="custom-control-input" required="required">

                                    <div class="card">
                                        <div class="card-body d-flex align-items-center justify-content-between">
                                            <div class="card-title mb-0"><?= $this->language->pay->custom_plan->annual ?></div>

                                            <div class="d-flex align-items-center">
                                                <?php if($data->plan->monthly_price && $annual_price_savings > 0): ?>
                                                    <div class="payment-price-savings mr-2">
                                                        <span><?= sprintf($this->language->pay->custom_plan->annual_savings, '<span class="badge badge-success">-' . $annual_price_savings, $this->settings->payment->currency . '</span>') ?></span>
                                                    </div>
                                                <?php endif ?>

                                                <span id="annual_price_amount" class="custom-radio-box-main-text"><?= $data->plan->annual_price ?></span>
                                                <span class="ml-1"><?= $this->settings->payment->currency ?></span>
                                            </div>

                                        </div>
                                    </div>
                                </label>
                            <?php endif ?>

                            <?php if($data->plan->lifetime_price): ?>
                                <label class="col-12 my-2 custom-radio-box">
                                    <input type="radio" id="lifetime_price" name="payment_frequency" value="lifetime" class="custom-control-input" required="required">

                                    <div class="card">
                                        <div class="card-body d-flex align-items-center justify-content-between">
                                            <div class="card-title mb-0"><?= $this->language->pay->custom_plan->lifetime ?></div>

                                            <div class="d-flex align-items-center">
                                                <div class="payment-price-savings mr-2">
                                                    <small><?= $this->language->pay->custom_plan->lifetime_help ?></small>
                                                </div>

                                                <span id="lifetime_price_amount" class="custom-radio-box-main-text"><?= $data->plan->lifetime_price ?></span>
                                                <span class="ml-1"><?= $this->settings->payment->currency ?></span>
                                            </div>

                                        </div>
                                    </div>
                                </label>
                            <?php endif ?>

                        </div>
                    </div>

                    <h2 class="h5 mt-5 mb-4 text-muted"><i class="fa fa-fw fa-sm fa-money-check-alt mr-1"></i> <?= $this->language->pay->custom_plan->payment_processor ?></h2>

                    <?php if(!$this->settings->paypal->is_enabled && !$this->settings->stripe->is_enabled && !$this->settings->offline_payment->is_enabled): ?>
                        <div class="alert alert-info" role="alert">
                            <?= $this->language->pay->custom_plan->no_processor ?>
                        </div>
                    <?php else: ?>

                        <div>
                            <div class="row d-flex align-items-stretch">

                                <?php if($this->settings->paypal->is_enabled): ?>
                                    <label class="col-12 my-2 custom-radio-box">

                                        <input type="radio" name="payment_processor" value="paypal" class="custom-control-input" required="required">

                                        <div class="card">
                                            <div class="card-body d-flex align-items-center justify-content-between">

                                                <div class="card-title mb-0"><?= $this->language->pay->custom_plan->paypal ?></div>

                                                <div class="">
                                                    <span class="custom-radio-box-main-icon"><i class="fab fa-fw fa-paypal"></i></span>
                                                </div>

                                            </div>
                                        </div>

                                    </label>
                                <?php endif ?>

                                <?php if($this->settings->stripe->is_enabled): ?>
                                    <label class="col-12 my-2 custom-radio-box">

                                        <input type="radio" name="payment_processor" value="stripe" class="custom-control-input" required="required">

                                        <div class="card">
                                            <div class="card-body d-flex align-items-center justify-content-between">

                                                <div class="card-title mb-0"><?= $this->language->pay->custom_plan->stripe ?></div>

                                                <div class="">
                                                    <span class="custom-radio-box-main-icon"><i class="fab fa-fw fa-stripe"></i></span>
                                                </div>

                                            </div>
                                        </div>

                                    </label>
                                <?php endif ?>

                                <?php if($this->settings->offline_payment->is_enabled): ?>
                                    <label class="col-12 my-2 custom-radio-box">

                                        <input type="radio" name="payment_processor" value="offline_payment" class="custom-control-input" required="required">

                                        <div class="card">
                                            <div class="card-body d-flex align-items-center justify-content-between">

                                                <div class="card-title mb-0"><?= $this->language->pay->custom_plan->offline_payment ?></div>

                                                <div class="">
                                                    <span class="custom-radio-box-main-icon"><i class="fa fa-fw fa-university"></i></span>
                                                </div>

                                            </div>
                                        </div>

                                    </label>
                                <?php endif ?>
                            </div>

                            <div id="offline_payment_processor_wrapper" style="display: none;">
                                <div class="form-group mt-4">
                                    <label><?= $this->language->pay->custom_plan->offline_payment_instructions ?></label>
                                    <div class="card"><div class="card-body"><?= $this->settings->offline_payment->instructions ?></div></div>
                                </div>

                                <div class="form-group mt-4">
                                    <label><?= $this->language->pay->custom_plan->offline_payment_proof ?></label>
                                    <input type="file" name="offline_payment_proof" accept=".png, .jpg, .jpeg" class="form-control" />
                                    <div class="mt-2"><span class="text-muted"><?= $this->language->pay->custom_plan->offline_payment_proof_help ?></span></div>
                                </div>
                            </div>
                        </div>
                    <?php endif ?>

                    <h2 class="h5 mt-5 mb-4 text-muted"><i class="fa fa-fw fa-sm fa-dollar-sign mr-1"></i> <?= $this->language->pay->custom_plan->payment_type ?></h2>

                    <div>
                        <div class="row d-flex align-items-stretch">

                            <label class="col-12 my-2 custom-radio-box" id="one_time_type_label" <?= in_array($this->settings->payment->type, ['one_time', 'both']) ? null : 'style="display: none"' ?>>
                                <input type="radio" id="one_time_type" name="payment_type" value="one_time" class="custom-control-input" required="required">

                                <div class="card">
                                    <div class="card-body d-flex align-items-center justify-content-between">

                                        <div class="card-title mb-0"><?= $this->language->pay->custom_plan->one_time_type ?></div>

                                        <div class="">
                                            <span class="custom-radio-box-main-icon"><i class="fa fa-fw fa-hand-holding-usd"></i></span>
                                        </div>

                                    </div>
                                </div>
                            </label>

                            <label class="col-12 my-2 custom-radio-box" id="recurring_type_label" <?= in_array($this->settings->payment->type, ['recurring', 'both']) ? null : 'style="display: none"' ?>>
                                <input type="radio" id="recurring_type" name="payment_type" value="recurring" class="custom-control-input" required="required">

                                <div class="card">
                                    <div class="card-body d-flex align-items-center justify-content-between">

                                        <div class="card-title mb-0"><?= $this->language->pay->custom_plan->recurring_type ?></div>

                                        <div class="">
                                            <span class="custom-radio-box-main-icon"><i class="fa fa-fw fa-sync-alt"></i></span>
                                        </div>

                                    </div>
                                </div>
                            </label>

                        </div>
                    </div>

                </div>

                <div class="mt-5 mt-xl-0 col-12 col-xl-4">
                    <div class="">
                        <div class="mb-5">
                            <h2 class="h4 mb-4 text-muted"><?= $this->language->pay->plan_details ?></h2>

                            <?= (new \Altum\Views\View('partials/plan_features', ['settings' => $this->settings]))->run(['plan_settings' => $data->plan->settings]) ?>
                        </div>

                        <div class="card">
                            <div class="card-header text-muted font-weight-bold">
                                <?= $this->language->pay->custom_plan->summary->header ?>
                            </div>

                            <div class="card-body">

                                <div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="text-muted">
                                            <?= $this->language->pay->custom_plan->summary->plan ?>
                                        </span>

                                        <span>
                                            <?= $data->plan->name ?>
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="text-muted">
                                            <?= $this->language->pay->custom_plan->summary->payment_frequency ?>
                                        </span>

                                        <div id="summary_payment_frequency_monthly" style="display: none;">
                                            <div class="d-flex flex-column">
                                                <span class="text-right">
                                                    <?= $this->language->pay->custom_plan->summary->monthly ?>
                                                </span>
                                                <small class="text-right text-muted">
                                                    <?= $this->language->pay->custom_plan->summary->monthly_help ?>
                                                </small>
                                            </div>
                                        </div>

                                        <div id="summary_payment_frequency_annual" style="display: none;">
                                            <div class="d-flex flex-column">
                                                <span class="text-right">
                                                    <?= $this->language->pay->custom_plan->summary->annual ?>
                                                </span>
                                                <small class="text-right text-muted">
                                                    <?= $this->language->pay->custom_plan->summary->annual_help ?>
                                                </small>
                                            </div>
                                        </div>

                                        <div id="summary_payment_frequency_lifetime" style="display: none;">
                                            <div class="d-flex flex-column">
                                                <span class="text-right">
                                                    <?= $this->language->pay->custom_plan->summary->lifetime ?>
                                                </span>
                                                <small class="text-right text-muted">
                                                    <?= $this->language->pay->custom_plan->summary->lifetime_help ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="text-muted">
                                            <?= $this->language->pay->custom_plan->summary->payment_type ?>
                                        </span>

                                        <div id="summary_payment_type_one_time" style="display: none;">
                                            <div class="d-flex flex-column">
                                                <span class="text-right">
                                                    <?= $this->language->pay->custom_plan->summary->one_time ?>
                                                </span>
                                                <small class="text-right text-muted">
                                                    <?= $this->language->pay->custom_plan->summary->one_time_help ?>
                                                </small>
                                            </div>
                                        </div>

                                        <div id="summary_payment_type_recurring" style="display: none;">
                                            <div class="d-flex flex-column">
                                                <span class="text-right">
                                                    <?= $this->language->pay->custom_plan->summary->recurring ?>
                                                </span>
                                                <small class="text-right text-muted">
                                                    <?= $this->language->pay->custom_plan->summary->recurring_help ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="text-muted">
                                            <?= $this->language->pay->custom_plan->summary->payment_processor ?>
                                        </span>

                                        <span id="summary_payment_processor_paypal" style="display: none;">
                                            <?= $this->language->pay->custom_plan->paypal ?>
                                        </span>

                                        <span id="summary_payment_processor_stripe" style="display: none;">
                                            <?= $this->language->pay->custom_plan->stripe ?>
                                        </span>

                                        <span id="summary_payment_processor_offline_payment" style="display: none;">
                                            <?= $this->language->pay->custom_plan->offline_payment ?>
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="text-muted">
                                            <?= $this->language->pay->custom_plan->summary->plan_price ?>
                                        </span>

                                        <div>
                                            <span id="summary_plan_price"></span>

                                            <span class="text-muted"><?= $this->settings->payment->currency ?></span>
                                        </div>
                                    </div>

                                    <div id="summary_discount" style="display: none;">
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="text-muted">
                                                <?= $this->language->pay->custom_plan->summary->discount ?>
                                            </span>

                                            <div>
                                                <span class="discount-value"></span>

                                                <span class="text-muted"><?= $this->settings->payment->currency ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if($data->plan_taxes): ?>
                                        <?php foreach($data->plan_taxes as $row): ?>

                                            <div id="summary_tax_id_<?= $row->tax_id ?>" class="d-flex justify-content-between mb-3">
                                                <div class="d-flex flex-column">
                                                    <span class="text-muted">
                                                        <?= $row->name ?>

                                                        <span data-toggle="tooltip" title="<?= $row->description ?>"><i class="fa fa-fw fa-sm fa-question-circle"></i></span>
                                                    </span>
                                                    <small class="text-muted">
                                                        <?= $this->language->pay->custom_plan->summary->{$row->type == 'inclusive' ? 'tax_inclusive' : 'tax_exclusive'} ?>
                                                    </small>
                                                </div>

                                                <span>
                                                    <?php if($row->value_type == 'percentage'): ?>

                                                        <span class="tax-value"></span>
                                                        <span class="text-muted"><?= $this->settings->payment->currency ?></span>
                                                        <span class="tax-details text-muted">(<?= $row->value ?>%)</span>

                                                    <?php elseif($row->value_type == 'fixed'): ?>

                                                        <span class="tax-value"></span>
                                                        <span class="tax-details"><?= '+' . $row->value ?> <span class="text-muted"><?= $this->settings->payment->currency ?></span></span>

                                                    <?php endif ?>
                                                </span>
                                            </div>

                                        <?php endforeach ?>
                                    <?php endif ?>
                                </div>

                                <?php if($this->settings->payment->codes_is_enabled): ?>
                                    <div class="mt-4">
                                        <button type="button" id="code_button" class="btn btn-block btn-outline-secondary border-gray-100"><?= $this->language->pay->custom_plan->code_button ?></button>

                                        <div style="display: none;" id="code_block">
                                            <div class="form-group">
                                                <label><i class="fa fa-fw fa-sm fa-tags mr-1"></i> <?= $this->language->pay->custom_plan->code ?></label>
                                                <input type="text" name="code" class="form-control" />
                                            </div>

                                            <div class="mt-2"><span id="code_help" class="text-muted"></span></div>
                                        </div>
                                    </div>

                                <?php ob_start() ?>
                                    <script>
                                        'use strict';

                                        $('#code_button').on('click', event => {
                                            $('#code_block').show();
                                            $('#code_button').hide();

                                            event.preventDefault();
                                        })

                                        let timer = null;

                                        $('input[name="code"]').on('change paste keyup', event => {

                                            let code = $(event.currentTarget).val();
                                            let is_valid = false;

                                            clearTimeout(timer);

                                            if(code.trim() == '') {
                                                $(event.currentTarget).removeClass('is-invalid').removeClass('is-valid');

                                                calculate_prices();

                                                return;
                                            }

                                            timer = setTimeout(() => {
                                                $.ajax({
                                                    type: 'POST',
                                                    url: `${url}pay/code`,
                                                    data: {code, global_token, plan_id: altum.plan_id},
                                                    success: data => {

                                                        if(data.status == 'success') {
                                                            is_valid = true;

                                                            /* Set the new discounted price */
                                                            altum.discount = parseInt(data.details.discount);

                                                        } else {
                                                            altum.discount = null;

                                                        }

                                                        $('#code_help').html(data.message);

                                                        if(is_valid) {
                                                            $(event.currentTarget).addClass('is-valid');
                                                            $(event.currentTarget).removeClass('is-invalid');
                                                        } else {
                                                            $(event.currentTarget).addClass('is-invalid');
                                                            $(event.currentTarget).removeClass('is-valid');
                                                        }

                                                        calculate_prices();

                                                    },
                                                    dataType: 'json'
                                                });
                                            }, 500);

                                        });
                                    </script>
                                    <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
                                <?php endif ?>

                            </div>

                            <div class="card-footer bg-white">
                                <div class="d-flex justify-content-between font-weight-bold">
                                    <span class="text-muted">
                                        <?= $this->language->pay->custom_plan->summary->total ?>
                                    </span>

                                    <div>
                                        <span id="summary_total"></span>

                                        <span class="text-muted"><?= $this->settings->payment->currency ?></span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-xl-8">

                    <?php if($this->settings->payment->taxes_and_billing_is_enabled && (empty($this->user->billing->name) || empty($this->user->billing->address) || empty($this->user->billing->city) || empty($this->user->billing->county) || empty($this->user->billing->zip))): ?>
                        <div class="mt-5">
                            <a href="<?= url('account#billing') ?>" class="btn btn-lg btn-block btn-outline-primary" target="_blank"><?= $this->language->pay->custom_plan->billing_required ?></a>
                        </div>
                    <?php else: ?>
                        <div class="mt-5">
                            <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary"><?= $this->language->pay->custom_plan->pay ?></button>
                        </div>

                        <div class="mt-3 text-muted text-center">
                            <small>
                                <?= sprintf(
                                    $this->language->pay->accept,
                                    '<a href="' . $this->settings->terms_and_conditions_url . '">' . $this->language->global->terms_and_conditions . '</a>',
                                    '<a href="' . $this->settings->privacy_policy_url . '">' . $this->language->global->privacy_policy . '</a>'
                                ) ?>
                            </small>
                        </div>
                    <?php endif ?>

                </div>
            </div>
        </form>


    <?php if($data->stripe_session): ?>
        <script src="https://js.stripe.com/v3/"></script>

        <script>
            'use strict';

            let stripe = Stripe(<?= json_encode($this->settings->stripe->publishable_key) ?>);

            stripe.redirectToCheckout({
                sessionId: <?= json_encode($data->stripe_session->id) ?>,
            }).then((result) => {

                /* Nothing for the moment */

            });
        </script>
    <?php endif ?>

    <?php endif ?>

</div>



<?php ob_start() ?>
<script>
    'use strict';

    /* Handlers */
    let check_payment_frequency = () => {
        let payment_frequency = $('[name="payment_frequency"]:checked').val();

        switch(payment_frequency) {
            case 'monthly':

                $('#summary_payment_frequency_monthly').show();
                $('#summary_payment_frequency_annual').hide();
                $('#summary_payment_frequency_lifetime').hide();

                if(altum.payment_type_one_time_enabled) {
                    $('#one_time_type_label').show();
                } else {
                    $('#one_time_type_label').hide();
                }

                if(altum.payment_type_recurring_enabled) {
                    $('#recurring_type_label').show();
                } else {
                    $('#recurring_type_label').hide();
                }

                break;

            case 'annual':

                $('#summary_payment_frequency_monthly').hide();
                $('#summary_payment_frequency_annual').show();
                $('#summary_payment_frequency_lifetime').hide();


                if(altum.payment_type_one_time_enabled) {
                    $('#one_time_type_label').show();
                } else {
                    $('#one_time_type_label').hide();
                }

                if(altum.payment_type_recurring_enabled) {
                    $('#recurring_type_label').show();
                } else {
                    $('#recurring_type_label').hide();
                }

                break;

            case 'lifetime':

                $('#summary_payment_frequency_monthly').hide();
                $('#summary_payment_frequency_annual').hide();
                $('#summary_payment_frequency_lifetime').show();

                /* Show only the one time payment option for the lifetime plan */
                $('#recurring_type_label').hide();
                $('#one_time_type_label').show();

                break;
        }

        $('[name="payment_type"]').filter(':visible:first').click();
    }

    $('[name="payment_frequency"]').on('change', event => {

        check_payment_frequency();

        check_payment_processor();

        calculate_prices();

    });

    let check_payment_processor = () => {
        let payment_processor = $('[name="payment_processor"]:checked').val();

        switch(payment_processor) {
            case 'paypal':

                $('#summary_payment_processor_paypal').show();
                $('#summary_payment_processor_stripe').hide();
                $('#summary_payment_processor_offline_payment').hide();
                $('#offline_payment_processor_wrapper').hide();

                break;

            case 'stripe':

                $('#summary_payment_processor_paypal').hide();
                $('#summary_payment_processor_stripe').show();
                $('#summary_payment_processor_offline_payment').hide();
                $('#offline_payment_processor_wrapper').hide();

                break;

            case 'offline_payment':

                $('#summary_payment_processor_paypal').hide();
                $('#summary_payment_processor_stripe').hide();
                $('#summary_payment_processor_offline_payment').show();
                $('#offline_payment_processor_wrapper').show();

                /* Show only the one time payment option for the lifetime plan */
                $('#recurring_type_label').hide();
                $('#one_time_type_label').show();

                break;
        }

        $('[name="payment_type"]').filter(':visible:first').click();

    };

    $('[name="payment_processor"]').on('change', event => {

        check_payment_frequency();

        check_payment_processor();

    });


    $('[name="payment_type"]').on('change', event => {
        let payment_type = $('[name="payment_type"]:checked').val();

        switch(payment_type) {
            case 'one_time':

                $('#summary_payment_type_one_time').show();
                $('#summary_payment_type_recurring').hide();

                break;

            case 'recurring':

                $('#summary_payment_type_one_time').hide();
                $('#summary_payment_type_recurring').show();

                break;
        }
    });

    let calculate_prices = () => {

        let payment_frequency = $('[name="payment_frequency"]:checked').val();

        let full_price = 0;
        let inclusive_taxes = 0;
        let exclusive_taxes = 0;
        let price_without_inclusive_taxes = 0;
        let price_with_taxes = 0;

        switch(payment_frequency) {
            case 'monthly':

                full_price = altum.monthly_price;

                break;

            case 'annual':

                full_price = altum.annual_price;

                break;

            case 'lifetime':

                full_price = altum.lifetime_price;

                break;
        }

        let price = parseFloat(full_price);

        /* Display the price */
        $('#summary_plan_price').html(price);

        /* Check for potential discounts */
        if(altum.discount) {

            let discount_value = parseFloat((price * altum.discount / 100).toFixed(2));

            price = price - discount_value;

            /* Show it on the summary */
            $('#summary_discount').show();

            $('#summary_discount .discount-value').html(nr(-discount_value, 2));

        } else {
            $('#summary_discount').hide();
        }

        /* Calculate with taxes, if any */
        if(altum.taxes) {

            /* Check for the inclusives */
            let inclusive_taxes_array = [];

            for(let row of altum.taxes) {
                if(row.type == 'exclusive') {
                    continue;
                }

                let inclusive_tax = parseFloat((price - (price / (1 + parseInt(row.value) / 100))).toFixed(2));

                inclusive_taxes_array.push(inclusive_tax);

                /* Display the value of the tax */
                $(`#summary_tax_id_${row.tax_id} .tax-value`).html(nr(inclusive_tax, 2));

            }

            inclusive_taxes = inclusive_taxes_array.reduce((total, number) => total + number, 0);

            price_without_inclusive_taxes = price - inclusive_taxes;

            /* Check for the exclusives */
            let exclusive_taxes_array = [];

            for(let row of altum.taxes) {
                if(row.type == 'inclusive') {
                    continue;
                }

                let exclusive_tax = parseFloat((row.value_type == 'percentage' ? price_without_inclusive_taxes * (parseInt(row.value) / 100) : parseFloat(row.value)).toFixed(2));

                exclusive_taxes_array.push(exclusive_tax);

                /* Display the value of the tax */
                if(row.value_type == 'percentage') {
                    $(`#summary_tax_id_${row.tax_id} .tax-value`).html(`+${nr(exclusive_tax, 2)}`);
                }

            }

            exclusive_taxes = exclusive_taxes_array.reduce((total, number) => total + number, 0);

            /* Price with all the taxes */
            price_with_taxes = price + exclusive_taxes;

            price = price_with_taxes;
        }

        /* Display the total */
        $('#summary_total').html(nr(price, 2));
    }

    /* Select default values */
    $('[name="payment_frequency"]:first').click();
    $('[name="payment_processor"]:first').click();
    $('[name="payment_type"]').filter(':visible:first').click();
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
