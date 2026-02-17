<?php

namespace App\Filament\Resources\ProductPageResource\Pages;

use App\Filament\Resources\ProductPageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductPages extends ListRecords
{
    protected static string $resource = ProductPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
