<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Models\Book;
use App\Exports\BookExporter;
use Filament\Forms\Form;
use Filament\Forms\Components\{
    TextInput,
    Textarea,
    Toggle,
    FileUpload,
    Select,
    Actions\Action as FormAction
};
use Filament\Resources\Resource;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\{
    TextColumn,
    ToggleColumn,
    ImageColumn
};
use Filament\Tables\Filters\{
    SelectFilter,
    TernaryFilter
};

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Books';
    protected static ?string $pluralModelLabel = 'Books';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')->required()->maxLength(255),
            TextInput::make('author')->required()->maxLength(255),
            Select::make('category_id')
                ->relationship('category', 'name')
                ->searchable()
                ->createOptionForm([
                    TextInput::make('name')->required()->maxLength(255),
                ])
                ->createOptionAction(fn () => FormAction::make('Create Category')->modalHeading('Create New Category'))
                ->required(),

            Select::make('tags')
                ->multiple()
                ->relationship('tags', 'name')
                ->searchable()
                ->preload()
                ->createOptionForm([
                    TextInput::make('name')->required()->maxLength(255),
                ])
                ->createOptionAction(fn () => FormAction::make('Create Tag')->modalHeading('Create New Tag')),

            TextInput::make('price')->numeric()->required(),
            TextInput::make('stock')->numeric()->required(),
            Toggle::make('is_devotional')->label('Devotional?'),
            Toggle::make('is_featured')->label('Featured?'),
            FileUpload::make('cover_image')
                ->directory('book-covers')
                ->image()
                ->imageEditor()
                ->maxSize(2048)
                ->acceptedFileTypes(['image/jpeg', 'image/png'])
                ->enableDownload()
                ->enableOpen()
                ->nullable(),
            Textarea::make('description')->rows(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')
                    ->circular()
                    ->defaultImageUrl('/images/default-cover.png'),
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('author')->searchable(),
                TextColumn::make('category.name')->label('Category')->searchable(),
                TextColumn::make('price')->money('KES'),
                ToggleColumn::make('is_featured'),
                ToggleColumn::make('is_devotional'),
                TextColumn::make('stock')
                    ->label('Stock')
                    ->badge()
                    ->color(fn ($record) => $record->isLowStock() ? 'danger' : 'success')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('tags')
                    ->multiple()
                    ->relationship('tags', 'name')
                    ->label('Tags'),
                SelectFilter::make('category')->relationship('category', 'name')->label('Category'),
                TernaryFilter::make('is_devotional')->label('Devotional'),
                TernaryFilter::make('is_featured')->label('Featured'),
                TernaryFilter::make('stock')
                    ->label('Low Stock')
                    ->queries(
                        true: fn ($query) => $query->lowStock(),
                        false: fn ($query) => $query->where('stock', '>', 5),
                        blank: fn ($query) => $query,
                    ),
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Export Books')
                    ->exports([
                        BookExporter::class,
                    ]),
                Action::make('manualExport')
                    ->label('Manual Export')
                    ->url('/admin/books/export')
                    ->openUrlInNewTab()
                    ->visible(fn () => auth()->user()->can('export data')),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\BookStats::class,
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'staff']) ?? false;
    }
}