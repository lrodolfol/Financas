<?php

namespace App\Controllers;

class _404Controller extends Controller {

    public function index(){
        $this->render("home/notFound");
    }

}
