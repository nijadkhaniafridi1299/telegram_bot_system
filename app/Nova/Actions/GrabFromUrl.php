<?php

namespace App\Nova\Actions;

use App\Actions\CreateProductPageByUrl;
use App\Actions\GrabKlazAd;
use App\Models\Image;
use App\Models\ProductPage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;
use Storage;
use Str;
use function app;
use function date;
use function end;
use function explode;
use function uniqid;

class GrabFromUrl extends Action
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
        $url = $fields->url;

        app(CreateProductPageByUrl::class)->createByUrl($url);

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
            URL::make('Url'),
        ];
    }
}
