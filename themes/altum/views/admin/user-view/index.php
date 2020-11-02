<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <div class="d-flex align-items-center">
        <h1 class="h3 mr-3"><i class="fa fa-fw fa-xs fa-user text-primary-900 mr-2"></i> <?= $this->language->admin_user_view->header ?></h1>

        <?= include_view(THEME_PATH . 'views/admin/partials/admin_user_dropdown_button.php', ['id' => $data->user->user_id]) ?>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->type ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $data->user->type ? $this->language->admin_user_view->main->type_admin : $this->language->admin_user_view->main->type_user ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->email ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $data->user->email ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->name ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $data->user->name ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->status ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $data->user->active ? $this->language->admin_user_view->main->status_active : $this->language->admin_user_view->main->status_disabled ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->ip ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $data->user->ip ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->country ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $data->user->country ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->last_activity ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $data->user->last_activity ? $data->user->last_activity : '-' ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->last_user_agent ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $data->user->last_user_agent ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->plan ?></label>
                    <div>
                        <a href="<?= url('admin/plan-update/' . $data->user->plan->plan_id) ?>"><?= $data->user->plan->name ?></a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->plan_expiration_date ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $data->user->plan_expiration_date ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->plan_trial_done ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $data->user->plan_trial_done ? $this->language->global->yes : $this->language->global->no ?>" readonly />
                </div>
            </div>


            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->total_logins ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $data->user->total_logins ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $this->language->admin_user_view->main->language ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $data->user->language ?>" readonly />
                </div>
            </div>

        </div>
    </div>
</div>

<div class="my-5 row justify-content-between">
    <div class="col-12 col-sm-6 col-xl-3 mb-4">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-tasks mr-1"></i> <?= $this->language->admin_user_view->projects ?></small>

                <div class="mt-3"><span class="h4"><?= nr($data->user_projects_total) ?></span></div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 mb-4">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-link mr-1"></i> <?= $this->language->admin_user_view->links ?></small>

                <div class="mt-3"><span class="h4"><?= nr($data->user_links_total) ?></span></div>
            </div>

            <div class="bg-gray-200 px-2 d-flex flex-column justify-content-center">
                <a href="<?= url('admin/links?email=' . $data->user->email) ?>">
                    <i class="fa fa-fw fa-arrow-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 mb-4">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-globe mr-1"></i> <?= $this->language->admin_user_view->domains ?></small>

                <div class="mt-3"><span class="h4"><?= nr($data->user_domains_total) ?></span></div>
            </div>

            <div class="bg-gray-200 px-2 d-flex flex-column justify-content-center">
                <a href="<?= url('admin/domains?email=' . $data->user->email) ?>">
                    <i class="fa fa-fw fa-arrow-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 mb-4">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-funnel-dollar mr-1"></i> <?= $this->language->admin_user_view->payments ?></small>

                <div class="mt-3"><span class="h4"><?= nr($data->user_payments_total) ?></span></div>
            </div>

            <div class="bg-gray-200 px-2 d-flex flex-column justify-content-center">
                <a href="<?= url('admin/payments?email=' . $data->user->email) ?>">
                    <i class="fa fa-fw fa-arrow-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<?php if($data->user_logs_result->num_rows): ?>
    <h2 class="h4 mt-5"><?= $this->language->admin_user_view->logs->header ?></h2>
    <p class="text-muted"><?= $this->language->admin_user_view->logs->subheader ?></p>

    <div class="table-responsive table-custom-container">
        <table class="table table-custom">
            <thead>
            <tr>
                <th><?= $this->language->admin_user_view->logs->type ?></th>
                <th><?= $this->language->admin_user_view->logs->ip ?></th>
                <th><?= $this->language->admin_user_view->logs->date ?></th>
            </tr>
            </thead>
            <tbody>

            <?php $nr = 1; while($row = $data->user_logs_result->fetch_object()): ?>
                <tr>
                    <td><?= $row->type ?></td>
                    <td><?= $row->ip ?></td>
                    <td class="text-muted"><?= \Altum\Date::get($row->date, 1) ?></td>
                </tr>
            <?php endwhile ?>

            </tbody>
        </table>
    </div>
<?php endif ?>
