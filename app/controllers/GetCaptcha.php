<?php

namespace Altum\Controllers;

class GetCaptcha extends Controller {

    public function index() {

        (new \Altum\Captcha())->create_simple_captcha();

    }

}
