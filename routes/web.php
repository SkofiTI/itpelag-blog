<?php

use App\Controllers\PostController;
use Framework\Routing\Route;

return [
    Route::get('/', [PostController::class, 'index']),
    Route::get('/posts/{id:\d+}', [PostController::class, 'show']),
    Route::get('/posts/create', [PostController::class, 'create']),
    Route::get('/login', [PostController::class, 'login']),
    Route::get('/registration', [PostController::class, 'registration']),
];