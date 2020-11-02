<?php defined('ALTUMCODE') || die() ?>

<div class="my-3 embed-responsive embed-responsive-16by9 link-iframe-round">
    <iframe
            class="embed-responsive-item"
            scrolling="no"
            frameborder="no"
            src="https://player.twitch.tv/?channel=<?= $data->embed ?>&autoplay=false&parent=<?= \Altum\Database\Database::clean_string($_SERVER['HTTP_HOST']) ?>"
    ></iframe>
</div>

