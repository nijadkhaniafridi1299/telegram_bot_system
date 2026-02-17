<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    public function handlePost(Request $request, User $shop)
    {
        app(TelegramService::class)->handleWebhook();
    }
}
