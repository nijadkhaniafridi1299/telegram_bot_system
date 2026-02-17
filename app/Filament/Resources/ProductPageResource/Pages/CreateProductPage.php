<?php

namespace App\Filament\Resources\ProductPageResource\Pages;

use App\Filament\Resources\ProductPageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProductPage extends CreateRecord
{
    protected static string $resource = ProductPageResource::class;
}
