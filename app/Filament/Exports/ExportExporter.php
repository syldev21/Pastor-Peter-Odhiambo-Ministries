<?php

namespace App\Filament\Exports;

use App\Models\ExportLog;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ExportExporter extends Exporter
{
    protected static ?string $model = ExportLog::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id'),
            ExportColumn::make('Type'),
            ExportColumn::make('metadata'),
        ];

    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your export log export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
