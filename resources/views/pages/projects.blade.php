<x-layout title="Portofolio" heading="Portofolio" lead="Beberapa proyek yang sudah kami kerjakan untuk klien.">
    <section class="section-space">
        <div class="container-x">
            @if ($categories->count() > 1)
                <nav aria-label="Filter kategori" data-aos="fade-up">
                    <ul class="flex flex-wrap gap-2">
                        @foreach ($categories->prepend(null) as $cat)
                            @php($active = $cat === $category)
                            <li>
                                <a href="{{ route('projects.index', array_filter(['kategori' => $cat])) }}"
                                   @class(['block rounded-lg border px-5 py-3 text-sm font-semibold transition-colors', 'border-brand-700 bg-brand-700 text-white' => $active, 'border-brand-200 bg-white text-slate-700 hover:bg-brand-50' => ! $active])
                                   @if ($active) aria-current="page" @endif>{{ $cat ?? 'Semua' }}</a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            @endif

            <div data-project-count="{{ $projects->count() }}" @class([
                'portfolio-grid mt-10 grid gap-6',
                'mx-auto max-w-md' => $projects->count() === 1,
                'mx-auto max-w-4xl sm:grid-cols-2' => $projects->count() === 2,
                'sm:grid-cols-2 lg:grid-cols-3' => $projects->count() >= 3,
            ])>
                @forelse ($projects as $project)
                    <div data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}"><x-project-card :project="$project" /></div>
                @empty
                    <p class="text-slate-600 sm:col-span-2 lg:col-span-3">{{ $category ? 'Belum ada proyek di kategori ini.' : 'Belum ada proyek yang ditampilkan.' }}</p>
                @endforelse
            </div>
        </div>
    </section>
</x-layout>
