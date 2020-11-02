<?php defined('ALTUMCODE') || die() ?>

<div class="dropdown">
    <a href="#" data-toggle="dropdown" class="text-secondary dropdown-toggle dropdown-toggle-simple">
        <i class="fa fa-fw fa-ellipsis-v"></i>

        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="admin/domain-update/<?= $data->id ?>"><i class="fa fa-fw fa-pencil-alt"></i> <?= \Altum\Language::get()->global->edit ?></a>
            <a class="dropdown-item" data-confirm="<?= \Altum\Language::get()->global->info_message->confirm_delete ?>" href="admin/domains/delete/<?= $data->id . \Altum\Middlewares\Csrf::get_url_query() ?>"><i class="fa fa-fw fa-times"></i> <?= \Altum\Language::get()->global->delete ?></a>
        </div>
    </a>
</div>
