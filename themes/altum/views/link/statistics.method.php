<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-md-row justify-content-between mb-3">
    <h2 class="h4 mr-3"><?= $this->language->link->statistics->header ?></h2>

    <div>
        <button
                id="daterangepicker"
                type="button"
                class="btn btn-sm btn-outline-primary"
                data-min-date="<?= (new \DateTime($data->link->date))->format('Y-m-d') ?>"
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

<?php if(!count($data->logs)): ?>

    <div class="d-flex flex-column align-items-center justify-content-center">
        <img src="<?= SITE_URL . ASSETS_URL_PATH . 'images/no_data.svg' ?>" class="col-10 col-md-6 col-lg-4 mb-3" alt="<?= $this->language->link->statistics->no_data ?>" />
        <h2 class="h4 text-muted"><?= $this->language->link->statistics->no_data ?></h2>
    </div>

<?php elseif(!$this->user->plan_settings->statistics): ?>

    <?php if($this->settings->payment->is_enabled): ?>
        <div class="d-flex flex-column align-items-center justify-content-center">
            <img src="<?= SITE_URL . ASSETS_URL_PATH . 'images/unlock.svg' ?>" class="col-10 col-md-6 col-lg-4 mb-3" alt="<?= $this->language->link->statistics->missing_statistics_plan ?>" />
            <h2 class="h4"><a href="<?= url('plan') ?>"><?= $this->language->global->info_message->unlock_feature ?></a></h2>
            <span class="text-muted"><?= $this->language->link->statistics->missing_statistics_plan ?></span>
        </div>
    <?php else: ?>
        <div class="d-flex flex-column align-items-center justify-content-center">
            <img src="<?= SITE_URL . ASSETS_URL_PATH . 'images/unlock.svg' ?>" class="col-10 col-md-6 col-lg-4 mb-3" alt="<?= $this->language->link->statistics->missing_statistics_plan ?>" />
            <h2 class="h4"><a href="<?= url('plan') ?>"><?= $this->language->global->info_message->locked_feature ?></a></h2>
            <span class="text-muted"><?= $this->language->link->statistics->missing_statistics_plan ?></span>
        </div>
    <?php endif ?>

<?php else: ?>

    <div class="chart-container mb-5">
        <canvas id="clicks_chart"></canvas>
    </div>

    <ul class="nav nav-pills flex-column flex-lg-row mb-3" role="tablist">
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'lastactivity' ? 'active' : null ?>" href="<?= url('link/' . $data->link->link_id . '/statistics/lastactivity?start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>">
                <i class="fa fa-fw fa-list mr-1"></i>
                <?= $this->language->link->statistics->lastactivity ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'referrers' ? 'active' : null ?>" href="<?= url('link/' . $data->link->link_id . '/statistics/referrers?start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>">
                <i class="fa fa-fw fa-random mr-1"></i>
                <?= $this->language->link->statistics->referrer ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'countries' ? 'active' : null ?>" href="<?= url('link/' . $data->link->link_id . '/statistics/countries?start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>">
                <i class="fa fa-fw fa-globe mr-1"></i>
                <?= $this->language->link->statistics->country_code ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'devices' ? 'active' : null ?>" href="<?= url('link/' . $data->link->link_id . '/statistics/devices?start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>">
                <i class="fa fa-fw fa-laptop mr-1"></i>
                <?= $this->language->link->statistics->device_type ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'browsers' ? 'active' : null ?>" href="<?= url('link/' . $data->link->link_id . '/statistics/browsers?start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>">
                <i class="fa fa-fw fa-window-restore mr-1"></i>
                <?= $this->language->link->statistics->browser_name ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'browserlanguages' ? 'active' : null ?>" href="<?= url('link/' . $data->link->link_id . '/statistics/browserlanguages?start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>">
                <i class="fa fa-fw fa-language mr-1"></i>
                <?= $this->language->link->statistics->browser_language ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'operatingsystems' ? 'active' : null ?>" href="<?= url('link/' . $data->link->link_id . '/statistics/operatingsystems?start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>">
                <i class="fa fa-fw fa-server mr-1"></i>
                <?= $this->language->link->statistics->os_name ?>
            </a>
        </li>
    </ul>

    <?= $this->views['statistics.method'] ?>

<?php endif ?>

<?php ob_start() ?>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/Chart.bundle.min.js' ?>"></script>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/moment.min.js' ?>"></script>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/daterangepicker.min.js' ?>"></script>

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
        redirect(`${$('#base_controller_url').val()}/statistics/<?= $data->type ?>?start_date=${start.format('YYYY-MM-DD')}&end_date=${end.format('YYYY-MM-DD')}`, true);

    });

    /* Charts */
    <?php if(count($data->logs)): ?>
    /* Charts */
    Chart.defaults.global.elements.line.borderWidth = 4;
    Chart.defaults.global.elements.point.radius = 3;
    Chart.defaults.global.elements.point.borderWidth = 7;

    let clicks_chart = document.getElementById('clicks_chart').getContext('2d');

    let gradient = clicks_chart.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(56, 178, 172, 0.6)');
    gradient.addColorStop(1, 'rgba(56, 178, 172, 0.05)');

    let gradient_white = clicks_chart.createLinearGradient(0, 0, 0, 250);
    gradient_white.addColorStop(0, 'rgba(255, 255, 255, 0.6)');
    gradient_white.addColorStop(1, 'rgba(255, 255, 255, 0.05)');

    new Chart(clicks_chart, {
        type: 'line',
        data: {
            labels: <?= $data->logs_chart['labels'] ?>,
            datasets: [{
                label: <?= json_encode($this->language->link->statistics->impressions) ?>,
                data: <?= $data->logs_chart['impressions'] ?? '[]' ?>,
                backgroundColor: gradient,
                borderColor: '#38B2AC',
                fill: true
            },
            {
                label: <?= json_encode($this->language->link->statistics->uniques) ?>,
                data: <?= $data->logs_chart['uniques'] ?? '[]' ?>,
                backgroundColor: gradient_white,
                borderColor: '#ebebeb',
                fill: true
            }]
        },
        options: {
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: (tooltipItem, data) => {
                        let value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];

                        return `${nr(value)} ${data.datasets[tooltipItem.datasetIndex].label}`;
                    }
                }
            },
            title: {
                display: true,
                text: <?= json_encode($this->language->link->statistics->clicks_chart) ?>
            },
            legend: {
                display: true
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    gridLines: {
                        display: false
                    },
                    ticks: {
                        userCallback: (value, index, values) => {
                            if (Math.floor(value) === value) {
                                return nr(value);
                            }
                        }
                    },
                    min: 0
                }],
                xAxes: [{
                    gridLines: {
                        display: false
                    }
                }]
            }
        }
    });

    <?php endif ?>
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
