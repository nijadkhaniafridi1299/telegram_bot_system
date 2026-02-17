<?php

namespace App\Filament\Resources\VonageAccountResource\Pages;

use App\Filament\Resources\VonageAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVonageAccount extends EditRecord
{
    protected static string $resource = VonageAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
