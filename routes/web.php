<?php

use App\Http\Controllers\ChartController;
use App\Http\Controllers\CronController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ManualController;
use App\Http\Controllers\OngController;
use App\Http\Controllers\PairsController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\RandomizeController;
use App\Http\Controllers\TargetController;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

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

/* chart data */
Route::get('/chart', [ChartController::class, 'data'])->name('chart.data');

/* Download csv */
Route::get('/download', [DownloadController::class, 'download'])->name('download');

/* chart page */
Route::get('/pair', [ChartController::class, 'pair'])->name('pair');

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

/* logs */
Route::get('logs', [LogViewerController::class, 'index'])->name('logs')->middleware('auth');

/* pairs */
Route::get('pairs', [PairsController::class, 'index'])->name('saved.pairs');
Route::post('pairs', [PairsController::class, 'create'])->name('create.pair');
Route::post('pairs/delete', [PairsController::class, 'delete'])->name('delete.pair');

/* balrecord */
Route::get('/brecord', [ManualController::class, 'brecord'])->name('brecord');

/* balpairrecord */
Route::get('/get_pair_data', [ManualController::class, 'getPairData'])->name('getPairData');

Route::get('/ong', [OngController::class, 'get']);

/* public 3 charts */
Route::get('zDvs1dgv55csnF0xgalE25', [PublicController::class, 'index'])->name('public');

Route::get('/randomize', [RandomizeController::class, 'randomPair'])->name('randomize');
Route::post('/dudpair', [RandomizeController::class, 'trash'])->name('trash');

Route::get('/latestprices', [ChartController::class, 'latestPrices']);
