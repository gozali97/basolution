<x-layout title="Halaman tidak ditemukan" :cta="false">
    <section class="py-20 sm:py-28">
        <div class="container-x">
            <p class="text-7xl font-extrabold text-brand-400" aria-hidden="true">404</p>
            <h1 class="mt-4 text-3xl font-extrabold text-slate-900">Halaman tidak ditemukan</h1>
            <p class="mt-3 text-slate-600">Halaman yang Anda cari mungkin sudah dipindahkan atau tidak pernah ada.</p>
            <a href="{{ route('home') }}" class="btn btn-primary mt-8">Kembali ke beranda</a>
        </div>
    </section>
</x-layout>
