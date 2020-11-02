<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <div class="d-flex align-items-center">
        <h1 class="h3 mr-3"><i class="fa fa-fw fa-xs fa-tags text-primary-900 mr-2"></i> <?= $this->language->admin_code_update->header ?></h1>

        <?= include_view(THEME_PATH . 'views/admin/partials/admin_code_dropdown_button.php', ['id' => $data->code->code_id]) ?>
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
                        <label for="type"><?= $this->language->admin_codes->main->type ?></label>
                        <select id="type" name="type" class="form-control form-control-lg">
                            <option value="discount" <?= $data->code->type == 'discount' ? 'selected="selected"' : null ?>><?= $this->language->admin_codes->main->type_discount ?></option>
                            <option value="redeemable" <?= $data->code->type == 'redeemable' ? 'selected="selected"' : null ?>><?= $this->language->admin_codes->main->type_redeemable ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="code"><?= $this->language->admin_codes->main->code ?></label>
                        <input type="text" id="code" name="code" class="form-control form-control-lg" required="required" value="<?= $data->code->code ?>" />
                    </div>

                    <div class="form-group">
                        <label for="plan_id"><?= $this->language->admin_codes->main->plan_id ?></label>
                        <select id="plan_id" name="plan_id" class="form-control form-control-lg">
                            <?php while($row = $data->plans_result->fetch_object()): ?>
                                <option value="<?= $row->plan_id ?>" <?= $data->code->plan_id == $row->plan_id ? 'selected="selected"' : null ?>><?= $row->name ?></option>
                            <?php endwhile ?>

                            <option value="" <?= !$data->code->plan_id ? 'selected="selected"' : null ?>><?= $this->language->admin_codes->main->plan_id_null ?></option>
                        </select>
                        <small class="text-muted"><?= $this->language->admin_codes->main->plan_id_help ?></small>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div id="discount_container" class="form-group">
                                <label for="discount"><?= $this->language->admin_codes->main->discount ?></label>
                                <input type="number" min="1" <?= $data->code->type == 'discount' ? 'max="99"' : 'max="100"' ?> id="discount" name="discount" class="form-control form-control-lg" value="<?= $data->code->discount ?>" />
                                <small class="text-muted"><?= $this->language->admin_codes->main->discount_help ?></small>
                            </div>

                            <div id="days_container" class="form-group">
                                <label for="days"><?= $this->language->admin_codes->main->days ?></label>
                                <input type="number" min="1" max="999999" id="days" name="days" class="form-control form-control-lg" value="<?= $data->code->days ?>" />
                                <small class="text-muted"><?= $this->language->admin_codes->main->days_help ?></small>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="quantity"><?= $this->language->admin_codes->main->quantity ?></label>
                                <input type="number" min="1" id="quantity" name="quantity" class="form-control form-control-lg" value="<?= $data->code->quantity ?>" />
                                <small class="text-muted"><?= $this->language->admin_codes->main->quantity_help ?></small>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info" role="alert">
                        <?= $this->language->admin_code_update->subheader ?>
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
    let checker = () => {
        let type = $('select[name="type"]').find(':selected').val();

        switch(type) {
            case 'discount':

                $('#discount_container').show();
                $('#days_container').hide();
                $('select[name="plan_id"] option[value=""]').show().removeAttr('disabled');

                break;

            case 'redeemable':

                $('#discount_container').hide();
                $('#days_container').show();
                $('select[name="plan_id"] option[value=""]').hide().attr('disabled', 'disabled');

                break;
        }
    };

    checker();

    $('select[name="type"]').on('change', checker);
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

