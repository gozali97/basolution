<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Production-safe defaults. Uses firstOrCreate so re-running never overwrites
 * what the admin has already changed in the CMS. Sample content lives in DemoSeeder.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles are created by the project-tables migration.
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@basolution.test')],
            ['name' => 'Admin', 'password' => env('ADMIN_PASSWORD', 'password')],
        )->assignRole('admin');

        $settings = [
            'site_name' => 'BASolution',
            'tagline' => 'Software house untuk aplikasi web, mobile, dan ERP',
            'hero_title' => 'Aplikasi web, mobile, dan ERP untuk bisnis Anda',
            'hero_subtitle' => 'BASolution mengerjakan proyek dari analisis kebutuhan sampai pemeliharaan setelah aplikasi berjalan.',
            'about' => 'BASolution adalah software house yang membangun aplikasi web, aplikasi mobile, dan sistem ERP untuk perusahaan.',
            'meta_description' => 'BASolution, software house yang membangun aplikasi web, mobile, dan sistem ERP untuk perusahaan.',
            // Only owner-confirmed claims: free first consultation and a reply within 1x24 working hours.
            'strengths' => [
                ['icon' => 'chat-bubble-left-right', 'title' => 'Konsultasi awal gratis', 'text' => 'Diskusikan kebutuhan aplikasi Anda lebih dulu, tanpa biaya.'],
                ['icon' => 'clock', 'title' => 'Balasan dalam 1x24 jam kerja', 'text' => 'Pesan dari formulir kontak atau WhatsApp kami balas paling lambat 1x24 jam kerja.'],
            ],
            'steps' => [
                ['title' => 'Ceritakan kebutuhan Anda', 'text' => 'Kirim gambaran singkat lewat formulir kontak atau WhatsApp. Kami membalas dalam 1x24 jam kerja.'],
                ['title' => 'Konsultasi awal gratis', 'text' => 'Kami membahas tujuan, fitur, dan pilihan teknologi bersama Anda.'],
                ['title' => 'Pengerjaan sampai pemeliharaan', 'text' => 'Kami mengerjakan proyek dari analisis kebutuhan sampai pemeliharaan setelah aplikasi berjalan.'],
            ],
            'faqs' => [
                ['question' => 'Apakah konsultasi awal berbayar?', 'answer' => 'Tidak. Konsultasi awal gratis, jadi Anda bisa membahas kebutuhan sebelum memutuskan apa pun.'],
                ['question' => 'Berapa lama pesan saya dibalas?', 'answer' => 'Kami membalas setiap pesan dari formulir kontak atau WhatsApp dalam 1x24 jam kerja.'],
                ['question' => 'Layanan apa saja yang tersedia?', 'answer' => 'Pengembangan aplikasi web, aplikasi mobile, sistem ERP, UI/UX design, cloud & DevOps, dan konsultasi IT.'],
            ],
        ];
        foreach ($settings as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
        cache()->forget('settings');

        $services = [
            ['Pengembangan Aplikasi Web', 'code-bracket', 'Portal pelanggan, dashboard, dan sistem internal berbasis web.'],
            ['Aplikasi Mobile', 'device-phone-mobile', 'Aplikasi Android dan iOS untuk pelanggan atau tim internal.'],
            ['Sistem ERP & Bisnis', 'building-office-2', 'Sistem untuk keuangan, inventori, dan HR dalam satu aplikasi.'],
            ['UI/UX Design', 'paint-brush', 'Desain antarmuka dan alur pengguna sebelum aplikasi dibangun.'],
            ['Cloud & DevOps', 'cloud', 'Setup server, CI/CD, dan pemantauan aplikasi.'],
            ['Konsultasi IT', 'light-bulb', 'Membantu memilih teknologi dan rencana proyek sebelum anggaran dikeluarkan.'],
        ];
        foreach ($services as $i => [$title, $icon, $excerpt]) {
            Service::firstOrCreate(['slug' => Str::slug($title)], [
                'title' => $title,
                'icon' => $icon,
                'excerpt' => $excerpt,
                'body' => "<p>{$excerpt}</p>",
                'sort' => $i,
            ]);
        }
    }
}
