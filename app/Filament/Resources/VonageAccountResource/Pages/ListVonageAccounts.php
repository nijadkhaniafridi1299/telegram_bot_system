<?php

namespace App\Filament\Resources\VonageAccountResource\Pages;

use App\Filament\Resources\VonageAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVonageAccounts extends ListRecords
{
    protected static string $resource = VonageAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
