@props(['service'])
<a href="{{ route('services.show', $service) }}" class="group flex h-full flex-col overflow-hidden rounded-lg border border-brand-200 bg-white transition-colors hover:border-brand-700">
    @if ($service->image)
        <img src="{{ Storage::url($service->image) }}" alt="{{ $service->title }}" loading="lazy" class="aspect-[4/3] w-full object-cover">
    @endif
    <div class="flex flex-1 flex-col p-6">
        <div class="flex items-start gap-3">
            <span class="mt-0.5 text-brand-700" aria-hidden="true">@svg('heroicon-o-'.($service->icon ?: 'code-bracket'), 'size-6')</span>
            <h3 class="text-xl font-bold text-brand-950 group-hover:text-brand-700">{{ $service->title }}</h3>
        </div>
        <p class="mt-3 flex-1 text-sm leading-relaxed text-slate-600">{{ $service->excerpt }}</p>
        <span class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-brand-700">Detail layanan @svg('heroicon-o-arrow-right', 'size-4', ['aria-hidden' => 'true'])</span>
    </div>
</a>
