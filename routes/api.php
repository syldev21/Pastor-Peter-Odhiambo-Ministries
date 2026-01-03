<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::post('/mpesa/callback', [PaymentController::class, 'callback']);