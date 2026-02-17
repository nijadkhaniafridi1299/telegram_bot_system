<?php

namespace App\Filament\Resources\PaymentWindowResource\Pages;

use App\Filament\Resources\PaymentWindowResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentWindow extends EditRecord
{
    protected static string $resource = PaymentWindowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
