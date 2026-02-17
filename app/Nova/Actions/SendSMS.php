<?php

namespace App\Nova\Actions;

use App\Jobs\SendSmsJob;
use App\Models\VonageAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use function app;
use function collect;
use function intval;
use function now;
use function preg_split;

class SendSMS extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $from = $fields->sender_name;
        $receiverData = $fields->receiver;
        $message = $fields->message;
        $queue = $fields->queue;
        $amountPerTask = $fields->sms_amount_per_task;
        $delayBetween = $fields->seconds_delay_between_tasks;

        $toArr = preg_split("/\r\n|\n|\r/", $receiverData);

        /** @var VonageAccount $model */
        foreach ($models as $model) {
            if ($queue) {
                $numberCol = collect($toArr);
                $chunks = $numberCol->chunk($amountPerTask);

                $task = 0;
                foreach ($chunks as $chunk) {
                    SendSmsJob::dispatch($model, $chunk->toArray(), $from, $message)
                        ->delay(now()->addSeconds(intval($delayBetween) * $task));
                    $task++;
                }
            } else {
                app(\App\Actions\SendSms::class)->send($model, $toArr, $from, $message);
            }
        }

        return $models;
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Text::make('Sender name')->rules(['nullable', 'string']),
            Textarea::make('Receiver')->rules(['min:5', 'required', 'string']),
            Textarea::make('Message')->rules(['max:160', 'required', 'string']),
            Boolean::make('Queue'),
            Number::make('SMS amount per task')->rules(['nullable', 'int']),
            Number::make('Seconds delay between tasks')->rules(['nullable', 'int']),
        ];
    }
}
