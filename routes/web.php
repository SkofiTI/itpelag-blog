<?php

use App\Controllers\PostController;
use Framework\Routing\Route;

return [
    Route::get('/', [PostController::class, 'index']),
    Route::get('/posts/{id:\d+}', [PostController::class, 'show']),
];