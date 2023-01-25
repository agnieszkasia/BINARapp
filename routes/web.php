<?php

use App\Http\Controllers\AllegroSalesStatementFileController;
use App\Http\Controllers\CorrectiveInvoiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\SaleInvoiceController;
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

Route::get('/user', [UserController::class, 'index'])->name('create_user');
Route::post('/user/add', [UserController::class, 'store'])->name('store_user');

Route::get('/sale_invoices', [SaleInvoiceController::class, 'index'])->name('show_sale_invoices');
Route::post('/sale_invoices', [SaleInvoiceController::class, 'store'])->name('add_sale_invoices');
Route::get('/sale_invoices/add', [SaleInvoiceController::class, 'create'])->name('create_sale_invoice_files');

Route::get('/correction_invoice/create', [CorrectiveInvoiceController::class, 'create'])->name('create_correction_invoice');
Route::post('/correction_invoice/add', [CorrectiveInvoiceController::class, 'store'])->name('add_correction_invoice');

Route::get('/allegro_sales', [AllegroSalesStatementFileController::class, 'create'])->name('create_allegro_sales');
Route::post('/allegro_sales/import', [AllegroSalesStatementFileController::class, 'store'])->name('import_allegro_sales');

Route::get('/undocumented_sales', [UndocumentedSalesController::class, 'index'])->name('show_undocumented_sales');
Route::post('/undocumented_sales/add', [UndocumentedSalesController::class, 'create'])->name('add_undocumented_sales');

Route::get('/purchase_invoices', [PurchaseInvoiceController::class, 'index'])->name('show_purchase_invoices');
Route::post('/purchase_invoices/add', [PurchaseInvoiceController::class, 'create'])->name('add_purchase_invoices');

Route::get('/summary', [SummaryController::class, 'index'])->name('summary');

Route::post('/generate', [MainController::class, 'generateFile'])->name('generateFile');

//Route::get('/{any}', function () {
//    return redirect('/');
//})->where('any', '.*');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
