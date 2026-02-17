<?php

namespace App\Filament\Resources\PaymentSessionResource\Pages;

use App\Filament\Resources\PaymentSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentSession extends CreateRecord
{
    protected static string $resource = PaymentSessionResource::class;
}
