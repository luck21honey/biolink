<?php defined('ALTUMCODE') || die() ?>

<div class="card">
    <div class="card-body">
        <h3 class="h5"><?= $this->language->link->statistics->lastactivity ?></h3>
        <p class="text-muted mb-4"><?= $this->language->link->statistics->lastactivity_help ?></p>

        <?php foreach($data->rows as $row): ?>

            <div class="row mb-4">
                <div class="col-10 col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center mr-3">
                            <img src="<?= SITE_URL . ASSETS_URL_PATH . 'images/countries/' . (!empty($row->country_code) ? strtolower($row->country_code) : 'unknown') . '.svg' ?>" class="img-fluid icon-favicon mr-1" />
                            <span class="align-middle"><?= $row->country_code ? get_country_from_country_code($row->country_code) : $this->language->link->statistics->country_code_unknown ?></span>
                        </div>

                        <small class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->last_date, 1) ?>"><?= \Altum\Date::get_timeago($row->last_date) ?></small>
                    </div>

                    <div class="text-truncate">
                        <?php if($row->referrer): ?>
                            <img src="https://external-content.duckduckgo.com/ip3/<?= parse_url($row->referrer)['host'] ?>.ico" class="img-fluid icon-favicon mr-1" />
                            <small><a href="<?= $row->referrer ?>" title="<?= $row->referrer ?>" class="text-muted align-middle"><?= $row->referrer ?></a></small>
                        <?php else: ?>
                            <small class="text-muted"><?= $this->language->link->statistics->referrer_direct ?></small>
                        <?php endif ?>
                    </div>
                </div>

                <div class="d-none d-md-block col-3">
                    <div class="d-flex align-items-center">
                        <?php if($row->device_type): ?>
                            <i class="fa fa-fw fa-sm fa-<?= $row->device_type ?> mr-1"></i>
                            <?= $this->language->link->statistics->{'device_type_' . $row->device_type} ?>
                        <?php else: ?>
                            <?= $this->language->link->statistics->device_type_unknown ?>
                        <?php endif ?>
                    </div>

                    <div class="text-muted">
                        <?php if($row->os_name): ?>
                            <?= $row->os_name ?>
                        <?php else: ?>
                            <?= $this->language->link->statistics->os_name_unknown ?>
                        <?php endif ?>
                    </div>
                </div>

                <div class="d-none d-md-block col-3">
                    <div class="d-flex">
                        <?php if($row->browser_name): ?>
                            <?= $row->browser_name ?>
                        <?php else: ?>
                            <?= $this->language->link->statistics->browser_name_unknown ?>
                        <?php endif ?>
                    </div>

                    <div class="text-muted">
                        <?php if($row->browser_language): ?>
                            <?= get_language_from_locale($row->browser_language) ?>
                        <?php else: ?>
                            <?= $this->language->link->statistics->browser_language_unknown ?>
                        <?php endif ?>
                    </div>
                </div>

            </div>
        <?php endforeach ?>
    </div>
</div>
