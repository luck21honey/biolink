<?php defined('ALTUMCODE') || die() ?>
<!DOCTYPE html>
<html class="admin" lang="<?= $this->language->language_code ?>">
    <head>
        <title><?= \Altum\Title::get() ?></title>
        <base href="<?= SITE_URL; ?>">
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta http-equiv="content-language" content="<?= $this->language->language_code ?>" />

        <?php if(!empty($this->settings->favicon)): ?>
            <link href="<?= SITE_URL . UPLOADS_URL_PATH . 'favicon/' . $this->settings->favicon ?>" rel="shortcut icon" />
        <?php endif ?>

        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700&display=swap" rel="stylesheet">

        <?php foreach(['admin-' . \Altum\ThemeStyle::get_file(), 'admin-custom.css', 'animate.min.css'] as $file): ?>
            <link href="<?= SITE_URL . ASSETS_URL_PATH ?>css/<?= $file ?>?v=<?= PRODUCT_CODE ?>" rel="stylesheet" media="screen">
        <?php endforeach ?>

        <?= \Altum\Event::get_content('head') ?>
    </head>

    <body class="admin" data-theme-style="<?= \Altum\ThemeStyle::get() ?>">

    <div class="admin-container">

        <?= $this->views['admin_sidebar'] ?>

        <section class="admin-content animated fadeIn">
            <div id="admin_overlay" class="admin-overlay" style="display: none"></div>

            <?= $this->views['admin_menu'] ?>

            <div class="p-5">
                <?= $this->views['content'] ?>

                <?= $this->views['footer'] ?>
            </div>
        </section>
    </div>

    <?= \Altum\Event::get_content('modals') ?>

    <input type="hidden" id="url" name="url" value="<?= url() ?>" />
    <input type="hidden" name="global_token" value="<?= \Altum\Middlewares\Csrf::get('global_token') ?>" />
    <input type="hidden" name="number_decimal_point" value="<?= $this->language->global->number->decimal_point ?>" />
    <input type="hidden" name="number_thousands_separator" value="<?= $this->language->global->number->thousands_separator ?>" />

    <?php foreach(['libraries/jquery.min.js', 'libraries/popper.min.js', 'libraries/bootstrap.min.js', 'main.js', 'functions.js', 'libraries/fontawesome.min.js'] as $file): ?>
        <script src="<?= SITE_URL . ASSETS_URL_PATH ?>js/<?= $file ?>?v=<?= PRODUCT_CODE ?>"></script>
    <?php endforeach ?>

    <?= \Altum\Event::get_content('javascript') ?>

    <script>
        let toggle_admin_sidebar = () => {

            /* Open sidebar menu */
            $('body').toggleClass('admin-sidebar-opened');

            /* Toggle overlay */
            $('#admin_overlay').fadeToggle(150);

            /* Change toggle button content */
            let button = $('#admin_menu_toggler');

            $(button).children().animate({opacity: 0}, 75, event => {
                if($('body').hasClass('admin-sidebar-opened')) {
                    $(button).html('<i class="fa fa-fw fa-times"></i>');
                } else {
                    $(button).html('<i class="fa fa-fw fa-bars"></i>');
                }

                $(button).css('opacity', 0).animate({opacity: 1}, 75)
            });
        };

        /* Toggler for the sidebar */
        $('#admin_menu_toggler').on('click', event => {
            event.preventDefault();

            toggle_admin_sidebar();

            if($('body').hasClass('admin-sidebar-opened')) {
                $('#admin_overlay').off().on('click', toggle_admin_sidebar);
            } else {
                $('#admin_overlay').off();
            }

        });
    </script>
    </body>
</html>
