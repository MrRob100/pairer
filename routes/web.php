<?php

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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/balance/{coin}',[App\Http\Controllers\HomeController::class, 'balance'])->name('balance');
Route::get('/price/{ticker}',[App\Http\Controllers\HomeController::class, 'price'])->name('price');

/* maybe needs to be API resource route */
Route::post('/addtrigger',[App\Http\Controllers\HomeController::class, 'addtrigger'])->name('addtrigger');
Route::get('/triggers', [App\Http\Controllers\HomeController::class, 'triggers'])->name('triggers');
Route::delete('/trigger/{id}',[App\Http\Controllers\HomeController::class, 'delete'])->name('delete');

/* cron script */
Route::get('/cron', [App\Http\Controllers\CronController::class, 'run'])->name('run');