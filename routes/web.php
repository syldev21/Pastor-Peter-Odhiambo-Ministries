<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\BookExportController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Http;

// ------------------------------
// 📚 Books & Home
// ------------------------------
Route::get('/', fn() => redirect()->route('books.index'))->name('home');
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
// 📦 Admin Book Export
// ------------------------------
Route::get('/admin/books/export', BookExportController::class)
    ->middleware(['auth', 'verified']);

// ------------------------------
// 🛒 Cart Routes (Guests & Auth)
// ------------------------------
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{book}', [CartController::class, 'add'])->name('cart.add');
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

// ------------------------------
// 💵 Payment (Auth Only)
// ------------------------------
Route::get('/orders/{order}/payment', [PaymentController::class, 'show'])->name('orders.payment.show');
Route::post('/orders/{order}/payment', [PaymentController::class, 'store'])->name('orders.payment.store');
Route::post('/orders/{order}/payment/stk', [PaymentController::class, 'stkPush'])->name('orders.payment.stk');

// ------------------------------
// 📦 Customer Order Tracking
// ------------------------------
Route::middleware(['auth'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});

// ------------------------------
// 📡 M-Pesa Callback
// ------------------------------
Route::post('/mpesa/callback', [PaymentController::class, 'callback'])->name('mpesa.callback');

// ------------------------------
// 🚨 Fallback & Diagnostics
// ------------------------------
Route::fallback(fn () => view('errors.404'));

Route::get('/test', fn () => dd(php_ini_loaded_file()));

Route::get('/curl-diagnostic', function () {
    $iniPath = php_ini_loaded_file();
    $curlInfo = curl_version();
    $testRequest = null;
    $error = null;

    try {
        $testRequest = Http::get('https://api.github.com')->status();
    } catch (\Exception $e) {
        $error = $e->getMessage();
    }

    return response()->json([
        'php_ini_path' => $iniPath,
        'curl_ssl_version' => $curlInfo['ssl_version'] ?? 'N/A',
        'curl_lib_version' => $curlInfo['version'] ?? 'N/A',
        'cacert_path' => ini_get('curl.cainfo'),
        'openssl_cafile' => ini_get('openssl.cafile'),
        'test_https_status' => $testRequest,
        'error_message' => $error,
    ]);
});