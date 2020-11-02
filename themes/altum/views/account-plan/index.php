<?php defined('ALTUMCODE') || die() ?>

<header class="header pb-0">
    <div class="container">
        <?= $this->views['account_header'] ?>
    </div>
</header>

<section class="container pt-5">

    <?php display_notifications() ?>

    <div class="d-flex flex-column flex-md-row justify-content-between mb-5">
        <div>
            <h2 class="h3"><?= $this->language->account_plan->header ?></h2>
        </div>

        <?php if($this->settings->payment->is_enabled): ?>
            <div class="col-auto p-0">
                <?php if($this->user->plan_id == 'free'): ?>
                    <a href="<?= url('plan/upgrade') ?>" class="btn btn-primary rounded-pill"><i class="fa fa-fw fa-arrow-up"></i> <?= $this->language->account->plan->upgrade_plan ?></a>
                <?php elseif($this->user->plan_id == 'trial'): ?>
                    <a href="<?= url('plan/renew') ?>" class="btn btn-primary rounded-pill"><i class="fa fa-fw fa-sync-alt"></i> <?= $this->language->account->plan->renew_plan ?></a>
                <?php else: ?>
                    <a href="<?= url('plan/renew') ?>" class="btn btn-primary rounded-pill"><i class="fa fa-fw fa-sync-alt"></i> <?= $this->language->account->plan->renew_plan ?></a>
                <?php endif ?>
            </div>
        <?php endif ?>
    </div>

    <div class="row">
        <div class="col-12 col-md-4">
            <h2 class="h3"><?= $this->user->plan->name ?></h2>

            <?php if($this->user->plan_id != 'free'): ?>
                <p class="text-muted">
                    <?= sprintf(
                        $this->user->payment_subscription_id ? $this->language->account_plan->plan->renews : $this->language->account_plan->plan->expires,
                        '<strong>' . \Altum\Date::get($this->user->plan_expiration_date, 2) . '</strong>'
                    ) ?>
                </p>
            <?php endif ?>
        </div>

        <div class="col">
            <?= (new \Altum\Views\View('partials/plan_features', ['settings' => $this->settings]))->run(['plan_settings' => $this->user->plan_settings]) ?>
        </div>
    </div>

    <?php if($this->user->plan_id != 'free' && $this->user->payment_subscription_id): ?>
        <div class="mt-8 d-flex justify-content-between">
            <div>
                <h2 class="h3"><?= $this->language->account_plan->cancel->header ?></h2>
                <p class="text-muted"><?= $this->language->account_plan->cancel->subheader ?></p>
            </div>

            <div class="col-auto">
                <a href="<?= url('account/cancelsubscription' . \Altum\Middlewares\Csrf::get_url_query()) ?>" class="btn btn-secondary" data-confirm="<?= $this->language->account_plan->cancel->confirm_message ?>"><?= $this->language->account_plan->cancel->cancel ?></a>
            </div>
        </div>
    <?php endif ?>

    <?php if($this->settings->payment->is_enabled && $this->settings->payment->codes_is_enabled): ?>
        <div class="row mt-8">
            <div class="col-12 col-md-4">
                <h2 class="h3"><?= $this->language->account_plan->code->header ?></h2>

                <p class="text-muted"><?= $this->language->account_plan->code->subheader ?></p>
            </div>

            <div class="col">
                <form id="code" action="<?= url('account-plan/redeem_code') ?>" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

                    <div class="form-group">
                        <label><i class="fa fa-fw fa-sm fa-tags text-muted mr-1"></i> <?= $this->language->account_plan->code->input ?></label>
                        <input type="text" name="code" class="form-control" />
                        <div class="mt-2"><span id="code_help" class="text-muted"></span></div>
                    </div>

                    <button id="code_submit" type="submit" name="submit" class="btn btn-primary" style="display: none;"><?= $this->language->account_plan->code->submit ?></button>
                </form>
            </div>
        </div>

    <?php ob_start() ?>
        <script>
            /* Disable form submission for code form */
            $('#code').on('submit', event => {
                let code = $('input[name="code"]').val();

                if(code.trim() == '') {
                    event.preventDefault();
                }
            });

            let timer = null;

            $('input[name="code"]').on('change paste keyup', event => {

                let code = $(event.currentTarget).val();
                let is_valid = false;

                clearTimeout(timer);

                if(code.trim() == '') {
                    $('#code_help').html('');
                    $(event.currentTarget).removeClass('is-invalid').removeClass('is-valid');
                    $('#code_submit').hide();

                    return;
                }

                timer = setTimeout(() => {
                    $.ajax({
                        type: 'POST',
                        url: `${url}/account-plan/code`,
                        data: {code, global_token},
                        success: data => {

                            if(data.status == 'success') {
                                is_valid = true;
                            }

                            $('#code_help').html(data.message);

                            if(is_valid) {
                                $(event.currentTarget).addClass('is-valid');
                                $(event.currentTarget).removeClass('is-invalid');
                                $('#code_submit').show();
                            } else {
                                $(event.currentTarget).addClass('is-invalid');
                                $(event.currentTarget).removeClass('is-valid');
                                $('#code_submit').hide();
                            }

                        },
                        dataType: 'json'
                    });
                }, 500);

            });
        </script>
        <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
    <?php endif ?>
</section>
