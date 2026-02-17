<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\TelegramWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/{pin}/{paymentSessionId}/{paymentWindowId}/poll', [ApiController::class, 'poll']);

Route::post('/webhooks/telegram', [TelegramWebhookController::class, 'handlePost'])
    ->middleware(['telegram.check-ip'])
    ->name('webhooks.telegram');
