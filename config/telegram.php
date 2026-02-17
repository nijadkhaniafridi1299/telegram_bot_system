<?php

return [

    'bot_token' => env('TELEGRAM_BOT_TOKEN'),
    'bot_username' => env('TELEGRAM_BOT_USERNAME'),
    'admin_telegram_ids' => explode(',', env('TELEGRAM_BOT_ADMIN_IDS', '')),

    'whitelisted_ips' => env('TELEGRAM_BOT_WHITELISTED_IPS', '149.154.160.0/20,91.108.4.0/22'),
    'webhook_url' => env('TELEGRAM_BOT_WEBHOOK_URL', env('APP_URL')),

];
