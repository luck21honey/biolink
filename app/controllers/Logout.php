<?php

namespace Altum\Controllers;

use Altum\Middlewares\Authentication;

class Logout extends Controller {

    public function index() {

        Authentication::logout();

    }

}
