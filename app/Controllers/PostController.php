<?php

namespace App\Controllers;

use Framework\Http\Response;

class PostController extends Controller
{
    public function index()
    {
        $content = '<h1>Posts</h1>';

        return new Response($content);
    }

    public function show(int $id): Response
    {
        $content = "<h1>Post - $id</h1>";

        return new Response($content);
    }
}