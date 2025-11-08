<?php

namespace App\Filament\Resources\ExportLogResource\Pages;

use App\Filament\Resources\ExportLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExportLogs extends ListRecords
{
    protected static string $resource = ExportLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
