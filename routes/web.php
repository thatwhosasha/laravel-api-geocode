<?php

use App\Http\Controllers\GeocodeController;

use Illuminate\Support\Facades\Route;

Route::get('/', [GeocodeController::class, 'index'])->name('geocode.index');
Route::post('/search', [GeocodeController::class, 'search'])->name('geocode.search');
