<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Ruta para procesar el Login (POST desde el Modal)
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('guest')
    ->name('login');

// Ruta para procesar el Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('guest')
    ->name('register');