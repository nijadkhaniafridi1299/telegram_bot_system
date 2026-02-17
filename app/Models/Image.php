<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_page_id',
        'path',
        'order',
    ];

    public function productPage(): BelongsTo
    {
        return $this->belongsTo(ProductPage::class);
    }
}
