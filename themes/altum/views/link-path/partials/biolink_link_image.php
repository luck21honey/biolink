<?php defined('ALTUMCODE') || die() ?>

<div class="my-3">
    <?php if($data->link->settings->location_url): ?>
    <a href="<?= $data->link->settings->location_url ?>" target="_blank">
        <img src="<?= $data->link->settings->image ?>" class="img-fluid rounded" />
    </a>
    <?php else: ?>
    <img src="<?= $data->link->settings->image ?>" class="img-fluid rounded" />
    <?php endif ?>
</div>

