<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OtpController;

// ارسال کد تایید (با محدودیت 1 درخواست در 2دقیقه)
Route::post('/otp/send', [OtpController::class, 'send'])
    ->middleware('throttle:1,2');

// بررسی کد تایید
Route::post('/otp/verify', [OtpController::class, 'verify']);
