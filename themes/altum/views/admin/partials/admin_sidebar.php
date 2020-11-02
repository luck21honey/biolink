<?php defined('ALTUMCODE') || die() ?>

<section class="admin-sidebar">
    <div class="admin-sidebar-title">
        <a href="<?= url() ?>" class="text-decoration-none">
            <?php if($this->settings->logo != ''): ?>
                <img src="<?= SITE_URL . UPLOADS_URL_PATH . 'logo/' . $this->settings->logo ?>" class="img-fluid admin-navbar-logo" alt="<?= $this->language->global->accessibility->logo_alt ?>" />
            <?php else: ?>
                <span class="admin-navbar-brand"><?= $this->settings->title ?></span>
            <?php endif ?>
        </a>
    </div>

    <ul class="admin-sidebar-links">
        <li class="<?= \Altum\Routing\Router::$controller == 'AdminIndex' ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-tv"></i></div>
                <div class="col">
                    <span class="d-inline"><?= $this->language->admin_index->menu ?></span>
                </div>
            </a>
        </li>

        <li class="<?= in_array(\Altum\Routing\Router::$controller, ['AdminUsers', 'AdminUserUpdate', 'AdminUserCreate', 'AdminUserView']) ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/users') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-users"></i></div>
                <div class="col">
                    <span class="d-inline"><?= $this->language->admin_users->menu ?></span>
                </div>
            </a>
        </li>

        <li class="<?= in_array(\Altum\Routing\Router::$controller, ['AdminLinks']) ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/links') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-link"></i></div>
                <div class="col">
                    <span class="d-inline"><?= $this->language->admin_links->menu ?></span>
                </div>
            </a>
        </li>

        <li class="<?= in_array(\Altum\Routing\Router::$controller, ['AdminDomains', 'AdminDomainCreate', 'AdminDomainUpdate']) ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/domains') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-globe"></i></div>
                <div class="col">
                    <span class="d-inline"><?= $this->language->admin_domains->menu ?></span>
                </div>
            </a>
        </li>

        <li class="<?= in_array(\Altum\Routing\Router::$controller, ['AdminPages', 'AdminPageCreate', 'AdminPageUpdate']) ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/pages') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-file-alt"></i></div>
                <div class="col">
                    <span class="d-inline"><?= $this->language->admin_pages->menu ?></span>
                </div>
            </a>
        </li>

        <li class="<?= in_array(\Altum\Routing\Router::$controller, ['AdminPlans', 'AdminPlanCreate', 'AdminPlanUpdate']) ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/plans') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-box-open"></i></div>
                <div class="col">
                    <span class="d-inline"><?= $this->language->admin_plans->menu ?></span>
                </div>
            </a>
        </li>

        <?php if(in_array($this->settings->license->type, ['SPECIAL','Extended License'])): ?>
        <li class="<?= in_array(\Altum\Routing\Router::$controller, ['AdminCodes', 'AdminCodeCreate', 'AdminCodeUpdate']) ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/codes') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-tags"></i></div>
                <div class="col">
                    <span class="d-inline"><?= $this->language->admin_codes->menu ?></span>
                </div>
            </a>
        </li>

        <li class="<?= in_array(\Altum\Routing\Router::$controller, ['AdminTaxes', 'AdminTaxCreate', 'AdminTaxUpdate']) ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/taxes') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-receipt"></i></div>
                <div class="col">
                    <span class="d-inline"><?= $this->language->admin_taxes->menu ?></span>
                </div>
            </a>
        </li>

        <li class="<?= \Altum\Routing\Router::$controller == 'AdminPayments' ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/payments') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-dollar-sign"></i></div>
                <div class="col">
                    <span class="d-inline"><?= $this->language->admin_payments->menu ?></span>
                </div>
            </a>
        </li>
        <?php endif ?>

        <li class="<?= \Altum\Routing\Router::$controller == 'AdminStatistics' ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/statistics') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-chart-line"></i></div>
                <div class="col">
                    <span class="d-inline"><?= $this->language->admin_statistics->menu ?></span>
                </div>
            </a>
        </li>

        <li class="<?= \Altum\Routing\Router::$controller == 'AdminSettings' ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/settings') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-wrench"></i></div>
                <div class="col">
                    <span class="d-inline"><?= $this->language->admin_settings->menu ?></span>
                </div>
            </a>
        </li>
    </ul>

    <hr />

    <ul class="admin-sidebar-links">
        <li>
            <a class="nav-link d-flex flex-row" target="_blank" href="<?= url('dashboard') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-home"></i></div>
                <div class="col">
                    <span class="d-inline"><?= $this->language->global->menu->website ?></span>
                </div>
            </a>
        </li>

        <li class="dropdown">
            <a class="nav-link d-flex flex-row dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                <div class="col-1 d-flex align-items-center"><img src="<?= get_gravatar($this->user->email) ?>" class="admin-avatar" /></div>
                <div class="col">
                    <span class="d-inline"><?= $this->user->name ?></span>
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="<?= url('account') ?>"><i class="fa fa-fw fa-sm fa-sm fa-wrench mr-1"></i> <?= $this->language->account->menu ?></a>
                <a class="dropdown-item" href="<?= url('logout') ?>"><i class="fa fa-fw fa-sm fa-sm fa-sign-out-alt mr-1"></i> <?= $this->language->global->menu->logout ?></a>
            </div>
        </li>
    </ul>
</section>
