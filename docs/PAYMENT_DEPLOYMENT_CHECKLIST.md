# Payment Integration Deployment Checklist

Dokumen ini berisi checklist lengkap untuk deployment fitur Payment Integration dengan Midtrans ke environment production.

## 📋 Pre-Deployment Checklist

### 1. Environment Variables (Required)

Pastikan semua environment variables berikut sudah dikonfigurasi di server production:

| Variable                   | Description                                               | Required       | Example                                                          |
| -------------------------- | --------------------------------------------------------- | -------------- | ---------------------------------------------------------------- |
| `MIDTRANS_SERVER_KEY`      | Server Key dari Midtrans Dashboard (RAHASIA)              | ✅ Yes         | `SB-Mid-server-xxx` (sandbox) atau `Mid-server-xxx` (production) |
| `MIDTRANS_CLIENT_KEY`      | Client Key untuk Snap.js di frontend                      | ✅ Yes         | `SB-Mid-client-xxx` (sandbox) atau `Mid-client-xxx` (production) |
| `MIDTRANS_IS_PRODUCTION`   | Mode environment (`false` = sandbox, `true` = production) | ✅ Yes         | `true`                                                           |
| `MIDTRANS_IS_SANITIZED`    | Enable input sanitization                                 | ⚠️ Recommended | `true`                                                           |
| `MIDTRANS_IS_3DS`          | Enable 3D Secure untuk credit card                        | ⚠️ Recommended | `true`                                                           |
| `MIDTRANS_EXPIRY_DURATION` | Durasi kadaluarsa pembayaran (menit)                      | Optional       | `1440` (24 jam)                                                  |

#### Cara Mendapatkan Credentials:

1. Login ke [Midtrans Dashboard](https://dashboard.midtrans.com)
2. Pilih environment (Sandbox/Production)
3. Pergi ke **Settings** → **Access Keys**
4. Copy Server Key dan Client Key

⚠️ **PENTING**: Jangan pernah commit credentials ke repository!

### 2. Database Migrations (Required)

Jalankan migrations berikut secara berurutan:

```bash
# Migration untuk tabel transactions
php artisan migrate --path=database/migrations/2025_12_28_000001_create_transactions_table.php

# Migration untuk kolom payment_date di bookings
php artisan migrate --path=database/migrations/2025_12_28_000002_add_payment_date_to_bookings_table.php

# Migration untuk kolom expiry_time di transactions
php artisan migrate --path=database/migrations/2025_12_28_000003_add_expiry_time_to_transactions_table.php

# Atau jalankan semua migrations sekaligus
php artisan migrate
```

#### Verifikasi Migrations:

```bash
php artisan migrate:status
```

Pastikan semua migration dengan prefix `2025_12_28` sudah berstatus "Ran".

### 3. Webhook URL Configuration di Midtrans Dashboard

Konfigurasi webhook URL di Midtrans Dashboard agar sistem menerima notifikasi pembayaran:

1. Login ke [Midtrans Dashboard](https://dashboard.midtrans.com)
2. Pilih environment yang sesuai (Sandbox/Production)
3. Pergi ke **Settings** → **Configuration**
4. Pada bagian **Payment Notification URL**, masukkan:

```
https://your-domain.com/midtrans/notification
```

5. Klik **Update** untuk menyimpan

#### Webhook URL Format:

| Environment | URL                                                                         |
| ----------- | --------------------------------------------------------------------------- |
| Development | `http://localhost:8000/midtrans/notification` (gunakan ngrok untuk testing) |
| Staging     | `https://staging.your-domain.com/midtrans/notification`                     |
| Production  | `https://your-domain.com/midtrans/notification`                             |

⚠️ **PENTING**:

-   URL harus menggunakan HTTPS untuk production
-   Pastikan endpoint dapat diakses dari internet (tidak di-block firewall)
-   Webhook endpoint sudah dikecualikan dari CSRF protection

### 4. Callback URLs Configuration

Konfigurasi callback URLs untuk redirect setelah pembayaran:

1. Di Midtrans Dashboard, pergi ke **Settings** → **Snap Preferences**
2. Konfigurasi URLs berikut:

| Callback Type         | URL                                         |
| --------------------- | ------------------------------------------- |
| Finish Redirect URL   | `https://your-domain.com/payments/finish`   |
| Unfinish Redirect URL | `https://your-domain.com/payments/unfinish` |
| Error Redirect URL    | `https://your-domain.com/payments/error`    |

---

## 🧪 Testing Steps

### Pre-Production Testing (Sandbox)

1. **Test Payment Creation**

    ```bash
    # Buat booking baru dan klik "Pay Now"
    # Pastikan Snap popup muncul dengan benar
    ```

2. **Test Successful Payment**

    - Gunakan test card: `4811 1111 1111 1114`
    - CVV: `123`
    - Expiry: Any future date
    - OTP: `112233`

3. **Test Failed Payment**

    - Gunakan test card: `4911 1111 1111 1113`
    - Pembayaran akan ditolak

4. **Test Payment Notification**

    - Setelah pembayaran berhasil, verifikasi:
        - Transaction status berubah ke "paid"
        - Booking status berubah ke "paid"
        - payment_date terisi

5. **Test Payment Retry**

    - Buat pembayaran yang gagal
    - Klik "Retry Payment"
    - Pastikan transaction baru dibuat

6. **Test Admin Features**
    - Login ke admin panel (`/admin`)
    - Verifikasi TransactionResource menampilkan data dengan benar
    - Test "Check Status" action

### Production Verification

1. **Verify Environment**

    ```bash
    php artisan tinker
    >>> config('midtrans.is_production')
    # Harus return: true
    ```

2. **Verify Webhook Accessibility**

    ```bash
    curl -X POST https://your-domain.com/midtrans/notification \
      -H "Content-Type: application/json" \
      -d '{"test": "ping"}'
    # Harus return HTTP 200 atau 400 (bukan 404 atau 500)
    ```

3. **Test Real Transaction (Small Amount)**
    - Lakukan pembayaran dengan nominal kecil
    - Verifikasi seluruh flow berjalan dengan benar
    - Refund jika perlu melalui Midtrans Dashboard

---

## 🚀 Deployment Steps

### Step 1: Deploy Code

```bash
# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Clear and cache config
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 2: Set Environment Variables

```bash
# Edit .env file
nano .env

# Tambahkan/update Midtrans credentials
MIDTRANS_SERVER_KEY=Mid-server-xxxxx
MIDTRANS_CLIENT_KEY=Mid-client-xxxxx
MIDTRANS_IS_PRODUCTION=true
```

### Step 3: Run Migrations

```bash
php artisan migrate --force
```

### Step 4: Verify Configuration

```bash
# Test configuration
php artisan tinker
>>> app(\App\Services\MidtransClient::class);
# Tidak boleh ada error
```

### Step 5: Configure Midtrans Dashboard

1. Set Notification URL
2. Set Callback URLs
3. Enable payment methods yang diinginkan

### Step 6: Test Payment Flow

1. Buat test booking
2. Lakukan pembayaran dengan nominal kecil
3. Verifikasi notification diterima
4. Verifikasi status terupdate

---

## 🔍 Monitoring & Troubleshooting

### Log Files

Payment logs tersimpan di:

```
storage/logs/payment-YYYY-MM-DD.log
```

### Common Issues

| Issue                         | Possible Cause    | Solution                              |
| ----------------------------- | ----------------- | ------------------------------------- |
| Snap popup tidak muncul       | Client Key salah  | Verifikasi MIDTRANS_CLIENT_KEY        |
| Notification tidak diterima   | Webhook URL salah | Cek konfigurasi di Midtrans Dashboard |
| Signature verification failed | Server Key salah  | Verifikasi MIDTRANS_SERVER_KEY        |
| "Payment service unavailable" | API timeout       | Cek koneksi ke Midtrans API           |

### Health Check Commands

```bash
# Check Midtrans configuration
php artisan tinker
>>> config('midtrans')

# Check recent transactions
>>> \App\Models\Transaction::latest()->take(5)->get(['order_id', 'transaction_status', 'created_at'])

# Check failed notifications in logs
grep "signature verification failed" storage/logs/payment-*.log
```

---

## 📞 Support Contacts

-   **Midtrans Support**: support@midtrans.com
-   **Midtrans Documentation**: https://docs.midtrans.com
-   **Midtrans Status Page**: https://status.midtrans.com

---

## ✅ Final Checklist

Before going live, ensure all items are checked:

-   [ ] Production credentials configured
-   [ ] All migrations executed
-   [ ] Webhook URL configured in Midtrans Dashboard
-   [ ] Callback URLs configured
-   [ ] HTTPS enabled for all payment URLs
-   [ ] Test payment successful in sandbox
-   [ ] Test payment successful in production (small amount)
-   [ ] Monitoring/logging configured
-   [ ] Team notified about new feature

---

_Last Updated: December 2025_
