<?php defined('ALTUMCODE') || die() ?>

<div class="dropdown">
    <a href="#" data-toggle="dropdown" class="text-secondary dropdown-toggle dropdown-toggle-simple">
        <i class="fa fa-fw fa-ellipsis-v mr-1 <?= $data->processor == 'offline_payment' && !$data->status ? 'text-danger' : null ?>"></i>

        <div class="dropdown-menu dropdown-menu-right">
            <?php if($data->processor == 'offline_payment'): ?>
                <a href="<?= SITE_URL . UPLOADS_URL_PATH . 'offline_payment_proofs/' . $data->payment_proof ?>" target="_blank" class="dropdown-item"><i class="fa fa-fw fa-sm fa-download mr-1"></i> <?= \Altum\Language::get()->admin_payments->table->action_view_proof ?></a>

                <?php if(!$data->status): ?>
                    <a href="#" data-toggle="modal" data-target="#payment_approve" data-payment-id="<?= $data->id ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-check mr-1"></i> <?= \Altum\Language::get()->admin_payments->table->action_approve_proof ?></a>
                <?php endif ?>
            <?php endif ?>

            <?php if($data->status): ?>
                <a href="<?= url('invoice/' . $data->id) ?>" target="_blank" class="dropdown-item"><i class="fa fa-fw fa-sm fa-file-invoice mr-1"></i> <?= \Altum\Language::get()->admin_payments->table->invoice ?></a>
            <?php endif ?>

            <a href="#" data-toggle="modal" data-target="#payment_delete" data-payment-id="<?= $data->id ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-times mr-1"></i> <?= \Altum\Language::get()->global->delete ?></a>
        </div>
    </a>
</div>
