<?php defined('ALTUMCODE') || die() ?>

<div class="dropdown">
    <a href="#" data-toggle="dropdown" class="text-secondary dropdown-toggle dropdown-toggle-simple">
        <i class="fa fa-fw fa-ellipsis-v mr-1"></i>

        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="admin/user-view/<?= $data->id ?>"><i class="fa fa-fw fa-sm fa-eye mr-1"></i> <?= \Altum\Language::get()->global->view ?></a>
            <a class="dropdown-item" href="admin/user-update/<?= $data->id ?>"><i class="fa fa-fw fa-sm fa-pencil-alt mr-1"></i> <?= \Altum\Language::get()->global->edit ?></a>
            <a href="#" data-toggle="modal" data-target="#user_delete" data-user-id="<?= $data->id ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-times mr-1"></i> <?= \Altum\Language::get()->global->delete ?></a>
            <a href="#" data-toggle="modal" data-target="#user_login" data-user-id="<?= $data->id ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-sign-in-alt mr-1"></i> <?= \Altum\Language::get()->global->login ?></a>
        </div>
    </a>
</div>
