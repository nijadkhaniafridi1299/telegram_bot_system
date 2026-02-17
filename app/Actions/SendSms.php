<?php

namespace App\Actions;

use App\Models\VonageAccount;
use Str;
use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;
use function trim;

class SendSms
{

    public function send(VonageAccount $account, array $numbers, string $from, string $message)
    {
        $basic = new Basic($account->api_key, $account->api_secret);
        $client = new Client($basic);

        $remainingBalance = null;
        $successCount = 0;
        $failureCount = 0;

        foreach ($numbers as $to) {
            $to = $this->formatNumber($to);
            if (empty($to)) continue;

            $result = $client->sms()->send(
                new SMS($to, $from, $message)
            )->current();

            if ($result->getStatus() == 0) {
                $successCount++;
            } else {
                $failureCount++;
            }

            $remainingBalance = $result->getRemainingBalance();
        }

        if ($remainingBalance != null) {
            $account->remaining_balance = $remainingBalance;
        }

        $account->sms_sent = $account->sms_sent + $successCount;
        $account->sms_failures = $account->sms_failures + $failureCount;
        $account->save();
    }

    private function formatNumber($number): ?string
    {
        $to = trim($number);
        if ($to == '') return null;
        $to = Str::replace(' ', '', $to);

        if (Str::startsWith($to, '00')) {
            $to = '+' . Str::substr($to, 2);
        }
        if (!Str::startsWith($to, '+49') && !Str::startsWith($to, '+') && !Str::startsWith($to, '49')) {
            if (Str::startsWith($to, '0')) {
                $to = Str::substr($to, 1);
            }

            $to = '+49' . $to;
        }
        if (Str::startsWith($to, '+')) {
            $to = Str::substr($to, 1);
        }

        return $to;
    }

}
