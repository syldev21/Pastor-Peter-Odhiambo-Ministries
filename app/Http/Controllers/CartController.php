<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class CartController extends Controller
{
    public function add(Book $book)
    {
        if (auth()->check()) {
            // Logged-in user: use database cart
            $cartItem = auth()->user()->cart()->firstOrCreate([
                'book_id' => $book->id,
            ], [
                'quantity' => 0,
            ]);

            $cartItem->increment('quantity');
        } else {
            // Guest user: use session cart
            $cart = session()->get('cart', []);

            if (isset($cart[$book->id])) {
                $cart[$book->id]['quantity']++;
            } else {
                $cart[$book->id] = [
                    'title' => $book->title,
                    'price' => $book->price,
                    'quantity' => 1,
                ];
            }

            session()->put('cart', $cart);
        }

        return back()->with('success', 'Book added to cart.');
    }

    public function index()
    {
        if (auth()->check()) {
            // Signed-in user: use database cart
            $cartItems = auth()->user()->cart()->with('book')->get();
            $total = $cartItems->sum(fn ($item) => $item->quantity * $item->book->price);
        } else {
            // Guest user: use session cart
            $sessionCart = session()->get('cart', []);

            // Convert session cart to object-like structure for Blade compatibility
            $cartItems = collect($sessionCart)->map(function ($item, $bookId) {
                return (object) [
                    'id' => $bookId,
                    'book_id' => $bookId,
                    'title' => $item['title'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                ];
            })->values();

            $total = $cartItems->sum(fn ($item) => $item->price * $item->quantity);
        }

        return view('cart.index', compact('cartItems', 'total'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item = auth()->user()->cart()->findOrFail($id);
        $item->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Quantity updated.');
    }
    public function destroy($id)
    {
        if (auth()->check()) {
            // Signed-in user: delete from database cart
            auth()->user()->cart()->findOrFail($id)->delete();
        } else {
            // Guest user: delete from session cart
            $cart = session()->get('cart', []);
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Item removed from cart.');
    }
    public function updateSession(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }
        return back()->with('success', 'Quantity updated.');
    }
    public function addSession(Request $request, Book $book)
    {
        $cart = session()->get('cart', []);
        $cart[$book->id] = [
            'title' => $book->title,
            'price' => $book->price,
            'quantity' => ($cart[$book->id]['quantity'] ?? 0) + 1,
        ];
        session()->put('cart', $cart);
        return back()->with('success', 'Book added to cart.');
    }

}
