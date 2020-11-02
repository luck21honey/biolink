<?php defined('ALTUMCODE') || die() ?>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<div class="container">
    <div class="d-flex flex-column align-items-center">
        <div class="col-xs-12 col-sm-10 col-md-8 col-lg-5">
            <?php display_notifications() ?>

            <div class="card border-0">
                <div class="card-body p-5">
                    <h4 class="card-title"><?= $this->language->login->header ?></h4>

                    <form action="" method="post" class="mt-4" role="form">
                        <div class="form-group">
                            <label><?= $this->language->login->form->email ?></label>
                            <input type="text" name="email" class="form-control" placeholder="<?= $this->language->login->form->email_placeholder ?>" value="<?= $data->values['email'] ?>" required="required" />
                        </div>

                        <div class="form-group">
                            <label><?= $this->language->login->form->password ?></label>
                            <input type="password" name="password" class="form-control" placeholder="<?= $this->language->login->form->password_placeholder ?>" <?= $data->login_account ? 'value="' . $data->values['password'] . '"' : null ?> required="required" />
                        </div>

                        <?php if($data->login_account && $data->login_account->twofa_secret && $data->login_account->active): ?>
                            <div class="form-group">
                                <label><?= $this->language->login->form->twofa_token ?></label>
                                <input type="text" name="twofa_token" class="form-control" placeholder="<?= $this->language->login->form->twofa_token_placeholder ?>" required="required" autocomplete="off" />
                            </div>
                        <?php endif ?>

                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="rememberme">
                                <small class="text-muted"><?= $this->language->login->form->remember_me ?></small>
                            </label>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" name="submit" class="btn btn-primary btn-block my-1"><?= $this->language->login->form->login ?></button>
                        </div>

                        <div class="row">
                            <?php if($this->settings->facebook->is_enabled): ?>
                                <div class="col-sm mt-1">
                                    <a href="<?= $data->facebook_login_url ?>" class="btn btn-light btn-block"><?= sprintf($this->language->login->display->facebook, "<i class=\"fab fa-fw fa-facebook\"></i>") ?></a>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="mt-4 text-center">
                            <small><a href="lost-password" class="text-muted" role="button"><?= $this->language->login->display->lost_password ?></a> / <a href="resend-activation" class="text-muted" role="button"><?= $this->language->login->display->resend_activation ?></a></small>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php if($this->settings->register_is_enabled): ?>
            <div class="text-center mt-4">
                <?= sprintf($this->language->login->display->register, '<a href="' . url('register') . '" class="font-weight-bold">' . $this->language->login->display->register_help . '</a>') ?></a>
            </div>
        <?php endif ?>
    </div>
</div>
