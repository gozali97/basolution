<?php

namespace Tests\Feature;

use App\Filament\Pages\Settings;
use App\Models\Client;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use Database\Seeders\DemoSeeder;
use Filament\Forms\Components\Repeater;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Once;
use Livewire\Livewire;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private const VALID_MESSAGE = ['name' => 'Tamu', 'email' => 'tamu@example.com', 'body' => 'Saya ingin membuat aplikasi.'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_public_pages_render_with_demo_content(): void
    {
        $this->seed(DemoSeeder::class);
        $service = Service::first();
        $project = Project::first();

        foreach (['/', '/tentang', '/layanan', "/layanan/{$service->slug}", '/portofolio', '/portofolio?kategori=ERP',
            '/portofolio?kategori[]=x', "/portofolio/{$project->slug}", '/kontak', '/sitemap.xml'] as $url) {
            $this->get($url)->assertOk();
        }

        $this->get('/')->assertSee($service->title)->assertSee('Proyek pilihan')->assertSee('Kata klien')
            ->assertDontSee('Testimoni berikutnya');
        Testimonial::create(['name' => 'Klien tambahan', 'quote' => 'Ulasan tambahan untuk memeriksa halaman kedua.']);
        $this->get('/')->assertSee('Testimoni berikutnya')->assertSee('aria-label="Testimoni klien"', false)
            ->assertSee('Klien tambahan');
        $this->get('/sitemap.xml')->assertSee(route('projects.show', $project), false);
    }

    public function test_pages_render_and_hide_empty_sections_without_demo_content(): void
    {
        foreach (['/', '/tentang', '/layanan', '/portofolio', '/kontak'] as $url) {
            $this->get($url)->assertOk();
        }

        $this->get('/')->assertSee('Aplikasi web, mobile, dan ERP')->assertDontSee('Proyek pilihan')->assertDontSee('Kata klien')
            ->assertSee('images/ba-logo-web.png')
            ->assertSee('Balasan dalam 1x24 jam kerja')->assertSee('Cara kerja kami')->assertSee('Apakah konsultasi awal berbayar?');

        Setting::saveMany(['strengths' => [], 'steps' => [], 'faqs' => []]);
        Once::flush();
        $this->get('/')->assertOk()->assertDontSee('Yang Anda dapatkan')->assertDontSee('Cara kerja kami')
            ->assertDontSee('Pertanyaan yang sering diajukan');
        $this->get('/tentang')->assertDontSee('Visi')->assertDontSee('Tim kami');
        $this->get('/portofolio')->assertSee('Belum ada proyek yang ditampilkan.');
        $this->get('/kontak')->assertDontSee('Peta lokasi kantor')->assertDontSee('Chat via WhatsApp');
    }

    public function test_reseeding_keeps_cms_edits_and_admin_password(): void
    {
        Setting::saveMany(['hero_title' => 'Judul dari CMS']);
        Service::first()->update(['excerpt' => 'Ringkasan dari CMS']);
        User::first()->update(['password' => 'rahasia-baru']);

        $this->seed();

        $this->assertSame('Judul dari CMS', Setting::where('key', 'hero_title')->value('value'));
        $this->assertSame('Ringkasan dari CMS', Service::first()->excerpt);
        $this->assertTrue(Hash::check('rahasia-baru', User::first()->password));
    }

    public function test_inactive_and_missing_pages_return_404(): void
    {
        $service = Service::first();
        $service->update(['is_active' => false]);

        $this->get("/layanan/{$service->slug}")->assertNotFound();
        $this->get('/tidak-ada')->assertNotFound()->assertSee('Halaman tidak ditemukan');
    }

    public function test_contact_form_saves_valid_message(): void
    {
        foreach ([[], ['website' => ''], ['website' => null]] as $honeypot) {
            $this->post('/kontak', self::VALID_MESSAGE + $honeypot)->assertRedirect()->assertSessionHas('success');
        }

        $this->assertDatabaseHas('messages', ['email' => 'tamu@example.com', 'read_at' => null]);
        $this->assertDatabaseCount('messages', 3);
    }

    public function test_contact_form_rejects_invalid_input_and_honeypot(): void
    {
        $this->post('/kontak', ['name' => '', 'email' => 'bukan-email', 'body' => 'pendek'])
            ->assertSessionHasErrors(['name', 'email', 'body']);
        $this->post('/kontak', self::VALID_MESSAGE + ['website' => 'http://spam.test'])
            ->assertSessionHasErrors('website');

        $this->assertDatabaseCount('messages', 0);
    }

    public function test_contact_form_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/kontak', self::VALID_MESSAGE)->assertRedirect();
        }

        $this->post('/kontak', self::VALID_MESSAGE)->assertTooManyRequests();
        $this->assertDatabaseCount('messages', 5);
    }

    public function test_admin_requires_login_and_all_cms_pages_load(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');

        $this->actingAs(User::first());
        foreach (['/admin', '/admin/services', '/admin/projects', '/admin/clients', '/admin/testimonials',
            '/admin/team-members', '/admin/messages', '/admin/settings'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_hero_images_are_managed_in_settings_and_rotate_on_home(): void
    {
        Storage::fake('public');
        $undoRepeaterFake = Repeater::fake();
        $this->actingAs(User::first());

        Livewire::test(Settings::class)
            ->fillForm(['hero_images' => [
                ['image' => [UploadedFile::fake()->image('kasir.jpg', 1600, 900)], 'alt' => 'Dashboard aplikasi kasir'],
                ['image' => [UploadedFile::fake()->image('erp.jpg', 1600, 900)], 'alt' => 'Laporan stok ERP'],
            ]])
            ->call('save')
            ->assertHasNoFormErrors();
        $undoRepeaterFake();

        $images = Setting::where('key', 'hero_images')->value('value');
        $this->assertCount(2, $images);
        Storage::disk('public')->assertExists($images[0]['image']);

        Once::flush();
        $this->get('/')->assertSee('Dashboard aplikasi kasir')->assertSee('Laporan stok ERP')
            ->assertSee('aria-roledescription="carousel"', false)->assertSee('Jeda slide')
            ->assertSee('hero-gallery agency-image')->assertSee('hero-gallery-slide')
            ->assertSee('Gambar sebelumnya')->assertSee('Gambar berikutnya');

        Setting::saveMany(['hero_images' => [$images[0]]]);
        Once::flush();
        $this->get('/')->assertSee('Dashboard aplikasi kasir')
            ->assertDontSee('aria-roledescription', false)->assertDontSee('Jeda slide')
            ->assertSee('hero-gallery-slide')->assertDontSee('Gambar berikutnya');
    }

    public function test_uploaded_service_image_appears_on_home_listing_and_detail(): void
    {
        $service = Service::first();
        $service->update(['image' => 'services/layanan.jpg']);

        foreach (['/', '/layanan', "/layanan/{$service->slug}"] as $url) {
            $this->get($url)->assertOk()->assertSee(Storage::url($service->image), false);
        }

        $service->update(['image' => null]);
        $this->get('/layanan')->assertOk()->assertDontSee('services/layanan.jpg');
    }

    public function test_single_client_logo_appears_next_to_heading_and_links_to_website(): void
    {
        Client::query()->delete();
        Client::create([
            'name' => 'Samora Group',
            'logo' => 'clients/samora.png',
            'url' => 'https://samoragroup.co.id',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Klien kami')
            ->assertSee('clients/samora.png', false)
            ->assertSee('href="https://samoragroup.co.id"', false)
            ->assertSee('Samora Group')
            ->assertSee('max-h-14', false)
            ->assertSee('border-brand-500', false)
            ->assertDontSee('min-h-28 min-w-36 flex-col items-center justify-center rounded-lg bg-white', false)
            ->assertDontSee('justify-around', false);
    }

    public function test_portfolio_grids_center_one_or_two_items_and_expand_at_three(): void
    {
        $createProject = fn (int $number) => Project::create([
            'title' => "Proyek {$number}",
            'slug' => "proyek-{$number}",
            'category' => 'Web App',
            'body' => '<p>Deskripsi proyek.</p>',
            'is_featured' => true,
        ]);

        $first = $createProject(1);
        foreach (['/', '/portofolio'] as $url) {
            $this->get($url)->assertSee('data-project-count="1"', false)->assertSee('max-w-md', false);
        }

        $createProject(2);
        foreach (['/', '/portofolio'] as $url) {
            $this->get($url)->assertSee('data-project-count="2"', false)->assertSee('max-w-4xl sm:grid-cols-2', false);
        }

        $createProject(3);
        foreach (['/', '/portofolio'] as $url) {
            $this->get($url)->assertSee('data-project-count="3"', false)->assertSee('sm:grid-cols-2 lg:grid-cols-3', false);
        }
        $this->get("/portofolio/{$first->slug}")
            ->assertSee('data-project-count="2"', false)
            ->assertSee('max-w-4xl sm:grid-cols-2', false);
    }
}
