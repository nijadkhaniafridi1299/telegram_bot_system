<?php

namespace App\TelegramCommands;

use App\Services\TelegramService;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use function app;

class HelpCommand extends UserCommand
{

    protected $name = 'help';
    protected $description = 'Show bot commands help';
    protected $usage = '/help';
    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        $text = app(TelegramService::class)->getHelpText($this->getMessage()->getChat()->getId());
        return $this->replyToChat($text, [
            'parse_mode' => 'markdown'
        ]);
    }
}
