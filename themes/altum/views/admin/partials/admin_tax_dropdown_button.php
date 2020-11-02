<?php defined('ALTUMCODE') || die() ?>

<div class="dropdown">
    <a href="#" data-toggle="dropdown" class="text-secondary dropdown-toggle dropdown-toggle-simple">
        <i class="fa fa-fw fa-ellipsis-v mr-1"></i>

        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="admin/tax-update/<?= $data->id ?>"><i class="fa fa-fw fa-sm fa-pencil-alt mr-1"></i> <?= \Altum\Language::get()->global->edit ?></a>
        </div>
    </a>
</div>
