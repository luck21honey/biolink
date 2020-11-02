<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="type" value="biolink" />
    <input type="hidden" name="subtype" value="text" />
    <input type="hidden" name="link_id" value="<?= $row->link_id ?>" />

    <div class="notification-container"></div>

    <div class="form-group">
        <label><i class="fa fa-fw fa-heading fa-sm mr-1"></i> <?= $this->language->create_biolink_text_modal->input->title ?></label>
        <input type="text" class="form-control" name="title" value="<?= $row->settings->title ?>" />
    </div>

    <div class="form-group">
        <label><i class="fa fa-fw fa-paragraph fa-sm mr-1"></i> <?= $this->language->create_biolink_text_modal->input->description ?></label>
        <textarea name="description" class="form-control"><?= $row->settings->description ?></textarea>
    </div>

    <div class="form-group">
        <label><i class="fa fa-fw fa-paint-brush fa-sm mr-1"></i> <?= $this->language->create_biolink_text_modal->input->title_text_color ?></label>
        <input type="hidden" name="title_text_color" class="form-control" value="<?= $row->settings->title_text_color ?>" required="required" />
        <div class="title_text_color_pickr"></div>
    </div>

    <div class="form-group">
        <label><i class="fa fa-fw fa-paint-brush fa-sm mr-1"></i> <?= $this->language->create_biolink_text_modal->input->description_text_color ?></label>
        <input type="hidden" name="description_text_color" class="form-control" value="<?= $row->settings->description_text_color ?>" required="required" />
        <div class="description_text_color_pickr"></div>
    </div>

    <div class="mt-4">
        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= $this->language->global->update ?></button>
    </div>
</form>
