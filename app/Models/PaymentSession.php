<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentSession extends Model
{
    use HasFactory;

    public const STATUS_CREATED = 'created';
    public const STATUS_PIN_ENTERED = 'pin_entered';
    public const STATUS_ADDING_ADDRESS = 'adding_address';
    public const STATUS_ADDRESS_ADDED = 'address_added';
    public const STATUS_SELECT_METHOD = 'select_method';
    public const STATUS_METHOD_SELECTED = 'method_selected';
    public const STATUS_PAYED = 'payed';

    protected $fillable = [
        'product_page_id',
        'status',
        'ip',
        'user_agent',
    ];

    public function productPage(): BelongsTo
    {
        return $this->belongsTo(ProductPage::class);
    }

    public function deliveryAddresses(): HasMany
    {
        return $this->hasMany(DeliveryAddress::class, 'payment_session_id');
    }

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class, 'payment_session_id');
    }

    public function paymentWindows(): HasMany
    {
        return $this->hasMany(PaymentWindow::class, 'payment_session_id');
    }

    public function getNovaUrl(): string
    {
        return rtrim(config('app.url'), '/') . '/' . trim(config('nova.path'), '/') . '/resources/payment-sessions/' . $this->id;
    }
}
