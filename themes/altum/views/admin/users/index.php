<?php defined('ALTUMCODE') || die() ?>

<header class="d-flex justify-content-between mb-4">
    <h1 class="h3"><i class="fa fa-fw fa-xs fa-users text-primary-900 mr-2"></i> <?= $this->language->admin_users->header ?></h1>

    <div class="col-auto">
        <a href="<?= url('admin/user-create') ?>" class="btn btn-outline-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= $this->language->admin_user_create->menu ?></a>
    </div>
</header>

<?php display_notifications() ?>

<div>
    <table id="results" class="table table-custom">
        <thead>
        <tr>
            <th><?= $this->language->admin_users->table->email ?></th>
            <th><?= $this->language->admin_users->table->name ?></th>
            <th><?= $this->language->admin_users->table->active ?></th>
            <th><?= $this->language->admin_users->table->plan_id ?></th>
            <th><?= $this->language->admin_users->table->registration_date ?></th>
            <th class="disable_export"></th>
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
        url: <?= json_encode(url('admin/users/read')) ?>,
        type: 'POST'
    },
    autoWidth: false,
    lengthMenu: [[25, 50, 100], [25, 50, 100]],
    buttons: [
        {
            extend: 'csvHtml5',
            exportOptions: {
                modifier: {
                    search: 'none'
                },
                columns: ':not(.disable_export)'
            }
        }
    ],
    columns: [
        {
            data: 'email',
            searchable: true,
            sortable: true
        },
        {
            data: 'name',
            searchable: true,
            sortable: true
        },
        {
            data: 'active',
            searchable: false,
            sortable: true
        },
        {
            data: 'plan_id',
            searchable: false,
            sortable: false
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
        }
    ],
    responsive: true,
    drawCallback: () => {
        $('[data-toggle="tooltip"]').tooltip();
    },
    dom: "<'row'<'col-sm-12 col-md-6'<'row'<'col-auto'B><'col-auto'l>>><'col-sm-12 col-md-6'f>>" +
        "<'table-responsive table-custom-container my-3'tr>" +
        "<'row'<'col-sm-12 col-md-5 text-muted'i><'col-sm-12 col-md-7'p>>"
});
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
