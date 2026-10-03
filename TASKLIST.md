# Tasklist — BASolution Company Profile

## Fase 1 — Setup
- [x] Buat project Laravel 12 di folder ini
- [x] Buat database MySQL `basolution`, isi `.env`
- [x] `git init` dan `.gitignore` (bawaan Laravel)
- [x] Pastikan Tailwind v4 + Vite jalan (`npm install`, `npm run build`)
- [x] Install Filament v4, buat panel `/admin`, warna primer biru laut (`#3a86ab`)
- [x] Buat user admin lewat seeder
- [x] `php artisan storage:link`

## Fase 2 — Database & Model
- [x] Migration + model: services, projects, clients, testimonials, team_members, messages, settings
- [x] Casts JSON (tech_stack, gallery, settings.value), boolean, datetime
- [x] Scope `active()` dan urutan `sort` (satu trait kecil bila dipakai >2 model, kalau tidak inline)
- [x] Helper `setting('key')` dengan cache
- [x] Seeder konten contoh untuk semua tabel

## Fase 3 — CMS Filament
- [x] Resource Layanan
- [x] Resource Portofolio (upload cover + gallery, tags tech stack, toggle featured)
- [x] Resource Klien
- [x] Resource Testimoni
- [x] Resource Tim
- [x] Resource Pesan Masuk (read-only, aksi "tandai dibaca", badge belum dibaca)
- [x] Custom page Pengaturan (satu form, simpan ke tabel settings, reset cache)
- [x] Reorder via kolom `sort` di tabel yang relevan
- [x] Logo dan nama brand di panel

## Fase 4 — UI Publik
- [x] Konfigurasi tema Tailwind (warna, font Plus Jakarta Sans)
- [x] Layout utama: header + menu mobile, footer, tombol WhatsApp melayang
- [x] Komponen Blade: tombol, judul section, kartu layanan, kartu proyek
- [x] Beranda: hero, layanan, portofolio unggulan, logo klien, testimoni, CTA
- [x] Beranda versi Figma "Landing-Page-V1": navbar/hero gelap, strip keunggulan, tentang, grid layanan, panel keunggulan, cara kerja, slider testimoni, proyek, FAQ, CTA, footer sea-700
- [x] Animasi scroll AOS 2.3.4 (fallback no-JS dan reduced motion) + slider generik di `app.js`
- [x] CMS: repeater keunggulan, cara kerja, FAQ, gambar pendukung beranda
- [x] Tema putih + hijau merek `#76C457`: token `sea-*` diganti `brand-*`, permukaan gelap dihapus, panel Filament ikut
- [x] Halaman Tentang (profil, visi–misi, tim)
- [x] Halaman Layanan + detail
- [x] Halaman Portofolio (filter kategori) + detail
- [x] Halaman Kontak (form, info, embed peta)
- [x] Halaman 404 bertema

## Fase 5 — Form Kontak
- [x] Validasi request, honeypot, rate limit 5/menit per IP
- [x] Simpan ke tabel messages
- [x] Email notifikasi ke admin (bila SMTP di-set)
- [x] Pesan sukses/gagal di halaman

## Fase 6 — SEO & Kualitas
- [x] Title, meta description, Open Graph per halaman
- [x] Route `sitemap.xml` dan `robots.txt`
- [x] Alt text, kontras AA, fokus keyboard terlihat
- [x] Gambar lazy load

## Fase 7 — Testing & Rilis
- [x] Feature test: halaman publik 200, form kontak (valid/invalid/rate limit), `/admin` menolak tamu
- [ ] Cek Lighthouse mobile (belum dijalankan)
- [x] Catatan deploy singkat di README (env produksi, `optimize`, `storage:link`)

## Fase 8 — Manajemen Proyek & Portal Klien
- [x] `spatie/laravel-permission` 6.25.0 (versi 7/8 butuh PHP 8.3), role `admin` dan `client`
- [x] Migration: `users.client_id`, `client_apps`, `feature_requests`, `feature_request_notes`, role + backfill `admin` untuk user lama
- [x] Admin: menu Akun (role + perusahaan), Aplikasi klien, Permintaan fitur (+ catatan progres, badge Diajukan)
- [x] Portal `/portal`: daftar, ajukan, edit selama Diajukan, lihat status/harga/deadline, balas catatan, profil
- [x] Feature test: pemisahan panel per role, isolasi data antarklien, id aplikasi palsu ditolak, kunci edit, log status, buat akun klien
- [ ] Jalankan `php artisan migrate` di MySQL lokal dan produksi
- [ ] Notifikasi email saat ada permintaan baru / status berubah (setelah SMTP di-set)
- [ ] Reset password lewat email untuk klien (setelah SMTP di-set)
