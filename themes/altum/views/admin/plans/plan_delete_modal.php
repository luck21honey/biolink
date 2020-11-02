<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="plan_delete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-fw fa-sm fa-trash-alt text-primary-900 mr-2"></i>
                    <?= $this->language->admin_plan_delete_modal->header ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= $this->language->global->close ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p class="text-muted"><?= $this->language->admin_plan_delete_modal->subheader ?></p>

                <div class="mt-4">
                    <a href="" id="plan_delete_url" class="btn btn-lg btn-block btn-danger"><?= $this->language->global->delete ?></a>
                </div>
            </div>

        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    /* On modal show load new data */
    $('#plan_delete').on('show.bs.modal', event => {
        let plan_id = $(event.relatedTarget).data('plan-id');

        $(event.currentTarget).find('#plan_delete_url').attr('href', `${url}admin/plans/delete/${plan_id}&global_token=${global_token}`);
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
