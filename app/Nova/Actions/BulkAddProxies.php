<?php

namespace App\Nova\Actions;

use App\Models\Proxy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use function collect;
use function explode;
use function preg_split;
use function trim;

class BulkAddProxies extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param \Laravel\Nova\Fields\ActionFields $fields
     * @param \Illuminate\Support\Collection $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $proxyLines = collect(preg_split("/\r\n|\n|\r/", $fields->proxies))
            ->map(fn($line) => trim($line))
            ->filter(fn($line) => !empty($line));

        foreach ($proxyLines as $proxyLine) {
            $parts = explode(':', $proxyLine);
            Proxy::firstOrCreate([
                'host' => $parts[0],
                'port' => $parts[1],
                'user' => $parts[2],
                'password' => $parts[3],
            ], [
                'enabled' => true
            ]);
        }

        return $models;
    }

    /**
     * Get the fields available on the action.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Textarea::make('Proxies'),
        ];
    }
}
