<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>

<div class="card">
    <div class="card-body">

        <form name="update_link" action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />
            <input type="hidden" name="request_type" value="update" />
            <input type="hidden" name="type" value="link" />
            <input type="hidden" name="link_id" value="<?= $data->link->link_id ?>" />

            <div class="notification-container"></div>

            <div class="form-group">
                <label><i class="fa fa-fw fa-signature fa-sm mr-1"></i> <?= $this->language->link->settings->location_url ?></label>
                <input type="text" class="form-control" name="location_url" value="<?= $data->link->location_url ?>" required="required" placeholder="<?= $this->language->link->settings->location_url_placeholder ?>" />
            </div>

            <div class="form-group">
                <label><i class="fa fa-fw fa-link fa-sm mr-1"></i> <?= $this->language->link->settings->url ?></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <?php if(count($data->domains)): ?>
                            <select name="domain_id" class="appearance-none select-custom-altum form-control input-group-text">
                                <?php if($this->settings->links->main_domain_is_enabled || \Altum\Middlewares\Authentication::is_admin()): ?>
                                    <option value="" <?= $data->link->domain ? 'selected="selected"' : null ?>><?= url() ?></option>
                                <?php endif ?>

                                <?php foreach($data->domains as $row): ?>
                                    <option value="<?= $row->domain_id ?>" <?= $data->link->domain && $row->domain_id == $data->link->domain->domain_id ? 'selected="selected"' : null ?>><?= $row->url ?></option>
                                <?php endforeach ?>
                            </select>
                        <?php else: ?>
                            <span class="input-group-text"><?= url() ?></span>
                        <?php endif ?>
                    </div>
                    <input type="text" class="form-control" name="url" placeholder="<?= $this->language->link->settings->url_placeholder ?>" value="<?= $data->link->url ?>" <?= !$this->user->plan_settings->custom_url ? 'readonly="readonly"' : null ?> />
                </div>
                <small class="text-muted"><?= $this->language->link->settings->url_help ?></small>
            </div>

            <div class="custom-control custom-switch mb-3">
                <input id="schedule" name="schedule" type="checkbox" class="custom-control-input" <?= !empty($data->link->start_date) && !empty($data->link->end_date) ? 'checked="checked"' : null ?> <?= !$this->user->plan_settings->scheduling ? 'disabled="disabled"': null ?>>
                <label class="custom-control-label" for="schedule"><?= $this->language->link->settings->schedule ?></label>
                <small class="form-text text-muted"><?= $this->language->link->settings->schedule_help ?></small>
            </div>

            <div id="schedule_container" class="row <?= !$this->user->plan_settings->scheduling ? 'container-disabled': null ?>" style="display: none;">
                <div class="col">
                    <div class="form-group">
                        <label><i class="fa fa-fw fa-clock fa-sm mr-1"></i> <?= $this->language->link->settings->start_date ?></label>
                        <input
                                type="text"
                                class="form-control"
                                name="start_date"
                                value="<?= $data->link->start_date ?>"
                                placeholder="<?= $this->language->link->settings->start_date ?>"
                                autocomplete="off"
                        >
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label><i class="fa fa-fw fa-clock fa-sm mr-1"></i> <?= $this->language->link->settings->end_date ?></label>
                        <input
                                type="text"
                                class="form-control"
                                name="end_date"
                                value="<?= $data->link->end_date ?>"
                                placeholder="<?= $this->language->link->settings->end_date ?>"
                                autocomplete="off"
                        >
                    </div>
                </div>
            </div>

            <div class="form-group" <?= !$this->user->plan_settings->password ? 'container-disabled': null ?>">
                <label for="password"><i class="fa fa-fw fa-key fa-sm mr-1"></i> <?= $this->language->link->settings->password ?></label>
                <input id="password" type="password" class="form-control" name="password" value="<?= $data->link->settings->password ?>" <?= !$this->user->plan_settings->password ? 'disabled="disabled"': null ?> />
                <small class="text-muted"><?= $this->language->link->settings->password_help ?></small>
            </div>

            <div class="custom-control custom-switch mr-3 mb-3 <?= !$this->user->plan_settings->sensitive_content ? 'container-disabled': null ?>">
                <input
                        type="checkbox"
                        class="custom-control-input"
                        id="sensitive_content"
                        name="sensitive_content"
                    <?= !$this->user->plan_settings->sensitive_content ? 'disabled="disabled"': null ?>
                    <?= $data->link->settings->sensitive_content ? 'checked="checked"' : null ?>
                >
                <label class="custom-control-label clickable" for="sensitive_content"><?= $this->language->link->settings->sensitive_content ?></label>
                <small class="form-text text-muted"><?= $this->language->link->settings->sensitive_content_help ?></small>
            </div>

            <div class="mt-4">
                <button type="submit" name="submit" class="btn btn-block btn-primary"><?= $this->language->global->update ?></button>
            </div>
        </form>

    </div>
</div>

<?php $html = ob_get_clean() ?>


<?php ob_start() ?>
<script>
    /* Settings Tab */
    let schedule_handler = () => {
        if($('#schedule').is(':checked')) {
            $('#schedule_container').show();
        } else {
            $('#schedule_container').hide();
        }
    };

    $('#schedule').on('change', schedule_handler);

    schedule_handler();

    /* Daterangepicker */
    $('[name="start_date"],[name="end_date"]').daterangepicker({
        minDate: new Date(),
        alwaysShowCalendars: true,
        singleCalendar: true,
        singleDatePicker: true,
        locale: <?= json_encode(require APP_PATH . 'includes/daterangepicker_translations.php') ?>,
    }, (start, end, label) => {
    });

    /* Form handling */
    $('form[name="update_link"]').on('submit', event => {
        let form = $(event.currentTarget)[0];
        let data = new FormData(form);
        let notification_container = $(event.currentTarget).find('.notification-container');

        $.ajax({
            type: 'POST',
            processData: false,
            contentType: false,
            cache: false,
            url: 'link-ajax',
            data: data,
            success: (data) => {
                display_notifications(data.message, data.status, notification_container);

                notification_container[0].scrollIntoView();
            },
            dataType: 'json'
        });

        event.preventDefault();
    })
</script>
<?php $javascript = ob_get_clean() ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
