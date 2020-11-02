<?php defined('ALTUMCODE') || die() ?>

<?php if($data->codes_result->num_rows): ?>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3"><i class="fa fa-fw fa-xs fa-tags text-primary-900 mr-2"></i> <?= $this->language->admin_codes->header ?></h1>

    <div class="col-auto p-0">
        <a href="<?= url('admin/code-create') ?>" class="btn btn-outline-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= $this->language->admin_codes->create ?></a>
    </div>
</div>

<?php display_notifications() ?>

<div class="table-responsive table-custom-container">
    <table class="table table-custom">
        <thead>
        <tr>
            <th><?= $this->language->admin_codes->table->code ?></th>
            <th><?= $this->language->admin_codes->table->type ?></th>
            <th><?= $this->language->admin_codes->table->plan_id ?></th>
            <th><?= $this->language->admin_codes->table->discount ?></th>
            <th><?= $this->language->admin_codes->table->quantity ?></th>
            <th><?= $this->language->admin_codes->table->redeemed_codes ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
            <?php while($row = $data->codes_result->fetch_object()): ?>

            <tr data-code-id="<?= $row->code_id ?>">
                <td><a href="<?= url('admin/code-update/' . $row->code_id) ?>"><?= $row->code ?></a></td>
                <td><?= $row->type == 'discount' ? '<span class="badge badge-pill badge-success">' . $row->type . '</span>' : '<span class="badge badge-pill badge-primary">' . $row->type . '</span>' ?></td>
                <td>
                    <span class="badge badge-pill badge-light">
                        <?= $row->plan_name ?: $this->language->admin_codes->table->plan_id_null ?>
                    </span>
                </td>
                <td><?= $row->discount . '%' ?></td>
                <td><?= $row->quantity ?></td>
                <td><i class="fa fa-fw fa-users text-muted"></i> <?= $row->redeemed ?></td>
                <td><?= include_view(THEME_PATH . 'views/admin/partials/admin_code_dropdown_button.php', ['id' => $row->code_id]) ?></td>
            </tr>

            <?php endwhile ?>
        </tbody>
    </table>
</div>

<?php else: ?>

<div class="d-flex flex-column flex-md-row align-items-md-center">
    <div class="mb-3 mb-md-0 mr-md-5">
        <i class="fa fa-fw fa-7x fa-tags text-primary-200"></i>
    </div>

    <div class="d-flex flex-column">
        <h1 class="h3"><?= $this->language->admin_codes->header_no_data ?></h1>
        <p class="text-muted"><?= $this->language->admin_codes->subheader_no_data ?></p>

        <div>
            <a href="<?= url('admin/code-create') ?>" class="btn btn-primary"><i class="fa fa-fw fa-sm fa-plus-circle"></i> <?= $this->language->admin_codes->create ?></a>
        </div>
    </div>
</div>

<?php endif ?>
