<?php

namespace App\Filament\Resources;

use App\Enums\PaymentProvider;
use App\Filament\Resources\WebhookLogResource\Pages;
use App\Filament\Resources\WebhookLogResource\RelationManagers;
use App\Models\WebhookLog;
use Filament\Forms;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WebhookLogResource extends Resource
{
    protected static ?string $model = WebhookLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('url')->disabled(),
                Toggle::make('is_debug')->disabled(),
                Textarea::make('error_message')->columnSpan('full')->disabled(),
                KeyValue::make('payload')->disabled(),
                Textarea::make('response_body')->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('transaction.transaction_id')->label('Transaction ID'),
                BadgeColumn::make('provider')->colors(['primary']),
                TextColumn::make('url')->limit(30),
                IconColumn::make('is_debug')
                    ->boolean()
                    ->trueIcon('heroicon-o-bug-ant')
                    ->falseIcon('heroicon-o-shield-check'),
                BadgeColumn::make('response_status')
                    ->colors([
                        'success' => fn ($state) => $state >= 200 && $state < 300,
                        'warning' => fn ($state) => $state >= 400 && $state < 500,
                        'danger'  => fn ($state) => $state >= 500,
                    ]),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('provider')->options(PaymentProvider::toArray()),
                TernaryFilter::make('is_debug'),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderByDesc('id');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWebhookLogs::route('/'),
            'create' => Pages\CreateWebhookLog::route('/create'),
            'edit' => Pages\EditWebhookLog::route('/{record}/edit'),
        ];
    }
}
