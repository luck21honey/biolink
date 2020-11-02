<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="type" value="biolink" />
    <input type="hidden" name="subtype" value="link" />
    <input type="hidden" name="link_id" value="<?= $row->link_id ?>" />

    <div class="notification-container"></div>

    <div class="form-group">
        <label><i class="fa fa-fw fa-signature fa-sm mr-1"></i> <?= $this->language->create_biolink_link_modal->input->location_url ?></label>
        <input type="text" class="form-control" name="location_url" value="<?= $row->location_url ?>" placeholder="<?= $this->language->create_biolink_link_modal->input->location_url_placeholder ?>" required="required" />
    </div>

    <div class="custom-control custom-switch mb-3">
        <input id="schedule_<?= $row->link_id ?>" name="schedule" type="checkbox" class="custom-control-input" <?= !empty($row->start_date) && !empty($row->end_date) ? 'checked="checked"' : null ?> <?= !$this->user->plan_settings->scheduling ? 'disabled="disabled"': null ?>>
        <label class="custom-control-label" for="schedule_<?= $row->link_id ?>"><?= $this->language->link->settings->schedule ?></label>
        <small class="form-text text-muted"><?= $this->language->link->settings->schedule_help ?></small>
    </div>

    <div class="row mt-3 schedule_container <?= !$this->user->plan_settings->scheduling ? 'container-disabled': null ?>" style="display: none;">
        <div class="col">
            <div class="form-group">
                <label><i class="fa fa-fw fa-clock fa-sm mr-1"></i> <?= $this->language->link->settings->start_date ?></label>
                <input
                        type="text"
                        class="form-control"
                        name="start_date"
                        value="<?= $row->start_date ?>"
                        placeholder="<?= $this->language->link->settings->start_date ?>"
                        autocomplete="off"
                >
            </div>
        </div>

        <div class="col">
            <div class="form-group">
                <label><i class="fa fa-fw fa-clock fa-sm mr-1"></i> <?= $this->language->link->settings->end_date ?></label>
                <input
                        type="text"
                        class="form-control"
                        name="end_date"
                        value="<?= $row->end_date ?>"
                        placeholder="<?= $this->language->link->settings->end_date ?>"
                        autocomplete="off"
                >
            </div>
        </div>
    </div>

    <div class="form-group">
        <label><i class="fa fa-fw fa-paragraph fa-sm mr-1"></i> <?= $this->language->create_biolink_link_modal->input->name ?></label>
        <input type="text" name="name" class="form-control" value="<?= $row->settings->name ?>" required="required" />
    </div>

    <div class="form-group">
        <label><i class="fa fa-fw fa-image fa-sm mr-1"></i> <?= $this->language->create_biolink_link_modal->input->image ?></label>
        <input type="text" name="image" class="form-control" value="<?= $row->settings->image ?>" placeholder="<?= $this->language->create_biolink_link_modal->input->image_placeholder ?>" />
        <small class="text-muted"><?= $this->language->create_biolink_link_modal->input->image_help ?></small>
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
    </div>

    <div class="mt-4">
        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= $this->language->global->update ?></button>
    </div>
</form>
