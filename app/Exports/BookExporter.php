<?php

namespace App\Exports;

use App\Models\Book;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Concerns\WithColumns;

class BookExporter extends ExcelExport implements WithColumns
{
    public function query()
    {
        return Book::query();
    }

    public function columns(): array
    {
        return [
            'title',
            'author',
            'category.name',
            'price',
            'stock',
            'is_featured',
            'is_devotional',
        ];
    }
}