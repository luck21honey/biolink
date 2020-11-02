<?php defined('ALTUMCODE') || die() ?>

<?php

use Altum\Middlewares\Authentication;

?>

<?php if($this->settings->payment->is_enabled): ?>

    <?php
    $plans = [];
    $available_payment_frequencies = [];

    $plans_result = $this->database->query("SELECT * FROM plans WHERE `status` = 1");

    while($plan = $plans_result->fetch_object()) {
        $plans[] = $plan;

        foreach(['monthly', 'annual', 'lifetime'] as $value) {
            if($plan->{$value . '_price'}) {
                $available_payment_frequencies[$value] = true;
            }
        }
    }

    ?>

    <?php if(count($plans)): ?>
        <div class="mb-5 d-flex justify-content-center">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">

                <?php if(isset($available_payment_frequencies['monthly'])): ?>
                    <label class="btn btn-outline-secondary active" data-payment-frequency="monthly">
                        <input type="radio" name="payment_frequency" checked> <?= $this->language->plan->custom_plan->monthly ?>
                    </label>
                <?php endif ?>

                <?php if(isset($available_payment_frequencies['annual'])): ?>
                    <label class="btn btn-outline-secondary" data-payment-frequency="annual">
                        <input type="radio" name="payment_frequency"> <?= $this->language->plan->custom_plan->annual ?>
                    </label>
                <?php endif ?>

                <?php if(isset($available_payment_frequencies['lifetime'])): ?>
                    <label class="btn btn-outline-secondary" data-payment-frequency="lifetime">
                        <input type="radio" name="payment_frequency"> <?= $this->language->plan->custom_plan->lifetime ?>
                    </label>
                <?php endif ?>

            </div>
        </div>
    <?php endif ?>
<?php endif ?>

<div class="pricing-container">
    <div class="pricing">

        <?php if($this->settings->plan_free->status == 1): ?>

            <div class="pricing-plan">
                <div class="pricing-header">
                    <span class="pricing-name"><?= $this->settings->plan_free->name ?></span>

                    <div class="pricing-price">
                        <span class="pricing-price-amount"><?= $this->language->plan->free->price ?></span>
                    </div>

                    <div class="pricing-details">&nbsp;</div>
                </div>

                <div class="pricing-body">
                    <ul class="pricing-features">
                        <?php if($this->settings->plan_free->settings->projects_limit == -1): ?>
                            <li>
                                <div><?= $this->language->global->plan_settings->unlimited_projects_limit ?></div>
                            </li>
                        <?php else: ?>
                            <li>
                                <div><?= sprintf($this->language->global->plan_settings->projects_limit, $this->settings->plan_free->settings->projects_limit) ?></div>
                            </li>
                        <?php endif ?>

                        <?php if($this->settings->plan_free->settings->biolinks_limit == -1): ?>
                            <li>
                                <div><?= $this->language->global->plan_settings->unlimited_biolinks_limit ?></div>
                            </li>
                        <?php else: ?>
                            <li>
                                <div><?= sprintf($this->language->global->plan_settings->biolinks_limit, $this->settings->plan_free->settings->biolinks_limit) ?></div>
                            </li>
                        <?php endif ?>

                        <?php if($this->settings->links->shortener_is_enabled): ?>
                            <?php if($this->settings->plan_free->settings->links_limit == -1): ?>
                                <li>
                                    <div><?= $this->language->global->plan_settings->unlimited_links_limit ?></div>
                                </li>
                            <?php else: ?>
                                <li>
                                    <div><?= sprintf($this->language->global->plan_settings->links_limit, $this->settings->plan_free->settings->links_limit) ?></div>
                                </li>
                            <?php endif ?>
                        <?php endif ?>

                        <?php if($this->settings->links->domains_is_enabled): ?>
                            <?php if($this->settings->plan_free->settings->domains_limit == -1): ?>
                                <li>
                                    <div><?= $this->language->global->plan_settings->unlimited_domains_limit ?></div>
                                </li>
                            <?php else: ?>
                                <li>
                                    <div><?= sprintf($this->language->global->plan_settings->domains_limit, $this->settings->plan_free->settings->domains_limit) ?></div>
                                </li>
                            <?php endif ?>
                        <?php endif ?>

                        <?php foreach($data->simple_user_plan_settings as $plan_setting): ?>
                            <li>
                                <div class="<?= $this->settings->plan_free->settings->{$plan_setting} ? null : 'text-muted' ?>">
                                    <span data-toggle="tooltip" title="<?= $this->language->global->plan_settings->{$plan_setting . '_help'} ?>"><?= $this->language->global->plan_settings->{$plan_setting} ?></span>
                                </div>

                                <i class="fa fa-fw fa-sm <?= $this->settings->plan_free->settings->{$plan_setting} ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
                            </li>
                        <?php endforeach ?>
                    </ul>

                    <?php if(Authentication::check() && $this->user->plan_id == 'free'): ?>
                        <button class="btn btn-lg btn-block btn-secondary pricing-button"><?= $this->language->plan->button->already_free ?></button>
                    <?php else: ?>
                        <a href="<?= Authentication::check() ? url('pay/free') : url('register?redirect=pay/free') ?>" class="btn btn-lg btn-block btn-primary pricing-button"><?= $this->language->plan->button->choose ?></a>
                    <?php endif ?>
                </div>
            </div>

        <?php endif ?>

        <?php if($this->settings->payment->is_enabled): ?>

            <?php if($this->settings->plan_trial->status == 1): ?>

                <div class="pricing-plan">
                    <div class="pricing-header">
                        <span class="pricing-name"><?= $this->settings->plan_trial->name ?></span>

                        <div class="pricing-price">
                            <span class="pricing-price-amount"><?= $this->language->plan->trial->price ?></span>
                        </div>

                        <div class="pricing-details">&nbsp;</div>
                    </div>

                    <div class="pricing-body">
                        <ul class="pricing-features">
                            <?php if($this->settings->plan_trial->settings->projects_limit == -1): ?>
                                <li>
                                    <div><?= $this->language->global->plan_settings->unlimited_projects_limit ?></div>
                                </li>
                            <?php else: ?>
                                <li>
                                    <div><?= sprintf($this->language->global->plan_settings->projects_limit, $this->settings->plan_trial->settings->projects_limit) ?></div>
                                </li>
                            <?php endif ?>

                            <?php if($this->settings->plan_trial->settings->biolinks_limit == -1): ?>
                                <li>
                                    <div><?= $this->language->global->plan_settings->unlimited_biolinks_limit ?></div>
                                </li>
                            <?php else: ?>
                                <li>
                                    <div><?= sprintf($this->language->global->plan_settings->biolinks_limit, $this->settings->plan_trial->settings->biolinks_limit) ?></div>
                                </li>
                            <?php endif ?>

                            <?php if($this->settings->links->shortener_is_enabled): ?>
                                <?php if($this->settings->plan_trial->settings->links_limit == -1): ?>
                                    <li>
                                        <div><?= $this->language->global->plan_settings->unlimited_links_limit ?></div>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <div><?= sprintf($this->language->global->plan_settings->links_limit, $this->settings->plan_trial->settings->links_limit) ?></div>
                                    </li>
                                <?php endif ?>
                            <?php endif ?>

                            <?php if($this->settings->links->domains_is_enabled): ?>
                                <?php if($this->settings->plan_trial->settings->domains_limit == -1): ?>
                                    <li>
                                        <div><?= $this->language->global->plan_settings->unlimited_domains_limit ?></div>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <div><?= sprintf($this->language->global->plan_settings->domains_limit, $this->settings->plan_trial->settings->domains_limit) ?></div>
                                    </li>
                                <?php endif ?>
                            <?php endif ?>

                            <?php foreach($data->simple_user_plan_settings as $plan_setting): ?>
                                <li>
                                    <div class="<?= $this->settings->plan_trial->settings->{$plan_setting} ? null : 'text-muted' ?>">
                                                        <span data-toggle="tooltip" title="<?= $this->language->global->plan_settings->{$plan_setting . '_help'} ?>"><?= $this->language->global->plan_settings->{$plan_setting} ?></span>
                                    </div>

                                    <i class="fa fa-fw fa-sm <?= $this->settings->plan_trial->settings->{$plan_setting} ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
                                </li>
                            <?php endforeach ?>
                        </ul>

                        <?php if(Authentication::check() && $this->user->plan_trial_done): ?>
                            <button class="btn btn-lg btn-block btn-secondary pricing-button"><?= $this->language->plan->button->disabled ?></button>
                        <?php else: ?>
                            <a href="<?= Authentication::check() ? url('pay/trial') : url('register?redirect=pay/trial') ?>" class="btn btn-lg btn-block btn-primary pricing-button"><?= $this->language->plan->button->choose ?></a>
                        <?php endif ?>
                    </div>
                </div>

            <?php endif ?>

            <?php foreach($plans as $plan): ?>

                <?php $plan->settings = json_decode($plan->settings) ?>

                <div
                    class="pricing-plan"
                    data-plan-monthly="<?= json_encode((bool) $plan->monthly_price) ?>"
                    data-plan-annual="<?= json_encode((bool) $plan->annual_price) ?>"
                    data-plan-lifetime="<?= json_encode((bool) $plan->lifetime_price) ?>"
                >
                    <div class="pricing-header">
                        <span class="pricing-name"><?= $plan->name ?></span>

                        <div class="pricing-price">
                            <span class="pricing-price-amount d-none" data-plan-payment-frequency="monthly"><?= $plan->monthly_price ?></span>
                            <span class="pricing-price-amount d-none" data-plan-payment-frequency="annual"><?= $plan->annual_price ?></span>
                            <span class="pricing-price-amount d-none" data-plan-payment-frequency="lifetime"><?= $plan->lifetime_price ?></span>
                            <span class="pricing-price-currency"><?= $this->settings->payment->currency ?></span>
                        </div>

                        <div class="pricing-details">
                            <span class="d-none" data-plan-payment-frequency="monthly"><?= $this->language->plan->custom_plan->monthly_payments ?></span>
                            <span class="d-none" data-plan-payment-frequency="annual"><?= $this->language->plan->custom_plan->annual_payments ?></span>
                            <span class="d-none" data-plan-payment-frequency="lifetime"><?= $this->language->plan->custom_plan->lifetime_payments ?></span>
                        </div>
                    </div>

                    <div class="pricing-body">
                        <ul class="pricing-features">
                            <?php if($plan->settings->projects_limit == -1): ?>
                                <li>
                                    <div><?= $this->language->global->plan_settings->unlimited_projects_limit ?></div>
                                </li>
                            <?php else: ?>
                                <li>
                                    <div><?= sprintf($this->language->global->plan_settings->projects_limit, $plan->settings->projects_limit) ?></div>
                                </li>
                            <?php endif ?>

                            <?php if($plan->settings->biolinks_limit == -1): ?>
                                <li>
                                    <div><?= $this->language->global->plan_settings->unlimited_biolinks_limit ?></div>
                                </li>
                            <?php else: ?>
                                <li>
                                    <div><?= sprintf($this->language->global->plan_settings->biolinks_limit, $plan->settings->biolinks_limit) ?></div>
                                </li>
                            <?php endif ?>

                            <?php if($this->settings->links->shortener_is_enabled): ?>
                                <?php if($plan->settings->links_limit == -1): ?>
                                    <li>
                                        <div><?= $this->language->global->plan_settings->unlimited_links_limit ?></div>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <div><?= sprintf($this->language->global->plan_settings->links_limit, $plan->settings->links_limit) ?></div>
                                    </li>
                                <?php endif ?>
                            <?php endif ?>

                            <?php if($this->settings->links->domains_is_enabled): ?>
                                <?php if($plan->settings->domains_limit == -1): ?>
                                    <li>
                                        <div><?= $this->language->global->plan_settings->unlimited_domains_limit ?></div>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <div><?= sprintf($this->language->global->plan_settings->domains_limit, $plan->settings->domains_limit) ?></div>
                                    </li>
                                <?php endif ?>
                            <?php endif ?>

                            <?php foreach($data->simple_user_plan_settings as $plan_setting): ?>
                                <li>
                                    <div class="<?= $plan->settings->{$plan_setting} ? null : 'text-muted' ?>">
                                        <span data-toggle="tooltip" title="<?= $this->language->global->plan_settings->{$plan_setting . '_help'} ?>"><?= $this->language->global->plan_settings->{$plan_setting} ?></span>
                                    </div>

                                    <i class="fa fa-fw fa-sm <?= $plan->settings->{$plan_setting} ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
                                </li>
                            <?php endforeach ?>
                        </ul>

                        <a href="<?= Authentication::check() ? url('pay/' . $plan->plan_id) : url('register?redirect=pay/' . $plan->plan_id) ?>" class="btn btn-lg btn-block btn-primary pricing-button"><?= $this->language->plan->button->choose ?></a>
                    </div>
                </div>

            <?php endforeach ?>

            <?php ob_start() ?>
            <script>
                'use strict';

                let payment_frequency_handler = (event = null) => {

                    let payment_frequency = null;

                    if(event) {
                        payment_frequency = $(event.currentTarget).data('payment-frequency');
                    } else {
                        payment_frequency = $('[name="payment_frequency"]:checked').closest('label').data('payment-frequency');
                    }

                    switch(payment_frequency) {
                        case 'monthly':
                            $(`[data-plan-payment-frequency="annual"]`).removeClass('d-inline-block').addClass('d-none');
                            $(`[data-plan-payment-frequency="lifetime"]`).removeClass('d-inline-block').addClass('d-none');

                            break;

                        case 'annual':
                            $(`[data-plan-payment-frequency="monthly"]`).removeClass('d-inline-block').addClass('d-none');
                            $(`[data-plan-payment-frequency="lifetime"]`).removeClass('d-inline-block').addClass('d-none');

                            break

                        case 'lifetime':
                            $(`[data-plan-payment-frequency="monthly"]`).removeClass('d-inline-block').addClass('d-none');
                            $(`[data-plan-payment-frequency="annual"]`).removeClass('d-inline-block').addClass('d-none');

                            break
                    }

                    $(`[data-plan-payment-frequency="${payment_frequency}"]`).addClass('d-inline-block');

                    $(`[data-plan-${payment_frequency}="true"]`).removeClass('d-none').addClass('');
                    $(`[data-plan-${payment_frequency}="false"]`).addClass('d-none').removeClass('');

                };

                $('[data-payment-frequency]').on('click', payment_frequency_handler);

                payment_frequency_handler();
            </script>
            <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

        <?php endif ?>

    </div>
</div>











