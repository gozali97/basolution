# PRD — Website Company Profile BASolution

## 1. Ringkasan
BASolution adalah software house. Website ini berfungsi sebagai etalase perusahaan: menjelaskan layanan, menampilkan portofolio, dan menghasilkan leads lewat form kontak. Admin mengelola semua konten lewat CMS Filament tanpa menyentuh kode.

## 2. Tujuan
- Menampilkan citra profesional dan tenang dengan tema laut (biru laut lembut).
- Menghasilkan leads (form kontak dan tombol WhatsApp).
- Seluruh konten bisa diubah admin tanpa developer.
- Cepat, SEO-friendly, dan responsif di mobile.

Di luar cakupan (v1): multi-bahasa, blog, karier/lowongan, login klien, pembayaran.

## 3. Stack
| Lapisan | Pilihan |
|---|---|
| Backend | Laravel 12, PHP 8.2 |
| Database | MySQL 8 |
| UI publik | Blade + Tailwind CSS v4 (via Vite, bawaan Laravel) |
| CMS | Filament v4 (panel `/admin`) |
| Penyimpanan file | Disk `public` Laravel (`storage:link`) |
| Email | Laravel Mail (SMTP, dikonfigurasi lewat `.env`) |

Tidak ada framework JS. Interaksi kecil (menu mobile, slider sederhana) cukup dengan CSS atau Alpine.js yang sudah ikut terpasang bersama Filament/Livewire.

## 4. Pengguna
- Pengunjung: calon klien yang ingin tahu layanan dan bukti kerja.
- Admin: tim internal BASolution yang mengelola konten dan membaca pesan masuk.
- Klien: perusahaan klien yang login di `/portal` untuk mengajukan dan memantau permintaan fitur pada aplikasinya.

## 5. Halaman Publik
| Halaman | URL | Isi |
|---|---|---|
| Beranda | `/` | Alur mengikuti Figma "Landing-Page-V1" (Home Services UI Kit): hero gelap + slider gambar aplikasi, strip keunggulan (maks. 4), tentang singkat, grid layanan + kartu "Butuh layanan lain?", panel keunggulan, cara kerja, slider testimoni, klien, proyek pilihan, FAQ, CTA kontak. Section tanpa data disembunyikan. Newsletter, blog, dan masking gambar bentuk rumah dari Figma sengaja tidak dipakai. |
| Tentang | `/tentang` | Profil, visi–misi, nilai perusahaan, tim |
| Layanan | `/layanan` | Daftar layanan |
| Detail layanan | `/layanan/{slug}` | Deskripsi lengkap, CTA kontak |
| Portofolio | `/portofolio` | Grid proyek, filter per kategori (query string) |
| Detail portofolio | `/portofolio/{slug}` | Gambar, deskripsi, klien, teknologi, link |
| Kontak | `/kontak` | Form kontak, alamat, email, telepon, peta (embed Google Maps) |

Elemen global: header dengan navigasi dan menu mobile, footer (kontak, sosial media, copyright), tombol WhatsApp melayang.

## 6. Modul CMS (Filament)
| Modul | Field utama |
|---|---|
| Layanan (Service) | title, slug, icon, excerpt, body (rich editor), image, sort, is_active |
| Portofolio (Project) | title, slug, category, client, tech_stack (tags), cover, gallery, body, url, is_featured, sort, is_active |
| Klien (Client) | name, logo, url, sort |
| Testimoni (Testimonial) | name, position, company, photo, quote, sort, is_active |
| Tim (TeamMember) | name, position, photo, sort, is_active |
| Pesan Masuk (Message) | name, email, phone, subject, body, read_at (hanya baca + tandai sudah dibaca) |
| Pengaturan (Setting) | 1 halaman: nama situs, logo, tagline, hero (judul, subjudul, maks. 6 gambar + deskripsi, bisa diurutkan), keunggulan (ikon, judul, teks; maks. 6), cara kerja (maks. 5 langkah), FAQ (maks. 12), gambar pendukung beranda (tentang, cara kerja, CTA), tentang, visi, misi, alamat, email, telepon, WhatsApp, sosial media, embed peta, meta default |
| Akun (User) | name, email, password, role (`admin`/`client`, Spatie), perusahaan klien (wajib untuk role klien) |
| Aplikasi klien (ClientApp) | klien, nama, url, catatan internal |
| Permintaan fitur (FeatureRequest) | aplikasi, jenis (fitur baru/perbaikan), judul, deskripsi, budget klien, status, harga disepakati, deadline, catatan progres |

Catatan:
- Kategori portofolio cukup kolom string, bukan tabel terpisah.
- Pengaturan disimpan di satu tabel `settings` (key–value JSON) dan diedit lewat satu custom page Filament. Tidak perlu plugin.
- Urutan tampil memakai kolom `sort` dengan fitur reorder bawaan tabel Filament.

## 7. Kebutuhan Fungsional
1. Admin login di `/admin` (butuh role `admin`), klien di `/portal` (butuh role `client` dan perusahaan klien). Akun dibuat admin; tidak ada registrasi publik.
2. CRUD untuk semua modul pada bagian 6, dengan upload gambar (validasi tipe dan ukuran maks 2 MB).
3. Slug dibuat otomatis dari judul dan unik (constraint di database).
4. Hanya data `is_active = true` yang tampil di situs.
5. Form kontak: validasi server, rate limit (5 kirim/menit per IP), honeypot anti-spam, data disimpan ke tabel `messages`, notifikasi email ke admin (opsional bila SMTP di-set).
6. Badge jumlah pesan belum dibaca di menu Filament.
7. SEO: title dan meta description per halaman, Open Graph tags, `sitemap.xml` dari route sederhana, `robots.txt`.
8. Portal klien: klien hanya melihat aplikasi dan permintaan milik perusahaannya (URL milik klien lain → 404). Klien mengajukan permintaan (status otomatis Diajukan), mengedit selama status Diajukan, melihat status/harga/deadline, dan membalas catatan progres.
9. Status permintaan: Diajukan, Ditinjau, Disetujui, Dikerjakan, Selesai, Ditolak. Setiap perubahan status otomatis dicatat sebagai catatan progres. Menu admin menampilkan badge jumlah permintaan berstatus Diajukan.

## 8. Kebutuhan Non-Fungsional
- Performa: Lighthouse mobile ≥ 90. Gambar `loading="lazy"`, aset di-build Vite.
- Responsif: 360 px sampai desktop lebar.
- Aksesibilitas: HTML semantik, alt text pada gambar, kontras teks memenuhi WCAG AA, navigasi keyboard, fokus terlihat.
- Keamanan: CSRF bawaan Laravel, validasi input, password di-hash, `.env` tidak masuk repo, `APP_DEBUG=false` di produksi.
- Cache pengaturan dengan `Cache::rememberForever`, di-reset saat disimpan.

## 9. Desain Visual
Kesan: bersih, segar, terpercaya. Dasar putih dengan satu hijau merek `#76C457` (pilihan pemilik) dan turunannya, plus slate sebagai netral. Token bernama `brand-*` (di `app.css` dan `AdminPanelProvider`), jadi ganti warna berikutnya cukup di dua tempat itu. Tidak ada permukaan gelap.

| Peran | Warna | Token |
|---|---|---|
| Latar halaman, navbar | `#FFFFFF` | white |
| Section bergantian, footer | `#F5FBF3` | brand-50 |
| Hero, header halaman, strip dan panel keunggulan, ikon layanan | `#EAF6E6` | brand-100 |
| Garis pemisah, border kartu | `#DDF0D5` | brand-200 |
| Hijau merek: tombol primer, band CTA, ikon strip | `#76C457` (hover `#589F3C`) | brand-400 / brand-500 |
| Titik slider, border input, angka langkah | `#589F3C` | brand-500 |
| Link, ikon, tombol WhatsApp, filter aktif | `#366722` (hover `#2A501B`) | brand-700 / brand-800 |
| Judul hero, teks di atas hijau merek | `#15250E` | brand-950 |
| Teks utama | `#1E293B` | slate-800 |

Aturan:
- `#76C457` hanya 2,1:1 di atas putih. Karena itu ia dipakai sebagai isian dengan teks brand-950 (7,5:1), tidak pernah sebagai warna teks, dan tidak dengan teks putih. Teks dan link memakai brand-700 sampai brand-950 (minimal 6:1). Titik slider, border input, dan angka langkah memakai brand-500 (3,1 sampai 3,3:1, elemen non-teks atau teks besar).
- Tanpa gradien dekoratif. Band CTA memakai brand-400 polos.
- Outline fokus brand-700 di semua permukaan (3,1:1 di atas band CTA).
- Tombol WhatsApp memakai brand-700, bukan hijau WhatsApp, agar satu keluarga dengan palet. Merah hanya untuk error.
- Font: Plus Jakarta Sans (heading dan body).
- Sudut membulat (`rounded-2xl` untuk kartu, tombol pill `rounded-full` seperti Figma), tanpa bayangan dekoratif, banyak ruang kosong. Strip keunggulan di bawah hero berbentuk tab trapesium (`hero-tab`, clip-path).
- Motif gelombang laut (`wave-mark`) dihapus bersama tema laut. Identitas dibawa oleh hijau merek dan tab trapesium di bawah hero.
- Hindari pola template AI: blob blur, badge sparkle, mockup kode palsu, label huruf kapital berjarak lebar, panah dekoratif, glow, efek angkat saat hover, garis warna di tepi kiri kartu, dan logo buatan.
- Arah (antislop): ENERGY 2, RHYTHM 2, MOTION 2 atas permintaan pemilik: animasi masuk saat scroll memakai AOS 2.3.4 (`fade-up`, 600 ms, sekali saja), plus slider. AOS dimatikan bila pengguna memilih reduced motion; tanpa JS, `<noscript>` memaksa elemen `[data-aos]` tetap terlihat.
- Slider (hero dan testimoni) memakai satu script generik di `resources/js/app.js` (`data-slider`, `data-slide`, `data-dot`, `data-prev`/`data-next`, `data-toggle`). Slide disembunyikan lewat `aria-hidden`. Testimoni berganti manual saja; slide ditumpuk di satu sel grid agar tinggi tidak melompat.
- Slider hero: berganti tiap 6 detik dan berhenti saat di-hover atau difokus keyboard. Ada tombol jeda/putar dan titik navigasi (titik aktif lebih lebar, bukan hanya beda warna). Tidak berputar jika pengguna memilih reduced motion. Dengan satu gambar, kontrol tidak dirender dan slider tidak aktif.
- Copy hanya memuat fakta yang dikonfirmasi pemilik. Saat ini: konsultasi awal gratis dan balasan maksimal 1x24 jam kerja.
- Panel Filament memakai skala `brand-*` yang sama sebagai warna primer (array shade eksplisit). Shade 600 `#437D2C` hanya ada di panel, untuk tombol berteks putih (5:1).
- Logo: master `public/images/ba-logo.png`; situs dan panel admin memakai `ba-logo-web.png` (dipangkas, 348x96, 24 KB). Favicon dibuat dari tanda "BA". Logo yang di-upload di Pengaturan menggantikan logo bawaan (`Setting::logoUrl()`). Navbar dan footer terang, jadi logo tampil dengan warna aslinya.

## 10. Model Data (ringkas)
```
users          id, client_id (nullable), name, email, password  (+ tabel role Spatie)
client_apps    id, client_id, name, url, notes
feature_requests       id, client_app_id, user_id, type, title, description, budget,
                       status, agreed_price, due_date
feature_request_notes  id, feature_request_id, user_id (null = sistem), body
settings       id, key (unique), value (json)
services       id, title, slug (unique), icon, excerpt, body, image, sort, is_active
projects       id, title, slug (unique), category, client, tech_stack (json), cover,
               gallery (json), body, url, is_featured, sort, is_active
clients        id, name, logo, url, sort
testimonials   id, name, position, company, photo, quote, sort, is_active
team_members   id, name, position, photo, sort, is_active
messages       id, name, email, phone, subject, body, read_at, ip
```
Semua tabel konten memakai `timestamps`.

## 11. Kriteria Selesai
- Semua halaman publik tampil dengan data dari CMS dan responsif.
- Admin bisa mengelola semua modul tanpa error.
- Form kontak tersimpan, tervalidasi, dan terlindungi rate limit + honeypot.
- `DatabaseSeeder` hanya mengisi default yang aman untuk produksi dan tidak menimpa hasil edit CMS. Data fiktif untuk preview lokal ada di `DemoSeeder`, yang menolak berjalan di produksi.
- Feature test lolos untuk: halaman publik (200), form kontak (valid, tidak valid, rate limit), akses admin (tamu ditolak).

## 12. Fase Berikutnya (bukan v1)
Blog/artikel, multi-bahasa (ID/EN), halaman karier, dark mode, Google Analytics.
