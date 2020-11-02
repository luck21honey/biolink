<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <div class="d-flex align-items-center">
        <h1 class="h3 mr-3"><i class="fa fa-fw fa-xs fa-user text-primary-900 mr-2"></i> <?= $this->language->admin_user_update->header ?></h1>

        <?= include_view(THEME_PATH . 'views/admin/partials/admin_user_dropdown_button.php', ['id' => $data->user->user_id]) ?>
    </div>
</div>

<?php display_notifications() ?>

<div class="card">
    <div class="card-body">

        <form action="" method="post" role="form" enctype="multipart/form-data">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <div class="row">
                <div class="col-12 col-md-4">
                    <h2 class="h4"><?= $this->language->admin_user_update->main->header ?></h2>
                    <p class="text-muted"><?= $this->language->admin_user_update->main->subheader ?></p>
                </div>

                <div class="col">

                    <div class="form-group">
                        <label><?= $this->language->admin_user_update->main->name ?></label>
                        <input type="text" name="name" class="form-control form-control-lg" value="<?= $data->user->name ?>" />
                    </div>

                    <div class="form-group">
                        <label><?= $this->language->admin_user_update->main->email ?></label>
                        <input type="text" name="email" class="form-control form-control-lg" value="<?= $data->user->email ?>" />
                    </div>

                    <div class="form-group">
                        <label><?= $this->language->admin_user_update->main->is_enabled ?></label>

                        <select class="form-control form-control-lg" name="is_enabled">
                            <option value="2" <?php if($data->user->active == 2) echo 'selected' ?>><?= $this->language->admin_user_update->main->is_enabled_disabled ?></option>
                            <option value="1" <?php if($data->user->active == 1) echo 'selected' ?>><?= $this->language->admin_user_update->main->is_enabled_active ?></option>
                            <option value="0" <?php if($data->user->active == 0) echo 'selected' ?>><?= $this->language->admin_user_update->main->is_enabled_unconfirmed ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= $this->language->admin_user_update->main->type ?></label>

                        <select class="form-control form-control-lg" name="type">
                            <option value="1" <?php if($data->user->type == 1) echo 'selected' ?>><?= $this->language->admin_user_update->main->type_admin ?></option>
                            <option value="0" <?php if($data->user->type == 0) echo 'selected' ?>><?= $this->language->admin_user_update->main->type_user ?></option>
                        </select>

                        <small class="text-muted"><?= $this->language->admin_user_update->main->type_help ?></small>
                    </div>
                </div>
            </div>

            <div class="mt-5"></div>

            <div class="row">
                <div class="col-12 col-md-4">
                    <h2 class="h4"><?= $this->language->admin_user_update->plan->header ?></h2>
                    <p class="text-muted"><?= $this->language->admin_user_update->plan->header_help ?></p>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label><?= $this->language->admin_user_update->plan->plan_id ?></label>

                        <select class="form-control form-control-lg" name="plan_id">
                            <option value="free" <?php if($data->user->plan->plan_id == 'free') echo 'selected' ?>><?= $this->settings->plan_free->name ?></option>
                            <option value="trial" <?php if($data->user->plan->plan_id == 'trial') echo 'selected' ?>><?= $this->settings->plan_trial->name ?></option>
                            <option value="custom" <?php if($data->user->plan->plan_id == 'custom') echo 'selected' ?>><?= $this->settings->plan_custom->name ?></option>

                            <?php while($row = $data->plans_result->fetch_object()): ?>
                                <option value="<?= $row->plan_id ?>" <?php if($data->user->plan->plan_id == $row->plan_id) echo 'selected' ?>><?= $row->name ?></option>
                            <?php endwhile ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= $this->language->admin_user_update->plan->plan_trial_done ?></label>

                        <select class="form-control form-control-lg" name="plan_trial_done">
                            <option value="1" <?= $data->user->plan_trial_done ? 'selected="selected"' : null ?>><?= $this->language->global->yes ?></option>
                            <option value="0" <?= !$data->user->plan_trial_done ? 'selected="selected"' : null ?>><?= $this->language->global->no ?></option>
                        </select>
                    </div>

                    <div id="plan_expiration_date_container" class="form-group">
                        <label><?= $this->language->admin_user_update->plan->plan_expiration_date ?></label>
                        <input type="text" class="form-control form-control-lg" name="plan_expiration_date" autocomplete="off" value="<?= $data->user->plan_expiration_date ?>">
                        <div class="invalid-feedback">
                            <?= $this->language->admin_user_update->plan->plan_expiration_date_invalid ?>
                        </div>
                    </div>

                    <div id="plan_settings" style="display: none">
                        <div class="form-group">
                            <label for="projects_limit"><?= $this->language->admin_plans->plan->projects_limit ?></label>
                            <input type="number" id="projects_limit" name="projects_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->projects_limit ?>" />
                            <small class="text-muted"><?= $this->language->admin_plans->plan->projects_limit_help ?></small>
                        </div>

                        <div class="form-group">
                            <label for="biolinks_limit"><?= $this->language->admin_plans->plan->biolinks_limit ?></label>
                            <input type="number" id="biolinks_limit" name="biolinks_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->biolinks_limit ?>" />
                            <small class="text-muted"><?= $this->language->admin_plans->plan->biolinks_limit_help ?></small>
                        </div>

                        <div class="form-group" <?= !$this->settings->links->shortener_is_enabled ? 'style="display: none"' : null ?>>
                            <label for="links_limit"><?= $this->language->admin_plans->plan->links_limit ?></label>
                            <input type="number" id="links_limit" name="links_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->links_limit ?>" />
                            <small class="text-muted"><?= $this->language->admin_plans->plan->links_limit_help ?></small>
                        </div>

                        <div class="form-group" <?= !$this->settings->links->domains_is_enabled ? 'style="display: none"' : null ?>>
                            <label for="biolinks_limit"><?= $this->language->admin_plans->plan->domains_limit ?></label>
                            <input type="number" id="domains_limit" name="domains_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->domains_limit ?>" />
                            <small class="text-muted"><?= $this->language->admin_plans->plan->domains_limit_help ?></small>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="additional_global_domains" name="additional_global_domains" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->additional_global_domains ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label" for="additional_global_domains"><?= $this->language->admin_plans->plan->additional_global_domains ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_plans->plan->additional_global_domains_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="custom_url" name="custom_url" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->custom_url ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label" for="custom_url"><?= $this->language->admin_plans->plan->custom_url ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_plans->plan->custom_url_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="deep_links" name="deep_links" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->deep_links ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label" for="deep_links"><?= $this->language->admin_plans->plan->deep_links ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_plans->plan->deep_links_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="no_ads" name="no_ads" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->no_ads ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label" for="no_ads"><?= $this->language->admin_plans->plan->no_ads ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_plans->plan->no_ads_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="removable_branding" name="removable_branding" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->removable_branding ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label" for="removable_branding"><?= $this->language->admin_plans->plan->removable_branding ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_plans->plan->removable_branding_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="custom_branding" name="custom_branding" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->custom_branding ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label" for="custom_branding"><?= $this->language->admin_plans->plan->custom_branding ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_plans->plan->custom_branding_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="custom_colored_links" name="custom_colored_links" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->custom_colored_links ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label" for="custom_colored_links"><?= $this->language->admin_plans->plan->custom_colored_links ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_plans->plan->custom_colored_links_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="statistics" name="statistics" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->statistics ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label" for="statistics"><?= $this->language->admin_plans->plan->statistics ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_plans->plan->statistics_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="google_analytics" name="google_analytics" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->google_analytics ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label" for="google_analytics"><?= $this->language->admin_plans->plan->google_analytics ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_plans->plan->google_analytics_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="facebook_pixel" name="facebook_pixel" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->facebook_pixel ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label" for="facebook_pixel"><?= $this->language->admin_plans->plan->facebook_pixel ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_plans->plan->facebook_pixel_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="custom_backgrounds" name="custom_backgrounds" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->custom_backgrounds ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label" for="custom_backgrounds"><?= $this->language->admin_plans->plan->custom_backgrounds ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_plans->plan->custom_backgrounds_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="verified" name="verified" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->verified ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label" for="verified"><?= $this->language->admin_plans->plan->verified ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_plans->plan->verified_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="scheduling" name="scheduling" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->scheduling ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label" for="scheduling"><?= $this->language->admin_plans->plan->scheduling ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_plans->plan->scheduling_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="seo" name="seo" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->seo ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label" for="seo"><?= $this->language->admin_plans->plan->seo ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_plans->plan->seo_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="utm" name="utm" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->utm ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label" for="utm"><?= $this->language->admin_plans->plan->utm ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_plans->plan->utm_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="socials" name="socials" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->socials ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label" for="socials"><?= $this->language->admin_plans->plan->socials ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_plans->plan->socials_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="music_embeds" name="music_embeds" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->music_embeds ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label" for="music_embeds"><?= $this->language->admin_plans->plan->music_embeds ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_plans->plan->music_embeds_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="video_embeds" name="video_embeds" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->video_embeds ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label" for="video_embeds"><?= $this->language->admin_plans->plan->video_embeds ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_plans->plan->video_embeds_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="fonts" name="fonts" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->fonts ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label" for="fonts"><?= $this->language->admin_plans->plan->fonts ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_plans->plan->fonts_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="password" name="password" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->password ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label" for="password"><?= $this->language->admin_plans->plan->password ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_plans->plan->password_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="sensitive_content" name="sensitive_content" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->sensitive_content ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label" for="sensitive_content"><?= $this->language->admin_plans->plan->sensitive_content ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_plans->plan->sensitive_content_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="leap_link" name="leap_link" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->leap_link ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label" for="leap_link"><?= $this->language->admin_plans->plan->leap_link ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_plans->plan->leap_link_help ?></small></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5"></div>

            <div class="row">
                <div class="col-12 col-md-4">
                    <h2 class="h4"><?= $this->language->admin_user_update->change_password->header ?></h2>
                    <p class="text-muted"><?= $this->language->admin_user_update->change_password->subheader ?></p>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label><?= $this->language->admin_user_update->change_password->new_password ?></label>
                        <input type="password" name="new_password" class="form-control form-control-lg" />
                    </div>

                    <div class="form-group">
                        <label><?= $this->language->admin_user_update->change_password->repeat_password ?></label>
                        <input type="password" name="repeat_password" class="form-control form-control-lg" />
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12 col-md-4"></div>

                <div class="col">
                    <button type="submit" name="submit" class="btn btn-primary"><?= $this->language->global->update ?></button>
                </div>
            </div>

        </form>
    </div>
</div>

<?php ob_start() ?>
    <link href="<?= SITE_URL . ASSETS_URL_PATH . 'css/daterangepicker.min.css' ?>" rel="stylesheet" media="screen">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
    <script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/moment.min.js' ?>"></script>
    <script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/daterangepicker.min.js' ?>"></script>
    <script>
        'use strict';

        let check_plan_id = () => {
            let selected_plan_id = $('[name="plan_id"]').find(':selected').attr('value');

            if(selected_plan_id == 'free') {
                $('#plan_expiration_date_container').hide();
            } else {
                $('#plan_expiration_date_container').show();
            }

            if(selected_plan_id == 'custom') {
                $('#plan_settings').show();
            } else {
                $('#plan_settings').hide();
            }
        };
        check_plan_id();

        /* Check for expiration date to show a warning if expired */
        let check_plan_expiration_date = () => {
            let plan_expiration_date = $('[name="plan_expiration_date"]');

            let plan_expiration_date_object = new Date(plan_expiration_date.val());
            let today_date_object = new Date();

            if(plan_expiration_date_object < today_date_object) {
                $(plan_expiration_date).addClass('is-invalid');
            } else {
                $(plan_expiration_date).removeClass('is-invalid');
            }
        };

        $('[name="plan_expiration_date"]').on('change', check_plan_expiration_date);
        check_plan_expiration_date();

        /* Daterangepicker */
        $('[name="plan_expiration_date"]').daterangepicker({
            startDate: <?= json_encode($data->user->plan_expiration_date) ?>,
            minDate: new Date(),
            alwaysShowCalendars: true,
            singleCalendar: true,
            singleDatePicker: true,
            locale: <?= json_encode(require APP_PATH . 'includes/daterangepicker_translations.php') ?>,
        }, (start, end, label) => {
            check_plan_expiration_date()
        });

        /* Dont show expiration date when the chosen plan is the free one */
        $('[name="plan_id"]').on('change', check_plan_id);
    </script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
