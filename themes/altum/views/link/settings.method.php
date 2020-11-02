<?php defined('ALTUMCODE') || die() ?>

<?php

/* Get some variables */
$biolink_backgrounds = require APP_PATH . 'includes/biolink_backgrounds.php';

/* Get the proper settings depending on the type of link */
$settings = require THEME_PATH . 'views/link/settings/settings.' . strtolower($data->link->type) . '.method.php';

?>

<?= $settings->html ?>

<?php ob_start() ?>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/moment.min.js' ?>"></script>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/daterangepicker.min.js' ?>"></script>

<script>
    let update_main_url = () => {
        let new_url = $('form[name="update_biolink"] input[name="url"], form[name="update_link"] input[name="url"]').val();

        /* Title */
        $('#link_url').text(new_url);

        /* Change link and copy link */
        let new_full_url = null;

        if($('select[name="domain_id"]').length) {
            let link_base = $('select[name="domain_id"]').find(':selected').text();
            new_full_url = `${link_base}${new_url}`;
        } else {
            new_full_url = `${$('input[name="link_base"]').val()}${new_url}`;
        }

        $('#link_full_url').text(new_full_url).attr('href', new_full_url);
        $('#link_full_url_copy').attr('data-clipboard-text', new_full_url);

        /* Refresh iframe */
        if($('#biolink_preview_iframe').length) {
            let biolink_preview_iframe = $('#biolink_preview_iframe');
            let biolink_preview_iframe_new_full_url = `${biolink_preview_iframe.data('url-prepend')}${new_url}${biolink_preview_iframe.data('url-append')}`;

            biolink_preview_iframe.attr('src', biolink_preview_iframe_new_full_url);
        }
    };

    /* Link url dynamic change handler */
    $('form[name="update_link"]').on('submit', update_main_url);
</script>

<?= $settings->javascript ?>

<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
