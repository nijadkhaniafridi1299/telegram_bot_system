<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductPageResource\Pages;
use App\Models\ProductPage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductPageResource extends Resource
{
    protected static ?string $model = ProductPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('pin_code')
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->prefix('€'),
                Forms\Components\TextInput::make('fees')
                    ->step(0.0000001),
                Forms\Components\Toggle::make('shipping'),
                Forms\Components\TextInput::make('shipping_price')
                    ->step(0.01),
                Forms\Components\TextInput::make('postal_code')
                    ->maxLength(255),
                Forms\Components\TextInput::make('city')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('iban_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('iban')
                    ->maxLength(255),
                Forms\Components\TextInput::make('bic')
                    ->maxLength(255),
                Forms\Components\TextInput::make('seller_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('seller_klaz_user_id')
                    ->label('Seller Klaz User ID')
                    ->maxLength(255),
                Forms\Components\TextInput::make('klaz_url')
                    ->label('Klaz URL')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pin_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->sortable(),
                Tables\Columns\IconColumn::make('shipping')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('seller_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->formatStateUsing(fn ($state) => (string) $state)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->formatStateUsing(fn ($state) => (string) $state)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Custom actions like GrabFromUrl need to be migrated to Filament Actions
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers for Images and PaymentSessions could be added here
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductPages::route('/'),
            'create' => Pages\CreateProductPage::route('/create'),
            'edit' => Pages\EditProductPage::route('/{record}/edit'),
        ];
    }
}
