<?php defined('ALTUMCODE') || die() ?>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<div class="container">
    <div class="d-flex flex-row align-items-center">
        <div class="mr-3">
            <i class="fa fa-fw fa-eye-slash fa-2x"></i>
        </div>

        <h1><?= $this->language->notfound->header ?></h1>
    </div>

    <p class="text-muted"><?= $this->language->notfound->subheader ?></p>
</div>
