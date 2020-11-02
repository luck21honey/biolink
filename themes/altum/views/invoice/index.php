<?php defined('ALTUMCODE') || die() ?>

<div class="container my-5 d-flex justify-content-center">
    <div class="col-12 col-lg-10">

        <div class="d-print-none d-flex justify-content-between mb-5">
            <a href="<?= url('account-payments') ?>" class="text-muted" data-toggle="tooltip" title="<?= $this->language->global->go_back_button ?>"><i class="fa fa-fw fa-arrow-left"></i></a>

            <button type="button" class="btn btn-primary" onclick="window.print()"><i class="fa fa-fw fa-sm fa-print"></i> <?= $this->language->invoice->print ?></button>
        </div>

        <div class="card border-0">
            <div class="card-body p-5">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <div class="mb-3 mb-md-0">
                        <?php if($this->settings->logo != ''): ?>
                            <img src="<?= SITE_URL . UPLOADS_URL_PATH . 'logo/' . $this->settings->logo ?>" class="img-fluid navbar-logo invoice-logo" alt="<?= $this->language->global->accessibility->logo_alt ?>" />
                        <?php else: ?>
                            <h1><?= $this->settings->title ?></h1>
                        <?php endif ?>
                    </div>

                    <div class="d-flex flex-column">
                        <h4><?= $this->language->invoice->invoice ?></h4>

                        <table>
                            <tbody>
                            <tr>
                                <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->invoice_nr ?>:</td>
                                <td><?= $this->settings->business->invoice_nr_prefix . $data->payment->id ?></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->invoice_date ?>:</td>
                                <td><?= \Altum\Date::get($data->payment->date, 1) ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-7">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-6 mb-md-0">
                            <h5><?= $this->language->invoice->vendor ?></h5>

                            <table>
                                <tbody>
                                <tr>
                                    <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->name ?>:</td>
                                    <td><?= $this->settings->business->name ?></td>
                                </tr>

                                <?php if(!empty($this->settings->business->address)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->address ?>:</td>
                                        <td><?= $this->settings->business->address ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($this->settings->business->city)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->city ?>:</td>
                                        <td><?= $this->settings->business->city ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($this->settings->business->county)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->county ?>:</td>
                                        <td><?= $this->settings->business->county ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($this->settings->business->zip)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->zip ?>:</td>
                                        <td><?= $this->settings->business->zip ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($this->settings->business->country)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->country ?>:</td>
                                        <td><?= get_countries_array()[$this->settings->business->country] ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($this->settings->business->email)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->email ?>:</td>
                                        <td><?= $this->settings->business->email ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($this->settings->business->phone)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->phone ?>:</td>
                                        <td><?= $this->settings->business->phone ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($this->settings->business->tax_type) && !empty($this->settings->business->tax_id)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $this->settings->business->tax_type ?>:</td>
                                        <td><?= $this->settings->business->tax_id ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($this->settings->business->custom_key_one) && !empty($this->settings->business->custom_value_one)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $this->settings->business->custom_key_one ?>:</td>
                                        <td><?= $this->settings->business->custom_value_one ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($this->settings->business->custom_key_two) && !empty($this->settings->business->custom_value_two)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $this->settings->business->custom_key_two ?>:</td>
                                        <td><?= $this->settings->business->custom_value_two ?></td>
                                    </tr>
                                <?php endif ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-12 col-md-6">
                            <h5><?= $this->language->invoice->customer ?></h5>

                            <table>
                                <tbody>
                                <?php if($data->payment->billing): ?>

                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->name ?>:</td>
                                        <td><?= $data->payment->billing->name ?></td>
                                    </tr>

                                    <?php if(!empty($data->payment->billing->address)): ?>
                                        <tr>
                                            <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->address ?>:</td>
                                            <td><?= $data->payment->billing->address ?></td>
                                        </tr>
                                    <?php endif ?>

                                    <?php if(!empty($data->payment->billing->city)): ?>
                                        <tr>
                                            <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->city ?>:</td>
                                            <td><?= $data->payment->billing->city ?></td>
                                        </tr>
                                    <?php endif ?>

                                    <?php if(!empty($data->payment->billing->county)): ?>
                                        <tr>
                                            <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->county ?>:</td>
                                            <td><?= $data->payment->billing->county ?></td>
                                        </tr>
                                    <?php endif ?>

                                    <?php if(!empty($data->payment->billing->zip)): ?>
                                        <tr>
                                            <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->zip ?>:</td>
                                            <td><?= $data->payment->billing->zip ?></td>
                                        </tr>
                                    <?php endif ?>

                                    <?php if(!empty($data->payment->billing->country)): ?>
                                        <tr>
                                            <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->country ?>:</td>
                                            <td><?= get_countries_array()[$data->payment->billing->country] ?></td>
                                        </tr>
                                    <?php endif ?>

                                    <?php if(!empty($data->payment->billing->email)): ?>
                                        <tr>
                                            <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->email ?>:</td>
                                            <td><?= $data->payment->billing->email ?></td>
                                        </tr>
                                    <?php endif ?>

                                    <?php if(!empty($data->payment->billing->phone)): ?>
                                        <tr>
                                            <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->phone ?>:</td>
                                            <td><?= $data->payment->billing->phone ?></td>
                                        </tr>
                                    <?php endif ?>

                                    <?php if($data->payment->billing->type == 'business'): ?>
                                        <tr>
                                            <td class="font-weight-bold text-muted pr-3"><?= !empty($this->settings->business->tax_type) ? $this->settings->business->tax_type : $this->language->invoice->tax_id ?>:</td>
                                            <td><?= $data->payment->billing->tax_id ?></td>
                                        </tr>
                                    <?php endif ?>

                                <?php else: ?>

                                    <?php if(!empty($data->payment->name)): ?>
                                        <tr>
                                            <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->name ?>:</td>
                                            <td><?= $data->payment->name ?></td>
                                        </tr>
                                    <?php endif ?>

                                    <?php if(!empty($data->payment->email)): ?>
                                        <tr>
                                            <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->email ?>:</td>
                                            <td><?= $data->payment->email ?></td>
                                        </tr>
                                    <?php endif ?>

                                <?php endif ?>
                                </tbody>
                            </table>

                            <textarea class="form-control mt-3" rows="4"></textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-7">
                    <table class="table invoice-table">
                        <thead>
                        <tr>
                            <th><?= $this->language->invoice->table->item ?></th>
                            <th class="text-right"><?= $this->language->invoice->table->amount ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <div class="d-flex flex-column">
                                    <span><?= sprintf($this->language->invoice->table->plan, $data->payment->plan->name) ?></span>
                                    <span class="text-muted"><?= sprintf($this->language->invoice->table->frequency, $this->language->invoice->table->{'frequency_' . $data->payment->frequency}) ?></span>
                                </div>
                            </td>
                            <td class="text-right"><?= ($data->payment->base_amount ? $data->payment->base_amount : $data->payment->total_amount) . ' ' . $data->payment->currency ?></td>
                        </tr>

                        <?php if($data->payment->discount_amount): ?>
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span><?= $this->language->invoice->table->code ?></span>
                                        <span class="text-muted"><?= sprintf($this->language->invoice->table->code_help, $data->payment->code) ?></span>
                                    </div>
                                </td>
                                <td class="text-right"><?= '-' . $data->payment->discount_amount . ' ' . $data->payment->currency ?></td>
                            </tr>
                        <?php endif ?>

                        <?php if(!empty($data->payment_taxes)): ?>
                            <?php foreach($data->payment_taxes as $row): ?>

                                <?php

                                $data->payment->discount_amount;

                                ?>


                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span><?= $row->name ?></span>
                                            <span class="text-muted"><?= $row->description ?></span>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <?php if($row->type == 'inclusive'): ?>
                                            <?= $row->amount ?>
                                        <?php else: ?>
                                            <?= '+' . $row->amount ?>
                                        <?php endif ?>
                                        <span><?= $data->payment->currency ?></span>
                                    </td>
                                </tr>

                            <?php endforeach ?>
                        <?php endif ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td class="d-flex flex-column">
                                <span class="font-weight-bold"><?= $this->language->invoice->table->total ?></span>
                                <small><?= sprintf($this->language->invoice->table->paid_via, $data->payment->processor) ?></small>
                            </td>
                            <td class="text-right font-weight-bold"><?= $data->payment->total_amount . ' ' . $data->payment->currency ?></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>
