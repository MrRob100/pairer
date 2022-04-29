<?php

use App\Http\Controllers\ChartController;
use App\Http\Controllers\CronController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InputController;
use App\Http\Controllers\ManualController;
use App\Http\Controllers\OngController;
use App\Http\Controllers\PairsController;
use App\Http\Controllers\RandomizeController;
use App\Http\Controllers\ResultsController;
use App\Http\Controllers\TargetController;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;
use Carbon\Carbon;

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

/* main page */
Route::get('/', [HomeController::class, 'index'])->name('home')->middleware(['pairs']);

/* chart data */
Route::get('/chart', [ChartController::class, 'data'])->name('chart.data')->middleware(['pairs']);

/* Download csv */
Route::get('/download', [DownloadController::class, 'download'])->name('download')->middleware(['pairs']);

/* chart page */
Route::get('/pair', [ChartController::class, 'pair'])->name('pair')->middleware(['pairs']);

/* set trigger */
Route::put('/target/set', [TargetController::class, 'set'])->name('target.set')->middleware(['pairs']);

/* Results */
Route::get('/results', [HomeController::class, 'results'])->middleware(['pairs']);
Route::get('/results/data', [ResultsController::class, 'index'])->middleware(['pairs']);

/* real cron script */
Route::get('/check', [CronController::class, 'check'])->name('check')->middleware(['pairs']);

/* manually checking / transfering */
Route::get('/transfer', [ManualController::class, 'transfer'])->name('transfer')->middleware(['pairs']);
Route::get('/balance', [ManualController::class, 'balance'])->name('balance')->middleware(['pairs']);

Route::get('/price', [ManualController::class, 'price'])->name('price')->middleware(['pairs']);

/* logs */
Route::get('logs', [LogViewerController::class, 'index'])->name('logs')->middleware(['pairs']);


/* pairs */
Route::get('pairs', [PairsController::class, 'index'])->name('saved.pairs')->middleware(['pairs']);
Route::post('pairs', [PairsController::class, 'create'])->name('create.pair')->middleware(['pairs']);
Route::post('pairs/delete', [PairsController::class, 'delete'])->name('delete.pair')->middleware(['pairs']);
Route::get('sync', [PairsController::class, 'sync'])->middleware(['pairs']);

/* balrecord */
Route::get('/brecord', [ManualController::class, 'brecord'])->name('brecord')->middleware(['pairs']);

/* balpairrecord */
Route::get('/get_pair_data', [ManualController::class, 'getPairData'])->name('getPairData')->middleware(['pairs']);

Route::get('/ong', [OngController::class, 'get'])->middleware(['pairs']);

Route::get('/randomize', [RandomizeController::class, 'randomPair'])->name('randomize')->middleware(['pairs']);
Route::post('/dudpair', [RandomizeController::class, 'trash'])->name('trash')->middleware(['pairs']);

Route::get('/latestprices', [ChartController::class, 'latestPrices'])->middleware(['pairs']);

Route::post('/input', [InputController::class, 'create'])->name('inputs.create')->middleware(['pairs']);

Route::post('/shave', [ManualController::class, 'shave'])->name('shave')->middleware(['pairs']);

Route::post('/pump', [ManualController::class, 'pump'])->name('pump')->middleware(['pairs']);

Route::get('/icon', [ManualController::class, 'icon'])->name('icon')->middleware(['pairs']);

Route::get('/limit_orders', [ManualController::class, 'getLimits']);

Route::post('/limit_buy', [ManualController::class, 'limitBuy']);

Route::post('/stop_limit_sell', [ManualController::class, 'stoplimitSell']);

Route::post('/cancel_orders', [ManualController::class, 'cancelOrders']);

Route::get('/lot_size', [ManualController::class, 'getLotSize']);

Route::get('/time', function() {
    $hereLastYear = 69;

    $ninteyDaysTotalDate = Carbon::parse('2022-02-28')->addDays((90 - 69));

    dump('90 days total took me to \/');
    dump($ninteyDaysTotalDate->format('Y-m-d'));

    dump('further 90 days extension will take me to \/');
    dump($ninteyDaysTotalDate->addDays(90)->format('Y-m-d'));

    $freshNintyTillDate = Carbon::parse('2022-02-28')->addDays(90);

    dump('fresh 90 from second arrival takes me until \/');
    dump($freshNintyTillDate->format('Y-m-d'));

    dump('a futerh 90 from fresh 90 takes me until \/');
    dump($freshNintyTillDate->addDays(90)->format('Y-m-d'));


//    $ninteyDaysSecondTime =
});

Auth::routes();
