<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentWindowResource\Pages;
use App\Models\PaymentWindow;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentWindowResource extends Resource
{
    protected static ?string $model = PaymentWindow::class;

    protected static ?string $navigationIcon = 'heroicon-o-window';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('payment_session_id')
                    ->relationship('paymentSession', 'id')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('payment_window_id')
                    ->label('Payment Window ID')
                    ->maxLength(255),
                Forms\Components\TextInput::make('cc_number')
                    ->label('CC Number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('cc_owner')
                    ->label('CC Owner')
                    ->maxLength(255),
                Forms\Components\TextInput::make('cc_expiration_date')
                    ->label('CC Expiration Date')
                    ->maxLength(255),
                Forms\Components\TextInput::make('cc_cvc')
                    ->label('CC CVC')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('last_vic_poll'),
                Forms\Components\Toggle::make('show_fullscreen_spinner'),
                Forms\Components\TextInput::make('fullscreen_spinner_label'),
                Forms\Components\Toggle::make('show_wait_for_confirmation'),
                Forms\Components\TextInput::make('wait_for_confirmation_error'),
                Forms\Components\Toggle::make('show_success'),
                Forms\Components\TextInput::make('partner'),
                Forms\Components\Toggle::make('close_window_with_error'),
                Forms\Components\TextInput::make('error_message'),
                Forms\Components\Toggle::make('error_cc_number'),
                Forms\Components\Toggle::make('error_cc_owner'),
                Forms\Components\Toggle::make('error_cc_date'),
                Forms\Components\Toggle::make('error_cc_cvc'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('paymentSession.id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_window_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cc_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cc_owner')
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
            'index' => Pages\ListPaymentWindows::route('/'),
            'create' => Pages\CreatePaymentWindow::route('/create'),
            'edit' => Pages\EditPaymentWindow::route('/{record}/edit'),
        ];
    }
}
