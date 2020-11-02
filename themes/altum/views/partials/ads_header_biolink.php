<?php
use Altum\Middlewares\Authentication;

if(
    !empty($this->settings->ads->header_biolink)
    && !$data->user->plan_settings->no_ads
): ?>
    <div class="container my-3"><?= $this->settings->ads->header_biolink ?></div>
<?php endif ?>
