<?php defined('ALTUMCODE') || die() ?>

<header class="header">
    <div class="container">

        <div class="row justify-content-between">
            <div class="col-12 col-md-6 col-xl-3 mb-3 mb-xl-0">
                <div class="card border-0 h-100">
                    <div class="card-body d-flex">

                        <div>
                            <div class="card border-0 bg-primary-200 text-primary-700 mr-3">
                                <div class="p-3 d-flex align-items-center justify-content-between">
                                    <i class="fa fa-fw fa-box-open fa-lg"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="card-title h4 m-0"><?= $this->user->plan->name ?></div>
                            <small class="text-muted"><?= $this->language->dashboard->header->plan ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <?php if($this->user->plan_id != 'free'): ?>
            <div class="col-12 col-md-6 col-xl-3 mb-3 mb-xl-0">
                <div class="card border-0 h-100">
                    <div class="card-body d-flex">

                        <div>
                            <div class="card border-0 bg-primary-200 text-primary-700 mr-3">
                                <div class="p-3 d-flex align-items-center justify-content-between">
                                    <i class="fa fa-fw fa-calendar fa-lg"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="card-title h4 m-0"><?= \Altum\Date::get_time_until($this->user->plan_expiration_date) ?></div>
                            <small class="text-muted"><?= $this->language->dashboard->header->plan_expiration_date ?></small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif ?>

            <div class="col-12 col-md-6 col-xl-3 mb-3 mb-xl-0">
                <div class="card border-0 h-100">
                    <div class="card-body d-flex">

                        <div>
                            <div class="card border-0 bg-primary-200 text-primary-700 mr-3">
                                <div class="p-3 d-flex align-items-center justify-content-between">
                                    <i class="fa fa-fw fa-chart-bar fa-lg"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="card-title h4 m-0"><?= nr($data->links_clicks_total) ?></div>
                            <small class="text-muted"><?= $this->language->dashboard->header->clicks ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3 mb-3 mb-xl-0">
                <div class="card border-0 h-100">
                    <div class="card-body d-flex">

                        <div>
                            <div class="card border-0 bg-primary-200 text-primary-700 mr-3">
                                <div class="p-3 d-flex align-items-center justify-content-between">
                                    <i class="fa fa-fw fa-link fa-lg"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="card-title h4 m-0"><?= nr($data->links_total) ?></div>
                            <small class="text-muted"><?= $this->language->dashboard->header->links ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</header>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<section class="container">

    <?php display_notifications() ?>

    <div class="mt-5 d-flex justify-content-between">
        <h2 class="h4"><?= $this->language->dashboard->projects->header ?></h2>

        <?php if(count($data->projects)): ?>
        <div class="col-auto p-0">
            <?php if($this->user->plan_settings->projects_limit != -1 && $data->projects_total >= $this->user->plan_settings->projects_limit): ?>
                <button type="button" data-confirm="<?= $this->language->project->error_message->projects_limit ?>"  class="btn btn-primary rounded-pill"><i class="fa fa-fw fa-plus-circle"></i> <?= $this->language->dashboard->projects->create ?></button>
            <?php else: ?>
                <button type="button" data-toggle="modal" data-target="#create_project" class="btn btn-primary rounded-pill"><i class="fa fa-fw fa-plus-circle"></i> <?= $this->language->dashboard->projects->create ?></button>
            <?php endif ?>
        </div>
        <?php endif ?>
    </div>
    <p class="text-muted"><?= $this->language->dashboard->projects->subheader ?></p>


    <?php if(count($data->projects)): ?>

        <?php foreach($data->projects as $row): ?>
            <?php

            /* Get some stats about the project */
            $row->statistics = $this->database->query("SELECT COUNT(*) AS `total`, SUM(`clicks`) AS `clicks` FROM `links` WHERE `project_id` = {$row->project_id}")->fetch_object();

            ?>
            <div class="d-flex custom-row align-items-center my-4" data-project-id="<?= $row->project_id ?>">
                <div class="col-6">
                    <div class="font-weight-bold text-truncate h6">
                        <a href="<?= url('project/' . $row->project_id) ?>"><?= $row->name ?></a>
                    </div>

                    <div class="text-muted d-flex align-items-center"><i class="fa fa-fw fa-calendar-alt fa-sm mr-1"></i> <?= \Altum\Date::get($row->date, 2) ?></div>
                </div>

                <div class="col-4 d-flex flex-column flex-lg-row justify-content-lg-around">
                    <div>
                        <span data-toggle="tooltip" title="<?= $this->language->project->links->total ?>" class="badge badge-info">
                            <i class="fa fa-fw fa-link mr-1"></i> <?= nr($row->statistics->total) ?>
                        </span>
                    </div>

                    <div>
                        <span data-toggle="tooltip" title="<?= $this->language->project->links->clicks ?>" class="badge badge-primary">
                            <i class="fa fa-fw fa-chart-bar mr-1"></i> <?= nr($row->statistics->clicks) ?>
                        </span>
                    </div>
                </div>

                <div class="col-2 d-flex justify-content-end">
                    <div class="dropdown">
                        <a href="#" data-toggle="dropdown" class="text-secondary dropdown-toggle dropdown-toggle-simple">
                            <i class="fa fa-ellipsis-v"></i>

                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="#" data-toggle="modal" data-target="#project_update" data-project-id="<?= $row->project_id ?>" data-name="<?= $row->name ?>" class="dropdown-item"><i class="fa fa-fw fa-pencil-alt"></i> <?= $this->language->global->edit ?></a>
                                <a href="#" data-toggle="modal" data-target="#project_delete" data-project-id="<?= $row->project_id ?>" class="dropdown-item"><i class="fa fa-fw fa-times"></i> <?= $this->language->global->delete ?></a>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach ?>

        <div class="mt-3"><?= $data->pagination ?></div>

    <?php else: ?>
        <div class="d-flex flex-column align-items-center justify-content-center mt-5">
            <img src="<?= SITE_URL . ASSETS_URL_PATH . 'images/no_data.svg' ?>" class="col-10 col-md-6 col-lg-4 mb-4" alt="<?= $this->language->dashboard->projects->no_data ?>" />
            <h2 class="h4 mb-5 text-muted"><?= $this->language->dashboard->projects->no_data ?></h2>

            <?php if($this->user->plan_settings->projects_limit != -1 && $data->projects_total >= $this->user->plan_settings->projects_limit): ?>
                <button type="button" data-confirm="<?= $this->language->project->error_message->projects_limit ?>"  class="btn btn-primary rounded-pill"><i class="fa fa-fw fa-plus-circle"></i> <?= $this->language->dashboard->projects->create ?></button>
            <?php else: ?>
                <button type="button" data-toggle="modal" data-target="#create_project" class="btn btn-primary rounded-pill"><i class="fa fa-fw fa-plus-circle"></i> <?= $this->language->dashboard->projects->create ?></button>
            <?php endif ?>
        </div>
    <?php endif ?>

</section>

