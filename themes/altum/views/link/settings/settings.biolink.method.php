<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<div class="row">
    <div class="col-12 col-lg-6">

        <div class="d-flex justify-content-between">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link <?= !isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == 'settings') ? 'active' : null ?>" id="settings-tab" data-toggle="pill" href="#settings" role="tab" aria-controls="settings" aria-selected="true"><?= $this->language->link->header->settings_tab ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($_GET['tab']) && $_GET['tab'] == 'links'? 'active' : null ?>" id="links-tab" data-toggle="pill" href="#links" role="tab" aria-controls="links" aria-selected="false"><?= $this->language->link->header->links_tab ?></a>
                </li>
            </ul>

            <div class="dropdown">
                <button type="button" data-toggle="dropdown" class="btn btn-primary rounded-pill dropdown-toggle dropdown-toggle-simple"><i class="fa fa-fw fa-plus-circle"></i> <?= $this->language->project->links->create ?></button>

                <div class="dropdown-menu dropdown-menu-right">
                    <?php $biolink_link_types = require APP_PATH . 'includes/biolink_link_types.php'; ?>

                    <?php foreach($biolink_link_types as $key): ?>
                    <a
                        href="#"
                        class="dropdown-item <?= ((in_array($key, ['soundcloud', 'spotify']) && !$this->user->plan_settings->music_embeds) || in_array($key, ['youtube', 'vimeo', 'twitch', 'tiktok']) && !$this->user->plan_settings->video_embeds) ? 'container-disabled' : null ?>"
                        data-toggle="modal"
                        data-target="#create_biolink_<?= $key ?>"
                    >
                        <i class="fa fa-fw fa-circle fa-sm mr-1" style="color: <?= $this->language->link->biolink->{$key}->color ?>"></i>

                        <?= $this->language->link->biolink->{$key}->name ?>
                    </a>
                    <?php endforeach ?>
                </div>
            </div>
        </div>

        <div class="tab-content">
            <div class="tab-pane fade <?= !isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == 'settings') ? 'show active' : null ?>" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                <div class="card">
                    <div class="card-body">

                        <form name="update_biolink" action="" method="post" role="form" enctype="multipart/form-data">
                            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />
                            <input type="hidden" name="request_type" value="update" />
                            <input type="hidden" name="type" value="biolink" />
                            <input type="hidden" name="link_id" value="<?= $data->link->link_id ?>" />

                            <div class="notification-container"></div>

                            <div class="form-group">
                                <label><i class="fa fa-fw fa-link fa-sm mr-1"></i> <?= $this->language->link->settings->url ?></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <?php if(count($data->domains)): ?>
                                            <select name="domain_id" class="appearance-none select-custom-altum form-control input-group-text">
                                                <?php if($this->settings->links->main_domain_is_enabled || \Altum\Middlewares\Authentication::is_admin()): ?>
                                                    <option value="" <?= $data->link->domain ? 'selected="selected"' : null ?>><?= url() ?></option>
                                                <?php endif ?>

                                                <?php foreach($data->domains as $row): ?>
                                                    <option value="<?= $row->domain_id ?>" <?= $data->link->domain && $row->domain_id == $data->link->domain->domain_id ? 'selected="selected"' : null ?>><?= $row->url ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        <?php else: ?>
                                            <span class="input-group-text"><?= url() ?></span>
                                        <?php endif ?>
                                    </div>
                                    <input type="text" class="form-control" name="url" placeholder="<?= $this->language->link->settings->url_placeholder ?>" value="<?= $data->link->url ?>" <?= !$this->user->plan_settings->custom_url ? 'readonly="readonly"' : null ?> />
                                </div>
                                <small class="text-muted"><?= $this->language->link->settings->url_help ?></small>
                            </div>

                            <?php

                            /* Check if we have avatar or we show the default */
                            if(empty($data->link->settings->image) || !file_exists(UPLOADS_PATH . 'avatars/' . $data->link->settings->image)) {
                                $data->link->settings->image_url = SITE_URL . ASSETS_URL_PATH . 'images/avatar_default.png';
                            } else {
                                $data->link->settings->image_url = SITE_URL . UPLOADS_URL_PATH . 'avatars/' . $data->link->settings->image;
                            }

                            ?>

                            <div class="form-group">
                                <div class="m-1 d-flex flex-column align-items-center justify-content-center">
                                    <label aria-label="<?= $this->language->link->settings->image ?>" class="clickable">
                                        <img id="image_file_preview" src="<?= $data->link->settings->image_url ?>" data-default-src="<?= $data->link->settings->image_url ?>" data-empty-src="<?= SITE_URL . ASSETS_URL_PATH . 'images/avatar_default.png' ?>" class="img-fluid link-image-preview" />
                                        <input id="image_file_input" type="file" name="image" accept=".gif, .ico, .png, .jpg, .jpeg, .svg" class="form-control" style="display:none;" />
                                        <input type="hidden" name="image_delete" value="0" class="form-control" />
                                    </label>

                                    <div id="image_file_status" <?= empty($data->link->settings->image) ? 'style="display: none;"' : null ?>>
                                        <button type="button" id="image_file_remove" class="btn btn-outline-secondary" data-toggle="tooltip" title="<?= $this->language->link->settings->image_remove ?>"><i class="fa fa-fw fa-trash-alt"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="settings_title"><i class="fa fa-fw fa-heading fa-sm mr-1"></i> <?= $this->language->link->settings->title ?></label>
                                <input type="text" id="settings_title" name="title" class="form-control" value="<?= $data->link->settings->title ?>" required="required" />
                            </div>

                            <div class="form-group">
                                <label for="settings_description"><i class="fa fa-fw fa-pen-fancy fa-sm mr-1"></i> <?= $this->language->link->settings->description ?></label>
                                <input type="text" id="settings_description" name="description" class="form-control" value="<?= $data->link->settings->description ?>" />
                            </div>

                            <div class="custom-control custom-switch mr-3 mb-3 <?= !$this->user->plan_settings->verified ? 'container-disabled': null ?>">
                                <input
                                        type="checkbox"
                                        class="custom-control-input"
                                        id="display_verified"
                                        name="display_verified"
                                    <?= !$this->user->plan_settings->verified ? 'disabled="disabled"': null ?>
                                    <?= $data->link->settings->display_verified ? 'checked="checked"' : null ?>
                                >
                                <label class="custom-control-label clickable" for="display_verified"><?= $this->language->link->settings->display_verified ?></label>
                            </div>

                            <div class="form-group">
                                <label for="settings_text_color"><i class="fa fa-fw fa-paint-brush fa-sm mr-1"></i> <?= $this->language->link->settings->text_color ?></label>
                                <input type="hidden" id="settings_text_color" name="text_color" class="form-control" value="<?= $data->link->settings->text_color ?>" required="required" />
                                <div id="settings_text_color_pickr"></div>
                            </div>

                            <div class="form-group">
                                <label for="settings_background_type"><i class="fa fa-fw fa-fill fa-sm mr-1"></i> <?= $this->language->link->settings->background_type ?></label>
                                <select id="settings_background_type" name="background_type" class="form-control">
                                    <?php foreach($biolink_backgrounds as $key => $value): ?>
                                        <option value="<?= $key ?>" <?= $data->link->settings->background_type == $key ? 'selected="selected"' : null?>><?= $this->language->link->settings->{'background_type_' . $key} ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div id="background_type_preset" class="row">
                                <?php foreach($biolink_backgrounds['preset'] as $key): ?>
                                    <label for="settings_background_type_preset_<?= $key ?>" class="m-0 col-4 mb-4">
                                        <input type="radio" name="background" value="<?= $key ?>" id="settings_background_type_preset_<?= $key ?>" class="d-none" <?= $data->link->settings->background == $key ? 'checked="checked"' : null ?>/>

                                        <div class="link-background-type-preset link-body-background-<?= $key ?>"></div>
                                    </label>
                                <?php endforeach ?>
                            </div>

                            <div class="<?= !$this->user->plan_settings->custom_backgrounds ? 'container-disabled': null ?>">
                                <div id="background_type_gradient">
                                    <div class="form-group">
                                        <label for="settings_background_type_gradient_color_one"><?= $this->language->link->settings->background_type_gradient_color_one ?></label>
                                        <input type="hidden" id="settings_background_type_gradient_color_one" name="background[]" class="form-control" value="<?= $data->link->settings->background->color_one ?? '' ?>" />
                                        <div id="settings_background_type_gradient_color_one_pickr"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="settings_background_type_gradient_color_two"><?= $this->language->link->settings->background_type_gradient_color_two ?></label>
                                        <input type="hidden" id="settings_background_type_gradient_color_two" name="background[]" class="form-control" value="<?= $data->link->settings->background->color_two ?? '' ?>" />
                                        <div id="settings_background_type_gradient_color_two_pickr"></div>
                                    </div>
                                </div>

                                <div id="background_type_color">
                                    <div class="form-group">
                                        <label for="settings_background_type_color"><?= $this->language->link->settings->background_type_color ?></label>
                                        <input type="hidden" id="settings_background_type_color" name="background" class="form-control" value="<?= is_string($data->link->settings->background) ? $data->link->settings->background : '' ?>" />
                                        <div id="settings_background_type_color_pickr"></div>
                                    </div>
                                </div>

                                <div id="background_type_image">
                                    <div class="form-group">
                                        <label><?= $this->language->link->settings->background_type_image ?></label>
                                        <?php if(is_string($data->link->settings->background) && file_exists(UPLOADS_PATH . 'backgrounds/' . $data->link->settings->background)): ?>
                                            <img id="background_type_image_preview" src="<?= SITE_URL . UPLOADS_URL_PATH . 'backgrounds/' . $data->link->settings->background ?>" data-default-src="<?= SITE_URL . UPLOADS_URL_PATH . 'backgrounds/' . $data->link->settings->background ?>" class="link-background-type-image img-fluid" />
                                        <?php endif ?>
                                        <input id="background_type_image_input" type="file" name="background" accept=".gif, .ico, .png, .jpg, .jpeg, .svg" class="form-control" />
                                        <p id="background_type_image_status" style="display: none;">
                                            <span class="text-muted"><?= $this->language->link->settings->image_status ?></span>
                                            <span id="background_type_image_remove" class="clickable" data-toggle="tooltip" title="<?= $this->language->link->settings->image_remove ?>"><i class="fa fa-fw fa-trash-alt"></i></span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group <?= !$this->user->plan_settings->leap_link ? 'container-disabled': null ?>">
                                <label for="leap_link"><i class="fa fa-fw fa-forward fa-sm mr-1"></i> <?= $this->language->link->settings->leap_link ?></label>
                                <input id="leap_link" type="text" class="form-control" name="leap_link" value="<?= $data->link->settings->leap_link ?>" <?= !$this->user->plan_settings->leap_link ? 'disabled="disabled"': null ?> />
                                <small class="text-muted"><?= $this->language->link->settings->leap_link_help ?></small>
                            </div>

                            <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#branding_container" aria-expanded="false" aria-controls="branding_container">
                                <?= $this->language->link->settings->branding_header ?>
                            </button>

                            <div class="collapse" id="branding_container">
                                <div class="custom-control custom-switch mr-3 mb-3 <?= !$this->user->plan_settings->removable_branding ? 'container-disabled': null ?>">
                                    <input
                                            type="checkbox"
                                            class="custom-control-input"
                                            id="display_branding"
                                            name="display_branding"
                                        <?= !$this->user->plan_settings->removable_branding ? 'disabled="disabled"': null ?>
                                        <?= $data->link->settings->display_branding ? 'checked="checked"' : null ?>
                                    >
                                    <label class="custom-control-label clickable" for="display_branding"><?= $this->language->link->settings->display_branding ?></label>
                                </div>

                                <div class="<?= !$this->user->plan_settings->custom_branding ? 'container-disabled': null ?>">
                                    <div class="form-group">
                                        <label><i class="fa fa-fw fa-random fa-sm mr-1"></i> <?= $this->language->link->settings->branding->name ?></label>
                                        <input id="branding_name" type="text" class="form-control" name="branding_name" value="<?= $data->link->settings->branding->name ?? '' ?>" />
                                        <small class="text-muted"><?= $this->language->link->settings->branding->name_help ?></small>
                                    </div>

                                    <div class="form-group">
                                        <label><i class="fa fa-fw fa-link fa-sm mr-1"></i> <?= $this->language->link->settings->branding->url ?></label>
                                        <input id="branding_url" type="text" class="form-control" name="branding_url" value="<?= $data->link->settings->branding->url ?? '' ?>" />
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#analytics_container" aria-expanded="false" aria-controls="analytics_container">
                                <?= $this->language->link->settings->analytics_header ?>
                            </button>

                            <div class="collapse" id="analytics_container">
                                <div class="<?= !$this->user->plan_settings->google_analytics ? 'container-disabled': null ?>">
                                    <div class="form-group">
                                        <label><i class="fab fa-fw fa-google fa-sm mr-1"></i> <?= $this->language->link->settings->google_analytics ?></label>
                                        <input id="google_analytics" type="text" class="form-control" name="google_analytics" value="<?= $data->link->settings->google_analytics ?? '' ?>" />
                                        <small class="text-muted"><?= $this->language->link->settings->google_analytics_help ?></small>
                                    </div>
                                </div>

                                <div class="<?= !$this->user->plan_settings->facebook_pixel ? 'container-disabled': null ?>">
                                    <div class="form-group">
                                        <label><i class="fab fa-fw fa-facebook fa-sm mr-1"></i> <?= $this->language->link->settings->facebook_pixel ?></label>
                                        <input id="facebook_pixel" type="text" class="form-control" name="facebook_pixel" value="<?= $data->link->settings->facebook_pixel ?? '' ?>" />
                                        <small class="text-muted"><?= $this->language->link->settings->facebook_pixel_help ?></small>
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#seo_container" aria-expanded="false" aria-controls="seo_container">
                                <?= $this->language->link->settings->seo_header ?>
                            </button>

                            <div class="collapse" id="seo_container">
                                <div class="<?= !$this->user->plan_settings->seo ? 'container-disabled': null ?>">
                                    <div class="custom-control custom-switch mb-3">
                                        <input id="seo_block" name="seo_block" type="checkbox" class="custom-control-input" <?= $data->link->settings->seo->block ? 'checked="checked"' : null ?>>
                                        <label class="custom-control-label" for="seo_block"><?= $this->language->link->settings->seo_block ?></label>
                                        <small class="form-text text-muted"><?= $this->language->link->settings->seo_block_help ?></small>
                                    </div>

                                    <div class="form-group">
                                        <label><i class="fa fa-fw fa-heading fa-sm mr-1"></i> <?= $this->language->link->settings->seo_title ?></label>
                                        <input id="seo_title" type="text" class="form-control" name="seo_title" value="<?= $data->link->settings->seo->title ?? '' ?>" />
                                        <small class="text-muted"><?= $this->language->link->settings->seo_title_help ?></small>
                                    </div>

                                    <div class="form-group">
                                        <label><i class="fa fa-fw fa-paragraph fa-sm mr-1"></i> <?= $this->language->link->settings->seo_meta_description ?></label>
                                        <input id="seo_meta_description" type="text" class="form-control" name="seo_meta_description" value="<?= $data->link->settings->seo->meta_description ?? '' ?>" />
                                        <small class="text-muted"><?= $this->language->link->settings->seo_meta_description_help ?></small>
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#utm_container" aria-expanded="false" aria-controls="utm_container">
                                <?= $this->language->link->settings->utm_header ?>
                            </button>

                            <div class="collapse" id="utm_container">
                                <div class="<?= !$this->user->plan_settings->utm ? 'container-disabled': null ?>">
                                    <small class="text-muted"><?= $this->language->link->settings->utm_campaign ?></small>

                                    <div class="form-group">
                                        <label><?= $this->language->link->settings->utm_medium ?></label>
                                        <input id="utm_medium" type="text" class="form-control" name="utm_medium" value="<?= $data->link->settings->utm->medium ?? '' ?>" />
                                    </div>

                                    <div class="form-group">
                                        <label><?= $this->language->link->settings->utm_source ?></label>
                                        <input id="utm_source" type="text" class="form-control" name="utm_source" value="<?= $data->link->settings->utm->source ?? '' ?>" />
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#socials_container" aria-expanded="false" aria-controls="socials_container">
                                <?= $this->language->link->settings->socials_header ?>
                            </button>

                            <div class="collapse" id="socials_container">
                                <div class="<?= !$this->user->plan_settings->socials ? 'container-disabled': null ?>">
                                    <div class="form-group">
                                        <label for="settings_socials_color"><i class="fa fa-fw fa-paint-brush fa-sm mr-1"></i> <?= $this->language->link->settings->socials_color ?></label>
                                        <input type="hidden" id="settings_socials_color" name="socials_color" class="form-control" value="<?= $data->link->settings->socials_color ?>" required="required" />
                                        <div id="settings_socials_color_pickr"></div>
                                    </div>

                                    <?php $biolink_socials = require APP_PATH . 'includes/biolink_socials.php'; ?>

                                    <?php foreach($biolink_socials as $key => $value): ?>

                                        <?php if($value['input_group']): ?>
                                            <div class="form-group">
                                                <label><i class="<?= $this->language->link->settings->socials->{$key}->icon ?> fa-fw fa-sm mr-1"></i> <?= $this->language->link->settings->socials->{$key}->name ?></label>

                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><?= str_replace('%s', '', $value['format']) ?></span>
                                                    </div>
                                                    <input type="text" class="form-control" name="socials[<?= $key ?>]" placeholder="<?= $this->language->link->settings->socials->{$key}->placeholder ?>" value="<?= $data->link->settings->socials->{$key} ?? '' ?>" />
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="form-group">
                                                <label><i class="<?= $this->language->link->settings->socials->{$key}->icon ?> fa-fw fa-sm mr-1"></i> <?= $this->language->link->settings->socials->{$key}->name ?></label>
                                                <input type="text" class="form-control" name="socials[<?= $key ?>]" placeholder="<?= $this->language->link->settings->socials->{$key}->placeholder ?>" value="<?= $data->link->settings->socials->{$key} ?? '' ?>" />
                                            </div>
                                        <?php endif ?>


                                    <?php endforeach ?>

                                </div>
                            </div>

                            <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#fonts_container" aria-expanded="false" aria-controls="fonts_container">
                                <?= $this->language->link->settings->fonts_header ?>
                            </button>

                            <div class="collapse" id="fonts_container">
                                <div class="<?= !$this->user->plan_settings->fonts ? 'container-disabled': null ?>">
                                    <?php $biolink_fonts = require APP_PATH . 'includes/biolink_fonts.php'; ?>

                                    <div class="form-group">
                                        <label for="settings_font"><i class="fa fa-fw fa-pen-nib fa-sm mr-1"></i> <?= $this->language->link->settings->font ?></label>
                                        <select id="settings_font" name="font" class="form-control">
                                            <?php foreach($biolink_fonts as $key => $value): ?>
                                                <option value="<?= $key ?>" <?= $data->link->settings->font == $key ? 'selected="selected"' : null?>><?= $value['name'] ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>

                                </div>
                            </div>

                            <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#protection_container" aria-expanded="false" aria-controls="protection_container">
                                <?= $this->language->link->settings->protection_header ?>
                            </button>

                            <div class="collapse" id="protection_container">
                                <div>

                                    <div class="form-group <?= !$this->user->plan_settings->password ? 'container-disabled': null ?>">
                                        <label for="password"><i class="fa fa-fw fa-key fa-sm mr-1"></i> <?= $this->language->link->settings->password ?></label>
                                        <input id="password" type="password" class="form-control" name="password" value="<?= $data->link->settings->password ?>" <?= !$this->user->plan_settings->password ? 'disabled="disabled"': null ?> />
                                        <small class="text-muted"><?= $this->language->link->settings->password_help ?></small>
                                    </div>

                                    <div class="custom-control custom-switch mr-3 mb-3 <?= !$this->user->plan_settings->sensitive_content ? 'container-disabled': null ?>">
                                        <input
                                                type="checkbox"
                                                class="custom-control-input"
                                                id="sensitive_content"
                                                name="sensitive_content"
                                            <?= !$this->user->plan_settings->sensitive_content ? 'disabled="disabled"': null ?>
                                            <?= $data->link->settings->sensitive_content ? 'checked="checked"' : null ?>
                                        >
                                        <label class="custom-control-label clickable" for="sensitive_content"><?= $this->language->link->settings->sensitive_content ?></label>
                                        <small class="form-text text-muted"><?= $this->language->link->settings->sensitive_content_help ?></small>
                                    </div>

                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" name="submit" class="btn btn-block btn-primary"><?= $this->language->global->update ?></button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <div class="tab-pane fade <?= isset($_GET['tab']) && $_GET['tab'] == 'links'? 'show active' : null ?>" id="links" role="tabpanel" aria-labelledby="links-tab">

                <?php if($data->link_links_result->num_rows): ?>
                    <?php while($row = $data->link_links_result->fetch_object()): ?>

                    <?php $row->settings = json_decode($row->settings) ?>

                        <div class="link card <?= $row->is_enabled ? null : 'custom-row-inactive' ?> my-4" data-link-id="<?= $row->link_id ?>">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="custom-row-side-controller">
                                        <span data-toggle="tooltip" title="<?= $this->language->link->links->link_sort ?>">
                                            <i class="fa fa-fw fa-bars text-muted custom-row-side-controller-grab drag"></i>
                                        </span>
                                    </div>

                                    <div class="col-1 mr-2 p-0 d-none d-lg-block">
                                        <span class="fa-stack fa-1x" data-toggle="tooltip" title="<?= $this->language->link->biolink->{$row->subtype}->name ?>">
                                            <i class="fa fa-circle fa-stack-2x" style="color: <?= $this->language->link->biolink->{$row->subtype}->color ?>"></i>
                                            <i class="fas <?= $this->language->link->biolink->{$row->subtype}->icon ?> fa-stack-1x fa-inverse"></i>
                                        </span>
                                    </div>

                                    <div class="col-6 col-md-6">
                                        <div class="d-flex flex-column">
                                            <a href="#"
                                               data-toggle="collapse"
                                               data-target="#link_expanded_content<?= $row->link_id ?>"
                                               aria-expanded="false"
                                               aria-controls="link_expanded_content<?= $row->link_id ?>"
                                            >
                                                <strong><?= in_array($row->subtype, ['spotify', 'youtube', 'vimeo', 'tiktok', 'twitch', 'soundcloud', 'text', 'image', 'mail']) ? $this->language->link->biolink->{$row->subtype}->name : $row->settings->name ?></strong>
                                            </a>

                                            <span class="d-flex align-items-center">
                                            <?php if(!empty($row->location_url)): ?>
                                                <img src="https://external-content.duckduckgo.com/ip3/<?= parse_url($row->location_url)['host'] ?>.ico" class="img-fluid icon-favicon mr-1" />
                                                <span class="d-inline-block text-truncate">
                                                <a href="<?= $row->location_url ?>" class="text-muted" title="<?= $row->location_url ?>"><?= $row->location_url ?></a>
                                            </span>
                                            <?php elseif(!empty($row->url)): ?>
                                                <img src="https://external-content.duckduckgo.com/ip3/<?= parse_url(url($row->url))['host'] ?>.ico" class="img-fluid icon-favicon mr-1" />
                                                <span class="d-inline-block text-truncate">
                                                <a href="<?= url($row->url) ?>" class="text-muted" title="<?= url($row->url) ?>"><?= url($row->url) ?></a>
                                            </span>
                                            <?php endif ?>
                                        </span>

                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <?php if(!in_array($row->subtype, ['mail', 'text', 'image', 'youtube', 'vimeo', 'tiktok', 'twitch', 'spotify', 'soundcloud'])): ?>
                                            <a href="<?= url('link/' . $row->link_id . '/statistics') ?>">
                                                <span data-toggle="tooltip" title="<?= $this->language->project->links->clicks ?>" class="badge badge-primary"><i class="fa fa-fw fa-chart-bar mr-1"></i> <?= nr($row->clicks) ?></span>
                                            </a>
                                        <?php endif ?>
                                    </div>

                                    <div class="col-2 col-md-auto">
                                        <div class="custom-control custom-switch" data-toggle="tooltip" title="<?= $this->language->link->links->is_enabled_tooltip ?>">
                                            <input
                                                    type="checkbox"
                                                    class="custom-control-input"
                                                    id="biolink_link_is_enabled_<?= $row->link_id ?>"
                                                    data-row-id="<?= $row->link_id ?>"
                                                    <?= $row->is_enabled ? 'checked="checked"' : null ?>
                                            >
                                            <label class="custom-control-label clickable" for="biolink_link_is_enabled_<?= $row->link_id ?>"></label>
                                        </div>
                                    </div>

                                    <div class="col-1 d-flex justify-content-end">
                                        <div class="dropdown">
                                            <a href="#" data-toggle="dropdown" class="text-secondary dropdown-toggle dropdown-toggle-simple">
                                                <i class="fa fa-ellipsis-v"></i>

                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a href="#"
                                                       class="dropdown-item"
                                                       data-toggle="collapse"
                                                       data-target="#link_expanded_content<?= $row->link_id ?>"
                                                       aria-expanded="false"
                                                       aria-controls="link_expanded_content<?= $row->link_id ?>"
                                                    >
                                                        <i class="fa fa-fw fa-pencil-alt"></i> <?= $this->language->global->edit ?>
                                                    </a>

                                                    <?php if(!in_array($row->subtype, ['mail', 'text', 'youtube', 'vimeo', 'tiktok', 'twitch', 'spotify', 'soundcloud'])): ?>
                                                        <a href="<?= url('link/' . $row->link_id . '/statistics') ?>" class="dropdown-item"><i class="fa fa-fw fa-chart-bar"></i> <?= $this->language->link->statistics->link ?></a>
                                                    <?php endif ?>

                                                    <?php if($row->subtype == 'link'): ?>
                                                        <a href="#" class="dropdown-item" data-duplicate="true" data-row-id="<?= $row->link_id ?>"><i class="fa fa-fw fa-copy"></i> <?= $this->language->link->links->duplicate ?></a>
                                                    <?php endif ?>

                                                    <a href="#" class="dropdown-item" data-delete="<?= $this->language->global->info_message->confirm_delete ?>" data-row-id="<?= $row->link_id ?>"><i class="fa fa-fw fa-times"></i> <?= $this->language->global->delete ?></a>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="collapse mt-3" id="link_expanded_content<?= $row->link_id ?>" data-link-subtype="<?= $row->subtype ?>">
                                    <?php require THEME_PATH . 'views/link/settings/' . $row->subtype . '_form.settings.biolink.method.php' ?>
                                </div>
                            </div>
                        </div>

                    <?php endwhile ?>
                <?php else: ?>

                    <div class="d-flex flex-column align-items-center justify-content-center mt-5">
                        <img src="<?= SITE_URL . ASSETS_URL_PATH . 'images/no_data.svg' ?>" class="col-10 col-md-8 col-lg-6 mb-4" alt="<?= $this->language->link->links->no_data ?>" />
                        <h2 class="h4 text-muted"><?= $this->language->link->links->no_data ?></h2>
                    </div>

                <?php endif ?>

            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6 mt-5 mt-lg-0 d-flex justify-content-center">
        <div class="biolink-preview-container">
            <div class="biolink-preview">
                <div class="biolink-preview-iframe-container">
                    <iframe id="biolink_preview_iframe" class="biolink-preview-iframe" src="<?= url($data->link->url . '?preview&link_id=' . $data->link->link_id) ?>" data-url-prepend="<?= url() ?>" data-url-append="<?= '?preview&link_id=' . $data->link->link_id ?>"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $html = ob_get_clean() ?>


<?php ob_start() ?>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/sortable.js' ?>"></script>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/pickr.min.js' ?>"></script>
<script>
    /* Settings Tab */
    /* Initiate the color picker */
    let pickr_options = {
        comparison: false,

        components: {
            preview: true,
            opacity: true,
            hue: true,
            comparison: false,
            interaction: {
                hex: true,
                rgba: false,
                hsla: false,
                hsva: false,
                cmyk: false,
                input: true,
                clear: false,
                save: true
            }
        }
    };

    /* Helper to generate avatar preview */
    function generate_image_preview(input) {

        console.log(input.files);

        if(input.files && input.files[0]) {
            let reader = new FileReader();

            reader.onload = event => {
                $('#image_file_preview').attr('src', event.target.result);
                $('#biolink_preview_iframe').contents().find('#image').attr('src', event.target.result).show();
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    $('#image_file_input').on('change', event => {
        $('#image_file_status').show();

        $('[data-toggle="tooltip"]').tooltip();

        $('input[name="image_delete"]').val(false);

        generate_image_preview(event.currentTarget);
    });

    $('#image_file_remove').on('click', () => {
        let default_src = $('#image_file_preview').attr('data-default-src');
        let empty_src = $('#image_file_preview').attr('data-empty-src');

        /* Check if new avatar is selected and act accordingly */
        if($('#image_file_input').get(0).files.length > 0) {

            /* Check if we had a non default image previously */
            if(default_src == empty_src) {
                $('#image_file_preview').attr('src', empty_src);
                $('#biolink_preview_iframe').contents().find('#image').hide();
                $('#image_file_status').hide();
            } else {
                $('#image_file_preview').attr('src', default_src);
                $('#image_file_input').replaceWith($('#image_file_input').val('').clone(true));
            }

        } else {
            $('#image_file_preview').attr('src', empty_src);
            $('#biolink_preview_iframe').contents().find('#image').hide();
            $('input[name="image_delete"]').val(true);
            $('#image_file_status').hide();
        }

    });

    /* Preview handlers */
    $('#settings_title').on('change paste keyup', event => {
        $('#biolink_preview_iframe').contents().find('#title').text($(event.currentTarget).val());
    });

    $('#settings_description').on('change paste keyup', event => {
        $('#biolink_preview_iframe').contents().find('#description').text($(event.currentTarget).val());
    });

    /* Text Color Handler */
    let settings_text_color_pickr = Pickr.create({
        el: '#settings_text_color_pickr',
        default: $('#settings_text_color').val(),
        ...{
            comparison: false,

            components: {
                preview: true,
                opacity: false,
                hue: true,
                comparison: false,
                interaction: {
                    hex: true,
                    rgba: false,
                    hsla: false,
                    hsva: false,
                    cmyk: false,
                    input: true,
                    clear: false,
                    save: true
                }
            }
        }
    });

    settings_text_color_pickr.on('change', hsva => {
        $('#settings_text_color').val(hsva.toHEXA().toString());


        $('#biolink_preview_iframe').contents().find('header').css('color', hsva.toHEXA().toString());
        $('#biolink_preview_iframe').contents().find('#branding').css('color', hsva.toHEXA().toString());
    });

    /* Socials Color Handler */
    let settings_socials_color_pickr = Pickr.create({
        el: '#settings_socials_color_pickr',
        default: $('#settings_socials_color').val(),
        ...pickr_options
    });

    settings_socials_color_pickr.on('change', hsva => {
        $('#settings_socials_color').val(hsva.toHEXA().toString());


        $('#biolink_preview_iframe').contents().find('#socials a svg').css('color', hsva.toHEXA().toString());
    });

    /* Background Type Handler */
    let background_type_handler = () => {
        let type = $('#settings_background_type').find(':selected').val();

        /* Show only the active background type */
        $(`div[id="background_type_${type}"]`).show();
        $(`div[id="background_type_${type}"]`).find('[name^="background"]').removeAttr('disabled');

        /* Disable the other possible types so they dont get submitted */
        let background_type_containers = $(`div[id^="background_type_"]:not(div[id$="_${type}"])`);

        background_type_containers.hide();
        background_type_containers.find('[name^="background"]').attr('disabled', 'disabled');
    };

    background_type_handler();

    $('#settings_background_type').on('change', background_type_handler);

    /* Preset Baclground Preview */
    $('#background_type_preset input[name="background"]').on('change', event => {
        let value = $(event.currentTarget).val();

        $('#biolink_preview_iframe').contents().find('body').attr('class', `link-body link-body-background-${value}`).attr('style', '');
    });

    /* Gradient Background */
    let settings_background_type_gradient_color_one_pickr = Pickr.create({
        el: '#settings_background_type_gradient_color_one_pickr',
        default: $('#settings_background_type_gradient_color_one').val(),
        ...pickr_options
    });

    settings_background_type_gradient_color_one_pickr.on('change', hsva => {
        $('#settings_background_type_gradient_color_one').val(hsva.toHEXA().toString());

        let color_one = $('#settings_background_type_gradient_color_one').val();
        let color_two = $('#settings_background_type_gradient_color_two').val();

        $('#biolink_preview_iframe').contents().find('body').attr('class', 'link-body').attr('style', `background-image: linear-gradient(135deg, ${color_one} 10%, ${color_two} 100%);`);
    });

    let settings_background_type_gradient_color_two_pickr = Pickr.create({
        el: '#settings_background_type_gradient_color_two_pickr',
        default: $('#settings_background_type_gradient_color_two').val(),
        ...pickr_options
    });

    settings_background_type_gradient_color_two_pickr.on('change', hsva => {
        $('#settings_background_type_gradient_color_two').val(hsva.toHEXA().toString());

        let color_one = $('#settings_background_type_gradient_color_one').val();
        let color_two = $('#settings_background_type_gradient_color_two').val();

        $('#biolink_preview_iframe').contents().find('body').attr('class', 'link-body').attr('style', `background-image: linear-gradient(135deg, ${color_one} 10%, ${color_two} 100%);`);
    });

    /* Color Background */
    let settings_background_type_color_pickr = Pickr.create({
        el: '#settings_background_type_color_pickr',
        default: $('#settings_background_type_color').val(),
        ...pickr_options
    });

    settings_background_type_color_pickr.on('change', hsva => {
        $('#settings_background_type_color').val(hsva.toHEXA().toString());

        $('#biolink_preview_iframe').contents().find('body').attr('class', 'link-body').attr('style', `background: ${hsva.toHEXA().toString()};`);
    });

    /* Image Background */
    function generate_background_preview(input) {
        if(input.files && input.files[0]) {
            let reader = new FileReader();

            reader.onload = event => {
                $('#background_type_image_preview').attr('src', event.target.result);
                $('#biolink_preview_iframe').contents().find('body').attr('class', 'link-body').attr('style', `background: url(${event.target.result});`);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    $('#background_type_image_input').on('change', event => {
        $('#background_type_image_status').show();

        generate_background_preview(event.currentTarget);
    });

    $('#background_type_image_remove').on('click', () => {
        $('#background_type_image_preview').attr('src', $('#background_type_image_preview').attr('data-default-src'));
        $('#biolink_preview_iframe').contents().find('body').attr('class', 'link-body').attr('style', `background: url(${$('#background_type_image_preview').attr('data-default-src')});`);

        $('#background_type_image_input').replaceWith($('#background_type_image_input').val('').clone(true));
        $('#background_type_image_status').hide();
    });

    /* Display branding switcher */
    $('#display_branding').on('change', event => {
        if($(event.currentTarget).is(':checked')) {
            $('#biolink_preview_iframe').contents().find('#branding').show();
        } else {
            $('#biolink_preview_iframe').contents().find('#branding').hide();
        }
    });

    /* Branding change */
    $('#branding_name').on('change paste keyup', event => {
        $('#biolink_preview_iframe').contents().find('#branding').text($(event.currentTarget).val());
    });

    $('#branding_url').on('change paste keyup', event => {
        $('#biolink_preview_iframe').contents().find('#branding').attr('src', ($(event.currentTarget).val()));
    });

    /* Form handling */
    $('form[name="update_biolink"],form[name="update_biolink_"]').on('submit', event => {
        let form = $(event.currentTarget)[0];
        let data = new FormData(form);
        let notification_container = $(event.currentTarget).find('.notification-container');

        $.ajax({
            type: 'POST',
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            url: 'link-ajax',
            data: data,
            success: (data) => {
                display_notifications(data.message, data.status, notification_container);

                notification_container[0].scrollIntoView();

                update_main_url();

            },
            dataType: 'json'
        });

        event.preventDefault();
    })
</script>
<script>
    /* Links tab */
    let sortable = Sortable.create(document.getElementById('links'), {
        animation: 150,
        handle: '.drag',
        onUpdate: (event) => {

            let links = [];
            $('#links > .link').each((i, elm) => {
                let link = {
                    link_id: $(elm).data('link-id'),
                    order: i
                };

                links.push(link);
            });

            $.ajax({
                type: 'POST',
                url: 'link-ajax',
                data: {
                    request_type: 'order',
                    links,
                    global_token
                },
                dataType: 'json'
            });

            /* Refresh iframe */
            $('#biolink_preview_iframe').attr('src', $('#biolink_preview_iframe').attr('src'));
        }
    });

    /* Status change handler for the links */
    $('[id^="biolink_link_is_enabled_"]').on('change', event => {
        ajax_call_helper(event, 'link-ajax', 'is_enabled_toggle', () => {

            let link_id = $(event.currentTarget).data('row-id');

            $(event.currentTarget).closest('.link').toggleClass('custom-row-inactive');

            /* Refresh iframe */
            $('#biolink_preview_iframe').attr('src', $('#biolink_preview_iframe').attr('src'));

        });
    });

    /* Duplicate link handler for the links */
    $('[data-duplicate="true"]').on('click', event => {
        ajax_call_helper(event, 'link-ajax', 'duplicate', (event, data) => {

            fade_out_redirect({ url: data.details.url, full: true });

        });
    });

    /* When an expanding happens for a link settings */
    $('[id^="link_expanded_content"]').on('show.bs.collapse', event => {
        let link_subtype = $(event.currentTarget).data('link-subtype');
        let link_id = $(event.currentTarget.querySelector('input[name="link_id"]')).val();
        let biolink_link = $('#biolink_preview_iframe').contents().find(`[data-link-id="${link_id}"]`);

        switch (link_subtype) {

            case 'text':
                let title_text_color_pickr_element = event.currentTarget.querySelector('.title_text_color_pickr');
                let description_text_color_pickr_element = event.currentTarget.querySelector('.description_text_color_pickr');

                if(title_text_color_pickr_element) {
                    let color_input = event.currentTarget.querySelector('input[name="title_text_color"]');

                    /* Color Handler */
                    let color_pickr = Pickr.create({
                        el: title_text_color_pickr_element,
                        default: $(color_input).val(),
                        ...pickr_options
                    });

                    color_pickr.off().on('change', hsva => {
                        $(color_input).val(hsva.toHEXA().toString());

                        biolink_link.find('h2').css('color', hsva.toHEXA().toString());
                    });
                }

                if(description_text_color_pickr_element) {
                    let color_input = event.currentTarget.querySelector('input[name="description_text_color"]');

                    /* Color Handler */
                    let color_pickr = Pickr.create({
                        el: description_text_color_pickr_element,
                        default: $(color_input).val(),
                        ...pickr_options
                    });

                    color_pickr.off().on('change', hsva => {
                        $(color_input).val(hsva.toHEXA().toString());

                        biolink_link.find('p').css('color', hsva.toHEXA().toString());
                    });
                }

                break;

            default:

                biolink_link = biolink_link.find('a');
                let text_color_pickr_element = event.currentTarget.querySelector('.text_color_pickr');
                let background_color_pickr_element = event.currentTarget.querySelector('.background_color_pickr');

                /* Schedule Handler */
                let schedule_handler = () => {
                    if($(event.currentTarget.querySelector('input[name="schedule"]')).is(':checked')) {
                        $(event.currentTarget.querySelector('.schedule_container')).show();
                    } else {
                        $(event.currentTarget.querySelector('.schedule_container')).hide();
                    }
                };

                $(event.currentTarget.querySelector('input[name="schedule"]')).off().on('change', schedule_handler);

                schedule_handler();

                /* Daterangepicker */
                $('[name="start_date"],[name="end_date"]').daterangepicker({
                    minDate: new Date(),
                    alwaysShowCalendars: true,
                    singleCalendar: true,
                    singleDatePicker: true,
                    locale: <?= json_encode(require APP_PATH . 'includes/daterangepicker_translations.php') ?>,
                }, (start, end, label) => {
                });


                /* Name, icon and image thumbnail */
                let outside_event = event;
                $(event.currentTarget.querySelector('input[name="name"]')).off().on('change paste keyup', event => {

                    let name = $(event.currentTarget).val();

                    /* Set the name in the preview */
                    biolink_link.text(name);
                    $(outside_event.currentTarget.querySelector('input[name="icon"]')).trigger('change');

                    /* Set the name in the form title */
                    $(`[data-target="#link_expanded_content${link_id}"] > strong`).text(name);

                });

                $(event.currentTarget.querySelector('input[name="icon"]')).off().on('change paste keyup', event => {
                    let icon = $(event.currentTarget).val();

                    if(!icon) {
                        biolink_link.find('svg').remove();
                    } else {

                        biolink_link.find('svg,i').remove();
                        biolink_link.prepend(`<i class="${icon} mr-1"></i>`);

                    }

                });

                $(event.currentTarget.querySelector('input[name="image"]')).off().on('change paste keyup', event => {
                    biolink_link.find('div').show();
                    biolink_link.find('img').attr('src', $(event.currentTarget).val());
                });

                if(text_color_pickr_element) {
                    let color_input = event.currentTarget.querySelector('input[name="text_color"]');

                    /* Background Color Handler */
                    let color_pickr = Pickr.create({
                        el: text_color_pickr_element,
                        default: $(color_input).val(),
                        ...pickr_options
                    });

                    color_pickr.off().on('change', hsva => {
                        $(color_input).val(hsva.toHEXA().toString());

                        biolink_link.css('color', hsva.toHEXA().toString());
                    });
                }

                if(background_color_pickr_element) {
                    let color_input = event.currentTarget.querySelector('input[name="background_color"]');

                    /* Background Color Handler */
                    let color_pickr = Pickr.create({
                        el: background_color_pickr_element,
                        default: $(color_input).val(),
                        ...pickr_options
                    });

                    color_pickr.off().on('change', hsva => {
                        $(color_input).val(hsva.toHEXA().toString());

                        /* Change the background or the border color */
                        if(biolink_link.css('background-color') != 'rgba(0, 0, 0, 0)') {
                            biolink_link.css('background-color', hsva.toHEXA().toString());
                        } else {
                            biolink_link.css('border-color', hsva.toHEXA().toString());
                        }
                    });
                }

                $(event.currentTarget.querySelector('input[name="outline"]')).off().on('change', event => {

                    let outline = $(event.currentTarget).is(':checked');

                    if(outline) {
                        /* From background color to border */
                        let background_color = biolink_link.css('background-color');

                        biolink_link.css('background-color', 'transparent');
                        biolink_link.css('border', `.1rem solid ${background_color}`);
                    } else {
                        /* From border to background color */
                        let border_color = biolink_link.css('border-color');

                        biolink_link.css('background-color', border_color);
                        biolink_link.css('border', 'none');
                    }

                });

                $(event.currentTarget.querySelector('select[name="border_radius"]')).off().on('change', event => {

                    let border_radius = $(event.currentTarget).find(':selected').val();

                    switch(border_radius) {
                        case 'straight':

                            biolink_link.removeClass('link-btn-round link-btn-rounded');

                            break;

                        case 'round':

                            biolink_link.removeClass('link-btn-rounded').addClass('link-btn-round');

                            break;

                        case 'rounded':

                            biolink_link.removeClass('link-btn-round').addClass('link-btn-rounded');

                            break;
                    }

                });

                let current_animation = $(event.currentTarget.querySelector('select[name="animation"]')).val();

                $(event.currentTarget.querySelector('select[name="animation"]')).off().on('change', event => {

                    let animation = $(event.currentTarget).find(':selected').val();

                    switch(animation) {
                        case 'false':

                            biolink_link.removeClass(`animated ${current_animation}`);
                            current_animation = false;

                            break;

                        default:

                            biolink_link.removeClass(`animated ${current_animation}`).addClass(`animated ${animation}`);
                            current_animation = animation;

                            break;
                    }

                });
        }



    })

</script>
<?php $javascript = ob_get_clean() ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
