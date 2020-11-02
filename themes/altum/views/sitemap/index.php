<?php defined('ALTUMCODE') || die() ?>
<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?= SITE_URL ?></loc>
    </url>
    <url>
        <loc><?= SITE_URL . 'login' ?></loc>
    </url>
    <url>
        <loc><?= SITE_URL . 'register' ?></loc>
    </url>
    <url>
        <loc><?= SITE_URL . 'lost-password' ?></loc>
    </url>
    <url>
        <loc><?= SITE_URL . 'resend-activation' ?></loc>
    </url>
    <url>
        <loc><?= SITE_URL . 'register' ?></loc>
    </url>
    <url>
        <loc><?= SITE_URL . 'pages' ?></loc>
    </url>
    <?php while($row = $data->pages_result->fetch_object()): ?>
        <url>
            <loc><?= SITE_URL . 'page/' . $row->url ?></loc>
        </url>
    <?php endwhile ?>

    <?php while($row = $data->biolinks_result->fetch_object()): ?>
        <url>
            <loc><?= SITE_URL . $row->url ?></loc>
        </url>
    <?php endwhile ?>
</urlset>
