<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OtpController extends Controller
{
    /**
     * ارسال کد تایید به شماره موبایل
     * @throws ConnectionException
     */
    public function send(Request $request)
    {
        // اعتبارسنجی شماره موبایل
        $request->validate([
            'phone' => ['required', 'size:11', 'regex:/^09[0-9]{9}$/']
        ]);

        $phone = $request->phone;

        // تولید کد ۵ رقمی تصادفی
        $code = str_pad(rand(1, 99999), 5, 0, STR_PAD_LEFT);

        // ذخیره کد در کش به مدت ۲ دقیقه
        Cache::put('otp_' . $phone, $code, 120);

        // ارسال پیامک با PromoSMS
        $response = Http::withToken(config('services.promosms.token'))
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->post('https://promosms.ir/api/send/pattern', [
                'number' => $phone,
                'data' => [
                    'data1' => $code,
                ],
                'pattern_code' => config('services.promosms.patterns.login')
            ]);

        // لاگ‌گیری برای دیباگ
        Log::info("OTP sent to {$phone}", [
            'status' => $response->successful(),
            'code' => $response->status(),
            'report_id' => $response->json()['info']['report_id'],
        ]);

        return response()->json([
            'success' => $response->successful(),
            'message' => $response->successful()
                ? 'کد تایید ارسال شد'
                : 'خطا در ارسال پیامک'
        ]);
    }

    /**
     * بررسی کد تایید واردشده توسط کاربر
     */
    public function verify(Request $request)
    {
        $request->validate([
            'phone' => 'required|size:11',
            'code' => 'required|size:5'
        ]);

        $phone = $request->phone;
        $code = $request->code;

        // دریافت کد ذخیره‌شده از کش
        $storedCode = Cache::get('otp_' . $phone);

        if (!$storedCode) {
            return response()->json([
                'success' => false,
                'message' => 'کد تایید منقضی شده یا وجود ندارد'
            ], 400);
        }

        // بررسی صحت کد
        if ($storedCode != $code) {
            return response()->json([
                'success' => false,
                'message' => 'کد تایید اشتباه است'
            ], 400);
        }

        // حذف کد از کش پس از موفقیت
        Cache::forget('otp_' . $phone);

        return response()->json([
            'success' => true,
            'message' => 'تایید هویت با موفقیت انجام شد'
        ]);
    }
}
