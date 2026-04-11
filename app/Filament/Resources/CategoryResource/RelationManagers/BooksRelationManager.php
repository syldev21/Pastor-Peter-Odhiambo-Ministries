<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\{
    TextInput,
    Textarea,
    Toggle,
    FileUpload,
    Select,
    Section,
    Actions\Action as FormAction
};
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
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use Filament\Tables\Actions\Action;

class BooksRelationManager extends RelationManager
{
    protected static string $relationship = 'books';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            Section::make("Basic Details")
            ->description("Provide the basic details of the book")
            ->collapsible()
            // ->aside()
            ->schema([
            TextInput::make('title')
                ->unique(ignoreRecord: true)
                ->required()
                ->maxLength(255),
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

            TextInput::make('price')->numeric()->minValue(200)->maxValue(2000)->required(),
            TextInput::make('stock')->numeric()->minValue(1)->required(),
            Toggle::make('is_devotional')->label('Devotional?'),
            Toggle::make('is_featured')->label('Featured?'),
            ])->columnSpan(1)->columns([
                "default"=>1,
                "md"=>2,
                "lg"=>3,
                "xl"=>3
            ]),
            Section::make("Meta")
            ->description("Provide the additional information about the book")
            ->collapsible()
            // ->aside()
            ->schema([

            Textarea::make('description')->rows(4)->columnSpan(2),
            FileUpload::make('cover_image')
                ->disk('public') // ✅ ensure public disk is used
                ->directory('book-covers')
                ->image()
                ->imageEditor()
                ->maxSize(2048)
                ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png']) // ✅ expanded types
                ->enableDownload()
                ->enableOpen()
                ->nullable(),
            ])->columnSpan(1)->columns([
                "default"=>1,
                "md"=>2,
                "lg"=>3,
                "xl"=>3
            ]),
            ]);
    }

    public function table(Table $table): Table
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
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create Book')
                    ->mutateFormDataUsing(function (array $data, RelationManager $livewire): array {
                        // Automatically set the category_id to the parent category
                        $data['category_id'] = $livewire->ownerRecord->id;
                        return $data;
                    }),
            ]);
    }
}
