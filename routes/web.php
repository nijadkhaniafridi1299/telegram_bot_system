<?php

use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::post('/', [FrontendController::class, 'enterPin']);
Route::get('/{pin}', [FrontendController::class, 'showProduct'])->name('pin');

Route::post('/{pin}/methode', [FrontendController::class, 'selectMethod']);
Route::get('/{pin}/methode', [FrontendController::class, 'showSelectMethod'])->name('select-method');

Route::post('/{pin}/bezahlen', [FrontendController::class, 'payProduct']);
Route::get('/{pin}/bezahlen', [FrontendController::class, 'showPayment'])->name('pay');

Route::post('/{pin}/bezahlt', [FrontendController::class, 'confirmPayment']);
Route::get('/{pin}/bezahlt', [FrontendController::class, 'showConfirmation'])->name('payed');
