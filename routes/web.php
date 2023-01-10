<?php

use App\Http\Controllers\AllegroSalesStatementFileController;
use App\Http\Controllers\CorrectiveInvoiceController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\SummaryController;
use App\Http\Controllers\UndocumentedSalesController;
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

Route::get('/', [MainController::class, 'showWelcomePage'])->name('welcome');
Route::get('/add_files', [FormController::class, 'index'])->name('show_add_files_page');
Route::post('/add_files', [FormController::class, 'create'])->name('send');

Route::get('/show_sale_invoice_list', [MainController::class, 'index'])->name('show_sale_invoices');

Route::get('/add_correction_invoice', [CorrectiveInvoiceController::class, 'index'])->name('show_correction_invoice_form');
Route::post('/add_correction_invoice', [CorrectiveInvoiceController::class, 'create'])->name('add_correction_invoice');

Route::get('/add_sales', [AllegroSalesStatementFileController::class, 'index'])->name('add_sales_page');
Route::post('/add_sales', [AllegroSalesStatementFileController::class, 'create'])->name('add_sales');

Route::get('/add_sales_form', [UndocumentedSalesController::class, 'index'])->name('show_undocumented_sales_form');
Route::post('/add_sales_form', [UndocumentedSalesController::class, 'create'])->name('add_undocumented_sales');

Route::get('/add_purchases', [PurchaseInvoiceController::class, 'index'])->name('show_purchases');
Route::post('/add_purchases', [PurchaseInvoiceController::class, 'create'])->name('add_purchases');

Route::get('/summary', [SummaryController::class, 'index'])->name('summary');
//Route::post('/summary', [MainController::class, 'index'])->name('show_summary');

Route::post('/generate', [MainController::class, 'generateFile'])->name('generateFile');

//Route::get('/{any}', function () {
//    return redirect('/');
//})->where('any', '.*');
