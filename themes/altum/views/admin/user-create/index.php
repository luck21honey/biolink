<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3"><i class="fa fa-fw fa-xs fa-user text-primary-900 mr-2"></i> <?= $this->language->admin_user_create->header ?></h1>
</div>

<?php display_notifications() ?>

<div class="card">
    <div class="card-body">

        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <div class="form-group">
                <label><?= $this->language->admin_user_create->form->name ?></label>
                <input type="text" name="name" class="form-control form-control-lg" value="<?= $data->values['name'] ?>" placeholder="<?= $this->language->admin_user_create->form->name_placeholder ?>" required="required" />
            </div>

            <div class="form-group">
                <label><?= $this->language->admin_user_create->form->email ?></label>
                <input type="text" name="email" class="form-control form-control-lg" value="<?= $data->values['email'] ?>" placeholder="<?= $this->language->admin_user_create->form->email_placeholder ?>" required="required" />
            </div>

            <div class="form-group">
                <label><?= $this->language->admin_user_create->form->password ?></label>
                <input type="password" name="password" class="form-control form-control-lg" value="<?= $data->values['password'] ?>" placeholder="<?= $this->language->admin_user_create->form->password_placeholder ?>" required="required" />
            </div>

            <div class="mt-4">
                <button type="submit" name="submit" class="btn btn-primary"><?= $this->language->global->create ?></button>
            </div>
        </form>

    </div>
</div>

