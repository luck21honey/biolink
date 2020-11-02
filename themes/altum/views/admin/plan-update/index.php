
<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <div class="d-flex align-items-center">
        <h1 class="h3 mr-3"><i class="fa fa-fw fa-xs fa-box-open text-primary-900 mr-2"></i> <?= sprintf($this->language->admin_plan_update->header, $data->plan->name) ?></h1>

        <?= include_view(THEME_PATH . 'views/admin/partials/admin_plan_dropdown_button.php', ['id' => $data->plan->plan_id]) ?>
    </div>
</div>

<?php display_notifications() ?>

<div class="card">
    <div class="card-body">

        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />
            <input type="hidden" name="type" value="update" />

            <div class="row">
                <div class="col-12 col-md-4">
                    <h2 class="h4"><?= $this->language->admin_plans->main->header ?></h2>
                    <p class="text-muted"><?= $this->language->admin_plans->main->subheader ?></p>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label for="name"><?= $this->language->admin_plans->main->name ?></label>
                        <input type="text" id="name" name="name" class="form-control form-control-lg" value="<?= $data->plan->name ?>" />
                    </div>

                    <div class="form-group">
                        <label for="status"><?= $this->language->admin_plans->main->status ?></label>
                        <select id="status" name="status" class="form-control form-control-lg">
                            <option value="1" <?= $data->plan->status == 1 ? 'selected="selected"' : null ?>><?= $this->language->global->active ?></option>
                            <option value="0" <?= $data->plan->status == 0 ? 'selected="selected"' : null ?>><?= $this->language->global->disabled ?></option>
                            <option value="2" <?= $data->plan->status == 2 ? 'selected="selected"' : null ?>><?= $this->language->global->hidden ?></option>
                        </select>
                    </div>

                    <?php if($data->plan_id == 'trial'): ?>
                        <div class="form-group">
                            <div class="form-group">
                                <label for="days"><?= $this->language->admin_plans->main->trial->days ?></label>
                                <input type="text" id="days" name="days" class="form-control form-control-lg" value="<?= $data->plan->days ?>" />
                                <div><small class="text-muted"><?= $this->language->admin_plans->main->trial->days_help ?></small></div>
                            </div>
                        </div>
                    <?php endif ?>

                    <?php if(is_numeric($data->plan_id)): ?>
                        <div class="row">
                            <div class="col-sm-12 col-xl-4">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label for="monthly_price"><?= $this->language->admin_plans->main->monthly_price ?> <small class="text-muted"><?= $this->settings->payment->currency ?></small></label>
                                        <input type="text" id="monthly_price" name="monthly_price" class="form-control form-control-lg" value="<?= $data->plan->monthly_price ?>" />
                                        <small class="text-muted"><?= sprintf($this->language->admin_plans->main->price_help, $this->language->admin_plans->main->monthly_price) ?></small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 col-xl-4">
                                <div class="form-group">
                                    <label for="annual_price"><?= $this->language->admin_plans->main->annual_price ?> <small class="text-muted"><?= $this->settings->payment->currency ?></small></label>
                                    <input type="text" id="annual_price" name="annual_price" class="form-control form-control-lg" value="<?= $data->plan->annual_price ?>" />
                                    <small class="text-muted"><?= sprintf($this->language->admin_plans->main->price_help, $this->language->admin_plans->main->annual_price) ?></small>
                                </div>
                            </div>

                            <div class="col-sm-12 col-xl-4">
                                <div class="form-group">
                                    <label for="lifetime_price"><?= $this->language->admin_plans->main->lifetime_price ?> <small class="text-muted"><?= $this->settings->payment->currency ?></small></label>
                                    <input type="text" id="lifetime_price" name="lifetime_price" class="form-control form-control-lg" value="<?= $data->plan->lifetime_price ?>" />
                                    <small class="text-muted"><?= sprintf($this->language->admin_plans->main->price_help, $this->language->admin_plans->main->lifetime_price) ?></small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <span><?= $this->language->admin_plans->main->taxes_ids ?></span>
                            <div><small class="text-muted"><?= sprintf($this->language->admin_plans->main->taxes_ids_help, '<a href="' . url('admin/taxes') .'">', '</a>') ?></small></div>
                        </div>

                        <?php if($data->taxes): ?>
                            <div class="row">
                                <?php foreach($data->taxes as $row): ?>
                                    <div class="col-12 col-xl-6">
                                        <div class="custom-control custom-switch my-3">
                                            <input id="<?= 'tax_id_' . $row->tax_id ?>" name="taxes_ids[<?= $row->tax_id ?>]" type="checkbox" class="custom-control-input" <?= in_array($row->tax_id, $data->plan->taxes_ids) ? 'checked="checked"' : null ?>>
                                            <label class="custom-control-label" for="<?= 'tax_id_' . $row->tax_id ?>"><?= $row->internal_name ?></label>
                                            <div><small><?= $row->name ?></small> - <small class="text-muted"><?= $row->description ?></small></div>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        <?php endif ?>

                    <?php endif ?>

                </div>
            </div>

            <div class="mt-5"></div>

            <div class="row">
                <div class="col-12 col-md-4">
                    <h2 class="h4"><?= $this->language->admin_plans->plan->header ?></h2>
                    <p class="text-muted"><?= $this->language->admin_plans->plan->subheader ?></p>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label for="projects_limit"><?= $this->language->admin_plans->plan->projects_limit ?></label>
                        <input type="number" id="projects_limit" name="projects_limit" min="-1" class="form-control form-control-lg" value="<?= $data->plan->settings->projects_limit ?>" />
                        <small class="text-muted"><?= $this->language->admin_plans->plan->projects_limit_help ?></small>
                    </div>

                    <div class="form-group">
                        <label for="biolinks_limit"><?= $this->language->admin_plans->plan->biolinks_limit ?></label>
                        <input type="number" id="biolinks_limit" name="biolinks_limit" min="-1" class="form-control form-control-lg" value="<?= $data->plan->settings->biolinks_limit ?>" />
                        <small class="text-muted"><?= $this->language->admin_plans->plan->biolinks_limit_help ?></small>
                    </div>

                    <div class="form-group" <?= !$this->settings->links->shortener_is_enabled ? 'style="display: none"' : null ?>>
                        <label for="links_limit"><?= $this->language->admin_plans->plan->links_limit ?></label>
                        <input type="number" id="links_limit" name="links_limit" min="-1" class="form-control form-control-lg" value="<?= $data->plan->settings->links_limit ?>" />
                        <small class="text-muted"><?= $this->language->admin_plans->plan->links_limit_help ?></small>
                    </div>

                    <div class="form-group" <?= !$this->settings->links->domains_is_enabled ? 'style="display: none"' : null ?>>
                        <label for="biolinks_limit"><?= $this->language->admin_plans->plan->domains_limit ?></label>
                        <input type="number" id="domains_limit" name="domains_limit" min="-1" class="form-control form-control-lg" value="<?= $data->plan->settings->domains_limit ?>" />
                        <small class="text-muted"><?= $this->language->admin_plans->plan->domains_limit_help ?></small>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="additional_global_domains" name="additional_global_domains" type="checkbox" class="custom-control-input" <?= $data->plan->settings->additional_global_domains ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="additional_global_domains"><?= $this->language->admin_plans->plan->additional_global_domains ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_plans->plan->additional_global_domains_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="custom_url" name="custom_url" type="checkbox" class="custom-control-input" <?= $data->plan->settings->custom_url ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="custom_url"><?= $this->language->admin_plans->plan->custom_url ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_plans->plan->custom_url_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="deep_links" name="deep_links" type="checkbox" class="custom-control-input" <?= $data->plan->settings->deep_links ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="deep_links"><?= $this->language->admin_plans->plan->deep_links ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_plans->plan->deep_links_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="no_ads" name="no_ads" type="checkbox" class="custom-control-input" <?= $data->plan->settings->no_ads ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="no_ads"><?= $this->language->admin_plans->plan->no_ads ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_plans->plan->no_ads_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="removable_branding" name="removable_branding" type="checkbox" class="custom-control-input" <?= $data->plan->settings->removable_branding ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="removable_branding"><?= $this->language->admin_plans->plan->removable_branding ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_plans->plan->removable_branding_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="custom_branding" name="custom_branding" type="checkbox" class="custom-control-input" <?= $data->plan->settings->custom_branding ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="custom_branding"><?= $this->language->admin_plans->plan->custom_branding ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_plans->plan->custom_branding_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="custom_colored_links" name="custom_colored_links" type="checkbox" class="custom-control-input" <?= $data->plan->settings->custom_colored_links ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="custom_colored_links"><?= $this->language->admin_plans->plan->custom_colored_links ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_plans->plan->custom_colored_links_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="statistics" name="statistics" type="checkbox" class="custom-control-input" <?= $data->plan->settings->statistics ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="statistics"><?= $this->language->admin_plans->plan->statistics ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_plans->plan->statistics_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="google_analytics" name="google_analytics" type="checkbox" class="custom-control-input" <?= $data->plan->settings->google_analytics ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="google_analytics"><?= $this->language->admin_plans->plan->google_analytics ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_plans->plan->google_analytics_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="facebook_pixel" name="facebook_pixel" type="checkbox" class="custom-control-input" <?= $data->plan->settings->facebook_pixel ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="facebook_pixel"><?= $this->language->admin_plans->plan->facebook_pixel ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_plans->plan->facebook_pixel_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="custom_backgrounds" name="custom_backgrounds" type="checkbox" class="custom-control-input" <?= $data->plan->settings->custom_backgrounds ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="custom_backgrounds"><?= $this->language->admin_plans->plan->custom_backgrounds ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_plans->plan->custom_backgrounds_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="verified" name="verified" type="checkbox" class="custom-control-input" <?= $data->plan->settings->verified ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="verified"><?= $this->language->admin_plans->plan->verified ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_plans->plan->verified_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="scheduling" name="scheduling" type="checkbox" class="custom-control-input" <?= $data->plan->settings->scheduling ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="scheduling"><?= $this->language->admin_plans->plan->scheduling ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_plans->plan->scheduling_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="seo" name="seo" type="checkbox" class="custom-control-input" <?= $data->plan->settings->seo ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="seo"><?= $this->language->admin_plans->plan->seo ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_plans->plan->seo_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="utm" name="utm" type="checkbox" class="custom-control-input" <?= $data->plan->settings->utm ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="utm"><?= $this->language->admin_plans->plan->utm ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_plans->plan->utm_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="socials" name="socials" type="checkbox" class="custom-control-input" <?= $data->plan->settings->socials ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="socials"><?= $this->language->admin_plans->plan->socials ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_plans->plan->socials_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="music_embeds" name="music_embeds" type="checkbox" class="custom-control-input" <?= $data->plan->settings->music_embeds ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="music_embeds"><?= $this->language->admin_plans->plan->music_embeds ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_plans->plan->music_embeds_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="video_embeds" name="video_embeds" type="checkbox" class="custom-control-input" <?= $data->plan->settings->video_embeds ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="video_embeds"><?= $this->language->admin_plans->plan->video_embeds ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_plans->plan->video_embeds_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="fonts" name="fonts" type="checkbox" class="custom-control-input" <?= $data->plan->settings->fonts ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="fonts"><?= $this->language->admin_plans->plan->fonts ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_plans->plan->fonts_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="password" name="password" type="checkbox" class="custom-control-input" <?= $data->plan->settings->password ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="password"><?= $this->language->admin_plans->plan->password ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_plans->plan->password_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="sensitive_content" name="sensitive_content" type="checkbox" class="custom-control-input" <?= $data->plan->settings->sensitive_content ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="sensitive_content"><?= $this->language->admin_plans->plan->sensitive_content ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_plans->plan->sensitive_content_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="leap_link" name="leap_link" type="checkbox" class="custom-control-input" <?= $data->plan->settings->leap_link ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="leap_link"><?= $this->language->admin_plans->plan->leap_link ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_plans->plan->leap_link_help ?></small></div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12 col-md-4"></div>

                <div class="col">
                    <button type="submit" name="submit" class="btn btn-primary mb-1"><?= $this->language->global->update ?></button>
                    <button type="submit" name="submit_update_users_plan_settings" class="btn btn-outline-primary mb-1"><?= $this->language->admin_plan_update->update_users_plan_settings->button ?></button>
                </div>
            </div>
        </form>

    </div>
</div>
