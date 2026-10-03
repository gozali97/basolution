# BASolution — Company Profile

Laravel 12, PHP 8.2, MySQL 8, Tailwind CSS v4, dan Filament v4 (CMS di `/admin`). Lihat `PRD.md` dan `TASKLIST.md`.

## Setup lokal
```bash
composer install && npm install
cp .env.example .env && php artisan key:generate   # isi DB_* di .env
php artisan migrate --seed                          # tabel + default + admin
php artisan db:seed --class=DemoSeeder              # opsional: data fiktif untuk preview lokal
php artisan storage:link
npm run build                                       # atau `npm run dev` saat develop
php artisan serve
```
Admin default dari seeder: `admin@basolution.test` / `password`. Override dengan `ADMIN_EMAIL` dan `ADMIN_PASSWORD` di `.env` sebelum seeding.

## Test
```bash
php artisan test
```

## Deploy produksi
1. `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://domain-anda`, `FILESYSTEM_DISK=public`, kredensial DB, dan SMTP (`MAIL_*`) agar notifikasi form kontak terkirim ke email di menu Pengaturan.
2. `composer install --no-dev --optimize-autoloader && npm ci && npm run build`
3. `php artisan migrate --force && php artisan storage:link`
4. `php artisan db:seed --force` untuk default dan admin, lalu segera ganti password admin. Seeder aman dijalankan ulang karena tidak menimpa hasil edit CMS. `DemoSeeder` sengaja menolak berjalan di produksi.
5. `php artisan optimize && php artisan filament:optimize`
