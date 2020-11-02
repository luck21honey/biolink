<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <div class="d-flex align-items-center">
        <h1 class="h3 mr-3"><i class="fa fa-fw fa-xs fa-tags text-primary-900 mr-2"></i> <?= $this->language->admin_tax_update->header ?></h1>

        <?= include_view(THEME_PATH . 'views/admin/partials/admin_tax_dropdown_button.php', ['id' => $data->tax->tax_id]) ?>
    </div>
</div>

<?php display_notifications() ?>

<div class="card">
    <div class="card-body">

        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <div class="row">
                <div class="col-12 col-md-4">
                    <h2 class="h4"><?= $this->language->admin_codes->main->header ?></h2>
                    <p class="text-muted"><?= $this->language->admin_codes->main->subheader ?></p>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label for="internal_name"><?= $this->language->admin_taxes->main->internal_name ?></label>
                        <input type="text" id="internal_name" name="internal_name" class="form-control form-control-lg" value="<?= $data->tax->internal_name ?>" required="required" />
                        <small class="text-muted"><?= $this->language->admin_taxes->main->internal_name_help ?></small>
                    </div>

                    <div class="form-group">
                        <label for="name"><?= $this->language->admin_taxes->main->name ?></label>
                        <input type="text" id="name" name="name" class="form-control form-control-lg" value="<?= $data->tax->name ?>" required="required" />
                    </div>

                    <div class="form-group">
                        <label for="description"><?= $this->language->admin_taxes->main->description ?></label>
                        <input type="text" id="description" name="description" class="form-control form-control-lg" value="<?= $data->tax->description ?>" required="required" />
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="value"><?= $this->language->admin_taxes->main->value ?></label>
                                <input type="number" id="value" name="value" class="form-control form-control-lg" value="<?= $data->tax->value ?>" disabled="disabled" />
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="value_type"><?= $this->language->admin_taxes->main->value_type ?></label>
                                <select id="value_type" name="value_type" class="form-control form-control-lg" disabled="disabled">
                                    <option value="percentage" <?= $data->tax->value_type == 'percentage' ? 'selected="selected"' : null ?>><?= $this->language->admin_taxes->main->value_type_percentage ?></option>
                                    <option value="fixed" <?= $data->tax->value_type == 'fixed' ? 'selected="selected"' : null ?>><?= $this->language->admin_taxes->main->value_type_fixed ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="type"><?= $this->language->admin_taxes->main->type ?></label>
                        <select id="type" name="type" class="form-control form-control-lg" disabled="disabled">
                            <option value="inclusive" <?= $data->tax->type == 'inclusive' ? 'selected="selected"' : null ?>><?= $this->language->admin_taxes->main->type_inclusive ?></option>
                            <option value="exclusive" <?= $data->tax->type == 'exclusive' ? 'selected="selected"' : null ?>><?= $this->language->admin_taxes->main->type_exclusive ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="billing_type"><?= $this->language->admin_taxes->main->billing_type ?></label>
                        <select id="billing_type" name="billing_type" class="form-control form-control-lg" disabled="disabled">
                            <option value="personal" <?= $data->tax->billing_type == 'personal' ? 'selected="selected"' : null ?>><?= $this->language->admin_taxes->main->billing_type_personal ?></option>
                            <option value="business" <?= $data->tax->billing_type == 'business' ? 'selected="selected"' : null ?>><?= $this->language->admin_taxes->main->billing_type_business ?></option>
                            <option value="both" <?= $data->tax->billing_type == 'both' ? 'selected="selected"' : null ?>><?= $this->language->admin_taxes->main->billing_type_both ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="countries"><?= $this->language->admin_taxes->main->countries ?></label>
                        <select id="countries" name="countries[]" class="form-control form-control-lg" multiple="multiple" disabled="disabled">
                            <?php foreach(get_countries_array() as $key => $value): ?>
                                <option value="<?= $key ?>" <?= $data->tax->countries && array_key_exists($key, $data->tax->countries)  ? 'selected="selected"' : null ?>><?= $value ?></option>
                            <?php endforeach ?>
                        </select>
                        <small class="text-muted"><?= $this->language->admin_taxes->main->countries_help ?></small>
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
<script>
    'use strict';

    let checker = () => {
        let value_type = document.querySelector('select[name="value_type"]').value;

        switch(value_type) {
            case 'percentage':

                document.querySelector('select[name="type"] option[value="inclusive"]').removeAttribute('disabled');
                document.querySelector('select[name="type"] option[value="exclusive"]').removeAttribute('selected');

                break;

            case 'fixed':

                document.querySelector('select[name="type"] option[value="inclusive"]').setAttribute('disabled', 'disabled');
                document.querySelector('select[name="type"] option[value="exclusive"]').setAttribute('selected', 'selected');

                break;
        }
    };

    checker();

    document.querySelector('select[name="value_type"]').addEventListener('change', checker);
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

