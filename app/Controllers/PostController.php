<?php

namespace App\Controllers;

use Framework\Controller\AbstractController;
use Framework\Http\Response;

class PostController extends AbstractController
{
    public function index()
    {
        $twig = $this->container->get('twig');
        
        $content = '<h1>Posts</h1>';

        return new Response($content);
    }

    public function show(int $id): Response
    {
        $content = "<h1>Post - $id</h1>";

        return new Response($content);
    }
}