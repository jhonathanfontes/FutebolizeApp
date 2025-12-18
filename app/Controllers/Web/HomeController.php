<?php

namespace App\Controllers\Web;

class HomeController extends BaseController
{
    public function index(): string
    {
        return $this->render('home/home.html.twig', [
            'title' => 'Welcome to Futebolize',
            
        ]);
    }
}
