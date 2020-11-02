<?php defined('ALTUMCODE') || die() ?>

<?php if($data->taxes_result->num_rows): ?>

<div class="d-flex flex-column flex-md-row justify-content-between mb-4">
    <h1 class="h3"><i class="fa fa-fw fa-xs fa-receipt text-primary-900 mr-2"></i> <?= $this->language->admin_taxes->header ?></h1>

    <div class="col-auto p-0">
        <a href="<?= url('admin/tax-create') ?>" class="btn btn-outline-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= $this->language->admin_taxes->create ?></a>
    </div>
</div>

<?php display_notifications() ?>

<div class="table-responsive table-custom-container">
    <table class="table table-custom">
        <thead>
        <tr>
            <th><?= $this->language->admin_taxes->table->tax ?></th>
            <th><?= $this->language->admin_taxes->table->name ?></th>
            <th><?= $this->language->admin_taxes->table->details ?></th>
            <th><?= $this->language->admin_taxes->table->billing_type ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
            <?php while($row = $data->taxes_result->fetch_object()): ?>

            <tr data-tax-id="<?= $row->tax_id ?>">
                <td><a href="<?= url('admin/tax-update/' . $row->tax_id) ?>"><?= $row->internal_name ?></a></td>
                <td>
                    <div class="d-flex flex-column">
                        <span><?= $row->name ?></span>
                        <span class="text-muted"><?= $row->description ?></span>
                    </div>
                </td>
                <td>
                    <div class="d-flex flex-column">
                        <span><?= $row->value_type == 'percentage' ? $row->value . '%' : $row->value . ' ' . $this->settings->payment->currency ?></span>
                        <span class="text-muted"><?= $row->value_type == 'inclusive' ? $this->language->admin_taxes->main->type_inclusive : $this->language->admin_taxes->main->type_exclusive ?></span>
                    </div>
                </td>
                <td>
                    <?= $this->language->admin_taxes->main->{'billing_type_' . $row->billing_type} ?>
                </td>
                <td><?= include_view(THEME_PATH . 'views/admin/partials/admin_tax_dropdown_button.php', ['id' => $row->tax_id]) ?></td>
            </tr>

            <?php endwhile ?>
        </tbody>
    </table>
</div>

<?php else: ?>

<div class="d-flex flex-column flex-md-row align-items-md-center">
    <div class="mb-3 mb-md-0 mr-md-5">
        <i class="fa fa-fw fa-7x fa-receipt text-primary-200"></i>
    </div>

    <div class="d-flex flex-column">
        <h1 class="h3"><?= $this->language->admin_taxes->header_no_data ?></h1>
        <p class="text-muted"><?= $this->language->admin_taxes->subheader_no_data ?></p>

        <div>
            <a href="<?= url('admin/tax-create') ?>" class="btn btn-primary"><i class="fa fa-fw fa-sm fa-plus-circle"></i> <?= $this->language->admin_taxes->create ?></a>
        </div>
    </div>
</div>

<?php endif ?>
