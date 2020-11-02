<?php defined('ALTUMCODE') || die() ?>

<div class="dropdown">
    <a href="#" data-toggle="dropdown" class="text-secondary dropdown-toggle dropdown-toggle-simple">
        <i class="fa fa-fw fa-ellipsis-v mr-1"></i>

        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="admin/page-update/<?= $data->id ?>"><i class="fa fa-fw fa-sm fa-pencil-alt mr-1"></i> <?= \Altum\Language::get()->global->edit ?></a>
            <a href="#" data-toggle="modal" data-target="#page_delete" data-page-id="<?= $data->id ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-times mr-1"></i> <?= \Altum\Language::get()->global->delete ?></a>
        </div>
    </a>
</div>
