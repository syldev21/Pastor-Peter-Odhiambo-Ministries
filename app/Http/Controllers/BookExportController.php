<?php

namespace App\Http\Controllers;

use App\Exports\BookExport;
use App\Models\ExportLog;
use Maatwebsite\Excel\Facades\Excel;

class BookExportController
{
    public function __invoke()
    {
        ExportLog::create([
            'user_id' => auth()->id(),
            'type' => 'manual',
            'metadata' => json_encode([
                'route' => '/admin/books/export',
                'timestamp' => now()->toDateTimeString(),
            ]),
        ]);

        return Excel::download(new BookExport, 'books-export.csv');
    }
    public function index()
    {
        $books = Book::with('tags')->paginate(12);
        return view('books.index', compact('books'));
    }
    }