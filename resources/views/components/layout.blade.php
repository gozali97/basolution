@props(['title' => null, 'description' => null, 'image' => null, 'heading' => null, 'eyebrow' => null, 'lead' => null, 'cta' => true])
@php
    $siteName = $site['site_name'] ?? config('app.name');
    $logo = \App\Models\Setting::logoUrl();
    $pageTitle = $title ? "{$title} | {$siteName}" : trim($siteName.' - '.($site['tagline'] ?? ''), ' -');
    $description ??= $site['meta_description'] ?? '';
    $heroImage = data_get($site, 'hero_images.0.image');
    $image ??= filled($heroImage) ? Storage::url($heroImage) : null;
    $nav = ['home' => 'Beranda', 'about' => 'Tentang', 'services.index' => 'Layanan', 'projects.index' => 'Portofolio', 'contact' => 'Kontak'];
    $isActive = fn (string $route) => request()->routeIs($route === 'home' ? 'home' : Str::before($route, '.').'*');
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=ba">
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if ($image)
        <meta property="og:image" content="{{ $image }}">
    @endif
    <meta name="theme-color" content="#ffffff">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- AOS hides [data-aos] in CSS; without JS nothing would ever reveal it. --}}
    <noscript><style>[data-aos]{opacity:1!important;transform:none!important}</style></noscript>
</head>
<body class="bg-white font-sans text-slate-800 antialiased">
<a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:font-semibold">Langsung ke konten</a>

<header class="sticky top-0 z-40 border-b border-brand-100 bg-white">
    <div class="container-x flex h-20 items-center justify-between gap-4 lg:h-24">
        <a href="{{ route('home') }}" class="block shrink-0">
            <img src="{{ $logo }}" alt="{{ $siteName }}" class="h-10 w-auto">
        </a>

        <nav aria-label="Navigasi utama" class="hidden lg:block">
            <ul class="flex items-center gap-1 text-sm font-semibold">
                @foreach ($nav as $route => $label)
                    <li>
                        <a href="{{ route($route) }}" @class(['block border-b-2 px-3 py-3 transition-colors lg:px-4', 'border-brand-700 text-brand-700' => $isActive($route), 'border-transparent text-slate-600 hover:border-brand-300 hover:text-brand-900' => ! $isActive($route)]) @if ($isActive($route)) aria-current="page" @endif>{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="hidden lg:block">
            <a href="{{ route('contact') }}" class="btn btn-ghost">@svg('heroicon-o-phone', 'size-4', ['aria-hidden' => 'true']) Konsultasi gratis</a>
        </div>

        {{-- ponytail: <details> gives a JS-free mobile menu; swap for Alpine if it needs click-outside-to-close. --}}
        <details class="group relative lg:hidden">
            <summary class="flex h-11 cursor-pointer list-none items-center gap-2 rounded-lg border border-brand-300 px-3 text-slate-800 [&::-webkit-details-marker]:hidden">
                @svg('heroicon-o-bars-3', 'size-5 group-open:hidden', ['aria-hidden' => 'true'])
                @svg('heroicon-o-x-mark', 'hidden size-5 group-open:block', ['aria-hidden' => 'true'])
                <span class="text-sm font-semibold">Menu</span>
            </summary>
            <nav aria-label="Navigasi mobile" class="absolute right-0 mt-3 w-64 rounded-xl bg-white p-2 shadow-lg ring-1 ring-brand-200">
                <ul class="font-semibold">
                    @foreach ($nav as $route => $label)
                        <li>
                            <a href="{{ route($route) }}" @class(['block rounded-lg px-4 py-3', 'bg-brand-100 text-brand-900' => $isActive($route), 'text-slate-700 hover:bg-brand-50' => ! $isActive($route)]) @if ($isActive($route)) aria-current="page" @endif>{{ $label }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </details>
    </div>
</header>

{{-- clip (not hidden) stops fade-left/right from adding a horizontal scrollbar without breaking position: sticky. --}}
<main id="main" class="overflow-x-clip">
    @if ($heading)
        <section class="border-b border-brand-100 bg-brand-50 py-14 sm:py-20">
            <div class="container-x" data-aos="fade-up">
                <nav aria-label="Breadcrumb" class="mb-7 flex flex-wrap items-center gap-3 text-sm text-slate-500"><a href="{{ route('home') }}" class="py-1 hover:text-brand-700">Beranda</a><span aria-hidden="true">/</span><span aria-current="page" class="text-brand-700">{{ $title ?? $heading }}</span></nav>
                @if ($eyebrow)
                    <p class="text-sm font-semibold text-brand-700">{{ $eyebrow }}</p>
                @endif
                <h1 class="mt-3 max-w-4xl text-4xl font-bold leading-tight tracking-tight text-brand-950 sm:text-5xl lg:text-6xl">{{ $heading }}</h1>
                @if ($lead)
                    <p class="mt-4 max-w-2xl text-lg text-slate-700">{{ $lead }}</p>
                @endif
            </div>
        </section>
    @endif

    {{ $slot }}

    @if ($cta)
        <section class="bg-white py-12 sm:py-16">
            <div class="container-x">
            <div @class(['overflow-hidden rounded-2xl bg-brand-100 px-6 py-12 sm:px-12 sm:py-16', 'grid items-center gap-10 lg:grid-cols-2' => filled($site['cta_image'] ?? null)])>
                <div class="max-w-2xl" data-aos="fade-right">
                    <h2 class="text-3xl font-bold leading-tight text-brand-950 sm:text-4xl lg:text-5xl">Ceritakan proyek Anda</h2>
                    <ul class="mt-5 flex flex-wrap gap-x-6 gap-y-2 font-medium">
                        @foreach (['Konsultasi awal gratis', 'Balasan dalam 1x24 jam kerja'] as $point)
                            <li class="flex items-center gap-2">@svg('heroicon-o-check', 'size-4', ['aria-hidden' => 'true']) {{ $point }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('contact') }}" class="btn btn-primary mt-8">Isi formulir kontak @svg('heroicon-o-arrow-right', 'size-4', ['aria-hidden' => 'true'])</a>
                </div>
                @if (filled($site['cta_image'] ?? null))
                    <img src="{{ Storage::url($site['cta_image']) }}" alt="" loading="lazy" class="mx-auto max-h-96 w-auto" data-aos="fade-left" data-aos-delay="100">
                @endif
            </div>
            </div>
        </section>
    @endif
</main>

<footer class="on-dark bg-brand-950 text-brand-100">
    <div class="container-x grid gap-10 py-16 md:grid-cols-4">
        <div class="md:col-span-2" data-aos="fade-up">
            <img src="{{ $logo }}" alt="{{ $siteName }}" loading="lazy" class="h-10 w-auto brightness-0 invert">
            <p class="mt-3 max-w-sm">{{ $site['tagline'] ?? '' }}</p>
            <div class="mt-6 flex gap-3">
                @foreach (['instagram' => 'Instagram', 'linkedin' => 'LinkedIn'] as $key => $label)
                    @if (filled($site[$key] ?? null))
                        <a href="{{ $site[$key] }}" target="_blank" rel="noopener" class="rounded-lg border border-brand-700 px-4 py-3 text-sm font-semibold transition-colors hover:border-brand-300 hover:text-white">{{ $label }}</a>
                    @endif
                @endforeach
            </div>
        </div>
        <div data-aos="fade-up" data-aos-delay="75">
            <h2 class="text-lg font-semibold text-white">Menu</h2>
            <ul class="mt-2">
                @foreach ($nav as $route => $label)
                    <li><a href="{{ route($route) }}" class="inline-block py-2.5 underline-offset-4 hover:text-white hover:underline">{{ $label }}</a></li>
                @endforeach
            </ul>
        </div>
        <div data-aos="fade-up" data-aos-delay="150">
            <h2 class="text-lg font-semibold text-white">Kontak</h2>
            <ul class="mt-2 break-words text-sm">
                @if (filled($site['address'] ?? null))
                    <li class="py-2.5">{{ $site['address'] }}</li>
                @endif
                @if (filled($site['email'] ?? null))
                    <li><a href="mailto:{{ $site['email'] }}" class="inline-block py-2.5 underline-offset-4 hover:text-white hover:underline">{{ $site['email'] }}</a></li>
                @endif
                @if (filled($site['phone'] ?? null))
                    <li><a href="tel:{{ preg_replace('/[^\d+]/', '', $site['phone']) }}" class="inline-block py-2.5 underline-offset-4 hover:text-white hover:underline">{{ $site['phone'] }}</a></li>
                @endif
            </ul>
        </div>
    </div>
    <div class="border-t border-brand-800">
        <p class="container-x pb-24 pt-6 text-center text-sm text-brand-200 sm:pb-6">&copy; {{ date('Y') }} {{ $siteName }}. Semua hak dilindungi.</p>
    </div>
</footer>

@if (filled($site['whatsapp'] ?? null))
    <a href="https://wa.me/{{ $site['whatsapp'] }}" target="_blank" rel="noopener" aria-label="Chat via WhatsApp"
       class="fixed bottom-5 right-5 z-40 grid size-14 place-items-center rounded-full bg-brand-700 text-white shadow-xl transition hover:bg-brand-800">
        @svg('heroicon-o-chat-bubble-oval-left-ellipsis', 'size-7', ['aria-hidden' => 'true'])
    </a>
@endif
</body>
</html>
