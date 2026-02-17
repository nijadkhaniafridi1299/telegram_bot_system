<?php

namespace App\Jobs;

use App\Actions\SendSms;
use App\Models\VonageAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use function app;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private VonageAccount $account,
        private array         $numbers,
        private string        $from,
        private string        $message
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        app(SendSms::class)->send($this->account, $this->numbers, $this->from, $this->message);
    }
}
