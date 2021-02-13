<?php

use App\Http\Controllers\CronController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\LogReaderController;
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

/* logs */
Route::get('admin/log-reader', [LogReaderController::class, 'getIndex']);

/* set trigger */
Route::put('/target/set', [TargetController::class, 'set'])->name('target.set');

/* main page */
Route::get('/home', [HomeController::class, 'index'])->name('home');

/* real cron script */
Route::get('/check', [CronController::class, 'check'])->name('check');

/* manually checking / transfering */
Route::get('/transfer', [ManualController::class, 'transfer'])->name('transfer');
Route::get('/balance', [ManualController::class, 'balance'])->name('balance');

Route::get('/price', [ManualController::class, 'price'])->name('price');

/*  */
Route::get('logs', [LogViewerController::class, 'index'])->name('logs');


Route::get('zDvs1dgv55csnF0xgalE25', [PublicController::class, 'index'])->name('public');
