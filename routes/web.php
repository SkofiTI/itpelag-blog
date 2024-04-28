<?php

use App\Controllers\DashboardController;
use App\Controllers\LoginController;
use App\Controllers\PostController;
use App\Controllers\RegisterController;
use Framework\Http\Middleware\Authenticate;
use Framework\Http\Middleware\Guest;
use Framework\Routing\Route;

return [
    Route::get('/', [PostController::class, 'index']),
    Route::get('/posts/{id:\d+}', [PostController::class, 'show']),
    Route::get('/posts/create', [PostController::class, 'create']),
    Route::post('/posts', [PostController::class, 'store']),

    Route::get('/login', [LoginController::class, 'index'], [Guest::class]),
    Route::post('/login', [LoginController::class, 'login'], [Guest::class]),
    Route::get('/register', [RegisterController::class, 'index'], [Guest::class]),
    Route::post('/register', [RegisterController::class, 'register'], [Guest::class]),

    Route::get('/dashboard', [DashboardController::class, 'index'], [Authenticate::class]),
];
