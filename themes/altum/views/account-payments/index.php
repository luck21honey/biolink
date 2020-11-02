<?php defined('ALTUMCODE') || die() ?>

<header class="header pb-0">
    <div class="container">
        <?= $this->views['account_header'] ?>
    </div>
</header>

<section class="container pt-5">

    <?php display_notifications() ?>

    <?php if(count($data->payments)): ?>
        <h2 class="h3"><?= $this->language->account_payments->header ?></h2>
        <p class="text-muted"><?= $this->language->account_payments->subheader ?></p>

        <div class="table-responsive table-custom-container">
            <table class="table table-custom">
                <thead>
                <tr>
                    <th><?= $this->language->account_payments->payments->customer ?></th>
                    <th><?= $this->language->account_payments->payments->plan_id ?></th>
                    <th><?= $this->language->account_payments->payments->type ?></th>
                    <th><?= $this->language->account_payments->payments->total_amount ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>

                <?php foreach($data->payments as $row): ?>

                    <tr>
                        <td>
                            <div class="d-flex flex-column">
                                <span><?= $row->email ?></span>
                                <span class="text-muted"><?= $row->name ?></span>
                            </div>
                        </td>

                        <td><?= $row->plan_name ?></td>

                        <td>
                            <div class="d-flex flex-column">
                                <span><?= $this->language->pay->custom_plan->{$row->type . '_type'} ?></span>
                                <span class="text-muted"><?= $this->language->pay->custom_plan->{$row->processor} ?></span>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex flex-column">
                                <span><span class="text-success"><?= $row->total_amount ?></span> <?= $row->currency ?></span>
                                <span class="text-muted"><span data-toggle="tooltip" title="<?= \Altum\Date::get($row->date, 1) ?>"><?= \Altum\Date::get($row->date, 2) ?></span></span>
                            </div>
                        </td>

                        <?php if($row->status): ?>
                            <?php if($this->settings->business->invoice_is_enabled): ?>

                                <td>
                                    <a href="<?= url('invoice/' . $row->id) ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa fa-fw fa-sm fa-file-invoice"></i> <?= $this->language->account_payments->payments->invoice ?>
                                    </a>
                                </td>

                            <?php else: ?>

                                <td>
                                    <span class="badge badge-success"><?= $this->language->account_payments->payments->status_approved ?></span>
                                </td>

                            <?php endif ?>
                        <?php else: ?>

                            <td>
                                <span class="badge badge-warning"><?= $this->language->account_payments->payments->status_pending ?></span>
                            </td>

                        <?php endif ?>
                    </tr>
                <?php endforeach ?>

                </tbody>
            </table>
        </div>

        <div class="mt-3"><?= $data->pagination ?></div>

    <?php else: ?>
        <div class="d-flex flex-column align-items-center justify-content-center">
            <img src="<?= SITE_URL . ASSETS_URL_PATH . 'images/no_data.svg' ?>" class="col-10 col-md-6 col-lg-4 mb-3" alt="<?= $this->language->account_payments->payments->no_data ?>" />
            <h2 class="h4 text-muted"><?= $this->language->account_payments->payments->no_data ?></h2>
        </div>
    <?php endif ?>
</section>
