<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3"><i class="fa fa-fw fa-xs fa-file-alt text-primary-900 mr-2"></i> <?= $this->language->admin_page_create->header ?></h1>
</div>

<?php display_notifications() ?>

<div class="card">
    <div class="card-body">

        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <div class="row">
                <div class="col-12 col-md-4">
                    <h2 class="h4"><?= $this->language->admin_pages->main->header ?></h2>
                    <p class="text-muted"><?= $this->language->admin_pages->main->subheader ?></p>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label><?= $this->language->admin_pages->input->type ?></label>
                        <select class="form-control form-control-lg" name="type">
                            <option value="internal"><?= $this->language->admin_pages->input->type_internal ?></option>
                            <option value="external"><?= $this->language->admin_pages->input->type_external ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label id="url_label"><?= $this->language->admin_pages->input->url_internal ?></label>
                        <div class="input-group">
                            <div id="url_prepend" class="input-group-prepend">
                                <span class="input-group-text"><?= SITE_URL . 'page/' ?></span>
                            </div>

                            <input type="text" name="url" class="form-control form-control-lg" placeholder="<?= $this->language->admin_pages->input->url_internal_placeholder ?>" required="required" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?= $this->language->admin_pages->input->title ?></label>
                        <input type="text" name="title" class="form-control form-control-lg" required="required" />
                    </div>

                    <div id="description_container">
                        <div class="form-group">
                            <label><?= $this->language->admin_pages->input->content ?></label>
                            <textarea id="content" name="content" class="form-control form-control-lg"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-12 col-md-4">
                    <h2 class="h4"><?= $this->language->admin_pages->secondary->header ?></h2>
                    <p class="text-muted"><?= $this->language->admin_pages->secondary->subheader ?></p>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label><?= $this->language->admin_pages->input->description ?></label>
                        <input type="text" name="description" class="form-control form-control-lg" />
                    </div>

                    <div class="form-group">
                        <label><?= $this->language->admin_pages->input->pages_category_id ?></label>
                        <select name="pages_category_id" class="form-control form-control-lg">
                            <?php while($row = $data->pages_categories_result->fetch_object()): ?>
                                <option value="<?= $row->pages_category_id ?>"><?= $row->title ?></option>
                            <?php endwhile ?>

                            <option value=""><?= $this->language->admin_pages->input->pages_category_id_null ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= $this->language->admin_pages->input->position ?></label>
                        <select class="form-control form-control-lg" name="position">
                            <option value="bottom"><?= $this->language->admin_pages->input->position_bottom ?></option>
                            <option value="top"><?= $this->language->admin_pages->input->position_top ?></option>
                            <option value="hidden"><?= $this->language->admin_pages->input->position_hidden ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= $this->language->admin_pages->input->order ?></label>
                        <input type="number" name="order" class="form-control form-control-lg" />
                        <small class="text-muted"><?= $this->language->admin_pages->input->order_help ?></small>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12 col-md-4"></div>

                <div class="col">
                    <button type="submit" name="submit" class="btn btn-primary"><?= $this->language->global->create ?></button>
                </div>
            </div>
        </form>

    </div>
</div>

<?php ob_start() ?>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/tinymce/tinymce.min.js' ?>"></script>
<script>
    tinymce.init({
        selector: '#content',
        plugins: 'code preview fullpage autolink directionality visualblocks visualchars fullscreen image link media codesample table hr pagebreak nonbreaking toc advlist lists imagetools',
        toolbar: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent | removeformat code',
    });

    $('[name="type"]').on('change', (event) => {

        let selectedOption = $(event.currentTarget).find(':selected').attr('value');

        switch(selectedOption) {

            case 'internal':

                $('#url_label').html(<?= json_encode($this->language->admin_pages->input->url_internal) ?>);
                $('#url_prepend').show();
                $('input[name="url"]').attr('placeholder', <?= json_encode($this->language->admin_pages->input->url_internal_placeholder) ?>);
                $('#description_container').show();

                break;

            case 'external':

                $('#url_label').html(<?= json_encode($this->language->admin_pages->input->url_external) ?>);
                $('#url_prepend').hide();
                $('input[name="url"]').attr('placeholder', <?= json_encode($this->language->admin_pages->input->url_external_placeholder) ?>);
                $('#description_container').hide();

                break;
        }

    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
