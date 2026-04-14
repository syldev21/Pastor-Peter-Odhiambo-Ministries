<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExportLogResource\Pages;
use App\Filament\Resources\ExportLogResource\RelationManagers;
use App\Models\ExportLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use App\Filament\Exports\ExportExporter;

class ExportLogResource extends Resource
{
    protected static ?string $model = ExportLog::class;

    protected static ?string $navigationIcon = 'heroicon-s-arrow-down-on-square-stack';
    protected static ?string $navigationGroup = 'Book';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("id"),
                TextColumn::make("User.name"),
                TextColumn::make("type"),
                TextColumn::make("metadata"),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                ExportAction::make()->exporter(ExportExporter::class)
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
            'index' => Pages\ListExportLogs::route('/'),
            'create' => Pages\CreateExportLog::route('/create'),
            'edit' => Pages\EditExportLog::route('/{record}/edit'),
        ];
    }
}
