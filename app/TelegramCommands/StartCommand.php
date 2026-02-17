<?php

namespace App\TelegramCommands;

use App\Services\TelegramService;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;

class StartCommand extends UserCommand
{

    protected $name = 'start';
    protected $description = 'Start and enable the Telegram Bot';
    protected $usage = '/start';
    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        $text = app(TelegramService::class)->getHelpText($this->getMessage()->getChat()->getId());

        return $this->replyToChat($text, [
            'parse_mode' => 'markdown'
        ]);
    }
}
