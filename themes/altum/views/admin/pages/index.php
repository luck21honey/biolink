<?php defined('ALTUMCODE') || die() ?>

<?php if($data->pages_categories_result->num_rows): ?>
    <div class="d-flex flex-column flex-md-row justify-content-between mb-4">
        <h1 class="h3"><i class="fa fa-fw fa-xs fa-book text-primary-900 mr-2"></i> <?= $this->language->admin_pages_categories->header ?></h1>

        <div class="col-auto p-0">
            <a href="<?= url('admin/pages-category-create') ?>" class="btn btn-outline-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= $this->language->admin_pages_categories->create ?></a>
        </div>
    </div>

    <?php display_notifications() ?>

    <div class="table-responsive table-custom-container">
        <table class="table table-custom">
            <thead>
            <tr>
                <th><?= $this->language->admin_pages_categories->pages_categories->pages_category ?></th>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php while($row = $data->pages_categories_result->fetch_object()): ?>

                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <?php if(!empty($row->icon)): ?>
                                <span class="round-circle-md bg-primary-100 text-primary p-3 mr-3"><i class="<?= $row->icon ?>"></i></span>
                            <?php endif ?>

                            <div class="d-flex flex-column">
                                <a href="<?= url('admin/pages-category-update/' . $row->pages_category_id) ?>"><?= $row->title ?></a>
                                <span><a href="<?= SITE_URL . 'pages/' . $row->url ?>" class="text-muted"><?= $row->url ?></a></span>
                            </div>
                        </div>
                    </td>
                    <td class="text-muted">
                        <i class="fa fa-fw fa-sm fa-file-alt"></i> <?= sprintf($this->language->admin_pages_categories->pages_categories->total_pages, $row->total_pages) ?>
                    </td>
                    <td><?= include_view(THEME_PATH . 'views/admin/partials/admin_pages_category_dropdown_button.php', ['id' => $row->pages_category_id]) ?></td>
                </tr>

            <?php endwhile ?>
            </tbody>
        </table>
    </div>

<?php else: ?>

    <div class="d-flex flex-column flex-md-row align-items-md-center">
        <div class="mb-3 mb-md-0 mr-md-5">
            <i class="fa fa-fw fa-7x fa-book text-primary-200"></i>
        </div>

        <div class="d-flex flex-column">
            <h1 class="h3"><?= $this->language->admin_pages_categories->header_no_data ?></h1>
            <p class="text-muted"><?= $this->language->admin_pages_categories->subheader_no_data ?></p>

            <div>
                <a href="<?= url('admin/pages-category-create') ?>" class="btn btn-primary"><i class="fa fa-fw fa-sm fa-plus-circle"></i> <?= $this->language->admin_pages_categories->create ?></a>
            </div>
        </div>
    </div>

<?php endif ?>


<hr class="my-5 border-gray-100" />


<?php if($data->pages_result->num_rows): ?>

    <div class="d-flex flex-column flex-md-row justify-content-between mb-4">
        <h1 class="h3"><i class="fa fa-fw fa-xs fa-file-alt text-primary-900 mr-2"></i> <?= $this->language->admin_pages->header ?></h1>

        <div class="col-auto p-0">
            <a href="<?= url('admin/page-create') ?>" class="btn btn-outline-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= $this->language->admin_pages->create ?></a>
        </div>
    </div>

    <div class="table-responsive table-custom-container">
        <table class="table table-custom">
            <thead>
            <tr>
                <th><?= $this->language->admin_pages->pages->page ?></th>
                <th><?= $this->language->admin_pages->pages->position ?></th>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php while($row = $data->pages_result->fetch_object()): ?>

                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <?php if(!empty($row->pages_category_icon)): ?>
                                <span class="round-circle-md bg-primary-100 text-primary p-3 mr-3" data-toggle="tooltip" title="<?= $row->pages_category_title ?>"><i class="<?= $row->pages_category_icon ?>"></i></span>
                            <?php endif ?>

                            <div class="d-flex flex-column">
                                <a href="<?= url('admin/page-update/' . $row->page_id) ?>"><?= $row->title ?></a>
                                <span><a href="<?= $row->type == 'internal' ? url('page/' . $row->url) : $row->url ?>" class="text-muted"><?= $row->url ?></a></span>
                            </div>
                        </div>
                    </td>
                    <td class="d-flex flex-column">
                        <?= $this->language->admin_pages->pages->{'position_' . $row->position} ?>
                        <small class="text-muted"><?= $this->language->admin_pages->input->{'type_' . strtolower($row->type)} ?></small>
                    </td>
                    <td class="text-muted"><?= sprintf($this->language->admin_pages->pages->total_views, nr($row->total_views)) ?></td>
                    <td><?= include_view(THEME_PATH . 'views/admin/partials/admin_page_dropdown_button.php', ['id' => $row->page_id]) ?></td>
                </tr>

            <?php endwhile ?>
            </tbody>
        </table>
    </div>

<?php else: ?>

    <div class="d-flex flex-column flex-md-row align-items-md-center">
        <div class="mb-3 mb-md-0 mr-md-5">
            <i class="fa fa-fw fa-7x fa-file-alt text-primary-200"></i>
        </div>

        <div class="d-flex flex-column">
            <h1 class="h3"><?= $this->language->admin_pages->header_no_data ?></h1>
            <p class="text-muted"><?= $this->language->admin_pages->subheader_no_data ?></p>

            <div>
                <a href="<?= url('admin/page-create') ?>" class="btn btn-primary"><i class="fa fa-fw fa-sm fa-plus-circle"></i> <?= $this->language->admin_pages->create ?></a>
            </div>
        </div>
    </div>

<?php endif ?>

