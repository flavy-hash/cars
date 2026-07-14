<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\FavoriteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CarController::class, 'home'])->name('home');
Route::get('/listings', [CarController::class, 'index'])->name('cars.index');
Route::get('/favorites', [CarController::class, 'favorites'])->name('favorites.index');
Route::post('/favorites/{car}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
Route::get('/cars/{car}', [CarController::class, 'show'])->name('cars.show');
