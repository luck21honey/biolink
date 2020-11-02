<?php defined('ALTUMCODE') || die() ?>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <small>
            <ol class="custom-breadcrumbs">
                <li><a href="<?= url() ?>"><?= $this->language->index->breadcrumb ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
                <li class="active" aria-current="page"><?= $this->language->pages->index->breadcrumb ?></li>
            </ol>
        </small>
    </nav>

    <div class="d-flex flex-row align-items-baseline">
        <div class="mr-3">
            <i class="fa fa-fw fa-life-ring fa-2x text-muted"></i>
        </div>

        <h1><?= $this->language->pages->header ?></h1>
    </div>
    <p class="text-muted"><?= $this->language->pages->subheader ?></p>

    <?php if($data->popular_pages_result->num_rows): ?>
        <div class="mt-5">
            <h2 class="mb-4"><?= $this->language->pages->index->popular_pages->header ?></h2>

            <div class="row">
                <?php while($row = $data->popular_pages_result->fetch_object()): ?>

                    <div class="col-12 col-md-6 mb-4">

                        <div class="d-flex align-items-baseline">
                            <a href="<?= $row->type == 'internal' ? SITE_URL . 'page/' . $row->url : $row->url ?>" target="<?= $row->type == 'internal' ? '_self' : '_blank' ?>" class="h5 mr-1"><?= $row->title ?></a>

                            <?php if($row->type == 'internal'): ?>
                                <small class="text-muted"><?= sprintf($this->language->pages->total_views, nr($row->total_views)) ?></small>
                            <?php endif ?>
                        </div>

                        <span class="text-muted"><?= $row->description ?></span>
                    </div>

                <?php endwhile ?>
            </div>
        </div>
    <?php endif ?>

    <?php if($data->pages_categories_result->num_rows): ?>
        <div class="mt-5">
            <h2 class="mb-4"><?= $this->language->pages->index->pages_categories->header ?></h2>

            <div class="row">
                <?php while($row = $data->pages_categories_result->fetch_object()): ?>

                    <div class="col-12 col-md-4 mb-4">
                        <a href="<?= SITE_URL . 'pages/' . $row->url ?>" class="text-decoration-none">
                            <div class="card bg-gray-200 border-0 h-100 p-3">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center">

                                    <?php if(!empty($row->icon)): ?>
                                        <span class="round-circle-lg bg-primary-100 text-primary p-3 mb-4"><i class="<?= $row->icon ?> fa-2x"></i></span>
                                    <?php endif ?>

                                    <div class="h5"><?= $row->title ?></div>

                                    <span class="text-muted"><?= sprintf($this->language->pages->index->pages_categories->total_pages, nr($row->total_pages)) ?></span>
                                </div>
                            </div>
                        </a>
                    </div>

                <?php endwhile ?>
            </div>
        </div>
    <?php endif ?>
</div>



