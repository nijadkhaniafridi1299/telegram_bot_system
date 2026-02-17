<?php

namespace App\TelegramCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Telegram;

class GenericMessageCommand extends SystemCommand
{

    protected $name = Telegram::GENERIC_MESSAGE_COMMAND;
    protected $description = 'Handle generic message';
    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        $chatId = $this->getMessage()->getChat()->getId();

        return new ServerResponse(['ok' => true, 'result' => true]);
    }
}
