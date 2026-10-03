<x-layout title="Layanan" heading="Layanan kami" lead="Pilih layanan untuk melihat ruang lingkup pekerjaannya.">
    <section class="section-space">
        <div class="container-x grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @if ($services->isEmpty())<p class="text-slate-600 sm:col-span-2 lg:col-span-3">Belum ada layanan yang ditampilkan.</p>@endif
            @foreach ($services as $service)
                <div data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}"><x-service-card :service="$service" /></div>
            @endforeach
        </div>
    </section>
</x-layout>
