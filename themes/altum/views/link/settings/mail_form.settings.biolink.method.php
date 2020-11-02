<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="type" value="biolink" />
    <input type="hidden" name="subtype" value="mail" />
    <input type="hidden" name="link_id" value="<?= $row->link_id ?>" />

    <div class="notification-container"></div>

    <div class="form-group">
        <label><i class="fa fa-fw fa-paragraph fa-sm mr-1"></i> <?= $this->language->create_biolink_link_modal->input->name ?></label>
        <input type="text" name="name" class="form-control" value="<?= $row->settings->name ?>" required="required" />
    </div>

    <div class="form-group">
        <label><i class="fa fa-fw fa-globe fa-sm mr-1"></i> <?= $this->language->create_biolink_link_modal->input->icon ?></label>
        <input type="text" name="icon" class="form-control" value="<?= $row->settings->icon ?>" placeholder="<?= $this->language->create_biolink_link_modal->input->icon_placeholder ?>" />
        <small class="text-muted"><?= $this->language->create_biolink_link_modal->input->icon_help ?></small>
    </div>

    <div class="<?= !$this->user->plan_settings->custom_colored_links ? 'container-disabled': null ?>">
        <div class="form-group">
            <label><i class="fa fa-fw fa-paint-brush fa-sm mr-1"></i> <?= $this->language->create_biolink_link_modal->input->text_color ?></label>
            <input type="hidden" name="text_color" class="form-control" value="<?= $row->settings->text_color ?>" required="required" />
            <div class="text_color_pickr"></div>
        </div>

        <div class="form-group">
            <label><i class="fa fa-fw fa-fill fa-sm mr-1"></i> <?= $this->language->create_biolink_link_modal->input->background_color ?></label>
            <input type="hidden" name="background_color" class="form-control" value="<?= $row->settings->background_color ?>" required="required" />
            <div class="background_color_pickr"></div>
        </div>

        <div class="custom-control custom-switch mr-3 mb-3">
            <input
                    type="checkbox"
                    class="custom-control-input"
                    id="outline_<?= $row->link_id ?>"
                    name="outline"
                <?= $row->settings->outline ? 'checked="checked"' : null ?>
            >
            <label class="custom-control-label clickable" for="outline_<?= $row->link_id ?>"><?= $this->language->create_biolink_link_modal->input->outline ?></label>
        </div>

        <div class="form-group">
            <label><?= $this->language->create_biolink_link_modal->input->border_radius ?></label>
            <select name="border_radius" class="form-control">
                <option value="straight" <?= $row->settings->border_radius == 'straight' ? 'selected="selected"' : null ?>><?= $this->language->create_biolink_link_modal->input->border_radius_straight ?></option>
                <option value="round" <?= $row->settings->border_radius == 'round' ? 'selected="selected"' : null ?>><?= $this->language->create_biolink_link_modal->input->border_radius_round ?></option>
                <option value="rounded" <?= $row->settings->border_radius == 'rounded' ? 'selected="selected"' : null ?>><?= $this->language->create_biolink_link_modal->input->border_radius_rounded ?></option>
            </select>
        </div>

        <div class="form-group">
            <label><?= $this->language->create_biolink_link_modal->input->animation ?></label>
            <select name="animation" class="form-control">
                <option value="false" <?= !$row->settings->animation ? 'selected="selected"' : null ?>>-</option>
                <option value="bounce" <?= $row->settings->animation == 'bounce' ? 'selected="selected"' : null ?>>bounce</option>
                <option value="tada" <?= $row->settings->animation == 'tada' ? 'selected="selected"' : null ?>>tada</option>
                <option value="wobble" <?= $row->settings->animation == 'wobble' ? 'selected="selected"' : null ?>>wobble</option>
                <option value="swing" <?= $row->settings->animation == 'swing' ? 'selected="selected"' : null ?>>swing</option>
                <option value="shake" <?= $row->settings->animation == 'shake' ? 'selected="selected"' : null ?>>shake</option>
                <option value="rubberBand" <?= $row->settings->animation == 'rubberBand' ? 'selected="selected"' : null ?>>rubberBand</option>
                <option value="pulse" <?= $row->settings->animation == 'pulse' ? 'selected="selected"' : null ?>>pulse</option>
                <option value="flash" <?= $row->settings->animation == 'flash' ? 'selected="selected"' : null ?>>flash</option>
            </select>
        </div>

        <div class="form-group">
            <label><?= $this->language->create_biolink_mail_modal->input->email_placeholder ?></label>
            <input type="text" name="email_placeholder" class="form-control" value="<?= $row->settings->email_placeholder ?>" required="required" />
        </div>

        <div class="form-group">
            <label><?= $this->language->create_biolink_mail_modal->input->button_text ?></label>
            <input type="text" name="button_text" class="form-control" value="<?= $row->settings->button_text ?>" required="required" />
        </div>

        <div class="form-group">
            <label><?= $this->language->create_biolink_mail_modal->input->success_text ?></label>
            <input type="text" name="success_text" class="form-control" value="<?= $row->settings->success_text ?>" required="required" />
        </div>

        <div class="custom-control custom-switch mr-3 mb-3">
            <input
                    type="checkbox"
                    class="custom-control-input"
                    id="show_agreement_<?= $row->link_id ?>"
                    name="show_agreement"
                <?= $row->settings->show_agreement ? 'checked="checked"' : null ?>
            >
            <label class="custom-control-label clickable" for="show_agreement_<?= $row->link_id ?>"><?= $this->language->create_biolink_mail_modal->input->show_agreement ?></label>
            <div><small class="text-muted"><?= $this->language->create_biolink_mail_modal->input->show_agreement_help ?></small></div>
        </div>

        <div class="form-group">
            <label><?= $this->language->create_biolink_mail_modal->input->agreement_text ?></label>
            <input type="text" name="agreement_text" class="form-control" value="<?= $row->settings->agreement_text ?>" />
        </div>

        <div class="form-group">
            <label><?= $this->language->create_biolink_mail_modal->input->agreement_url ?></label>
            <input type="text" name="agreement_url" class="form-control" value="<?= $row->settings->agreement_url ?>" />
        </div>

        <div class="form-group">
            <label><?= $this->language->create_biolink_mail_modal->input->mailchimp_api ?></label>
            <input type="text" name="mailchimp_api" class="form-control" value="<?= $row->settings->mailchimp_api ?>" />
            <small class="text-muted"><?= $this->language->create_biolink_mail_modal->input->mailchimp_api_help ?></small>
        </div>

        <div class="form-group">
            <label><?= $this->language->create_biolink_mail_modal->input->mailchimp_api_list ?></label>
            <input type="text" name="mailchimp_api_list" class="form-control" value="<?= $row->settings->mailchimp_api_list ?>" />
            <small class="text-muted"><?= $this->language->create_biolink_mail_modal->input->mailchimp_api_list_help ?></small>
        </div>

        <div class="form-group">
            <label><?= $this->language->create_biolink_mail_modal->input->webhook_url ?></label>
            <input type="text" name="webhook_url" class="form-control" value="<?= $row->settings->webhook_url ?>" />
            <small class="text-muted"><?= $this->language->create_biolink_mail_modal->input->webhook_url_help ?></small>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= $this->language->global->update ?></button>
    </div>
</form>
