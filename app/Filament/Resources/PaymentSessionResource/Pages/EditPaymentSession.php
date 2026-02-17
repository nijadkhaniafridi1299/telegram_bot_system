<?php

namespace App\Filament\Resources\PaymentSessionResource\Pages;

use App\Filament\Resources\PaymentSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentSession extends EditRecord
{
    protected static string $resource = PaymentSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
