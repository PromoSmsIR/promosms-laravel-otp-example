<div align="center">

# سیستم احراز هویت با کد تایید (OTP)

پروژه‌ای ساده برای ارسال و بررسی کد تایید از طریق پیامک با استفاده از **PromoSMS**

[![نسخه PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

</div>

<p align="center">
سرویس پیامک: <a href="https://promosms.ir" target="_blank">promosms.ir</a>
</p>

---

آموزش کامل رو میتونید توی [مقاله ویرگول](https://vrgl.ir/rjNjR) دنبال کنید

---

> ⚠️ **هشدار امنیتی مهم**
>
> این پروژه صرفاً جهت **آموزش و تست** طراحی شده است.
> قبل از استفاده در محیط Production، حتماً موارد زیر را بررسی کنید:
> - اعتبارسنجی‌های اضافی
> - مدیریت خطاهای پیشرفته
> - لاگ‌گیری امنیتی
> - رعایت قوانین حریم خصوصی

---

## ویژگی‌ها

- ارسال کد تایید ۵ رقمی به شماره موبایل
- اعتبار کد: ۲ دقیقه
- استفاده از سرویس PromoSMS برای ارسال پیامک
- Rate Limit: حداکثر ۱ درخواست هر ۲ دقیقه
- ذخیره کد در Cache

---

## پیش‌نیازها

- PHP >= 8.2
- Composer
---

## نصب

```bash
# کپی فایل محیطی
copy .env.example .env

# نصب وابستگی‌ها
composer install
npm install

# تولید کلید و اجرای مهاجرت‌ها
php artisan key:generate
php artisan migrate

# بیلد فایل‌های استاتیک
npm run build
```

---

## پیکربندی

فایل `.env` را تنظیم کنید:

```env
# توکن PromoSMS
PROMOSMS_API_TOKEN=your_api_token_here
PROMOSMS_PATTERN_CODE_LOGIN=your_pattern_code
```

> **نکته:** الگوی پیامک باید در پنل PromoSMS ساخته شود و متغیر `data1` را برای کد تایید داشته باشد.

---

## مسیرهای API

### ۱. ارسال کد تایید

```http
POST /api/otp/send
Content-Type: application/json

{
    "phone": "09123456789"
}
```

**اعتبارسنجی:**
- شماره باید ۱۱ رقم و با فرمت `09xxxxxxxxx` باشد

**پاسخ موفقیت:**
```json
{
    "success": true,
    "message": "کد تایید ارسال شد"
}
```

---

### ۲. بررسی کد تایید

```http
POST /api/otp/verify
Content-Type: application/json

{
    "phone": "09123456789",
    "code": "12345"
}
```

**پاسخ موفقیت:**
```json
{
    "success": true,
    "message": "تایید هویت با موفقیت انجام شد"
}
```

**پاسخ خطا:**
```json
{
    "success": false,
    "message": "کد تایید منقضی شده یا وجود ندارد"
}
```

---


## مجوز

MIT
