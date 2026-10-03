<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use App\Models\Setting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Fictional people, companies, and contact details for local previews only.
 * Run with: php artisan db:seed --class=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            throw new RuntimeException('DemoSeeder contains fictional data and must not run in production.');
        }

        $this->call(DatabaseSeeder::class);

        Setting::saveMany([
            'vision' => 'Contoh visi perusahaan.',
            'mission' => "Contoh misi pertama.\nContoh misi kedua.",
            'values' => "Contoh nilai 1\nContoh nilai 2\nContoh nilai 3",
            'address' => 'Jl. Contoh No. 123, Jakarta',
            'email' => 'hello@basolution.test',
            'phone' => '+62 21 0000 0000',
            'whatsapp' => '6281200000000',
            'map_embed_url' => 'https://maps.google.com/maps?q=Monas%20Jakarta&output=embed',
        ]);

        $projects = [
            ['Portal Layanan Pelanggan', 'Web App', 'PT Contoh Satu', ['Laravel', 'MySQL', 'Tailwind CSS']],
            ['Aplikasi Kasir Mobile', 'Mobile App', 'Toko Contoh', ['Flutter', 'Firebase']],
            ['ERP Manufaktur', 'ERP', 'PT Contoh Dua', ['Laravel', 'Filament', 'PostgreSQL']],
            ['Dashboard Analitik Penjualan', 'Web App', 'PT Contoh Tiga', ['Vue.js', 'Laravel', 'Redis']],
            ['Aplikasi Booking Klinik', 'Mobile App', 'Klinik Contoh', ['React Native', 'Node.js']],
            ['Sistem Informasi Sekolah', 'Web App', 'Sekolah Contoh', ['Laravel', 'MySQL']],
        ];
        foreach ($projects as $i => [$title, $category, $client, $tech]) {
            Project::updateOrCreate(['slug' => Str::slug($title)], [
                'title' => $title,
                'category' => $category,
                'client' => $client,
                'tech_stack' => $tech,
                'excerpt' => "Proyek {$category} untuk {$client}.",
                'body' => "<p>Contoh deskripsi proyek {$title}.</p>",
                'is_featured' => $i < 3,
                'sort' => $i,
            ]);
        }

        foreach (['PT Contoh Satu', 'PT Contoh Dua', 'PT Contoh Tiga', 'Toko Contoh'] as $i => $name) {
            Client::updateOrCreate(['name' => $name], ['sort' => $i]);
        }

        $testimonials = [
            ['Contoh Klien A', 'Direktur', 'PT Contoh Satu', 'Contoh testimoni klien tentang proyek yang dikerjakan.'],
            ['Contoh Klien B', 'Manajer', 'PT Contoh Dua', 'Contoh testimoni kedua dengan panjang teks yang berbeda untuk mengecek tata letak kartu.'],
        ];
        foreach ($testimonials as $i => [$name, $position, $company, $quote]) {
            Testimonial::updateOrCreate(['name' => $name], compact('position', 'company', 'quote') + ['sort' => $i]);
        }

        foreach ([['Contoh Anggota 1', 'CEO'], ['Contoh Anggota 2', 'CTO'], ['Contoh Anggota 3', 'Developer']] as $i => [$name, $position]) {
            TeamMember::updateOrCreate(['name' => $name], ['position' => $position, 'sort' => $i]);
        }
    }
}
