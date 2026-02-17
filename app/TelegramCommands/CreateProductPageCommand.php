<?php

namespace App\TelegramCommands;

use App\Actions\CreateProductPageByUrl;
use App\Actions\GrabKlazAd;
use App\Models\User;
use App\Services\TelegramService;
use Cache;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Str;

class CreateProductPageCommand extends UserCommand
{

    protected $name = 'create';
    protected $description = 'Create a new product page';
    protected $usage = '/create';
    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        $chatId = $this->getMessage()->getChat()->getId();

        $telegramService = app(TelegramService::class);

        if (!$telegramService->isChatIdAdmin($chatId)) {
            return $this->replyToChat('You do not have permissions!');
        }

        $adUrl = trim($this->getMessage()->getText(true));

        if (empty($adUrl)) {
            return $this->replyToChat('Please add the url to this command! Please use: /create URL');
        }

        if (Str::contains($adUrl, ' ')) {
            return $this->replyToChat('There is something wrong with your ad url! Please use: /create URL');
        }

        $productPage = app(CreateProductPageByUrl::class)->createByUrl($adUrl);

        $liveUrl = route('home');
        $adminUrl = rtrim(config('app.url'), '/') . '/' . trim(config('nova.path'), '/') . '/resources/product-pages/' . $productPage->id;

        return $this->replyToChat("Product Page *{$productPage->id}* created.\n\n*PIN:* {$productPage->pin_code}\n*Url:* {$liveUrl}\n\n*Admin Url*: {$adminUrl}", [
            'parse_mode' => 'markdown'
        ]);
    }
}
