<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleAndPermissionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'doLogin']);

    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'doRegister']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    Route::get('/change-password', [AuthController::class, 'changePassword'])->name('change-password');
    Route::put('/change-password', [AuthController::class, 'doChangePassword']);

    // user manajemen
    Route::get('/permissions/refresh', [RoleAndPermissionController::class, 'refreshPermission'])->name('permissions.refresh');
    Route::resource('roles', RoleAndPermissionController::class);
    Route::resource('users', UserController::class);
});
