<?php

use App\Http\Controllers\TaxSettlementController;
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
})->name('welcome');

Route::post('/show', [TaxSettlementController::class, 'show'])->name('send');
Route::post('/generateCSV', [TaxSettlementController::class, 'generateCSVFile'])->name('generateCSV');
Route::post('/add_sales', [TaxSettlementController::class, 'showAddSalesPage'])->name('add_sales');
Route::post('/add_purchases', [TaxSettlementController::class, 'showAddPurchasesPage'])->name('add_purchases');

//Route::get('/{any}', function () {
//    return redirect('/');
//})->where('any', '.*');
