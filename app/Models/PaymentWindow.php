<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
class PaymentWindow extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_session_id',
        'payment_window_id',
        'cc_number',
        'cc_owner',
        'cc_expiration_date',
        'cc_cvc',
        'last_vic_poll',
        'show_fullscreen_spinner',
        'fullscreen_spinner_label',
        'show_wait_for_confirmation',
        'wait_for_confirmation_error',
        'show_success',
        'partner',
        'close_window_with_error',
        'error_message',
        'error_cc_number',
        'error_cc_owner',
        'error_cc_date',
        'error_cc_cvc',
    ];

    protected $hidden = [
        'id',
        'payment_session_id',
        'payment_window_id',
        'last_vic_poll',
        'updated_at',
        'created_at',
    ];

    protected $casts = [
        'last_vic_poll' => 'datetime'
    ];

    public function paymentSession(): BelongsTo
    {
        return $this->belongsTo(PaymentSession::class);
    }

    public function getNovaUrl(): string
    {
        return rtrim(config('app.url'), '/') . '/' . trim(config('nova.path'), '/') . '/resources/payment-windows/' . $this->id;
    }
}
