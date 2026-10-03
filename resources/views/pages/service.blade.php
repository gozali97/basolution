<x-layout :title="$service->title" :description="$service->excerpt" :image="$service->image ? Storage::url($service->image) : null"
          eyebrow="Layanan" :heading="$service->title" :lead="$service->excerpt">
    <section class="section-space">
        <div class="container-x grid gap-12 lg:grid-cols-3">
            <article class="lg:col-span-2">
                @if ($service->image)
                    <img src="{{ Storage::url($service->image) }}" alt="{{ $service->title }}" class="agency-image mb-10 aspect-[4/3] w-full object-cover" data-aos="fade-up">
                @endif
                <div class="prose-content" data-aos="fade-up">
                    {!! \Filament\Forms\Components\RichEditor\RichContentRenderer::make($service->body)->toHtml() !!}
                </div>
            </article>
            <aside class="space-y-6">
                <div class="rounded-xl border border-brand-200 bg-brand-50 p-7 text-brand-950" data-aos="fade-up">
                    <h2 class="text-xl font-bold">Tertarik dengan layanan ini?</h2>
                    <p class="mt-2 text-slate-600">Konsultasi awal gratis. Kami membalas dalam 1x24 jam kerja.</p>
                    <a href="{{ route('contact') }}" class="btn btn-primary mt-6 w-full">Kirim pertanyaan</a>
                </div>
                @if ($others->isNotEmpty())
                    <nav aria-label="Layanan lainnya" class="rounded-xl border border-brand-200 bg-white p-7" data-aos="fade-up">
                        <h2 class="font-bold text-slate-900">Layanan lainnya</h2>
                        <ul class="mt-4 space-y-2">
                            @foreach ($others as $other)
                                <li><a href="{{ route('services.show', $other) }}" class="inline-block py-2 text-slate-700 hover:text-brand-700">{{ $other->title }}</a></li>
                            @endforeach
                        </ul>
                    </nav>
                @endif
            </aside>
        </div>
    </section>
</x-layout>
