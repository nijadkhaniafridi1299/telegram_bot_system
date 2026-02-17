<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VonageAccountResource\Pages;
use App\Models\VonageAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VonageAccountResource extends Resource
{
    protected static ?string $model = VonageAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $modelLabel = 'SMS Spamming Account';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('display_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('remaining_balance'),
                Forms\Components\TextInput::make('sms_sent'),
                Forms\Components\TextInput::make('sms_failures'),
                Forms\Components\TextInput::make('api_key')
                    ->maxLength(255),
                Forms\Components\TextInput::make('api_secret')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('display_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('remaining_balance'),
                Tables\Columns\TextColumn::make('sms_sent')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sms_failures')
                    ->sortable(),
                Tables\Columns\TextColumn::make('api_key')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                // Custom action SendSMS migrated later
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
            'index' => Pages\ListVonageAccounts::route('/'),
            'create' => Pages\CreateVonageAccount::route('/create'),
            'edit' => Pages\EditVonageAccount::route('/{record}/edit'),
        ];
    }
}
