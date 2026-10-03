@php
    $siteName = $site['site_name'] ?? config('app.name');
    $lines = fn (?string $text) => array_values(array_filter(array_map('trim', preg_split('/\R/', $text ?? ''))));
    $mission = $lines($site['mission'] ?? null);
    $values = $lines($site['values'] ?? null);
    $hasVisionMission = filled($site['vision'] ?? null) || $mission;
    $aboutImage = $site['intro_image'] ?? data_get($site, 'hero_images.0.image');
@endphp
<x-layout title="Tentang Kami" :heading="'Tentang '.$siteName" :lead="$site['tagline'] ?? null">
    <section class="section-space">
        <div @class(['container-x grid items-center gap-12 lg:gap-16', 'lg:grid-cols-2' => filled($aboutImage)])>
            @if (filled($aboutImage))<img src="{{ Storage::url($aboutImage) }}" alt="" loading="lazy" class="agency-image aspect-[4/3] w-full object-cover" data-aos="fade-up">@endif
            <div class="max-w-2xl" data-aos="fade-up">
                <x-heading>Siapa kami</x-heading>
                <p class="mt-6 text-lg leading-relaxed text-slate-700">{{ $site['about'] ?? '' }}</p>
            </div>
        </div>
    </section>
            @if ($hasVisionMission)
        <section class="section-space bg-brand-50">
                <div class="container-x grid gap-8 md:grid-cols-2">
                    @if (filled($site['vision'] ?? null))
                        <div class="rounded-xl border border-brand-200 bg-white p-8 text-brand-950" data-aos="fade-up">
                            @svg('heroicon-o-eye', 'mb-6 size-10 text-brand-700', ['aria-hidden' => 'true'])
                            <h2 class="text-2xl font-bold text-brand-950">Visi</h2>
                            <p class="mt-3 text-xl font-semibold">{{ $site['vision'] }}</p>
                        </div>
                    @endif
                    @if ($mission)
                        <div class="rounded-xl border border-brand-200 bg-white p-8" data-aos="fade-up">
                            @svg('heroicon-o-flag', 'mb-6 size-10 text-brand-700', ['aria-hidden' => 'true'])
                            <h2 class="text-2xl font-bold text-brand-950">Misi</h2>
                            <ul class="mt-4 list-disc space-y-2 pl-5 marker:text-brand-500">
                                @foreach ($mission as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
        </section>
            @endif

    @if ($values)
        <section class="section-space">
            <div class="container-x">
                <x-heading data-aos="fade-up">Nilai kami</x-heading>
                <ul class="mt-8 flex flex-wrap gap-x-10 gap-y-3 text-lg font-bold text-slate-900">
                    @foreach ($values as $value)
                        <li class="flex items-center gap-3" data-aos="fade-up">@svg('heroicon-o-check-circle', 'size-6 text-brand-700', ['aria-hidden' => 'true']) {{ $value }}</li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($team->isNotEmpty())
        <section class="section-space bg-brand-50">
            <div class="container-x">
                <x-heading class="mx-auto text-center" data-aos="fade-up">Tim kami</x-heading>
                <ul class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($team as $member)
                        <li class="overflow-hidden rounded-lg border border-brand-200 bg-white" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 4) * 100 }}">
                            @if ($member->photo)
                                <img src="{{ Storage::url($member->photo) }}" alt="Foto {{ $member->name }}" loading="lazy" class="aspect-square w-full object-cover">
                            @endif
                            <div class="p-6"><p class="font-bold text-brand-950">{{ $member->name }}</p><p class="mt-1 text-sm text-slate-600">{{ $member->position }}</p></div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif
</x-layout>
