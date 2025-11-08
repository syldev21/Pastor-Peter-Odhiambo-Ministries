<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\BookExportController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\BookController;

// ------------------------------
// 📚 Books & Home
// ------------------------------
Route::get('/', fn() => redirect('/books'))->name('home');
Route::get('/books', [BookController::class, 'index'])->name('books.index');

// ------------------------------
// 🧭 Dashboard & Settings (Auth Only)
// ------------------------------
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

// ------------------------------
// 📦 Export
// ------------------------------
Route::get('/admin/books/export', BookExportController::class)
    ->middleware(['auth', 'verified']);

// ------------------------------
// 🛒 Cart Routes (Both Guests & Auth Users)
// ------------------------------
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

// Add to cart (handles both guest and auth)
Route::post('/cart/add/{book}', [CartController::class, 'add'])->name('cart.add');

// Update & Remove (auth users)
Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');

// Guest session cart
Route::post('/cart/session/add/{book}', [CartController::class, 'addSession'])->name('cart.add.session');
Route::post('/cart/session/update/{id}', [CartController::class, 'updateSession'])->name('cart.update.session');
Route::post('/cart/session/remove/{id}', [CartController::class, 'destroy'])->name('cart.remove.session');

// ------------------------------
// 💳 Checkout (Open to Guests)
// ------------------------------
Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::view('/orders/thankyou', 'checkout.thankyou')->name('orders.thankyou');

// Authenticated users: payment steps
Route::middleware(['auth'])->group(function () {
    Route::get('/orders/{order}/payment', [CheckoutController::class, 'paymentForm'])->name('orders.payment.form');
    Route::post('/orders/{order}/payment', [CheckoutController::class, 'submitPayment'])->name('orders.payment.submit');
});

// ------------------------------
// 🚨 Fallback
// ------------------------------
Route::fallback(fn () => view('errors.404'));
