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
use WesselPerik\StatusField\StatusField;

class PaymentSession extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\PaymentSession>
     */
    public static $model = \App\Models\PaymentSession::class;

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
        'id', 'status', 'ip', 'user_agent'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        $statusColor = 'primary';
        if ($this->status == \App\Models\PaymentSession::STATUS_CREATED) {
            $statusColor = 'red';
        } else if ($this->status == \App\Models\PaymentSession::STATUS_PIN_ENTERED) {
            $statusColor = 'red';
        } else if ($this->status == \App\Models\PaymentSession::STATUS_ADDING_ADDRESS) {
            $statusColor = 'red';
        } else if ($this->status == \App\Models\PaymentSession::STATUS_ADDRESS_ADDED) {
            $statusColor = 'yellow';
        } else if ($this->status == \App\Models\PaymentSession::STATUS_PAYED) {
            $statusColor = 'green';
        }

        return [
            ID::make()->sortable(),

            BelongsTo::make(__('Product page'), 'productPage', ProductPage::class),

            StatusField::make('', 'status')
                ->icons([
                    'plus' => $this->status == \App\Models\PaymentSession::STATUS_CREATED,
                    'finger-print' => $this->status == \App\Models\PaymentSession::STATUS_PIN_ENTERED,
                    'truck' => $this->status == \App\Models\PaymentSession::STATUS_ADDING_ADDRESS,
                    'credit-card' => $this->status == \App\Models\PaymentSession::STATUS_ADDRESS_ADDED,
                    'check-circle' => $this->status == \App\Models\PaymentSession::STATUS_PAYED,
                ])
                ->color($statusColor . '-600') // optional
                ->solid(true) // optional
                ->tooltip($this->status) // optional
                ->info($this->status) // optional
                ->exceptOnForms(),

            Text::make(__('Status'), 'status'),
            Text::make(__('Ip'), 'ip'),
            Text::make(__('User agent'), 'user_agent'),
            DateTime::make(__('Created at'), 'created_at')->sortable()->hideWhenCreating()->hideWhenUpdating(),
            DateTime::make(__('Updated at'), 'updated_at')->sortable()->hideWhenCreating()->hideWhenUpdating(),

            HasMany::make(__('Delivery addresses'), 'deliveryAddresses', DeliveryAddress::class),
            HasMany::make(__('Payment methods'), 'paymentMethods', PaymentMethod::class),
            HasMany::make(__('Payment windows'), 'paymentWindows', PaymentWindow::class),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}
