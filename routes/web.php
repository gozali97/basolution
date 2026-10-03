<?php

use App\Models\Client;
use App\Models\Message;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Support\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('pages.home', [
    'services' => Service::active()->limit(6)->get(),
    'projects' => Project::active()->where('is_featured', true)->limit(6)->get(),
    'clients' => Client::orderBy('sort')->get(),
    'testimonials' => Testimonial::active()->limit(6)->get(),
]))->name('home');

Route::get('/tentang', fn () => view('pages.about', [
    'team' => TeamMember::active()->get(),
]))->name('about');

Route::get('/layanan', fn () => view('pages.services', [
    'services' => Service::active()->get(),
]))->name('services.index');

Route::get('/layanan/{service}', function (Service $service) {
    abort_unless($service->is_active, 404);

    return view('pages.service', [
        'service' => $service,
        'others' => Service::active()->whereKeyNot($service->id)->get(),
    ]);
})->name('services.show');

// ponytail: no pagination; a company profile rarely has more than a few dozen projects. Swap get() for paginate(12) when it does.
Route::get('/portofolio', function (Request $request) {
    $category = is_string($request->query('kategori')) ? $request->query('kategori') : null;

    return view('pages.projects', [
        'category' => $category,
        'categories' => Project::where('is_active', true)->distinct()->orderBy('category')->pluck('category'),
        'projects' => Project::active()->when($category, fn ($q) => $q->where('category', $category))->get(),
    ]);
})->name('projects.index');

Route::get('/portofolio/{project}', function (Project $project) {
    abort_unless($project->is_active, 404);

    return view('pages.project', [
        'project' => $project,
        'related' => Project::active()->where('category', $project->category)->whereKeyNot($project->id)->limit(3)->get(),
    ]);
})->name('projects.show');

Route::view('/kontak', 'pages.contact')->name('contact');

Route::post('/kontak', function (Request $request) {
    $data = $request->validate([
        'name' => ['required', 'string', 'max:100'],
        'email' => ['required', 'email', 'max:150'],
        'phone' => ['nullable', 'string', 'max:30'],
        'subject' => ['nullable', 'string', 'max:150'],
        'body' => ['required', 'string', 'min:10', 'max:5000'],
        'website' => ['prohibited'], // honeypot: hidden field, only bots fill it
    ], [
        'required' => ':attribute wajib diisi.',
        'email' => 'Format email tidak valid.',
        'max' => ':attribute maksimal :max karakter.',
        'min' => ':attribute minimal :min karakter.',
        'prohibited' => 'Permintaan tidak valid.',
    ], [
        'name' => 'Nama', 'email' => 'Email', 'phone' => 'Telepon', 'subject' => 'Subjek', 'body' => 'Pesan',
    ]);

    unset($data['website']);
    $message = Message::create($data + ['ip' => $request->ip()]);

    if ($to = Setting::values()['email'] ?? null) {
        // The message is already saved, so a mail failure must not turn into a 500 for the visitor.
        rescue(fn () => Mail::raw(
            "Nama: {$message->name}\nEmail: {$message->email}\nTelepon: {$message->phone}\n\n{$message->body}",
            fn ($mail) => $mail->to($to)->replyTo($message->email)->subject('[Website] '.($message->subject ?: 'Pesan baru')),
        ));
    }

    return back()->with('success', 'Terima kasih, pesan Anda sudah kami terima. Kami akan segera menghubungi Anda.');
})->middleware('throttle:5,1')->name('contact.send');

Route::get('/sitemap.xml', fn () => response()->view('sitemap', [
    'urls' => collect(['home', 'about', 'services.index', 'projects.index', 'contact'])->map(fn ($r) => ['loc' => Seo::url(route($r, [], false))])
        ->merge(Service::active()->get()->map(fn ($s) => ['loc' => Seo::url(route('services.show', $s, false)), 'lastmod' => $s->updated_at?->toAtomString()]))
        ->merge(Project::active()->get()->map(fn ($p) => ['loc' => Seo::url(route('projects.show', $p, false)), 'lastmod' => $p->updated_at?->toAtomString()])),
])->header('Content-Type', 'application/xml'))->name('sitemap');
