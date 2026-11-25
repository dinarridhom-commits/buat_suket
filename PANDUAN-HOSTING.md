# 🚀 Panduan Hosting Portal Tata Usaha BWS Kalimantan I

Aplikasi ini dibangun menggunakan **Laravel 11+** dengan arsitektur modern, sistem autentikasi aman, dan database terintegrasi (SQLite / MySQL).

---

## 🔐 Kredensial Login Default
Setelah di-hosting, gunakan akun pengelola resmi berikut:
- **URL Login:** `https://domain-anda.com/login`
- **Email:** `admin@pu.go.id`
- **Password:** `admin123`

*(Catatan: Anda dapat mengubah kata sandi atau menambahkan user baru melalui seeder atau manajemen database).*

---

## 🛠️ Pilihan Metode Hosting

### OPSI 1: Shared Hosting (cPanel / DirectAdmin) — Paling Umum & Mudah

1. **Kompresi File:**
   - Compress seluruh isi folder `WEBSITE` menjadi file `.zip` (abaikan folder `node_modules` jika ada).
2. **Unggah ke cPanel:**
   - Buka **File Manager** di cPanel Anda.
   - Ekstrak file zip ke direktori root atau ke dalam `public_html`.
   - File `.htaccess` di root proyek akan otomatis mengarahkan trafik ke folder `public/`.
3. **Versi PHP:**
   - Di cPanel, buka **Select PHP Version** dan pastikan memilih **PHP 8.2 atau 8.3**.
   - Aktifkan ekstensi yang dibutuhkan: `pdo_sqlite` (jika menggunakan SQLite bawaan) atau `pdo_mysql` (jika menggunakan MySQL), `mbstring`, `fileinfo`, `openssl`, `tokenizer`, `xml`, `curl`.
4. **Konfigurasi `.env`:**
   - Ubah `APP_ENV=production`
   - Ubah `APP_DEBUG=false`
   - Ubah `APP_URL=https://domain-anda.com`
   - Jika ingin beralih dari SQLite ke MySQL cPanel:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=nama_cpanel_db
     DB_USERNAME=nama_cpanel_user
     DB_PASSWORD=password_db_anda
     ```
   - Generate APP_KEY jika belum ada via terminal cPanel:
     ```bash
     php artisan key:generate
     ```
5. **Migrasi & Seeder Awal:**
   - Buka **Terminal** di cPanel (atau via cron job sekali jalan):
     ```bash
     php artisan migrate --force
     php artisan db:seed --force
     ```

---

### OPSI 2: VPS (Ubuntu / Debian + Nginx / Apache)

1. **Clone repository atau upload file** ke `/var/www/portal-bws1`.
2. **Set Permission:**
   ```bash
   sudo chown -R www-data:www-data /var/www/portal-bws1/storage /var/www/portal-bws1/bootstrap/cache /var/www/portal-bws1/database
   sudo chmod -R 775 /var/www/portal-bws1/storage /var/www/portal-bws1/bootstrap/cache /var/www/portal-bws1/database
   ```
3. **Arahkan Document Root Nginx:**
   Arahkan `root` web server ke folder `/var/www/portal-bws1/public;`
   ```nginx
   server {
       listen 80;
       server_name portal.bws-kalimantan1.pu.go.id;
       root /var/www/portal-bws1/public;

       index index.php index.html;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           include snippets/fastcgi-php.conf;
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
       }
   }
   ```
4. **Optimasi Performa Production:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

## 🛡️ Checklist Keamanan Sebelum Online
- [x] Sistem Login aktif (`/login`) dengan Rate Limiter anti brute-force
- [x] Semua rute persuratan & pegawai terlindungi middleware `auth`
- [ ] Ubah `APP_DEBUG=false` di `.env` saat live hosting
- [ ] Pasang SSL Certificate (HTTPS / Let's Encrypt gratis dari cPanel)
- [ ] Ganti kata sandi default `admin123` dengan kata sandi kuat kedinasan
