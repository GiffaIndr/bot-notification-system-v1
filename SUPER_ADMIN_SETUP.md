# Super Admin Panel - Setup & Deployment Guide

## Overview
Sistem Super Admin telah diimplementasikan lengkap dengan fitur:
- Dashboard monitoring real-time
- Pricing component management
- User activity monitoring
- Revenue & transaction reports
- Email verification security
- Secure authentication dengan middleware

---

## 🚀 Setup & Deployment

### Step 1: Run Migrations
Jalankan semua migration untuk setup database schema:

```bash
php artisan migrate
```

**Migrations yang akan dijalankan:**
- `2026_04_21_100000_add_super_admin_to_users_table` - Tambah kolom is_super_admin & phone ke users
- `2026_04_21_100100_update_activity_logs_table` - Tambah kolom action, ip_address, user_agent

### Step 2: Run Seeders
Seed database dengan data awal termasuk super admin account:

```bash
php artisan db:seed
```

Atau seed hanya super admin:

```bash
php artisan db:seed --class=SuperAdminSeeder
```

---

## 👤 Super Admin Account

### Default Credentials
```
Email:    tugas.tasku@gmail.com
Password: SuperAdmin@2024!Secure
```

⚠️ **IMPORTANT:** Ubah password segera setelah login pertama!

### Login URL
```
http://your-domain/superadmin/login
```

---

## 📋 Features & Pages

### 1. Dashboard (`/superadmin/dashboard`)
**Tampilkan:**
- Total users count
- Active subscriptions
- Total revenue generated
- Pending payments
- Recent activity logs

**Fungsi:** Overview real-time status aplikasi

---

### 2. Pricing Management (`/superadmin/pricing`)
**Fitur:**
- View semua pricing components
- Edit pricing components
- Log activity untuk setiap perubahan

**Pricing Components:**
- `base_plan` - Harga dasar (10 member): Rp 15.000
- `additional_members` - Per 5 member blocks: Rp 5.000
- `whatsapp` - WhatsApp Bot: Rp 15.000
- `discord` - Discord Bot: Rp 10.000
- `telegram` - Telegram Bot: Rp 10.000

**Rumus Perhitungan:**
```
Monthly Cost = Base Plan + Additional Members + Bot Fees
Total = Monthly Cost × Duration + PPN 10%
```

---

### 3. Users Management (`/superadmin/users`)
**Fitur:**
- List semua regular users
- Search by name atau email
- View subscription status
- View payment history
- Monitor user activity

**User Detail Page (`/superadmin/users/{id}`):**
- Profile lengkap user
- Subscription details
- Active bots
- Payment history
- Days remaining

---

### 4. Activity Logs (`/superadmin/activity-logs`)
**Fitur:**
- Log monitoring semua aktivitas super admin
- Filter by action, user, date range
- Track IP address & user agent
- Pagination (50 items per page)

**Tracked Actions:**
- `update_pricing` - Perubahan pricing component
- (Expandable untuk action lain)

---

### 5. Revenue Reports (`/superadmin/revenue`)
**Fitur:**
- Revenue per period
- Transaction history
- Filter by date range
- Statistics:
  - Total Revenue
  - Total Transactions
  - Average Transaction Amount

---

### 6. Account Settings (`/superadmin/change-password`)
**Fitur:**
- Change password dengan validasi ketat

**Password Requirements:**
- Minimum 8 characters
- Uppercase letter (A-Z)
- Lowercase letter (a-z)
- Number (0-9)
- Special character (@, $, !, %, *, ?, &)

---

## 🔐 Security Features

### 1. Route Protection
Semua super admin routes dilindungi dengan middleware:
```php
Route::middleware(['auth', 'is_super_admin'])->prefix('superadmin')->group(...);
```

### 2. Email Verification
- Super admin harus verify email sebelum akses dashboard
- Email verification link dengan expiry time
- Custom verification notification

### 3. Activity Logging
Setiap perubahan pricing dicatat:
- User yang melakukan perubahan
- IP address & user agent
- Timestamp
- Detail perubahan

### 4. Password Security
- Strong password requirement
- Current password verification untuk ubah password
- Password confirmation

---

## 📧 Email Configuration

Pastikan `.env` sudah dikonfigurasi untuk email:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourapp.com
MAIL_FROM_NAME="Your App Name"
```

---

## 🗂️ File Structure

### Controllers
- `app/Http/Controllers/SuperAdmin/SuperAdminController.php` - Main dashboard & features
- `app/Http/Controllers/SuperAdmin/SuperAdminAuthController.php` - Login & authentication

### Middleware
- `app/Http/Middleware/IsSuperAdmin.php` - Auth guard untuk super admin routes

### Routes
- `routes/superadmin.php` - Semua super admin routes

### Views
- `resources/views/superadmin/layout.blade.php` - Main layout
- `resources/views/superadmin/auth/login.blade.php` - Login page
- `resources/views/superadmin/auth/verify-email.blade.php` - Email verification
- `resources/views/superadmin/auth/change-password.blade.php` - Password change
- `resources/views/superadmin/dashboard.blade.php` - Dashboard
- `resources/views/superadmin/pricing/index.blade.php` - Pricing list
- `resources/views/superadmin/pricing/edit.blade.php` - Pricing edit
- `resources/views/superadmin/activity-logs.blade.php` - Activity logs
- `resources/views/superadmin/users/index.blade.php` - Users list
- `resources/views/superadmin/users/show.blade.php` - User detail
- `resources/views/superadmin/revenue.blade.php` - Revenue reports

### Models
- `app/Models/User.php` - Updated dengan is_super_admin & MustVerifyEmail
- `app/Models/ActivityLog.php` - Updated dengan action, ip_address, user_agent

### Migrations
- `2026_04_21_100000_add_super_admin_to_users_table.php`
- `2026_04_21_100100_update_activity_logs_table.php`

### Seeders
- `database/seeders/SuperAdminSeeder.php` - Create super admin account

---

## 🧪 Testing

### Login Test
1. Navigate to `http://your-domain/superadmin/login`
2. Enter email: `tugas.tasku@gmail.com`
3. Enter password: `SuperAdmin@2024!Secure`
4. Verify email if prompted
5. Should redirect to dashboard

### Pricing Edit Test
1. Go to `/superadmin/pricing`
2. Click edit pada salah satu component
3. Ubah price
4. Submit
5. Check activity logs untuk verify change tercatat

### User Monitoring Test
1. Go to `/superadmin/users`
2. Click view pada salah satu user
3. Check subscription details
4. Check payment history

---

## 📝 Notes

### Default Pricing Components
Pricing seeder automatically create:
```php
[
    'key' => 'base_plan',
    'price' => 15000,
    'name' => 'Harga Dasar (10 Member)',
],
[
    'key' => 'additional_members',
    'price' => 5000,
    'name' => 'Tambahan Kapasitas (per 5 Member)',
],
[
    'key' => 'whatsapp',
    'price' => 15000,
    'name' => 'Integrasi Bot WhatsApp',
],
[
    'key' => 'discord',
    'price' => 10000,
    'name' => 'Integrasi Bot Discord',
],
[
    'key' => 'telegram',
    'price' => 10000,
    'name' => 'Integrasi Bot Telegram',
],
```

### Backup & Recovery
1. Backup database sebelum update pricing
2. ActivityLog mencatat semua perubahan
3. Change password history via logs

---

## 🆘 Troubleshooting

### Email Verification tidak terkirim
- Check `.env` mail configuration
- Check spam folder
- Resend verification link di halaman verification

### Cannot access super admin panel
- Ensure user `is_super_admin = true`
- Ensure email_verified_at is not null
- Check middleware configuration di `app/Http/Kernel.php`

### Pricing changes tidak terlihat di user side
- Changes hanya apply ke new subscriptions
- Existing subscriptions tetap menggunakan pricing lama
- User harus renew subscription untuk pricing baru

---

## 📞 Support

Untuk pertanyaan atau issue:
1. Check activity logs untuk track perubahan
2. Verify user & subscription data
3. Review controller logic di `SuperAdminController`

---

**Last Updated:** April 21, 2026
**Version:** 1.0
