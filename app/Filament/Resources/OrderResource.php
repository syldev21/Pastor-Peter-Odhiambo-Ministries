<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers\ItemsRelationManager;
use App\Models\Order;
use Filament\Forms\Form;
use Filament\Forms\Components\{
    Select,
    TextInput
};
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\{
    TextColumn,
    BadgeColumn
};
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Orders';
    protected static ?string $pluralModelLabel = 'Orders';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'payment_initiated' => 'Payment Initiated',
                    'processing' => 'Processing',
                    'paid' => 'Paid',
                    'shipped' => 'Shipped',
                    'cancelled' => 'Cancelled',
                    'failed' => 'Failed',
                    'refunded' => 'Refunded',
                ])
                ->required(),

            TextInput::make('payment_ref')
                ->label('Payment Reference')
                ->maxLength(255)
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('Order #')->sortable(),
                TextColumn::make('user.name')->label('Customer')->searchable(),
                TextColumn::make('total_amount')->money('KES')->sortable(),
                BadgeColumn::make('status')->colors([
                    'pending' => 'warning',
                    'payment_initiated' => 'warning',
                    'processing' => 'info',
                    'paid' => 'success',
                    'shipped' => 'primary',
                    'cancelled' => 'gray',
                    'failed' => 'danger',
                    'refunded' => 'secondary',
                ]),
                TextColumn::make('payment_ref')->label('Payment Ref')->searchable()->limit(20),
                TextColumn::make('created_at')->label('Date')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'payment_initiated' => 'Payment Initiated',
                        'processing' => 'Processing',
                        'paid' => 'Paid',
                        'shipped' => 'Shipped',
                        'cancelled' => 'Cancelled',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ]),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}