<x-layout :title="$project->title" :description="$project->excerpt" :image="$project->cover ? Storage::url($project->cover) : null"
          :eyebrow="$project->category" :heading="$project->title" :lead="$project->excerpt">
    <section class="section-space">
        <div class="container-x grid gap-12 lg:grid-cols-3">
            <article class="space-y-10 lg:col-span-2">
                @if ($project->cover)
                    <img src="{{ Storage::url($project->cover) }}" alt="{{ $project->title }}" class="agency-image aspect-[4/3] w-full object-cover" data-aos="fade-up">
                @endif
                <div class="prose-content" data-aos="fade-up">
                    {!! \Filament\Forms\Components\RichEditor\RichContentRenderer::make($project->body)->toHtml() !!}
                </div>
                @if (filled($project->gallery))
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900" data-aos="fade-up">Galeri</h2>
                        <div class="mt-6 grid gap-4 sm:grid-cols-2">
                            @foreach ($project->gallery as $i => $path)
                                <img src="{{ Storage::url($path) }}" alt="{{ $project->title }}, gambar {{ $i + 1 }}" loading="lazy" class="aspect-[16/10] w-full rounded-2xl object-cover" data-aos="fade-up">
                            @endforeach
                        </div>
                    </div>
                @endif
            </article>
            <aside>
                <dl class="sticky top-28 space-y-6 rounded-xl border border-brand-200 bg-brand-50 p-7" data-aos="fade-up">
                    @if ($project->client)
                        <div><dt class="text-sm font-semibold text-slate-600">Klien</dt><dd class="mt-1 font-bold text-slate-900">{{ $project->client }}</dd></div>
                    @endif
                    <div><dt class="text-sm font-semibold text-slate-600">Kategori</dt><dd class="mt-1 font-bold text-slate-900">{{ $project->category }}</dd></div>
                    @if (filled($project->tech_stack))
                        <div>
                            <dt class="text-sm font-semibold text-slate-600">Teknologi</dt>
                            <dd class="mt-2 flex flex-wrap gap-2">
                                @foreach ($project->tech_stack as $tech)
                                    <span class="rounded-md bg-brand-100 px-2.5 py-1 text-xs font-bold text-brand-900">{{ $tech }}</span>
                                @endforeach
                            </dd>
                        </div>
                    @endif
                    @if ($project->url)
                        <a href="{{ $project->url }}" target="_blank" rel="noopener" class="btn btn-primary w-full">
                            Kunjungi proyek @svg('heroicon-o-arrow-top-right-on-square', 'size-4', ['aria-hidden' => 'true'])
                        </a>
                    @endif
                </dl>
            </aside>
        </div>
    </section>

    @if ($related->isNotEmpty())
        <section class="section-space bg-brand-50">
            <div class="container-x">
                <x-heading data-aos="fade-up">Proyek terkait</x-heading>
                <div data-project-count="{{ $related->count() }}" @class([
                    'portfolio-grid mt-10 grid gap-6',
                    'mx-auto max-w-md' => $related->count() === 1,
                    'mx-auto max-w-4xl sm:grid-cols-2' => $related->count() === 2,
                    'sm:grid-cols-2 lg:grid-cols-3' => $related->count() >= 3,
                ])>
                    @foreach ($related as $item)
                        <div data-aos="fade-up" data-aos-delay="{{ $loop->index * 75 }}"><x-project-card :project="$item" /></div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layout>
