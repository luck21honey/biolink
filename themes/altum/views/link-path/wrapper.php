<?php defined('ALTUMCODE') || die() ?>
<!DOCTYPE html>
<html lang="<?= $this->language->language_code ?>" class="link-html">
    <head>
        <title><?= !empty($this->link->settings->seo->title) ? $this->link->settings->seo->title : \Altum\Title::get() ?></title>
        <base href="<?= SITE_URL; ?>">
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

        <?php if($this->link->settings->seo->meta_description): ?>
        <meta name="description" content="<?= $this->link->settings->seo->meta_description ?>">
        <?php endif ?>

        <?php if($this->link->settings->seo->block): ?>
            <meta name="robots" content="noindex">
        <?php endif ?>

        <?php if(!empty($this->settings->favicon)): ?>
            <link href="<?= SITE_URL . UPLOADS_URL_PATH . 'favicon/' . $this->settings->favicon ?>" rel="shortcut icon" />
        <?php endif ?>

        <?php if(!$this->link->settings->font): ?>
            <link href="https://fonts.googleapis.com/css?family=Lato&display=swap" rel="stylesheet">
        <?php endif ?>

        <?php foreach(['bootstrap.min.css', 'custom.css', 'link-custom.css', 'animate.min.css'] as $file): ?>
            <link href="<?= SITE_URL . ASSETS_URL_PATH . 'css/' . $file . '?v=' . PRODUCT_CODE ?>" rel="stylesheet" media="screen">
        <?php endforeach ?>

        <?php if($this->link->settings->font): ?>
            <?php $biolink_fonts = require APP_PATH . 'includes/biolink_fonts.php' ?>
            <link href="https://fonts.googleapis.com/css?family=<?= $biolink_fonts[$this->link->settings->font]['font-family'] ?>&display=swap" rel="stylesheet">
            <style>
                body {
                    font-family: '<?= $biolink_fonts[$this->link->settings->font]['name'] ?>', sans-serif !important;
                }
            </style>
        <?php endif ?>

        <?= \Altum\Event::get_content('head') ?>

        <?php if(!empty($this->settings->custom->head_js)): ?>
            <?= $this->settings->custom->head_js ?>
        <?php endif ?>

        <link rel="canonical" href="<?= $this->link->full_url ?>" />
    </head>

    <?= $this->views['content'] ?>

    <input type="hidden" id="url" name="url" value="<?= url() ?>" />
    <input type="hidden" name="global_token" value="<?= \Altum\Middlewares\Csrf::get('global_token') ?>" />
    <input type="hidden" name="number_decimal_point" value="<?= $this->language->global->number->decimal_point ?>" />
    <input type="hidden" name="number_thousands_separator" value="<?= $this->language->global->number->thousands_separator ?>" />

    <?php foreach(['libraries/jquery.min.js', 'libraries/popper.min.js', 'libraries/bootstrap.min.js', 'main.js', 'functions.js', 'libraries/fontawesome.min.js'] as $file): ?>
        <script src="<?= SITE_URL . ASSETS_URL_PATH ?>js/<?= $file ?>?v=<?= PRODUCT_CODE ?>"></script>
    <?php endforeach ?>

    <?= \Altum\Event::get_content('javascript') ?>
</html>
