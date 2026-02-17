<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Storage;

class ProductPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'pin_code',
        'title',
        'price',
        'shipping',
        'shipping_price',
        'postal_code',
        'city',
        'description',
        'seller_name',
        'seller_klaz_user_id',
        'klaz_url',
        'iban_name',
        'iban',
        'bic',
        'fees',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'product_page_id');
    }

    public function getOrderedImageLinks(): array
    {
        return $this->images()
            ->orderBy('order')
            ->get()
            ->map(function ($image) {
                return Storage::disk('public')->url($image->path);
            })
            ->all();
    }

    public function paymentSessions(): HasMany
    {
        return $this->hasMany(PaymentSession::class, 'product_page_id');
    }

    public function getNovaUrl(): string
    {
        return rtrim(config('app.url'), '/') . '/' . trim(config('nova.path'), '/') . '/resources/product-pages/' . $this->id;
    }

}
