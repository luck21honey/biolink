<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="type" value="biolink" />
    <input type="hidden" name="subtype" value="image" />
    <input type="hidden" name="link_id" value="<?= $row->link_id ?>" />

    <div class="notification-container"></div>

    <div class="form-group">
        <label><i class="fa fa-fw fa-image fa-sm mr-1"></i> <?= $this->language->create_biolink_image_modal->input->image ?></label>
        <input type="text" class="form-control" name="image" value="<?= $row->settings->image ?>" required="required" />
    </div>

    <div class="form-group">
        <label><i class="fa fa-fw fa-link fa-sm mr-1"></i> <?= $this->language->create_biolink_image_modal->input->location_url ?></label>
        <input type="text" class="form-control" name="location_url" value="<?= $row->settings->location_url ?>" />
    </div>

    <div class="mt-4">
        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= $this->language->global->update ?></button>
    </div>
</form>
