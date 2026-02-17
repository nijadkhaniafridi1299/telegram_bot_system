<?php

namespace App\Nova;

use App\Nova\Actions\GrabFromUrl;
use App\Nova\Actions\UploadImage;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;

class ProductPage extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\ProductPage>
     */
    public static $model = \App\Models\ProductPage::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'pin_code', 'title', 'postal_code', 'city', 'iban_name', 'iban', 'bic', 'seller_name'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            Text::make(__('Pin code'), 'pin_code'),
            Text::make(__('Title'), 'title'),
            Number::make(__('Price'), 'price')->sortable(),
            Number::make(__('Fees'), 'fees')->step(0.0000001)->sortable(),
            Boolean::make(__('Shipping'), 'shipping')->sortable(),
            Number::make(__('Shipping price'), 'shipping_price')->step(0.01)->sortable(),
            Text::make(__('Postal code'), 'postal_code'),
            Text::make(__('City'), 'city'),
            Textarea::make(__('Description'), 'description'),
            Text::make(__('Iban name'), 'iban_name'),
            Text::make(__('Iban'), 'iban'),
            Text::make(__('Bic'), 'bic'),
            Text::make(__('Seller name'), 'seller_name'),
            Text::make(__('Seller klaz user id'), 'seller_klaz_user_id'),
            Text::make(__('Klaz url'), 'klaz_url'),
            DateTime::make(__('Created at'), 'created_at')->sortable()->hideWhenCreating()->hideWhenUpdating(),
            DateTime::make(__('Updated at'), 'updated_at')->sortable()->hideWhenCreating()->hideWhenUpdating(),

            HasMany::make(__('Images'), 'images', Image::class),
            HasMany::make(__('Payment sessions'), 'paymentSessions', PaymentSession::class),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            GrabFromUrl::make()->standalone()->canSee(function (NovaRequest $request) {
                return $request->user()?->isAdmin();
            }),
            UploadImage::make()->canSee(function (NovaRequest $request) {
                return $request->user()?->isAdmin();
            })
        ];
    }
}
