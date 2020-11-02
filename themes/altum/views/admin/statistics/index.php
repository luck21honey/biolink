<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-lg-row justify-content-between mb-4">
    <div>
        <div class="d-flex justify-content-between">
            <h1 class="h3"><i class="fa fa-fw fa-xs fa-chart-line text-primary-900 mr-2"></i> <?= sprintf($this->language->admin_statistics->header) ?></h1>
        </div>
        <p class="text-muted"><?= $this->language->admin_statistics->subheader ?></p>
    </div>

    <div class="col-auto p-0">
        <button
                id="daterangepicker"
                type="button"
                class="btn btn-sm btn-outline-secondary"
                data-max-date="<?= (new \DateTime())->format('Y-m-d') ?>"
        >
            <i class="fa fa-fw fa-calendar mr-1"></i>
            <span>
                <?php if($data->date->start_date == $data->date->end_date): ?>
                    <?= \Altum\Date::get($data->date->start_date, 2, \Altum\Date::$default_timezone) ?>
                <?php else: ?>
                    <?= \Altum\Date::get($data->date->start_date, 2, \Altum\Date::$default_timezone) . ' - ' . \Altum\Date::get($data->date->end_date, 2, \Altum\Date::$default_timezone) ?>
                <?php endif ?>
            </span>
            <i class="fa fa-fw fa-caret-down ml-1"></i>
        </button>
    </div>
</div>

<?php display_notifications() ?>

<div class="row">
    <div class="mb-5 mb-xl-0 col-12 col-xl-3">
        <div class="nav flex-column nav-pills">
            <a class="nav-link <?= $data->type == 'growth' ? 'active' : null ?>" href="<?= url('admin/statistics/growth?start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>"><i class="fa fa-fw fa-sm fa-seedling mr-1"></i> <?= $this->language->admin_statistics->growth->menu ?></a>
            <?php if(in_array($this->settings->license->type, ['SPECIAL','Extended License'])): ?>
            <a class="nav-link <?= $data->type == 'payments' ? 'active' : null ?>" href="<?= url('admin/statistics/payments?start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>"><i class="fa fa-fw fa-sm fa-dollar-sign mr-1"></i> <?= $this->language->admin_statistics->payments->menu ?></a>
            <?php endif ?>
            <a class="nav-link <?= $data->type == 'links' ? 'active' : null ?>" href="<?= url('admin/statistics/links?start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>"><i class="fa fa-fw fa-sm fa-link mr-1"></i> <?= $this->language->admin_statistics->links->menu ?></a>
        </div>
    </div>

    <div class="col-12 col-xl-9">

        <?php

        /* Load the proper type view */
        $partial = require THEME_PATH . 'views/admin/statistics/partials/' . $data->type . '.php';

        echo $partial->html;

        ?>

    </div>
</div>

<?php ob_start() ?>
<link href="<?= SITE_URL . ASSETS_URL_PATH . 'css/daterangepicker.min.css' ?>" rel="stylesheet" media="screen">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/moment.min.js' ?>"></script>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/daterangepicker.min.js' ?>"></script>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/Chart.bundle.min.js' ?>"></script>

    <script>
        'use strict';

        /* Daterangepicker */
        $('#daterangepicker').daterangepicker({
            startDate: <?= json_encode($data->date->start_date) ?>,
            endDate: <?= json_encode($data->date->end_date) ?>,
            minDate: $('#daterangepicker').data('min-date'),
            maxDate: $('#daterangepicker').data('max-date'),
            ranges: {
                <?= json_encode($this->language->global->date->today) ?>: [moment(), moment()],
                <?= json_encode($this->language->global->date->yesterday) ?>: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                <?= json_encode($this->language->global->date->last_7_days) ?>: [moment().subtract(6, 'days'), moment()],
                <?= json_encode($this->language->global->date->last_30_days) ?>: [moment().subtract(29, 'days'), moment()],
                <?= json_encode($this->language->global->date->this_month) ?>: [moment().startOf('month'), moment().endOf('month')],
                <?= json_encode($this->language->global->date->last_month) ?>: [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            alwaysShowCalendars: true,
            singleCalendar: true,
            locale: <?= json_encode(require APP_PATH . 'includes/daterangepicker_translations.php') ?>,
        }, (start, end, label) => {

            /* Redirect */
            redirect(`<?= url('admin/statistics/' . $data->type) ?>?start_date=${start.format('YYYY-MM-DD')}&end_date=${end.format('YYYY-MM-DD')}`, true);

        });

        /* Default chart settings */
        Chart.defaults.global.elements.line.borderWidth = 4;
        Chart.defaults.global.elements.point.radius = 3;
        Chart.defaults.global.elements.point.borderWidth = 5;

        /* Default chart options */
        let chart_options = {
            animation: {
                duration: 0
            },
            hover: {
                animationDuration: 0
            },
            responsiveAnimationDuration: 0,
            elements: {
                line: {
                    tension: 0
                }
            },
            tooltips: {
                mode: 'index',
                intersect: false
            },
            title: {
                text: '',
                display: true
            },
            scales: {
                yAxes: [{
                    gridLines: {
                        display: false
                    },
                    ticks: {
                        beginAtZero: true,
                        userCallback: (value, index, values) => {
                            if (Math.floor(value) === value) {
                                return nr(value);
                            }
                        },
                    }
                }],
                xAxes: [{
                    gridLines: {
                        display: false
                    },
                    ticks: {
                        callback: (tick, index, array) => {
                            return index % 2 ? '' : tick;
                        }
                    }
                }]
            },
            responsive: true,
            maintainAspectRatio: false
        };

        let css = window.getComputedStyle(document.body)
    </script>

<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php ob_start() ?>
<?= $partial->javascript ?>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
