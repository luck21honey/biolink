<?php defined('ALTUMCODE') || die() ?>

<nav id="navbar" class="
    navbar
    navbar-main
    <?= \Altum\Routing\Router::$controller_settings['menu_no_margin'] ? null : 'mb-8'?>
    navbar-expand-lg
    navbar-light
    <?= \Altum\Routing\Router::$controller_key == 'index' ? 'navbar-absolute navbar-index' : null ?>
">
    <div class="container">
        <a class="navbar-brand" href="<?= url() ?>">
            <?php if($this->settings->logo != ''): ?>
                <img src="<?= SITE_URL . UPLOADS_URL_PATH . 'logo/' . $this->settings->logo ?>" class="img-fluid navbar-logo" alt="<?= $this->language->global->accessibility->logo_alt ?>" />
            <?php else: ?>
                <?= $this->settings->title ?>
            <?php endif ?>
        </a>

        <button class="btn navbar-custom-toggler d-lg-none" type="button" data-toggle="collapse" data-target="#main_navbar" aria-controls="main_navbar" aria-expanded="false" aria-label="<?= $this->language->global->accessibility->toggle_navigation ?>">
            <i class="fa fa-fw fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="main_navbar">
            <ul class="navbar-nav">

                <?php foreach($data->pages as $data): ?>
                <li class="nav-item"><a class="nav-link" href="<?= $data->url ?>" target="<?= $data->target ?>"><?= $data->title ?></a></li>
                <?php endforeach ?>


                <?php if(\Altum\Middlewares\Authentication::check()): ?>

                    <li class="nav-item"><a class="nav-link" href="<?= url('dashboard') ?>"> <?= $this->language->dashboard->menu ?></a></li>

                    <li class="dropdown">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                            <img src="<?= get_gravatar($this->user->email) ?>" class="navbar-avatar mr-1" />
                            <?= $this->user->name ?> <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <?php if(\Altum\Middlewares\Authentication::is_admin()): ?>
                                <a class="dropdown-item" href="<?= url('admin') ?>"><i class="fa fa-fw fa-sm fa-user-shield mr-1"></i> <?= $this->language->global->menu->admin ?></a>
                            <?php endif ?>
                            <a class="dropdown-item" href="<?= url('account') ?>"><i class="fa fa-fw fa-sm fa-wrench mr-1"></i> <?= $this->language->account->menu ?></a>

                            <?php if($this->settings->links->domains_is_enabled): ?>
                            <a class="dropdown-item" href="<?= url('domains') ?>"><i class="fa fa-fw fa-sm fa-globe mr-1"></i> <?= $this->language->domains->menu ?></a>
                            <?php endif ?>

                            <a class="dropdown-item" href="<?= url('account-plan') ?>"><i class="fa fa-fw fa-sm fa-box-open mr-1"></i> <?= $this->language->account_plan->menu ?></a>

                            <?php if($this->settings->payment->is_enabled): ?>
                            <a class="dropdown-item" href="<?= url('account-payments') ?>"><i class="fa fa-fw fa-sm fa-dollar-sign mr-1"></i> <?= $this->language->account_payments->menu ?></a>
                            <?php endif ?>

                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?= url('logout') ?>"><i class="fa fa-fw fa-sm fa-sign-out-alt mr-1"></i> <?= $this->language->global->menu->logout ?></a>
                        </div>
                    </li>

                <?php else: ?>

                    <li class="nav-item active"><a class="nav-link" href="<?= url('login') ?>"><i class="fa fa-fw fa-sm fa-sign-in-alt mr-1"></i> <?= $this->language->login->menu ?></a></li>

                    <?php if($this->settings->register_is_enabled): ?>
                    <li class="nav-item active"><a class="nav-link" href="<?= url('register') ?>"><i class="fa fa-fw fa-sm fa-plus mr-1"></i> <?= $this->language->register->menu ?></a></li>
                    <?php endif ?>

                <?php endif ?>

            </ul>
        </div>
    </div>
</nav>
