<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<div class="card">
    <div class="card-body">
        <h2 class="h4"><i class="fa fa-fw fa-dollar-sign fa-xs text-muted"></i> <?= $this->language->admin_statistics->payments->header ?></h2>

        <div class="chart-container">
            <canvas id="payments"></canvas>
        </div>
    </div>
</div>
<?php $html = ob_get_clean() ?>

<?php ob_start() ?>
<script>
    'use strict';

    let total_payments_color = css.getPropertyValue('--gray-500');
    let total_amount_color = css.getPropertyValue('--primary');

    /* Display chart */
    let payments_chart = document.getElementById('payments').getContext('2d');

    let total_amount_color_gradient = payments_chart.createLinearGradient(0, 0, 0, 250);
    total_amount_color_gradient.addColorStop(0, 'rgba(63, 136, 253, .1)');
    total_amount_color_gradient.addColorStop(1, 'rgba(63, 136, 253, 0.025)')

    let total_payments_color_gradient = payments_chart.createLinearGradient(0, 0, 0, 250);
    total_payments_color_gradient.addColorStop(0, 'rgba(160, 174, 192, .1)');
    total_payments_color_gradient.addColorStop(1, 'rgba(160, 174, 192, 0.025)')

    new Chart(payments_chart, {
        type: 'line',
        data: {
            labels: <?= $data->payments_chart['labels'] ?>,
            datasets: [
                {
                    label: <?= json_encode($this->language->admin_statistics->payments->chart_total_payments) ?>,
                    data: <?= $data->payments_chart['total_payments'] ?? '[]' ?>,
                    backgroundColor: total_payments_color_gradient,
                    borderColor: total_payments_color,
                    fill: true
                },
                {
                    label: <?= json_encode($this->language->admin_statistics->payments->chart_total_amount) ?>,
                    data: <?= $data->payments_chart['total_amount'] ?? '[]' ?>,
                    backgroundColor: total_amount_color_gradient,
                    borderColor: total_amount_color,
                    fill: true
                }
            ]
        },
        options: chart_options
    });
</script>
<?php $javascript = ob_get_clean() ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
