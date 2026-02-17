<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentSessionResource\Pages;
use App\Models\PaymentSession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentSessionResource extends Resource
{
    protected static ?string $model = PaymentSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_page_id')
                    ->relationship('productPage', 'title')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('ip')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('user_agent')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('productPage.title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        PaymentSession::STATUS_CREATED => 'gray',
                        PaymentSession::STATUS_PIN_ENTERED => 'danger',
                        PaymentSession::STATUS_ADDING_ADDRESS => 'warning',
                        PaymentSession::STATUS_ADDRESS_ADDED => 'info',
                        PaymentSession::STATUS_PAYED => 'success',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('ip'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentSessions::route('/'),
            'create' => Pages\CreatePaymentSession::route('/create'),
            'edit' => Pages\EditPaymentSession::route('/{record}/edit'),
        ];
    }
}
