<?php
use Altum\Middlewares\Authentication;

if(
    !empty($this->settings->ads->footer)
    && (
        !Authentication::check() ||
        (Authentication::check() && !$this->user->plan_settings->no_ads)
    )
    && !\Altum\Routing\Router::$controller_settings['no_ads']
): ?>
    <div class="container my-3"><?= $this->settings->ads->footer ?></div>
<?php endif ?>
