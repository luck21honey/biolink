<?php defined('ALTUMCODE') || die() ?>

<header class="header pb-0">
    <div class="container">
        <?= $this->views['account_header'] ?>
    </div>
</header>

<section class="container pt-5">

    <?php display_notifications() ?>

    <h2 class="h4"><?= $this->language->account_delete->header ?></h2>
    <p class="text-muted"><?= $this->language->account_delete->subheader ?></p>

    <form action="" method="post" role="form">
        <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

        <div class="form-group">
            <label for="current_password"><?= $this->language->account_delete->current_password ?></label>
            <input type="password" id="current_password" name="current_password" class="form-control" />
        </div>

        <button type="submit" name="submit" class="btn btn-block btn-secondary"><?= $this->language->global->delete ?></button>
    </form>

</section>
