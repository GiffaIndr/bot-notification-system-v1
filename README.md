# 🤖 Bot Notification System

Sistem notifikasi berbasis group dengan dukungan multi-platform bot (WhatsApp, Discord, Telegram), dibangun menggunakan **Laravel** + **Node.js (Baileys)**.

---

## 📋 Daftar Isi

- [Fitur](#-fitur)
- [Tech Stack](#-tech-stack)
- [Persyaratan](#-persyaratan)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Menjalankan Aplikasi](#-menjalankan-aplikasi)
- [Struktur Database](#-struktur-database)
- [Sistem Role](#-sistem-role)
- [Subscription & Pricing](#-subscription--pricing)
- [Fitur Bot](#-fitur-bot)
- [Akun Demo](#-akun-demo)

---

## ✨ Fitur

### 👥 Group Management
- Buat & kelola grup pengumuman
- Sistem undangan dengan kode unik (PJ & Member)
- Kick member
- Edit nama group

### 🔐 Role & Permission
- Role custom per group (nama, warna, permission)
- Permission granular: buat announcement, edit, manage member, generate code, manage bot, buat poll
- Role owner tidak bisa diubah

### 📢 Announcement
- CRUD announcement dengan jadwal kirim
- Pengulangan otomatis (daily, weekly, monthly)
- Pin announcement penting
- Lampiran file & gambar (max 3 file, max 20MB)
- Reaction/emoji (👍❤️😂😮😢😡)
- Random Picker (dari member group atau custom list)
- Read & track aktivitas via Activity Log

### 🤖 Bot Integration
- **WhatsApp** via Baileys (Node.js)
- **Discord** via Discord.js (Node.js)
- **Telegram** via Telegram Bot API (HTTP)
- Kirim teks, gambar, dan dokumen

### 📊 Poll & Voting
- Tipe poll: Ya/Tidak & Pilihan Ganda
- Mode anonymous atau publik
- Batas waktu poll
- Toggle close poll

### 🎰 Random Picker
- Standalone picker di halaman group
- Terintegrasi dengan announcement
- Mode member group atau custom list
- Animasi spin
- Hasil tersimpan & persisten

### 💳 Subscription & Payment
- Flexible pricing (per bot, per group, per member)
- Integrasi Midtrans payment gateway
- Receipt PDF via DomPDF
- Riwayat pembayaran

---

## 🛠 Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 10 (PHP 8.2) |
| Frontend | Bootstrap 5, Font Awesome 6 |
| Database | MySQL |
| WhatsApp Bot | Node.js + Baileys |
| Discord Bot | Node.js + Discord.js |
| Telegram Bot | Telegram Bot API |
| Payment | Midtrans Snap |
| PDF | DomPDF (barryvdh/laravel-dompdf) |

---

## 📦 Persyaratan

- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL >= 5.7
- NPM
- Akun Midtrans Sandbox
- Akun Discord Developer
- Akun Telegram BotFather
- Nomor WhatsApp khusus untuk bot

---

## 🚀 Instalasi

### 1. Clone & Install Laravel

```bash
git clone <repo-url>
cd bot-notification-system

composer install
cp .env.example .env
php artisan key:generate
```

### 2. Setup Database

```bash
php artisan migrate --seed
```

### 3. Storage Link

```bash
php artisan storage:link
```

### 4. Install Node.js Service

```bash
cd bot-service
npm install
cp .env.example .env
```

---

## ⚙️ Konfigurasi

### Laravel `.env`

```env
# App
APP_NAME="Bot Notification System"
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_botnotification
DB_USERNAME=root
DB_PASSWORD=

# Midtrans
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key

# Discord
DISCORD_CLIENT_ID=your_client_id
DISCORD_REDIRECT_URI=http://localhost/discord/callback

# Telegram
TELEGRAM_BOT_TOKEN=your_bot_token
TELEGRAM_BOT_USERNAME=your_bot_username

# WhatsApp Service (Node.js)
WHATSAPP_SERVICE_URL=http://localhost:3000
```

### Node.js `bot-service/.env`

```env
DISCORD_TOKEN=your_discord_bot_token
```

---

## ▶️ Menjalankan Aplikasi

### Terminal 1 — Laravel

```bash
php artisan serve
```

### Terminal 2 — Node.js Bot Service

```bash
cd bot-service
node server.js
```

> Scan QR code yang muncul menggunakan WhatsApp nomor bot.

### Terminal 3 — Laravel Scheduler

```bash
php artisan schedule:work
```

---

## 🗄 Struktur Database

```
users                    → Data pengguna
plans                    → (legacy)
subscriptions            → Langganan aktif user
payments                 → Riwayat pembayaran
pricing_components       → Harga per komponen (bot, group, member)

groups                   → Data group
group_members            → Relasi user & group
group_roles              → Role custom per group
group_bots               → Bot yang aktif per group

announcements            → Pengumuman
announcement_attachments → Lampiran file announcement
announcement_reactions   → Emoji reaction
activity_logs            → Log aktivitas group

polls                    → Poll/voting
poll_options             → Pilihan poll
poll_votes               → Vote member
```

---

## 🔐 Sistem Role

Setiap group punya 3 role default yang bisa di-rename & dikustomisasi:

| Role | Default Permission |
|---|---|
| **Owner** | Semua permission |
| **Editor** | Buat & edit announcement, buat poll |
| **Member** | Lihat announcement & poll, react, vote |

Permission yang bisa dikonfigurasi per role:
- `can_create_announcement`
- `can_edit_announcement`
- `can_manage_member`
- `can_generate_code`
- `can_manage_bot`
- `can_create_poll`

---

## 💰 Subscription & Pricing

Sistem pricing fleksibel — bayar sesuai kebutuhan:

| Komponen | Harga / 6 Bulan |
|---|---|
| WhatsApp Bot | Rp 10.000 |
| Discord Bot | Rp 8.000 |
| Telegram Bot | Rp 8.000 |
| Per Group | Rp 15.000 |
| Per Member | Rp 5.000 |

Contoh: WA + Discord + 2 Group + 20 Member = **Rp 116.000 / 6 bulan**

---

## 🤖 Fitur Bot

### WhatsApp (Baileys)
- Kirim pesan teks + gambar + dokumen
- QR scan untuk login

### Discord (Discord.js)
- Invite bot via OAuth2
- Kirim ke channel spesifik
- Support file attachment

### Telegram
- Setup via BotFather
- Auto-detect Chat ID
- Support foto & dokumen

### Format Pesan Bot
```
📢 *Judul Announcement*

Isi announcement...

🎰 *Yang Kena Giliran:*
1. Nama Member

🏢 _Nama Group_
🕐 _Tanggal & Waktu_

👍 5  ❤️ 3  😂 1
```

---

## 👤 Akun Demo

Setelah `php artisan migrate --seed`:

| Email | Password | Keterangan |
|---|---|---|
| user1@test.com | user123 | User 1 |
| user2@test.com | user123 | User 2 |
| user3@test.com | user123 | User 3 |

---

## 📝 Catatan Penting

- Untuk production, ganti Midtrans sandbox ke production mode
- Gunakan nomor WhatsApp **khusus** untuk bot (bukan nomor pribadi)
- Node.js service harus selalu jalan agar bot aktif
- Scheduler harus jalan untuk pengiriman announcement terjadwal
- Pastikan `php artisan storage:link` sudah dijalankan untuk akses file lampiran

---

## 📄 Lisensi

MIT License — bebas digunakan dan dimodifikasi.
