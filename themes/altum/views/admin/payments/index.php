<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3"><i class="fa fa-fw fa-xs fa-dollar-sign text-primary-900 mr-2"></i> <?= $this->language->admin_payments->header ?></h1>
</div>

<?php display_notifications() ?>

<div>
    <table id="results" class="table table-custom">
        <thead>
        <tr>
            <th><?= $this->language->admin_payments->table->user_email ?></th>
            <th><?= $this->language->admin_payments->table->type ?></th>
            <th><?= $this->language->admin_payments->table->name ?></th>
            <th><?= $this->language->admin_payments->table->email ?></th>
            <th><?= $this->language->admin_payments->table->total_amount ?></th>
            <th><?= $this->language->admin_payments->table->date ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<?php ob_start() ?>
<link href="<?= SITE_URL . ASSETS_URL_PATH . 'css/datatables.min.css' ?>" rel="stylesheet" media="screen">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/datatables.min.js' ?>"></script>
<script>
    let datatable = $('#results').DataTable({
        language: <?= json_encode($this->language->datatable) ?>,
        search: {
            search: <?= json_encode($_GET['email'] ?? '') ?>
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: <?= json_encode(url('admin/payments/read')) ?>,
            type: 'POST'
        },
        autoWidth: false,
        lengthMenu: [[25, 50, 100], [25, 50, 100]],
        columns: [
            {
                data: 'user_email',
                searchable: true,
                sortable: true
            },
            {
                data: 'type',
                searchable: false,
                sortable: false
            },
            {
                data: 'name',
                searchable: true,
                sortable: true
            },
            {
                data: 'email',
                searchable: true,
                sortable: true
            },
            {
                data: 'total_amount',
                searchable: false,
                sortable: true
            },
            {
                data: 'date',
                searchable: false,
                sortable: true
            },
            {
                data: 'actions',
                searchable: false,
                sortable: false
            },
        ],
        order: [[5, 'desc']],
        responsive: true,
        drawCallback: () => {
            $('[data-toggle="tooltip"]').tooltip();
        },
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
            "<'table-responsive table-custom-container my-3'tr>" +
            "<'row'<'col-sm-12 col-md-5 text-muted'i><'col-sm-12 col-md-7'p>>"
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
