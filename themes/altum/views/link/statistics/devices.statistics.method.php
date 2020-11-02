<?php defined('ALTUMCODE') || die() ?>

<div class="card">
    <div class="card-body">
        <h3 class="h5"><?= $this->language->link->statistics->device_type ?></h3>
        <p class="text-muted mb-3"><?= $this->language->link->statistics->device_type_help ?></p>

        <?php foreach($data->rows as $row): ?>
            <div class="mb-2">
                <div class="d-flex justify-content-between">
                    <div class="text-truncate">
                        <?php if(!$row->device_type): ?>
                            <span><?= $this->language->link->statistics->device_type_unknown ?></span>
                        <?php else: ?>
                            <span><?= $this->language->link->statistics->{'device_type_' . $row->device_type} ?></span>
                        <?php endif ?>
                    </div>

                    <div>
                        <span class="badge badge-pill badge-primary"><?= nr($row->total) ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</div>
