<?php

namespace App\Filament\Resources;

use App\Enums\PaymentProvider;
use App\Enums\TransactionStatus;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('provider')
                    ->options(PaymentProvider::toArrayWithLabels())
                    ->required(),

                Forms\Components\TextInput::make('transaction_id')
                    ->required()
                    ->disabled(),

                Forms\Components\TextInput::make('order_id')
                    ->required(),

                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->required()
                    ->label('Amount (in UZS)')
                    ->suffix('uzs')
                    ->dehydrateStateUsing(fn ($state) => intval($state * 100))
                    ->formatStateUsing(fn ($state) => number_format($state / 100, 2, '.', '')),

                Forms\Components\Select::make('status')
                    ->options(TransactionStatus::toArrayWithLabels())
                    ->required(),

                Textarea::make('requested_payload')
                    ->label('Request')
                    ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE))
                    ->rows(10)
                    ->disabled(),

                Textarea::make('response_payload')
                    ->label('Response')
                    ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE))
                    ->rows(10)
                    ->disabled(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_id')->searchable(),
                TextColumn::make('provider')->badge(),
                TextColumn::make('order_id'),
                TextColumn::make('amount')->money('UZS'),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->since(),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderByDesc('id');
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
