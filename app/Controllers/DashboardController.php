<?php

namespace App\Controllers;

use Framework\Controller\AbstractController;

class DashboardController extends AbstractController
{
    public function index()
    {
        return $this->render('dashboard.html.twig');
    }
}
