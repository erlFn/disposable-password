<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn() => Inertia::render('welcome'))->name('welcome');
Route::post('/validate', [AuthController::class, 'validate'])->name('auth.validate');
Route::get('/verification/{token}', [AuthController::class, 'verification'])->name('auth.verification');
Route::post('/verify/{token}', [AuthController::class, 'verify'])->name('auth.verify');

Route::get('/dashboard/{token}', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/dashboard/{token}', [DashboardController::class, 'destroy'])->name('dashboard.logout');