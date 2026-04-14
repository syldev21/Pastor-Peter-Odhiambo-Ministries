<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Models\Book;
// use App\Exports\BookExporter;
use App\Filament\Exports\BookExporter;
use Filament\Forms\Form;
use Filament\Forms\Components\{
    TextInput,
    Textarea,
    Toggle,
    FileUpload,
    Select,
    Section,
    Actions\Action as FormAction
};
use Filament\Resources\Resource;
use Filament\Tables\Table;
// use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use Filament\Tables\Actions\ExportAction;
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
use \Filament\Tables\Actions\EditAction;
use \Filament\Tables\Actions\DeleteAction;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-s-book-open';
    protected static ?string $navigationLabel = 'Books';
    protected static ?string $pluralModelLabel = 'Books';
    protected static ?string $navigationGroup = 'Book';

    public static function form(Form $form): Form
{
    return $form->schema([
        Tabs::make('Book Form')
            ->tabs([
                Tab::make('Basic Details')
                    ->icon("heroicon-o-arrow-right-start-on-rectangle")
                    ->schema([
                        TextInput::make('title')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                        TextInput::make('author')
                            ->required()
                            ->maxLength(255),

                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->createOptionAction(fn () => FormAction::make('Create Category')->modalHeading('Create New Category'))
                            ->required(),
                        TextInput::make('price')
                            ->numeric()
                            ->minValue(200)
                            ->maxValue(2000)
                            ->required(),
                        TextInput::make('stock')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                        Toggle::make('is_devotional')->label('Devotional?'),
                        Toggle::make('is_featured')->label('Featured?'),
                    ]),

                Tab::make('Meta')
                    ->icon("heroicon-o-ellipsis-vertical")
                    ->schema([
                        Select::make('tags')
                            ->multiple()
                            ->relationship('tags', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->createOptionAction(fn () => FormAction::make('Create Tag')->modalHeading('Create New Tag')),
                        Textarea::make('description')
                            ->rows(4)
                            ->columnSpan(2),
                        FileUpload::make('cover_image')
                            ->disk('public')
                            ->directory('book-covers')
                            ->image()
                            ->imageEditor()
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png'])
                            ->enableDownload()
                            ->enableOpen()
                            ->nullable(),
                    ]),
            ])->columnSpanFull()
            ->activeTab(1)
            ->persistTabInQueryString(),
    ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("id")
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->searchable(),
                ImageColumn::make('cover_image')
                    ->disk('public') // ✅ ensure correct disk
                    ->defaultImageUrl(asset('POMI.png'))
                    ->toggleable(), // ✅ asset helper for default
                TextColumn::make('title')->searchable()->sortable()
                    ->toggleable(),
                TextColumn::make('author')->searchable()
                    ->toggleable(),
                TextColumn::make('category.name')->label('Category')->searchable()->sortable()
                    ->toggleable(),
                TextColumn::make('price')->money('KES')
                    ->toggleable(),
                ToggleColumn::make('is_featured')
                    ->toggleable(),
                ToggleColumn::make('is_devotional')
                    ->toggleable(),
                TextColumn::make('stock')
                    ->label('Stock')
                    ->badge()
                    ->color(fn ($record) => $record->isLowStock() ? 'danger' : 'success')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make("created_at")
                    ->date()
                    ->searchable()
                    ->sortable()
                    ->label("Date Created")
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('tags')
                    ->multiple()
                    ->relationship('tags', 'name')
                    ->label('Tags')
                    ->preload(),
                SelectFilter::make('category')->relationship('category', 'name')->label('Category')->searchable()->preload(),
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
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(BookExporter::class),
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