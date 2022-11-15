<?php

use App\Http\Controllers\AnimalController;
use App\Http\Controllers\GoutteController;
use App\Http\Controllers\LotteryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('get-all-data', [GoutteController::class, 'index']);
Route::get('by-hours', [LotteryController::class, 'by_hours']);
Route::get('animals-by-hours', [AnimalController::class, 'animals_by_hours']);
Route::get('simulator', [AnimalController::class, 'simulator']);
Route::get('by-hour', [AnimalController::class, 'by_hour']);
