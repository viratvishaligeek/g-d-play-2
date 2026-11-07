<?php

use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::get('/chart', [FrontendController::class, 'chartsIndex'])->name('charts.index');
Route::get('/result/{slug}', [FrontendController::class, 'market'])->where('slug', '[A-Za-z0-9-]+')->name('market');
Route::get('/{slug}', [FrontendController::class, 'page'])->where('slug', '[A-Za-z0-9-]+')->name('page');
Route::post('/get_result', [FrontendController::class, 'chart'])->name('chart');
