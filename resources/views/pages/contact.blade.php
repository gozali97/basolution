@php
    $contacts = array_filter([
        ['map-pin', 'Alamat', $site['address'] ?? null, null],
        ['envelope', 'Email', $site['email'] ?? null, filled($site['email'] ?? null) ? 'mailto:'.$site['email'] : null],
        ['phone', 'Telepon', $site['phone'] ?? null, filled($site['phone'] ?? null) ? 'tel:'.preg_replace('/[^\d+]/', '', $site['phone']) : null],
        ['chat-bubble-oval-left-ellipsis', 'WhatsApp', $site['whatsapp'] ?? null, filled($site['whatsapp'] ?? null) ? 'https://wa.me/'.$site['whatsapp'] : null],
    ], fn ($c) => filled($c[2]));
    $hasAside = $contacts || filled($site['map_embed_url'] ?? null);
@endphp
<x-layout title="Kontak" heading="Mari diskusikan proyek Anda" lead="Konsultasi awal gratis. Isi formulir di bawah atau hubungi kami langsung, dan kami membalas dalam 1x24 jam kerja." :cta="false">
    <section class="section-space">
        <div @class(['container-x grid items-start gap-10 lg:gap-16', 'lg:grid-cols-5' => $hasAside])>
            <div data-aos="fade-up" @class(['rounded-xl border border-brand-200 bg-white p-6 sm:p-10', 'lg:col-span-3' => $hasAside, 'max-w-3xl' => ! $hasAside])>
                <h2 class="mb-7 text-2xl font-bold text-brand-950">Ceritakan kebutuhan Anda</h2>
                @if (session('success'))
                    <p role="status" class="mb-6 rounded-xl bg-green-50 p-4 font-semibold text-green-800 ring-1 ring-green-200">{{ session('success') }}</p>
                @endif
                @error('website')
                    <p role="alert" class="mb-6 rounded-xl bg-red-50 p-4 font-semibold text-red-800 ring-1 ring-red-200">{{ $message }}</p>
                @enderror

                <form method="POST" action="{{ route('contact.send') }}" class="grid gap-5 sm:grid-cols-2">
                    @csrf
                    {{-- Honeypot: off-screen and hidden from assistive tech, so only bots fill it. --}}
                    <div class="absolute -left-[9999px]" aria-hidden="true">
                        <label for="website">Website</label>
                        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    @foreach ([
                        ['name', 'Nama', 'text', true, 'name'],
                        ['email', 'Email', 'email', true, 'email'],
                        ['phone', 'Telepon / WhatsApp', 'tel', false, 'tel'],
                        ['subject', 'Subjek', 'text', false, 'off'],
                    ] as [$field, $label, $type, $required, $autocomplete])
                        <div>
                            <label for="{{ $field }}" class="mb-1.5 block text-sm font-semibold text-slate-800">
                                {{ $label }} @if ($required)<span class="text-red-700" aria-hidden="true">*</span>@endif
                            </label>
                            <input type="{{ $type }}" id="{{ $field }}" name="{{ $field }}" value="{{ old($field) }}" autocomplete="{{ $autocomplete }}"
                                   @required($required) @error($field) aria-invalid="true" aria-describedby="{{ $field }}-error" @enderror
                                   class="w-full rounded-lg border border-brand-500 bg-brand-50/50 px-4 py-3 focus:border-brand-700 focus:bg-white">
                            @error($field)
                                <p id="{{ $field }}-error" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach

                    <div class="sm:col-span-2">
                        <label for="body" class="mb-1.5 block text-sm font-semibold text-slate-800">Pesan <span class="text-red-700" aria-hidden="true">*</span></label>
                        <textarea id="body" name="body" rows="6" required minlength="10"
                                  @error('body') aria-invalid="true" aria-describedby="body-error" @enderror
                                  class="w-full rounded-lg border border-brand-500 bg-brand-50/50 px-4 py-3 focus:border-brand-700 focus:bg-white">{{ old('body') }}</textarea>
                        @error('body')
                            <p id="body-error" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <button type="submit" class="btn btn-primary">Kirim pesan @svg('heroicon-o-paper-airplane', 'size-4', ['aria-hidden' => 'true'])</button>
                    </div>
                </form>
            </div>

            @if ($hasAside)
                <div class="space-y-6 lg:col-span-2">
                    @if ($contacts)
                        <ul class="space-y-4 rounded-xl bg-brand-50 p-6 text-brand-950 sm:p-8" data-aos="fade-up">
                            @foreach ($contacts as [$icon, $label, $value, $href])
                                <li class="flex gap-4">
                                    <span class="mt-1 grid size-10 shrink-0 place-items-center rounded-lg bg-brand-100 text-brand-700" aria-hidden="true">@svg('heroicon-o-'.$icon, 'size-5')</span>
                                    <span>
                                        <span class="block text-sm font-semibold text-brand-700">{{ $label }}</span>
                                        @if ($href)
                                            <a href="{{ $href }}" class="inline-block break-all py-2.5 underline-offset-4 hover:underline" @if (str_starts_with($href, 'https')) target="_blank" rel="noopener" @endif>{{ $value }}</a>
                                        @else
                                            <span class="block py-2.5">{{ $value }}</span>
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if (filled($site['map_embed_url'] ?? null))
                        <iframe src="{{ $site['map_embed_url'] }}" title="Peta lokasi kantor" loading="lazy" referrerpolicy="no-referrer-when-downgrade" data-aos="fade-up"
                                class="h-72 w-full rounded-2xl border-0 ring-1 ring-brand-200"></iframe>
                    @endif
                </div>
            @endif
        </div>
    </section>
</x-layout>
