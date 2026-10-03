@props(['project'])
<a href="{{ route('projects.show', $project) }}" class="group block h-full overflow-hidden rounded-lg border border-brand-200 bg-white transition-colors hover:border-brand-700">
    <div class="aspect-[4/3] overflow-hidden bg-brand-100">
        @if ($project->cover)
            <img src="{{ Storage::url($project->cover) }}" alt="{{ $project->title }}" loading="lazy" class="size-full object-cover">
        @else
            <div class="grid size-full place-items-center text-sm font-semibold text-brand-800" aria-hidden="true">Gambar belum tersedia</div>
        @endif
    </div>
    <div class="p-6">
        <span class="text-sm font-medium text-brand-700">{{ $project->category }}</span>
        <h3 class="mt-2 text-xl font-bold text-brand-950 group-hover:text-brand-700">{{ $project->title }}</h3>
        @if ($project->client)
            <p class="mt-1 text-sm text-slate-600">{{ $project->client }}</p>
        @endif
        @if ($project->excerpt)
            <p class="mt-3 line-clamp-3 text-slate-600">{{ $project->excerpt }}</p>
        @endif
    </div>
</a>
