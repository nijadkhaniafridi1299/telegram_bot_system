<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class VonageAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'display_name',
        'api_key',
        'api_secret',
    ];

    protected $casts = [
        'api_secret' => 'encrypted'
    ];
}
