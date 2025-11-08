<?php

namespace App\Exports;

use App\Models\Book;
use App\Models\ExportLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BookExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $books = Book::with('category')->get()->map(function ($book) {
            return [
                'Title' => $book->title,
                'Author' => $book->author,
                'Category' => $book->category?->name,
                'Price' => $book->price,
                'Stock' => $book->stock,
                'Featured' => $book->is_featured ? 'Yes' : 'No',
                'Devotional' => $book->is_devotional ? 'Yes' : 'No',
                'Created At' => $book->created_at,
                'Updated At' => $book->updated_at,
            ];
        });

        ExportLog::create([
            'user_id' => auth()->id(),
            'type' => 'filament',
            'metadata' => json_encode([
                'columns' => ['title', 'author', 'category', 'price'],
                'timestamp' => now()->toDateTimeString(),
            ]),
        ]);

        return $books;
    }

    public function headings(): array
    {
        return [
            'Title', 'Author', 'Category', 'Price', 'Stock',
            'Featured', 'Devotional', 'Created At', 'Updated At'
        ];
    }
}