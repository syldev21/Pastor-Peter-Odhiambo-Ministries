<?php

namespace App\Filament\Resources\ExportLogResource\Pages;

use App\Filament\Resources\ExportLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExportLog extends EditRecord
{
    protected static string $resource = ExportLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
