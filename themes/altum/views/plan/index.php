<?php defined('ALTUMCODE') || die() ?>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<div class="container">
    <div class="d-flex flex-column justify-content-center">

        <?php display_notifications() ?>

        <nav aria-label="breadcrumb">
            <small>
                <ol class="custom-breadcrumbs">
                    <li><a href="<?= url() ?>"><?= $this->language->index->breadcrumb ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
                    <li class="active" aria-current="page"><?= $this->language->plan->breadcrumb ?></li>
                </ol>
            </small>
        </nav>

        <?php if(\Altum\Middlewares\Authentication::check() && $this->user->plan_is_expired && $this->user->plan_id != 'free'): ?>
            <div class="alert alert-info" role="alert">
                <?= $this->language->global->info_message->user_plan_is_expired ?>
            </div>
        <?php endif ?>

        <?php if($data->type == 'new'): ?>

            <h1 class="h3"><?= $this->language->plan->header_new ?></h1>
            <span class="text-muted"><?= $this->language->plan->subheader_new ?></span>

        <?php elseif($data->type == 'upgrade'): ?>

            <h1 class="h3"><?= $this->language->plan->header_upgrade ?></h1>
            <span class="text-muted"><?= $this->language->plan->subheader_upgrade ?></span>

        <?php elseif($data->type == 'renew'): ?>

            <h1 class="h3"><?= $this->language->plan->header_renew ?></h1>
            <span class="text-muted"><?= $this->language->plan->subheader_renew ?></span>

        <?php endif ?>


        <div class="mt-5 col-12">
            <?= $this->views['plans'] ?>
        </div>

    </div>
</div>
