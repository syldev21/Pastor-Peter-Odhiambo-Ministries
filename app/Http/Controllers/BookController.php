<?php

namespace App\Http\Controllers;

use App\Models\Book;

class BookController
{public function index()
    {
        $books = Book::with('tags')->paginate(4);
        return view('books.index', compact('books'));
    }
}
    