<?php defined('ALTUMCODE') || die() ?>

<div class="mb-4">
    <div class="d-flex align-items-center">
        <h1 class="h3 mr-3"><i class="fa fa-fw fa-xs fa-globe text-primary-900 mr-2"></i> <?= $this->language->admin_domain_update->header ?></h1>

        <?= include_view(THEME_PATH . 'views/admin/partials/admin_domain_dropdown_button.php', ['id' => $data->domain->domain_id]) ?>
    </div>

    <p class="text-muted"><?= $this->language->admin_domain_create->subheader ?></p>
</div>

<?php display_notifications() ?>

<div class="card">
    <div class="card-body">

        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <?php $url = parse_url(SITE_URL); $host = $url['host'] . (strlen($url['path']) > 1 ? $url['path'] : null); ?>

            <p class="text-muted"><?= sprintf($this->language->admin_domain_create->form->helper, '<strong>' . $_SERVER['SERVER_ADDR'] . '</strong>', '<strong>' . $host . '</strong>') ?></p>

            <div class="form-group">
                <label><i class="fa fa-fw fa-globe fa-sm mr-1 text-primary-900 mr-2"></i> <?= $this->language->admin_domain_create->form->host ?></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <select name="scheme" class="appearance-none select-custom-altum form-control form-control-lg input-group-text">
                            <option value="https://" <?= $data->domain->scheme == 'https://' ? 'selected="selected"' : null ?>>https://</option>
                            <option value="http://" <?= $data->domain->scheme == 'http://' ? 'selected="selected"' : null ?>>http://</option>
                        </select>
                    </div>

                    <input type="text" class="form-control form-control-lg" name="host" placeholder="<?= $this->language->admin_domain_create->form->host_placeholder ?>" value="<?= $data->domain->host ?>" required="required" />
                </div>
                <small class="text-muted"><i class="fa fa-fw fa-info-circle"></i> <?= $this->language->admin_domain_create->form->host_help ?></small>
            </div>

            <div class="form-group">
                <label><i class="fa fa-fw fa-sitemap fa-sm mr-1"></i> <?= $this->language->admin_domain_create->form->custom_index_url ?></label>
                <input type="text" class="form-control" name="custom_index_url" value="<?= $data->domain->custom_index_url ?>" placeholder="<?= $this->language->admin_domain_create->form->custom_index_url_placeholder ?>" />
                <small class="text-muted"><?= $this->language->admin_domain_create->form->custom_index_url_help ?></small>
            </div>

            <div class="form-group">
                <label><?= $this->language->admin_domain_create->form->is_enabled ?></label>

                <select name="is_enabled" class="form-control form-control-lg">
                    <option value="1" <?= $data->domain->is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->yes ?></option>
                    <option value="0" <?= !$data->domain->is_enabled ? 'selected="selected"' : null ?>><?= $this->language->global->no ?></option>
                </select>
            </div>

            <div class="mt-4">
                <button type="submit" name="submit" class="btn btn-primary"><?= $this->language->global->update ?></button>
            </div>
        </form>

    </div>
</div>
