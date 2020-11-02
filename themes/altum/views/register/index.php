<?php defined('ALTUMCODE') || die() ?>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<div class="container">
    <div class="d-flex flex-column align-items-center">
        <div class="col-xs-12 col-sm-10 col-md-8 col-lg-5">
            <?php display_notifications() ?>

            <div class="card border-0">
                <div class="card-body p-5">
                    <h4 class="card-title"><?= $this->language->register->header ?></h4>

                    <form action="" method="post" class="mt-4" role="form">
                        <div class="form-group">
                            <label><?= $this->language->register->form->name ?></label>
                            <input type="text" name="name" class="form-control" value="<?= $data->values['name'] ?>" placeholder="<?= $this->language->register->form->name_placeholder ?>" required="required" />
                        </div>

                        <div class="form-group">
                            <label><?= $this->language->register->form->email ?></label>
                            <input type="text" name="email" class="form-control" value="<?= $data->values['email'] ?>" placeholder="<?= $this->language->register->form->email_placeholder ?>" required="required" />
                        </div>

                        <div class="form-group">
                            <label><?= $this->language->register->form->password ?></label>
                            <input type="password" name="password" class="form-control" value="<?= $data->values['password'] ?>" placeholder="<?= $this->language->register->form->password_placeholder ?>" required="required" />
                        </div>

                        <div class="form-group">
                            <?php $data->captcha->display() ?>
                        </div>

                        <div class="form-check">
                            <label class="form-check-label">
                                <input class="form-check-input" name="accept" type="checkbox" required="required">
                                <small class="text-muted">
                                    <?= sprintf(
                                        $this->language->register->form->accept,
                                        '<a href="' . $this->settings->terms_and_conditions_url . '" target="_blank">' . $this->language->global->terms_and_conditions . '</a>',
                                        '<a href="' . $this->settings->privacy_policy_url . '" target="_blank">' . $this->language->global->privacy_policy . '</a>'
                                    ) ?>
                                </small>
                            </label>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" name="submit" class="btn btn-primary btn-block"><?= $this->language->register->form->register ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <small><a href="login" class="text-muted" role="button"><?= $this->language->register->login ?></a></small>
        </div>
    </div>
</div>

<?php ob_start() ?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

