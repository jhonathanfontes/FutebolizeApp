<?php

namespace App\Controllers\Web;

class HomeController extends BaseController
{
    public function index(): string
    {
        return $this->render('site/home', [
            'title' => 'Welcome to Futebolize',
            'module' => 'site',            
        ]);
    }
}
