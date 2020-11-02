<?php defined('ALTUMCODE') || die() ?>

<body class="link-body">
    <div class="container animated fadeIn">
        <div class="row justify-content-center mt-5 mt-lg-10">
            <div class="col-md-8">

                <div class="mb-4 d-flex">
                    <div class="text-center">
                        <h1 class="h3 mb-4"><?= $this->language->link->password->header  ?></h1>
                        <span class="text-muted">
                            <?= $this->language->link->password->subheader ?>
                        </span>
                    </div>
                </div>

                <?php display_notifications(false) ?>

                <form action="" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />
                    <input type="hidden" name="type" value="password" />

                    <div class="form-group">
                        <label for="password"><?= $this->language->link->password->input ?></label>
                        <input type="password" id="password" name="password" value="" class="form-control" required="required" />
                    </div>

                    <button type="submit" name="submit" class="btn btn-block btn-primary mt-4"><?= $this->language->global->submit ?></button>
                </form>

            </div>
        </div>
    </div>
</body>


