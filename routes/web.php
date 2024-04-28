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
    Route::get('/posts/create', [PostController::class, 'create'], [Authenticate::class]),
    Route::post('/posts', [PostController::class, 'store'], [Authenticate::class]),

    Route::get('/login', [LoginController::class, 'index'], [Guest::class]),
    Route::post('/login', [LoginController::class, 'login'], [Guest::class]),
    Route::get('/register', [RegisterController::class, 'index'], [Guest::class]),
    Route::post('/register', [RegisterController::class, 'register'], [Guest::class]),
    Route::post('/logout', [LoginController::class, 'logout'], [Authenticate::class]),

    Route::get('/dashboard', [DashboardController::class, 'index'], [Authenticate::class]),
];
