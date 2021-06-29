<?php

use App\Http\Controllers\CSVFileController;
use App\Http\Controllers\TaxSettlementController;
use App\Http\Controllers\XMLFileController;
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

Route::get('/', [TaxSettlementController::class, 'showWelcomePage'])->name('welcome');
Route::get('/add_files', [TaxSettlementController::class, 'showAddFilesPage'])->name('show_add_files_page');

Route::post('/add_sales', [TaxSettlementController::class, 'showAddSalesPage'])->name('add_sales');
Route::get('/add_sales', [TaxSettlementController::class, 'addSales'])->name('add_sales_page');
Route::post('/add_purchases', [TaxSettlementController::class, 'showAddPurchasesPage'])->name('add_purchases');
Route::get('/add_purchases', [TaxSettlementController::class, 'addPurchases '])->name('add_purchases');
Route::post('/generate', [TaxSettlementController::class, 'generateFile'])->name('generateFile');
Route::post('/show', [TaxSettlementController::class, 'addInvoices'])->name('send');
Route::get('/show', [TaxSettlementController::class, 'show'])->name('send');
Route::post('/summary', [TaxSettlementController::class, 'showSummaryPage'])->name('summary');

//Route::get('/{any}', function () {
//    return redirect('/');
//})->where('any', '.*');
