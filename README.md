# PasteBox (پیست‌باکس)

PasteBox یک پلتفرم اشتراک‌گذاری متن و فایل با لینک کوتاه ۶ رقمی است که با PHP و MySQL ساخته شده و رابط کاربری RTL کاملا سفارشی دارد.

## ویژگی‌ها

- لینک کوتاه یکتای ۶ رقمی برای هر پیست
- زمان انقضای قابل انتخاب (`5m`, `10m`, `1h`, `1d`, `7d`, `14d`)
- رمز عبور اختیاری با `password_hash`
- آپلود فایل امن با اعتبارسنجی نوع/حجم
- جلوگیری از SQL Injection با PDO prepared statements
- جلوگیری از XSS با escaping در خروجی
- فرم‌های POST با CSRF token
- UI کاملاً RTL با فونت Vazirmatn و سوییچ تم روشن/تیره

## فونت محلی Vazirmatn

- استایل پروژه از فونت لوکال استفاده می‌کند:
  - `public/assets/fonts/vazirmatn/Vazirmatn-VariableFont_wght.ttf`
- اگر فایل فونت هنوز کپی نشده، پکیج فونت در مسیر `vazirmatn/` موجود است و باید فایل TTF به مسیر بالا منتقل شود.

## ساختار پروژه

```
app/
  Controllers/
  Core/
  Models/
  Services/
  Views/
config/
database/
public/
scripts/
storage/
```

## راه‌اندازی

1. یک دیتابیس MySQL بسازید (مثلاً `pastebox`).
2. فایل `database/schema.sql` را اجرا کنید.
3. متغیرهای محیطی را تنظیم کنید (یا از مقادیر پیش‌فرض استفاده کنید):
   - `APP_BASE_URL` (مثلاً `http://pastebox.test`)
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
   - `MAX_UPLOAD_SIZE` (پیش‌فرض: ۲۵۶ مگابایت؛ مقدار بر حسب بایت)
4. document root را روی `public/` بگذارید.
5. پوشه‌های زیر باید قابل نوشتن باشند:
   - `storage/uploads`
   - `storage/logs`

## مسیرها

- `GET /` فرم ساخت پیست
- `POST /paste` ساخت پیست
- `GET /{6digit}` نمایش پیست
- `POST /{6digit}/unlock` بازکردن پیست رمزدار
- `GET /attachment/{id}/download` دانلود فایل پیوست

## کران‌جاب پاکسازی

اسکریپت:

```bash
php scripts/cleanup_expired.php
```

### Linux cron example

```cron
*/10 * * * * /usr/bin/php /path/to/pastebox/scripts/cleanup_expired.php >> /path/to/pastebox/storage/logs/cron.log 2>&1
```

### Windows Task Scheduler example

- Program/script: `php`
- Add arguments: `C:\laragon\www\pastebox\scripts\cleanup_expired.php`
- Trigger: Every 10 minutes

## نکات امنیتی

- فایل‌های آپلودی با نام رندوم ذخیره می‌شوند.
- نوع فایل با MIME و extension بررسی می‌شود.
- دانلود فقط از طریق endpoint کنترل‌شده انجام می‌شود.
- اطلاعات حساس دیتابیس را در متغیر محیطی نگه دارید.

## چک‌لیست تست

- ساخت پیست ساده و مشاهده لینک
- تست تمام گزینه‌های انقضا
- تست رمز عبور صحیح/غلط
- تست پیست منقضی شده (`410`)
- تست آپلود/دانلود فایل معتبر
- تست payloadهای XSS در محتوا
- تست تلاش زیاد روی unlock/create (rate limit)
