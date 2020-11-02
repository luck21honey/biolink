<?php defined('ALTUMCODE') || die() ?>

<div class="card">
    <div class="card-body">
        <h3 class="h5"><?= $this->language->link->statistics->country_code ?></h3>
        <p class="text-muted mb-3"><?= $this->language->link->statistics->country_code_help ?></p>

        <?php foreach($data->rows as $row): ?>
            <div class="mb-2">
                <div class="d-flex justify-content-between">
                    <div class="text-truncate">
                        <img src="<?= SITE_URL . ASSETS_URL_PATH . 'images/countries/' . (!empty($row->country_code) ? strtolower($row->country_code) : 'unknown') . '.svg' ?>" class="img-fluid icon-favicon mr-1" />
                        <span class="align-middle"><?= $row->country_code ? get_country_from_country_code($row->country_code) : $this->language->link->statistics->country_code_unknown ?></span>
                    </div>

                    <div>
                        <span class="badge badge-pill badge-primary"><?= nr($row->total) ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</div>
