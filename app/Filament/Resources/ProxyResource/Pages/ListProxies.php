<?php

namespace App\Filament\Resources\ProxyResource\Pages;

use App\Filament\Resources\ProxyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProxies extends ListRecords
{
    protected static string $resource = ProxyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
