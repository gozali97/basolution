@php
    $siteName = $site['site_name'] ?? config('app.name');
    $listed = fn (string $key, string $field) => collect($site[$key] ?? [])->filter(fn ($item) => filled($item[$field] ?? null))->values();
    $slides = $listed('hero_images', 'image');
    $strengths = $listed('strengths', 'title');
    $steps = $listed('steps', 'title');
    $faqs = $listed('faqs', 'question');
    $aboutImage = $site['intro_image'] ?? $slides->first()['image'] ?? null;
@endphp
<x-layout>
    <section class="bg-white">
        <div @class(['container-x section-space', 'grid items-center gap-12 lg:grid-cols-2 lg:gap-20' => $slides->isNotEmpty()])>
            <div class="max-w-2xl" data-aos="fade-right">
                <p class="mb-5 text-sm font-semibold text-brand-700">{{ $site['tagline'] ?? $siteName }}</p>
                <h1 class="text-4xl font-bold leading-[1.12] tracking-tight text-brand-950 sm:text-5xl xl:text-6xl">{{ $site['hero_title'] ?? '' }}</h1>
                <p class="mt-6 max-w-lg text-base leading-relaxed text-slate-600 sm:text-lg">{{ $site['hero_subtitle'] ?? '' }}</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('contact') }}" class="btn btn-primary">@svg('heroicon-o-phone', 'size-4', ['aria-hidden' => 'true']) Konsultasi gratis</a>
                    <a href="{{ route('projects.index') }}" class="btn btn-ghost">Lihat portofolio</a>
                </div>
                @if ($strengths->isNotEmpty())
                    <ul aria-label="Keunggulan" class="mt-10 grid gap-3 sm:grid-cols-3">
                        @foreach ($strengths->take(3) as $strength)
                            <li class="rounded-lg border border-brand-200 bg-brand-50 p-4">
                                @svg('heroicon-o-'.($strength['icon'] ?? 'check-badge'), 'mb-3 size-6 text-brand-700', ['aria-hidden' => 'true'])
                                <span class="text-sm font-semibold leading-relaxed text-brand-950">{{ $strength['title'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            @if ($slides->isNotEmpty())
                <div class="min-w-0" data-aos="fade-left" data-aos-delay="100"
                     @if ($slides->count() > 1) data-slider data-autoplay="6000" role="region" aria-roledescription="carousel" aria-label="Contoh aplikasi" @endif>
                    <div class="hero-gallery agency-image" data-live @if ($slides->count() > 1) aria-live="off" @endif>
                        @foreach ($slides as $i => $slide)
                            <img src="{{ Storage::url($slide['image']) }}" alt="{{ $slide['alt'] ?? '' }}" decoding="async" data-slide
                                 @if ($i === 0) fetchpriority="high" @else aria-hidden="true" @endif class="hero-gallery-slide">
                        @endforeach
                    </div>
                    @if ($slides->count() > 1)
                        <div class="mt-4 hidden items-center justify-end gap-1" data-controls>
                            <button type="button" class="grid size-11 place-items-center rounded-lg text-brand-700 hover:bg-brand-100" aria-label="Gambar sebelumnya" data-prev>@svg('heroicon-o-arrow-left', 'size-5', ['aria-hidden' => 'true'])</button>
                            @foreach ($slides as $i => $slide)
                                <button type="button" class="group grid size-11 place-items-center rounded-lg hover:bg-brand-100" data-dot aria-label="Tampilkan gambar {{ $i + 1 }} dari {{ $slides->count() }}" @if ($i === 0) aria-current="true" @endif><span class="h-2 w-2 rounded-full bg-brand-300 group-aria-current:w-5 group-aria-current:bg-brand-700"></span></button>
                            @endforeach
                            <button type="button" class="grid size-11 place-items-center rounded-lg text-brand-700 hover:bg-brand-100" aria-label="Gambar berikutnya" data-next>@svg('heroicon-o-arrow-right', 'size-5', ['aria-hidden' => 'true'])</button>
                            <button type="button" class="grid size-11 place-items-center rounded-lg text-brand-700 hover:bg-brand-100" aria-label="Jeda slide" data-toggle>
                                @svg('heroicon-o-pause', 'size-5', ['aria-hidden' => 'true', 'data-icon' => 'pause'])
                                @svg('heroicon-o-play', 'size-5', ['aria-hidden' => 'true', 'data-icon' => 'play', 'hidden' => true])
                            </button>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </section>

    @if ($clients->isNotEmpty())
        <section class="bg-brand-100 py-8" aria-labelledby="klien">
            <div class="container-x flex flex-wrap items-center gap-x-10 gap-y-6">
                <h2 id="klien" class="shrink-0 text-xl font-bold text-brand-950" data-aos="fade-up">Klien kami</h2>
                <ul class="flex flex-wrap items-center gap-4 sm:gap-6">
                    @foreach ($clients as $client)
                        <li data-aos="fade-up" data-aos-delay="{{ ($loop->index % 4) * 100 }}">
                            @php($tag = filled($client->url) ? 'a' : 'span')
                            <{{ $tag }} @if (filled($client->url)) href="{{ $client->url }}" target="_blank" rel="noopener" @endif
                                class="group inline-flex min-w-36 flex-col items-center text-center">
                                <span class="inline-flex h-20 min-w-32 items-center justify-center rounded-lg border border-brand-500 px-4 py-2 transition-colors group-hover:border-brand-700">
                                    @if ($client->logo)
                                        <img src="{{ Storage::url($client->logo) }}" alt="{{ $client->name }}" loading="lazy" class="max-h-14 w-auto max-w-40 object-contain">
                                    @else
                                        @svg('heroicon-o-building-office-2', 'size-12 text-brand-300', ['aria-hidden' => 'true'])
                                    @endif
                                </span>
                                <span class="mt-3 text-sm font-semibold text-brand-950 group-hover:text-brand-700">{{ $client->name }}</span>
                            </{{ $tag }}>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($services->isNotEmpty())
        <section class="section-space">
            <div class="container-x">
                <div class="mb-12 text-center" data-aos="fade-up"><p class="mb-3 text-sm font-medium text-slate-500">Dari kebutuhan hingga aplikasi berjalan</p><x-heading class="mx-auto">Layanan kami</x-heading></div>
                <ul class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($services as $i => $service)<li data-aos="fade-up" data-aos-delay="{{ ($i % 3) * 100 }}"><x-service-card :service="$service" /></li>@endforeach
                </ul>
                <p class="mt-10 text-center" data-aos="fade-up"><a href="{{ route('services.index') }}" class="btn btn-ghost">Semua layanan @svg('heroicon-o-arrow-right', 'size-4', ['aria-hidden' => 'true'])</a></p>
            </div>
        </section>
    @endif

    @if ($projects->isNotEmpty())
        <section class="section-space bg-brand-50">
            <div class="container-x">
                <div class="mb-12 text-center" data-aos="fade-up"><p class="mb-3 text-sm font-medium text-brand-700">Portofolio</p><x-heading class="mx-auto">Proyek pilihan</x-heading><p class="mx-auto mt-4 max-w-xl text-slate-600">Beberapa proyek yang sudah kami kerjakan untuk klien.</p></div>
                <div data-project-count="{{ $projects->count() }}" @class([
                    'portfolio-grid grid gap-6',
                    'mx-auto max-w-md' => $projects->count() === 1,
                    'mx-auto max-w-4xl sm:grid-cols-2' => $projects->count() === 2,
                    'sm:grid-cols-2 lg:grid-cols-3' => $projects->count() >= 3,
                ])>
                    @foreach ($projects as $i => $project)<div data-aos="fade-up" data-aos-delay="{{ ($i % 3) * 100 }}"><x-project-card :project="$project" /></div>@endforeach
                </div>
                <p class="mt-10 text-center" data-aos="fade-up"><a href="{{ route('projects.index') }}" class="btn btn-primary">Lihat semua proyek @svg('heroicon-o-arrow-right', 'size-4', ['aria-hidden' => 'true'])</a></p>
            </div>
        </section>
    @endif

    <section class="section-space">
        <div class="container-x">
            <div class="mb-12 text-center" data-aos="fade-up"><p class="mb-3 text-sm font-medium text-slate-500">Tentang {{ $siteName }}</p><x-heading class="mx-auto">Dari ide menjadi solusi bisnis</x-heading></div>
            <div @class(['grid items-center gap-10 lg:gap-16', 'lg:grid-cols-2' => filled($aboutImage)])>
                @if (filled($aboutImage))<img src="{{ Storage::url($aboutImage) }}" alt="" loading="lazy" class="agency-image aspect-[4/3] w-full object-cover" data-aos="fade-right">@endif
                <div class="max-w-2xl" data-aos="fade-left" data-aos-delay="100">
                    <p class="text-lg leading-relaxed text-slate-600">{{ $site['about'] ?? '' }}</p>
                    @foreach (['vision' => 'Visi', 'mission' => 'Misi'] as $key => $label)
                        @if (filled($site[$key] ?? null))
                            <div class="mt-7 flex gap-4">@svg($key === 'vision' ? 'heroicon-o-eye' : 'heroicon-o-flag', 'size-9 shrink-0 text-brand-700', ['aria-hidden' => 'true'])<div><h3 class="text-xl font-bold text-brand-950">{{ $label }}</h3><p class="mt-2 whitespace-pre-line leading-relaxed text-slate-600">{{ $site[$key] }}</p></div></div>
                        @endif
                    @endforeach
                    <a href="{{ route('about') }}" class="btn btn-primary mt-8">Selengkapnya tentang kami @svg('heroicon-o-arrow-right', 'size-4', ['aria-hidden' => 'true'])</a>
                </div>
            </div>
        </div>
    </section>

    @if ($testimonials->isNotEmpty())
        @php($groups = $testimonials->chunk(2)->values())
        <section class="section-space bg-brand-50">
            <div class="container-x">
                <div class="mb-12 text-center" data-aos="fade-up"><p class="mb-3 text-sm font-medium text-slate-500">Testimoni</p><x-heading class="mx-auto">Kata klien</x-heading></div>
                <div @if ($groups->count() > 1) data-slider role="region" aria-roledescription="carousel" aria-label="Testimoni klien" @endif>
                    <div class="grid" data-live aria-live="polite">
                        @foreach ($groups as $i => $group)
                            <div data-slide @if ($i > 0) aria-hidden="true" @endif class="col-start-1 row-start-1 grid gap-6 md:grid-cols-2 transition-[opacity,visibility] duration-500 aria-hidden:invisible aria-hidden:opacity-0 motion-reduce:transition-none">
                                @foreach ($group as $t)
                                    <figure class="flex flex-col rounded-xl border border-brand-100 bg-white p-7 sm:p-9" data-aos="fade-up">
                                        <span class="text-5xl leading-none text-brand-700" aria-hidden="true">&ldquo;</span>
                                        <blockquote class="mt-3 flex-1 leading-relaxed text-slate-600">{{ $t->quote }}</blockquote>
                                        <figcaption class="mt-7 flex items-center gap-3">
                                            @if ($t->photo)<img src="{{ Storage::url($t->photo) }}" alt="" loading="lazy" class="size-12 rounded-full object-cover">@endif
                                            <span><span class="block font-bold text-brand-950">{{ $t->name }}</span><span class="mt-1 block text-sm text-slate-500">{{ collect([$t->position, $t->company])->filter()->join(', ') }}</span></span>
                                        </figcaption>
                                    </figure>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    @if ($groups->count() > 1)
                        <div class="mt-8 hidden items-center justify-center gap-1" data-controls>
                            <button type="button" class="grid size-11 place-items-center rounded-lg text-brand-700 hover:bg-brand-100" aria-label="Testimoni sebelumnya" data-prev>@svg('heroicon-o-arrow-left', 'size-5', ['aria-hidden' => 'true'])</button>
                            @foreach ($groups as $i => $group)<button type="button" class="group grid size-11 place-items-center rounded-lg" data-dot aria-label="Tampilkan kelompok testimoni {{ $i + 1 }}" @if ($i === 0) aria-current="true" @endif><span class="h-2 w-2 rounded-full bg-brand-300 group-aria-current:w-5 group-aria-current:bg-brand-700"></span></button>@endforeach
                            <button type="button" class="grid size-11 place-items-center rounded-lg text-brand-700 hover:bg-brand-100" aria-label="Testimoni berikutnya" data-next>@svg('heroicon-o-arrow-right', 'size-5', ['aria-hidden' => 'true'])</button>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if ($strengths->isNotEmpty())
        <section class="section-space">
            <div class="container-x">
                <x-heading data-aos="fade-up">Yang Anda dapatkan dari {{ $siteName }}</x-heading>
                <ul class="mt-10 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($strengths as $i => $strength)<li class="flex gap-4" data-aos="fade-up" data-aos-delay="{{ ($i % 3) * 100 }}">@svg('heroicon-o-'.($strength['icon'] ?? 'check-badge'), 'size-8 shrink-0 text-brand-700', ['aria-hidden' => 'true'])<div><h3 class="font-bold text-brand-950">{{ $strength['title'] }}</h3>@if (filled($strength['text'] ?? null))<p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $strength['text'] }}</p>@endif</div></li>@endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($steps->isNotEmpty())
        <section class="section-space bg-brand-50">
            <div @class(['container-x grid items-center gap-12 lg:gap-16', 'lg:grid-cols-2' => filled($site['steps_image'] ?? null)])>
                @if (filled($site['steps_image'] ?? null))<img src="{{ Storage::url($site['steps_image']) }}" alt="" loading="lazy" class="agency-image aspect-[4/3] w-full object-cover" data-aos="fade-right">@endif
                <div><x-heading data-aos="fade-left">Cara kerja kami</x-heading>
                    <ol class="mt-8 divide-y divide-brand-200">
                        @foreach ($steps as $i => $step)<li class="flex gap-5 py-6" data-aos="fade-left" data-aos-delay="{{ $i * 100 }}"><span class="text-3xl font-semibold text-brand-700" aria-hidden="true">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span><div><h3 class="text-lg font-bold text-brand-950">{{ $step['title'] }}</h3>@if (filled($step['text'] ?? null))<p class="mt-2 leading-relaxed text-slate-600">{{ $step['text'] }}</p>@endif</div></li>@endforeach
                    </ol>
                </div>
            </div>
        </section>
    @endif

    @if ($faqs->isNotEmpty())
        <section class="section-space">
            <div class="container-x grid gap-10 lg:grid-cols-3">
                <div data-aos="fade-right"><x-heading>Pertanyaan yang sering diajukan</x-heading><p class="mt-4 text-slate-600">Masih ada pertanyaan? <a href="{{ route('contact') }}" class="inline-block py-2 font-semibold text-brand-700 underline underline-offset-4">Hubungi kami</a></p></div>
                <div class="divide-y divide-brand-200 lg:col-span-2">
                    @foreach ($faqs as $i => $faq)<details class="group py-1" data-aos="fade-up" data-aos-delay="{{ min($i, 3) * 100 }}" @if ($i === 0) open @endif><summary class="flex min-h-12 cursor-pointer list-none items-center justify-between gap-4 py-5 font-semibold text-brand-950 [&::-webkit-details-marker]:hidden">{{ $faq['question'] }} @svg('heroicon-o-plus', 'size-5 shrink-0 text-brand-700 group-open:rotate-45 motion-reduce:transition-none', ['aria-hidden' => 'true'])</summary><p class="max-w-2xl pb-6 leading-relaxed text-slate-600">{{ $faq['answer'] ?? '' }}</p></details>@endforeach
                </div>
            </div>
        </section>
    @endif
</x-layout>
