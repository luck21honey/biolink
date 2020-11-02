<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex mb-4">
    <h1 class="h3"><i class="fa fa-fw fa-xs fa-wrench text-primary-900 mr-2"></i> <?= $this->language->admin_settings->header ?></h1>
</div>

<?php display_notifications() ?>

<div class="row">
    <div class="mb-5 mb-xl-0 col-12 col-xl-3">
        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            <a class="nav-link active" href="#main" data-toggle="pill" role="tab"><i class="fa fa-fw fa-home fa-sm mr-1"></i> <?= $this->language->admin_settings->tab->main ?></a>
            <a class="nav-link" href="#links" data-toggle="pill" role="tab"><i class="fa fa-fw fa-link fa-sm mr-1"></i> <?= $this->language->admin_settings->tab->links ?></a>
            <a class="nav-link" href="#payment" data-toggle="pill" role="tab"><i class="fa fa-fw fa-dollar-sign fa-sm mr-1"></i> <?= $this->language->admin_settings->tab->payment ?></a>
            <a class="nav-link" href="#business" data-toggle="pill" role="tab"><i class="fa fa-fw fa-briefcase fa-sm mr-1"></i> <?= $this->language->admin_settings->tab->business ?></a>
            <a class="nav-link" href="#captcha" data-toggle="pill" role="tab"><i class="fa fa-fw fa-low-vision fa-sm mr-1"></i> <?= $this->language->admin_settings->tab->captcha ?></a>
            <a class="nav-link" href="#facebook" data-toggle="pill" role="tab"><i class="fab fa-fw fa-facebook fa-sm mr-1"></i> <?= $this->language->admin_settings->tab->facebook ?></a>
            <a class="nav-link" href="#ads" data-toggle="pill" role="tab"><i class="fa fa-fw fa-ad fa-sm mr-1"></i> <?= $this->language->admin_settings->tab->ads ?></a>
            <a class="nav-link" href="#socials" data-toggle="pill" role="tab"><i class="fab fa-fw fa-instagram fa-sm mr-1"></i> <?= $this->language->admin_settings->tab->socials ?></a>
            <a class="nav-link" href="#smtp" data-toggle="pill" role="tab"><i class="fa fa-fw fa-mail-bulk fa-sm mr-1"></i> <?= $this->language->admin_settings->tab->smtp ?></a>
            <a class="nav-link" href="#custom" data-toggle="pill" role="tab"><i class="fa fa-fw fa-paint-brush fa-sm mr-1"></i> <?= $this->language->admin_settings->tab->custom ?></a>
            <a class="nav-link" href="#email_notifications" data-toggle="pill" role="tab"><i class="fa fa-fw fa-bell fa-sm mr-1"></i> <?= $this->language->admin_settings->tab->email_notifications ?></a>
            <a class="nav-link" href="#license" data-toggle="pill" role="tab"><i class="fa fa-fw fa-sm fa-key fa-sm mr-1"></i> <?= $this->language->admin_settings->tab->license ?></a>
        </div>
    </div>

    <div class="col">
        <div class="card">
            <div class="card-body">

                <form action="" method="post" role="form" enctype="multipart/form-data">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="main">
                            <div class="form-group">
                                <label><i class="fa fa-fw fa-heading fa-sm mr-1 text-muted"></i> <?= $this->language->admin_settings->main->title ?></label>
                                <input type="text" name="title" class="form-control form-control-lg" value="<?= $this->settings->title ?>" />
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-fw fa-language fa-sm mr-1 text-muted"></i> <?= $this->language->admin_settings->main->default_language ?></label>
                                <select name="default_language" class="form-control form-control-lg">
                                    <?php foreach(\Altum\Language::$languages as $value) echo '<option value="' . $value . '" ' . (($this->settings->default_language == $value) ? 'selected="selected"' : null) . '>' . $value . '</option>' ?>
                                </select>
                                <small class="text-muted"><?= $this->language->admin_settings->main->default_language_help ?></small>
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-fw fa-sm fa-eye text-muted mr-1"></i> <?= $this->language->admin_settings->main->logo ?></label>
                                <?php if($this->settings->logo != ''): ?>
                                    <div class="m-1">
                                        <img src="<?= SITE_URL . UPLOADS_URL_PATH . 'logo/' . $this->settings->logo ?>" class="img-fluid" style="max-height: 2.5rem;height: 2.5rem;" />
                                    </div>
                                <?php endif ?>
                                <input id="logo-file-input" type="file" name="logo" accept=".gif, .ico, .png, .jpg, .jpeg, .svg" class="form-control form-control-lg" />
                                <small class="text-muted"><?= $this->language->admin_settings->main->logo_help ?></small>
                                <?php if($this->settings->logo != ''): ?>
                                <small class="text-muted"><a href="admin/settings/removelogo<?= \Altum\Middlewares\Csrf::get_url_query() ?>"><?= $this->language->admin_settings->main->logo_remove ?></a></small>
                                <?php endif ?>
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-fw fa-sm fa-icons text-muted mr-1"></i> <?= $this->language->admin_settings->main->favicon ?></label>
                                <?php if($this->settings->favicon != ''): ?>
                                    <div class="m-1">
                                        <img src="<?= SITE_URL . UPLOADS_URL_PATH . 'favicon/' . $this->settings->favicon ?>" class="img-fluid" style="max-height: 32px;height: 32px;" />
                                    </div>
                                <?php endif ?>
                                <input id="favicon-file-input" type="file" name="favicon" accept=".gif, .ico, .png, .jpg, .jpeg, .svg" class="form-control form-control-lg" />
                                <small class="text-muted"><?= $this->language->admin_settings->main->favicon_help ?></small>
                                <?php if($this->settings->favicon != ''): ?>
                                <small class="text-muted"><a href="admin/settings/removefavicon<?= \Altum\Middlewares\Csrf::get_url_query() ?>"><?= $this->language->admin_settings->main->favicon_remove ?></a></small>
                                <?php endif ?>
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-fw fa-atlas fa-sm mr-1 text-muted"></i> <?= $this->language->admin_settings->main->default_timezone ?></label>
                                <select name="default_timezone" class="form-control form-control-lg">
                                    <?php foreach(DateTimeZone::listIdentifiers() as $timezone) echo '<option value="' . $timezone . '" ' . (($this->settings->default_timezone == $timezone) ? 'selected="selected"' : null) . '>' . $timezone . '</option>' ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-fw fa-envelope fa-sm mr-1 text-muted"></i> <?= $this->language->admin_settings->main->email_confirmation ?></label>
                                <select class="form-control form-control-lg" name="email_confirmation">
                                    <option value="1" <?= $this->settings->email_confirmation ? 'selected="selected"' : null ?>><?= $this->language->global->yes ?></option>
                                    <option value="0" <?= !$this->settings->email_confirmation ? 'selected="selected"' : null ?>><?= $this->language->global->no ?></option>
                                </select>
                                <small class="text-muted"><?= $this->language->admin_settings->main->email_confirmation_help ?></small>
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-fw fa-users fa-sm mr-1 text-muted"></i> <?= $this->language->admin_settings->main->register_is_enabled ?></label>

                                <select class="form-control form-control-lg" name="register_is_enabled">
                                    <option value="1" <?= $this->settings->register_is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->yes ?></option>
                                    <option value="0" <?= !$this->settings->register_is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->no ?></option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-fw fa-sitemap fa-sm mr-1 text-muted"></i> <?= $this->language->admin_settings->main->index_url ?></label>
                                <input type="text" name="index_url" class="form-control form-control-lg" value="<?= $this->settings->index_url ?>" />
                                <small class="text-muted"><?= $this->language->admin_settings->main->index_url_help ?></small>
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-fw fa-file-word fa-sm mr-1 text-muted"></i> <?= $this->language->admin_settings->main->terms_and_conditions_url ?></label>
                                <input type="text" name="terms_and_conditions_url" class="form-control form-control-lg" value="<?= $this->settings->terms_and_conditions_url ?>" />
                                <small class="text-muted"><?= $this->language->admin_settings->main->terms_and_conditions_url_help ?></small>
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-fw fa-file-word fa-sm mr-1 text-muted"></i> <?= $this->language->admin_settings->main->privacy_policy_url ?></label>
                                <input type="text" name="privacy_policy_url" class="form-control form-control-lg" value="<?= $this->settings->privacy_policy_url ?>" />
                                <small class="text-muted"><?= $this->language->admin_settings->main->privacy_policy_url_help ?></small>
                            </div>
                        </div>


                        <div class="tab-pane fade" id="links">
                            <div class="form-group">
                                <label><?= $this->language->admin_settings->links->shortener_is_enabled ?></label>

                                <select name="links_shortener_is_enabled" class="form-control form-control-lg">
                                    <option value="1" <?= $this->settings->links->shortener_is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->yes ?></option>
                                    <option value="0" <?= !$this->settings->links->shortener_is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->no ?></option>
                                </select>
                                <small class="text-muted"><?= $this->language->admin_settings->links->shortener_is_enabled_help ?></small>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->links->domains_is_enabled ?></label>

                                <select name="links_domains_is_enabled" class="form-control form-control-lg">
                                    <option value="1" <?= $this->settings->links->domains_is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->yes ?></option>
                                    <option value="0" <?= !$this->settings->links->domains_is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->no ?></option>
                                </select>
                                <small class="text-muted"><?= $this->language->admin_settings->links->domains_is_enabled_help ?></small>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->links->main_domain_is_enabled ?></label>

                                <select name="links_main_domain_is_enabled" class="form-control form-control-lg">
                                    <option value="1" <?= $this->settings->links->main_domain_is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->yes ?></option>
                                    <option value="0" <?= !$this->settings->links->main_domain_is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->no ?></option>
                                </select>
                                <small class="text-muted"><?= $this->language->admin_settings->links->main_domain_is_enabled_help ?></small>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->links->blacklisted_domains ?></label>
                                <textarea class="form-control form-control-lg" name="links_blacklisted_domains"><?= $this->settings->links->blacklisted_domains ?></textarea>
                                <small class="text-muted"><?= $this->language->admin_settings->links->blacklisted_domains_help ?></small>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->links->blacklisted_keywords ?></label>
                                <textarea class="form-control form-control-lg" name="links_blacklisted_keywords"><?= $this->settings->links->blacklisted_keywords ?></textarea>
                                <small class="text-muted"><?= $this->language->admin_settings->links->blacklisted_keywords_help ?></small>
                            </div>

                            <hr class="my-4">

                            <p class="h5"><i class="fa fa-fw fa-fish fa-sm mr-1 text-muted"></i> <?= $this->language->admin_settings->links->phishtank ?></p>
                            <p class="text-muted"><?= $this->language->admin_settings->links->phishtank_help ?></p>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->links->phishtank_is_enabled ?></label>

                                <select name="links_phishtank_is_enabled" class="form-control form-control-lg">
                                    <option value="1" <?= $this->settings->links->phishtank_is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->yes ?></option>
                                    <option value="0" <?= !$this->settings->links->phishtank_is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->no ?></option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->links->phishtank_api_key ?></label>
                                <input type="text" name="links_phishtank_api_key" class="form-control form-control-lg" value="<?= $this->settings->links->phishtank_api_key ?>" />
                                <small class="text-muted"><?= $this->language->admin_settings->links->phishtank_api_key_help ?></small>
                            </div>

                            <hr class="my-4">

                            <p class="h5"><i class="fab fa-fw fa-google fa-sm mr-1 text-muted"></i> <?= $this->language->admin_settings->links->google_safe_browsing ?></p>
                            <p class="text-muted"><?= $this->language->admin_settings->links->google_safe_browsing_help ?></p>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->links->google_safe_browsing_is_enabled ?></label>

                                <select name="links_google_safe_browsing_is_enabled" class="form-control form-control-lg">
                                    <option value="1" <?= $this->settings->links->google_safe_browsing_is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->yes ?></option>
                                    <option value="0" <?= !$this->settings->links->google_safe_browsing_is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->no ?></option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->links->google_safe_browsing_api_key ?></label>
                                <input type="text" name="links_google_safe_browsing_api_key" class="form-control form-control-lg" value="<?= $this->settings->links->google_safe_browsing_api_key ?>" />
                                <small class="text-muted"><?= $this->language->admin_settings->links->google_safe_browsing_api_key_help ?></small>
                            </div>

                        </div>


                        <div class="tab-pane fade" id="payment">
                            <?php if(!in_array($this->settings->license->type, ['SPECIAL','Extended License'])): ?>
                                <div class="alert alert-primary" role="alert">
                                    You need to own the Extended License in order to activate the payment system.
                                </div>
                            <?php endif ?>

                            <div class="<?= !in_array($this->settings->license->type, ['SPECIAL','Extended License']) ? 'container-disabled' : null ?>">
                                <div class="form-group">
                                    <label><i class="fa fa-fw fa-dollar-sign fa-sm mr-1 text-muted"></i> <?= $this->language->admin_settings->payment->is_enabled ?></label>

                                    <select name="payment_is_enabled" class="form-control form-control-lg">
                                        <option value="1" <?= $this->settings->payment->is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->yes ?></option>
                                        <option value="0" <?= !$this->settings->payment->is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->no ?></option>
                                    </select>
                                    <small class="text-muted"><?= $this->language->admin_settings->payment->is_enabled_help ?></small>
                                </div>

                                <div class="form-group">
                                    <label><i class="fa fa-fw fa-credit-card fa-sm mr-1 text-muted"></i> <?= $this->language->admin_settings->payment->type ?></label>

                                    <select name="payment_type" class="form-control form-control-lg">
                                        <option value="one_time" <?= $this->settings->payment->type == 'one_time' ? 'selected="selected"' : null ?>><?= $this->language->admin_settings->payment->type_one_time ?></option>
                                        <option value="recurring" <?= $this->settings->payment->type == 'recurring' ? 'selected="selected"' : null ?>><?= $this->language->admin_settings->payment->type_recurring ?></option>
                                        <option value="both" <?= $this->settings->payment->type == 'both' ? 'selected="selected"' : null ?>><?= $this->language->admin_settings->payment->type_both ?></option>
                                    </select>
                                    <small class="text-muted"><?= $this->language->admin_settings->payment->type_help ?></small>
                                </div>

                                <div class="form-group">
                                    <label><i class="fa fa-fw fa-copyright fa-sm mr-1 text-muted"></i> <?= $this->language->admin_settings->payment->brand_name ?></label>
                                    <input type="text" name="payment_brand_name" class="form-control form-control-lg" value="<?= $this->settings->payment->brand_name ?>" />
                                    <small class="text-muted"><?= $this->language->admin_settings->payment->brand_name_help ?></small>
                                </div>

                                <div class="form-group">
                                    <label><i class="fa fa-fw fa-coins fa-sm mr-1 text-muted"></i> <?= $this->language->admin_settings->payment->currency ?></label>
                                    <input type="text" name="payment_currency" class="form-control form-control-lg" value="<?= $this->settings->payment->currency ?>" />
                                    <small class="text-muted"><?= $this->language->admin_settings->payment->currency_help ?></small>
                                </div>

                                <div class="form-group">
                                    <label><i class="fa fa-fw fa-sm fa-tags text-muted mr-1"></i> <?= $this->language->admin_settings->payment->codes_is_enabled ?></label>

                                    <select name="payment_codes_is_enabled" class="form-control form-control-lg">
                                        <option value="1" <?= $this->settings->payment->codes_is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->yes ?></option>
                                        <option value="0" <?= !$this->settings->payment->codes_is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->no ?></option>
                                    </select>
                                    <small class="text-muted"><?= $this->language->admin_settings->payment->codes_is_enabled_help ?></small>
                                </div>

                                <div class="form-group">
                                    <label><i class="fa fa-fw fa-sm fa-receipt text-muted mr-1"></i> <?= $this->language->admin_settings->payment->taxes_and_billing_is_enabled ?></label>

                                    <select name="payment_taxes_and_billing_is_enabled" class="form-control form-control-lg">
                                        <option value="1" <?= $this->settings->payment->taxes_and_billing_is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->yes ?></option>
                                        <option value="0" <?= !$this->settings->payment->taxes_and_billing_is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->no ?></option>
                                    </select>
                                    <small class="text-muted"><?= $this->language->admin_settings->payment->taxes_and_billing_is_enabled_help ?></small>
                                </div>

                                <hr class="my-4">

                                <p class="h5"><?= $this->language->admin_settings->payment->paypal ?></p>

                                <div class="form-group">
                                    <label><?= $this->language->admin_settings->payment->paypal_is_enabled ?></label>

                                    <select name="paypal_is_enabled" class="form-control form-control-lg">
                                        <option value="1" <?= $this->settings->paypal->is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->yes ?></option>
                                        <option value="0" <?= !$this->settings->paypal->is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->no ?></option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label><?= $this->language->admin_settings->payment->paypal_mode ?></label>

                                    <select name="paypal_mode" class="form-control form-control-lg">
                                        <option value="live" <?= $this->settings->paypal->mode == 'live' ? 'selected="selected"' : null ?>>live</option>
                                        <option value="sandbox" <?= $this->settings->paypal->mode == 'sandbox' ? 'selected="selected"' : null ?>>sandbox</option>
                                    </select>

                                    <small class="text-muted"><?= $this->language->admin_settings->payment->paypal_mode_help ?></small>
                                </div>

                                <div class="form-group">
                                    <label><?= $this->language->admin_settings->payment->paypal_client_id ?></label>
                                    <input type="text" name="paypal_client_id" class="form-control form-control-lg" value="<?= $this->settings->paypal->client_id ?>" />
                                </div>

                                <div class="form-group">
                                    <label><?= $this->language->admin_settings->payment->paypal_secret ?></label>
                                    <input type="text" name="paypal_secret" class="form-control form-control-lg" value="<?= $this->settings->paypal->secret ?>" />
                                </div>

                                <hr class="my-4">

                                <p class="h5"><?= $this->language->admin_settings->payment->stripe ?></p>

                                <div class="form-group">
                                    <label><?= $this->language->admin_settings->payment->stripe_is_enabled ?></label>

                                    <select name="stripe_is_enabled" class="form-control form-control-lg">
                                        <option value="1" <?= $this->settings->stripe->is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->yes ?></option>
                                        <option value="0" <?= !$this->settings->stripe->is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->no ?></option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label><?= $this->language->admin_settings->payment->stripe_publishable_key ?></label>
                                    <input type="text" name="stripe_publishable_key" class="form-control form-control-lg" value="<?= $this->settings->stripe->publishable_key ?>" />
                                </div>

                                <div class="form-group">
                                    <label><?= $this->language->admin_settings->payment->stripe_secret_key ?></label>
                                    <input type="text" name="stripe_secret_key" class="form-control form-control-lg" value="<?= $this->settings->stripe->secret_key ?>" />
                                </div>

                                <div class="form-group">
                                    <label><?= $this->language->admin_settings->payment->stripe_webhook_secret ?></label>
                                    <input type="text" name="stripe_webhook_secret" class="form-control form-control-lg" value="<?= $this->settings->stripe->webhook_secret ?>" />
                                </div>

                                <hr class="my-4">

                                <p class="h5"><?= $this->language->admin_settings->payment->offline_payment ?></p>

                                <div class="form-group">
                                    <label><?= $this->language->admin_settings->payment->offline_payment_is_enabled ?></label>

                                    <select name="offline_payment_is_enabled" class="form-control form-control-lg">
                                        <option value="1" <?= $this->settings->offline_payment->is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->yes ?></option>
                                        <option value="0" <?= !$this->settings->offline_payment->is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->no ?></option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label><?= $this->language->admin_settings->payment->offline_payment_instructions ?></label>
                                    <textarea class="form-control form-control-lg" name="offline_payment_instructions"><?= $this->settings->offline_payment->instructions ?></textarea>
                                    <small class="text-muted"><?= $this->language->admin_settings->payment->offline_payment_instructions_help ?></small>
                                </div>

                            </div>
                        </div>


                        <div class="tab-pane fade" id="business">
                            <p class="h5"><?= $this->language->admin_settings->business->header ?></p>
                            <p class="text-muted"><?= $this->language->admin_settings->business->subheader ?></p>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->business->invoice_is_enabled ?></label>

                                <select name="business_invoice_is_enabled" class="form-control form-control-lg">
                                    <option value="1" <?= $this->settings->business->invoice_is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->yes ?></option>
                                    <option value="0" <?= !$this->settings->business->invoice_is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->no ?></option>
                                </select>

                                <small class="text-muted"><?= $this->language->admin_settings->business->invoice_is_enabled_help ?></small>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->business->invoice_nr_prefix ?></label>
                                <input type="text" name="business_invoice_nr_prefix" class="form-control form-control-lg" value="<?= $this->settings->business->invoice_nr_prefix ?>" />
                                <small class="text-muted"><?= $this->language->admin_settings->business->invoice_nr_prefix_help ?></small>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->name ?></label>
                                        <input type="text" name="business_name" class="form-control form-control-lg" value="<?= $this->settings->business->name ?>" />
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->address ?></label>
                                        <input type="text" name="business_address" class="form-control form-control-lg" value="<?= $this->settings->business->address ?>" />
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->city ?></label>
                                        <input type="text" name="business_city" class="form-control form-control-lg" value="<?= $this->settings->business->city ?>" />
                                    </div>
                                </div>

                                <div class="col-12 col-lg-4">
                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->county ?></label>
                                        <input type="text" name="business_county" class="form-control form-control-lg" value="<?= $this->settings->business->county ?>" />
                                    </div>
                                </div>

                                <div class="col-12 col-lg-2">
                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->zip ?></label>
                                        <input type="text" name="business_zip" class="form-control form-control-lg" value="<?= $this->settings->business->zip ?>" />
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->country ?></label>
                                        <select name="business_country" class="form-control form-control-lg">
                                            <?php foreach(get_countries_array() as $key => $value): ?>
                                                <option value="<?= $key ?>" <?= $this->settings->business->country == $key ? 'selected="selected"' : null ?>><?= $value ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->email ?></label>
                                        <input type="text" name="business_email" class="form-control form-control-lg" value="<?= $this->settings->business->email ?>" />
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->phone ?></label>
                                        <input type="text" name="business_phone" class="form-control form-control-lg" value="<?= $this->settings->business->phone ?>" />
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->tax_type ?></label>
                                        <input type="text" name="business_tax_type" class="form-control form-control-lg" value="<?= $this->settings->business->tax_type ?>" placeholder="<?= $this->language->admin_settings->business->tax_type_placeholder ?>" />
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->tax_id ?></label>
                                        <input type="text" name="business_tax_id" class="form-control form-control-lg" value="<?= $this->settings->business->tax_id ?>" />
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->custom_key_one ?></label>
                                        <input type="text" name="business_custom_key_one" class="form-control form-control-lg" value="<?= $this->settings->business->custom_key_one ?>" />
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->custom_value_one ?></label>
                                        <input type="text" name="business_custom_value_one" class="form-control form-control-lg" value="<?= $this->settings->business->custom_value_one ?>" />
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->custom_key_two ?></label>
                                        <input type="text" name="business_custom_key_two" class="form-control form-control-lg" value="<?= $this->settings->business->custom_key_two ?>" />
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->business->custom_value_two ?></label>
                                        <input type="text" name="business_custom_value_two" class="form-control form-control-lg" value="<?= $this->settings->business->custom_value_two ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="tab-pane fade" id="ads">
                            <p class="text-muted"><?= $this->language->admin_settings->ads->ads_help ?></p>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->ads->header ?></label>
                                <textarea class="form-control form-control-lg" name="ads_header"><?= $this->settings->ads->header ?></textarea>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->ads->footer ?></label>
                                <textarea class="form-control form-control-lg" name="ads_footer"><?= $this->settings->ads->footer ?></textarea>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->ads->header_biolink ?></label>
                                <textarea class="form-control form-control-lg" name="ads_header_biolink"><?= $this->settings->ads->header_biolink ?></textarea>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->ads->footer_biolink ?></label>
                                <textarea class="form-control form-control-lg" name="ads_footer_biolink"><?= $this->settings->ads->footer_biolink ?></textarea>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="captcha">

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->captcha->recaptcha_is_enabled ?></label>

                                <select name="captcha_recaptcha_is_enabled" class="form-control form-control-lg">
                                    <option value="1" <?= $this->settings->captcha->recaptcha_is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->yes ?></option>
                                    <option value="0" <?= !$this->settings->captcha->recaptcha_is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->no ?></option>
                                </select>

                                <small class="text-muted"><?= $this->language->admin_settings->captcha->recaptcha_is_enabled_help ?></small>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->captcha->recaptcha_public_key ?></label>
                                <input type="text" name="captcha_recaptcha_public_key" class="form-control form-control-lg" value="<?= $this->settings->captcha->recaptcha_public_key ?>" />
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->captcha->recaptcha_private_key ?></label>
                                <input type="text" name="captcha_recaptcha_private_key" class="form-control form-control-lg" value="<?= $this->settings->captcha->recaptcha_private_key ?>" />
                            </div>
                        </div>


                        <div class="tab-pane fade" id="facebook">
                            <div class="form-group">
                                <label><?= $this->language->admin_settings->facebook->is_enabled ?></label>

                                <select name="facebook_is_enabled" class="form-control form-control-lg">
                                    <option value="1" <?= $this->settings->facebook->is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->yes ?></option>
                                    <option value="0" <?= !$this->settings->facebook->is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->no ?></option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->facebook->app_id ?></label>
                                <input type="text" name="facebook_app_id" class="form-control form-control-lg" value="<?= $this->settings->facebook->app_id ?>" />
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->facebook->app_secret ?></label>
                                <input type="text" name="facebook_app_secret" class="form-control form-control-lg" value="<?= $this->settings->facebook->app_secret ?>" />
                            </div>
                        </div>

                        <div class="tab-pane fade" id="socials">
                            <p class="text-muted"><?= $this->language->admin_settings->socials->socials_help ?></p>

                            <?php foreach(require APP_PATH . 'includes/admin_socials.php' AS $key => $value): ?>
                                <div class="form-group">
                                    <label><i class="<?= $value['icon'] ?> fa-fw fa-sm mr-1 fa-sm mr-1 text-muted"></i> <?= $value['name'] ?></label>
                                    <input type="text" name="socials_<?= $key ?>" class="form-control form-control-lg" value="<?= $this->settings->socials->{$key} ?>" />
                                </div>
                            <?php endforeach ?>
                        </div>


                        <div class="tab-pane fade" id="smtp">

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->smtp->host ?></label>
                                <input type="text" name="smtp_host" class="form-control form-control-lg" value="<?= $this->settings->smtp->host ?>" />
                                <small class="form-text text-muted"><?= $this->language->admin_settings->smtp->host_help ?></small>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->smtp->from ?></label>
                                <input type="text" name="smtp_from" class="form-control form-control-lg" value="<?= $this->settings->smtp->from ?>" />
                                <small class="form-text text-muted"><?= $this->language->admin_settings->smtp->from_help ?></small>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->smtp->encryption ?></label>
                                        <select name="smtp_encryption" class="form-control form-control-lg">
                                            <option value="0" <?= $this->settings->smtp->encryption == '0' ? 'selected="selected"' : null ?>>None</option>
                                            <option value="ssl" <?= $this->settings->smtp->encryption == 'ssl' ? 'selected="selected"' : null ?>>SSL</option>
                                            <option value="tls" <?= $this->settings->smtp->encryption == 'tls' ? 'selected="selected"' : null ?>>TLS</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label><?= $this->language->admin_settings->smtp->port ?></label>
                                        <input type="text" name="smtp_port" class="form-control form-control-lg" value="<?= $this->settings->smtp->port ?>" />
                                    </div>
                                </div>
                            </div>

                            <div class="custom-control custom-switch mb-3">
                                <input id="smtp_auth" name="smtp_auth" type="checkbox" class="custom-control-input" <?= $this->settings->smtp->auth ? 'checked="checked"' : null ?>>
                                <label class="custom-control-label" for="smtp_auth"><?= $this->language->admin_settings->smtp->auth ?></label>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->smtp->username ?></label>
                                <input type="text" name="smtp_username" class="form-control form-control-lg" value="<?= $this->settings->smtp->username ?>" <?= ($this->settings->smtp->auth) ? null : 'disabled' ?>/>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->smtp->password ?></label>
                                <input type="password" name="smtp_password" class="form-control form-control-lg" value="<?= $this->settings->smtp->password ?>" <?= ($this->settings->smtp->auth) ? null : 'disabled' ?>/>
                            </div>

                            <div class="my-3">
                                <a href="admin/settings/testemail<?= \Altum\Middlewares\Csrf::get_url_query() ?>" class="btn btn-outline-info"><?= $this->language->admin_settings->button->test_email ?></a>
                                <small class="form-text text-muted"><?= $this->language->admin_settings->button->test_email_help ?></small>
                            </div>

                        </div>


                        <div class="tab-pane fade" id="custom">
                            <div class="form-group">
                                <label><i class="fab fa-fw fa-js fa-sm mr-1 text-muted"></i> <?= $this->language->admin_settings->custom->head_js ?></label>
                                <textarea class="form-control form-control-lg" name="custom_head_js"><?= $this->settings->custom->head_js ?></textarea>
                                <small class="text-muted"><?= $this->language->admin_settings->custom->head_js_help ?></small>
                            </div>

                            <div class="form-group">
                                <label><i class="fab fa-fw fa-css3 fa-sm mr-1 text-muted"></i> <?= $this->language->admin_settings->custom->head_css ?></label>
                                <textarea class="form-control form-control-lg" name="custom_head_css"><?= $this->settings->custom->head_css ?></textarea>
                                <small class="text-muted"><?= $this->language->admin_settings->custom->head_css_help ?></small>
                            </div>
                        </div>


                        <div class="tab-pane fade" id="email_notifications">

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->email_notifications->emails ?></label>
                                <textarea class="form-control form-control-lg" name="email_notifications_emails" rows="5"><?= $this->settings->email_notifications->emails ?></textarea>
                                <small class="form-text text-muted"><?= $this->language->admin_settings->email_notifications->emails_help ?></small>
                            </div>

                            <div class="custom-control custom-switch my-3">
                                <input id="email_notifications_new_user" name="email_notifications_new_user" type="checkbox" class="custom-control-input" <?= $this->settings->email_notifications->new_user ? 'checked' : null?>>
                                <label class="custom-control-label" for="email_notifications_new_user"><?= $this->language->admin_settings->email_notifications->new_user ?></label>
                                <small class="form-text text-muted"><?= $this->language->admin_settings->email_notifications->new_user_help ?></small>
                            </div>

                            <div class="custom-control custom-switch my-3">
                                <input id="email_notifications_new_payment" name="email_notifications_new_payment" type="checkbox" class="custom-control-input" <?= $this->settings->email_notifications->new_payment ? 'checked' : null?>>
                                <label class="custom-control-label" for="email_notifications_new_payment"><?= $this->language->admin_settings->email_notifications->new_payment ?></label>
                                <small class="form-text text-muted"><?= $this->language->admin_settings->email_notifications->new_payment_help ?></small>
                            </div>

                        </div>


                        <div class="tab-pane fade" id="license">
                            <div class="form-group">
                                <label><?= $this->language->admin_settings->license->license ?></label>
                                <input type="text" class="form-control form-control-lg disabled" name="license_license" value="<?= $this->settings->license->license ?>" readonly="readonly" />
                                <small class="form-text text-muted"><?= $this->language->admin_settings->license->license_help ?></small>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->license->type ?></label>
                                <input type="text" class="form-control form-control-lg disabled" name="license_type" value="<?= $this->settings->license->type ?>" readonly="readonly" />
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_settings->license->new_license ?></label>
                                <input type="text" class="form-control form-control-lg" name="license_new_license" value="" />
                                <small class="form-text text-muted"><?= $this->language->admin_settings->license->new_license_help ?></small>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" name="submit" class="btn btn-primary"><?= $this->language->global->update ?></button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <p class="text-muted my-3"><?= $this->language->admin_settings->documentation ?></p>
    </div>
</div>


<?php ob_start() ?>
<script>
    $('input[name="smtp_auth"]').on('change', (event) => {

        if($(event.currentTarget).is(':checked')) {
            $('input[name="smtp_username"],input[name="smtp_password"]').removeAttr('disabled');
        } else {
            $('input[name="smtp_username"],input[name="smtp_password"]').attr('disabled', 'true');
        }

    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
