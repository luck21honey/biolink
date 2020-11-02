<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="create_project" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= $this->language->project_create_modal->header ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <p class="text-muted modal-subheader"><?= $this->language->project_create_modal->subheader ?></p>

            <div class="modal-body">
                <form name="create_project" method="post" role="form">
                    <div class="notification-container"></div>

                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />

                    <div class="form-group">
                        <label><i class="fa fa-fw fa-signature fa-sm mr-1"></i> <?= $this->language->project_create_modal->input->name ?></label>
                        <input type="text" class="form-control" name="name" required="required" />
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= $this->language->global->create ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    $('form[name="create_project"]').on('submit', event => {

        $.ajax({
            type: 'POST',
            url: 'project-ajax',
            data: $(event.currentTarget).serialize(),
            success: (data) => {
                if (data.status == 'error') {
                    let notification_container = $(event.currentTarget).find('.notification-container');

                    notification_container.html('');

                    display_notifications(data.message, 'error', notification_container);
                }

                else if(data.status == 'success') {

                    /* Hide modal */
                    $('#create_project').modal('hide');

                    /* Clear input values */
                    $('form[name="create_project"] input').val('');

                    /* Fade out refresh */
                    redirect(data.details.url, true);

                }
            },
            dataType: 'json'
        });

        event.preventDefault();
    })
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
