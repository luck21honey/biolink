<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<div class="card mb-5">
    <div class="card-body">
        <h2 class="h4"><i class="fa fa-fw fa-link fa-xs text-muted"></i> <?= $this->language->admin_statistics->links->track_links->header ?></h2>
        <p class="text-muted"><?= $this->language->admin_statistics->links->track_links->subheader ?></p>

        <div class="chart-container">
            <canvas id="track_links"></canvas>
        </div>
    </div>
</div>
<?php $html = ob_get_clean() ?>

<?php ob_start() ?>
<script>
    let color = css.getPropertyValue('--primary');
    let color_gradient = null;

    /* Display chart */
    let track_links_chart = document.getElementById('track_links').getContext('2d');
    color_gradient = track_links_chart.createLinearGradient(0, 0, 0, 250);
    color_gradient.addColorStop(0, 'rgba(63, 136, 253, .1)');
    color_gradient.addColorStop(1, 'rgba(63, 136, 253, 0.025)');

    /* Display chart */
    new Chart(track_links_chart, {
        type: 'line',
        data: {
            labels: <?= $data->track_links_chart['labels'] ?>,
            datasets: [{
                label: <?= json_encode($this->language->admin_statistics->links->track_links->chart_track_links) ?>,
                data: <?= $data->track_links_chart['track_links'] ?? '[]' ?>,
                backgroundColor: color_gradient,
                borderColor: color,
                fill: true
            }]
        },
        options: chart_options
    });
</script>
<?php $javascript = ob_get_clean() ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
