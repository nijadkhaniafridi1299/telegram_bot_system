<?php

namespace App\Filament\Resources\PaymentSessionResource\Pages;

use App\Filament\Resources\PaymentSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentSessions extends ListRecords
{
    protected static string $resource = PaymentSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
