<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-lg-row justify-content-between align-items-center">
    <div class="">
        <p class="text-muted">
            <?= sprintf($this->language->global->pagination->results, $data->paginator->getCurrentPageFirstItem(), $data->paginator->getCurrentPageLastItem(), $data->paginator->getTotalItems()) ?>
        </p>
    </div>

    <div class="">
        <ul class="pagination">
            <?php if ($data->paginator->getPrevUrl()): ?>
                <li class="page-item"><a href="<?= $data->paginator->getPrevUrl(); ?>" class="page-link" aria-label="<?= $this->language->global->pagination->previous ?>">‹</a></li>
            <?php endif; ?>

            <?php foreach ($data->paginator->getPages() as $page): ?>
                <?php if ($page['url']): ?>
                    <li class="page-item <?= $page['isCurrent'] ? 'active' : ''; ?>">
                        <a href="<?= $page['url']; ?>" class="page-link"><?= $page['num']; ?></a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled"><span class="page-link"><?= $page['num']; ?></span></li>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if ($data->paginator->getNextUrl()): ?>
                <li class="page-item"><a href="<?= $data->paginator->getNextUrl(); ?>" class="page-link" aria-label="<?= $this->language->global->pagination->next ?>">›</a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>


