<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mr-3"><i class="fa fa-fw fa-xs fa-tags text-primary-900 mr-2"></i> <?= $this->language->admin_code_create->header ?></h1>
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
                            <option value="discount"><?= $this->language->admin_codes->main->type_discount ?></option>
                            <option value="redeemable"><?= $this->language->admin_codes->main->type_redeemable ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="code"><?= $this->language->admin_codes->main->code ?></label>
                        <input type="text" id="code" name="code" class="form-control form-control-lg" required="required" />
                    </div>

                    <div class="form-group">
                        <label for="plan_id"><?= $this->language->admin_codes->main->plan_id ?></label>
                        <select id="plan_id" name="plan_id" class="form-control form-control-lg">
                            <?php while($row = $data->plans_result->fetch_object()): ?>
                            <option value="<?= $row->plan_id ?>"><?= $row->name ?></option>
                            <?php endwhile ?>

                            <option value=""><?= $this->language->admin_codes->main->plan_id_null ?></option>
                        </select>
                        <small class="text-muted"><?= $this->language->admin_codes->main->plan_id_help ?></small>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div id="discount_container" class="form-group">
                                <label for="discount"><?= $this->language->admin_codes->main->discount ?></label>
                                <input type="number" min="1" max="99" id="discount" name="discount" class="form-control form-control-lg" value="1" />
                                <small class="text-muted"><?= $this->language->admin_codes->main->discount_help ?></small>
                            </div>

                            <div id="days_container" class="form-group">
                                <label for="days"><?= $this->language->admin_codes->main->days ?></label>
                                <input type="number" min="1" max="999999" id="days" name="days" class="form-control form-control-lg" value="1" />
                                <small class="text-muted"><?= $this->language->admin_codes->main->days_help ?></small>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="quantity"><?= $this->language->admin_codes->main->quantity ?></label>
                                <input type="number" min="1" id="quantity" name="quantity" class="form-control form-control-lg" value="1" />
                                <small class="text-muted"><?= $this->language->admin_codes->main->quantity_help ?></small>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12 col-md-4"></div>

                <div class="col">
                    <button type="submit" name="submit" class="btn btn-primary"><?= $this->language->global->create ?></button>
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
