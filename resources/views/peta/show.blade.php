@extends('layouts.app')

@section('title', $titikWisata->nama.' — '.__('peta.judul_situs'))

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-5">
        <a href="{{ route('peta.index') }}" class="text-wisata-green-600 text-sm font-medium">&larr; {{ __('peta.kembali_ke_peta') }}</a>

        @if ($titikWisata->foto)
            <img src="{{ asset('storage/'.$titikWisata->foto) }}" alt="{{ $titikWisata->nama }}"
                 class="w-full h-56 object-cover rounded-xl mt-3">
        @endif

        <div class="mt-4 flex items-center gap-2">
            <span class="inline-block bg-wisata-green-600/10 text-wisata-green-700 text-xs font-semibold px-2 py-1 rounded-full">
                {{ __('peta.kategori.'.$titikWisata->kategori) }}
            </span>
            <span class="text-xs text-gray-500">{{ __('peta.dusun') }} {{ $titikWisata->dusun }}</span>
        </div>

        <h1 class="text-2xl font-bold mt-2">{{ $titikWisata->nama }}</h1>

        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $titikWisata->latitude }},{{ $titikWisata->longitude }}"
           target="_blank" rel="noopener"
           class="mt-4 inline-flex items-center gap-2 bg-wisata-green-600 text-white text-sm font-semibold px-4 py-2 rounded-full">
            🧭 {{ __('peta.petunjuk_arah') }}
        </a>

        @if ($titikWisata->deskripsi)
            <p class="mt-3 text-gray-700">{{ $titikWisata->deskripsi }}</p>
        @endif

        @if ($titikWisata->video_embed_url)
            <div class="mt-5 aspect-video rounded-xl overflow-hidden">
                <iframe src="{{ $titikWisata->video_embed_url }}" class="w-full h-full" allowfullscreen
                        loading="lazy" title="{{ $titikWisata->nama }}"></iframe>
            </div>
        @endif

        @if ($titikWisata->cerita_lokal)
            <div class="mt-5 bg-gray-50 rounded-xl p-4">
                <h2 class="font-semibold text-sm text-gray-500 uppercase tracking-wide">{{ __('peta.cerita_lokal') }}</h2>
                <p class="mt-2 text-gray-700 whitespace-pre-line">{{ $titikWisata->cerita_lokal }}</p>
            </div>
        @endif

        <div class="mt-5 text-sm text-gray-500">
            {{ __('peta.koordinat') }}: {{ $titikWisata->latitude }}, {{ $titikWisata->longitude }}
            &middot;
            <a href="https://www.openstreetmap.org/?mlat={{ $titikWisata->latitude }}&mlon={{ $titikWisata->longitude }}#map=17/{{ $titikWisata->latitude }}/{{ $titikWisata->longitude }}"
               target="_blank" rel="noopener" class="text-wisata-green-600 font-medium">{{ __('peta.buka_di_peta') }}</a>
        </div>

        @if ($lainnya->isNotEmpty())
            <div class="mt-8">
                <h2 class="font-semibold text-sm text-gray-500 uppercase tracking-wide mb-3">{{ __('peta.titik_lainnya') }}</h2>
                <div class="grid grid-cols-2 gap-3">
                    @foreach ($lainnya as $titik)
                        <a href="{{ route('titik-wisata.show', $titik) }}" class="block rounded-xl border border-gray-200 p-3">
                            <span class="text-sm font-semibold">{{ $titik->nama }}</span>
                            <span class="block text-xs text-gray-500 mt-1">{{ __('peta.dusun') }} {{ $titik->dusun }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
