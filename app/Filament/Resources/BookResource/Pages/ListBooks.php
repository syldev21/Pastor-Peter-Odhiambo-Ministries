<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;

class ListBooks extends ListRecords
{
    protected static string $resource = BookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTabs(): array{
        return [
            'All' => Tab::make(),
            'Devotional' => Tab::make()->modifyQueryUsing(
                function ($query){
                    $query->where("is_devotional", 1);
                }
            ),
            'Featured' => Tab::make()->modifyQueryUsing(
                function ($query){
                    $query->where("is_featured", 1);
                }
            ),
            'Has Thumbnail' => Tab::make()->modifyQueryUsing(
                function ($query){
                    $query->where("cover_image", "!=", NULL);
                }
            ),
            'No Thumbnail' => Tab::make()->modifyQueryUsing(
                function ($query){
                    $query->where("cover_image", NULL);
                }
            )
        ];
    }
}
