<?php

namespace App\Nova\Actions;

use App\Models\Image;
use App\Models\ProductPage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
use Str;
use function date;
use function is_array;
use function uniqid;

class UploadImage extends Action
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
        /** @var UploadedFile $file */
        $file = $fields->image;
        $order = $fields->order;

        $path = $file->storeAs(date('Y-m'), Str::random(20) . '.' . $file->getClientOriginalExtension(), 'public');

        /** @var ProductPage $model */
        foreach ($models as $model) {
            $orderToUse = $order;
            if ($orderToUse == null) {
                $orderToUse = ($model->images()->max('order') ?? 0) + 1;
            }

            Image::create([
                'product_page_id' => $model->id,
                'path' => $path,
                'order' => $orderToUse
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
            File::make('Image')->rules(['required', 'file', 'mimes:png,jpg,jpeg,webp']),
            Number::make('Order'),
        ];
    }
}
