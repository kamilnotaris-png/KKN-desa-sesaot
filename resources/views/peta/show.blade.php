@extends('layouts.app')

@section('title', $titikWisata->nama.' — Wisata Desa Sesaot')

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-5">
        <a href="{{ route('peta.index') }}" class="text-wisata-green-600 text-sm font-medium">&larr; Kembali ke peta</a>

        @if ($titikWisata->foto)
            <img src="{{ asset('storage/'.$titikWisata->foto) }}" alt="{{ $titikWisata->nama }}"
                 class="w-full h-56 object-cover rounded-xl mt-3">
        @endif

        <div class="mt-4 flex items-center gap-2">
            <span class="inline-block bg-wisata-green-600/10 text-wisata-green-700 text-xs font-semibold px-2 py-1 rounded-full">
                {{ \App\Models\TitikWisata::KATEGORI[$titikWisata->kategori] ?? $titikWisata->kategori }}
            </span>
            <span class="text-xs text-gray-500">Dusun {{ $titikWisata->dusun }}</span>
        </div>

        <h1 class="text-2xl font-bold mt-2">{{ $titikWisata->nama }}</h1>

        @if ($titikWisata->deskripsi)
            <p class="mt-3 text-gray-700">{{ $titikWisata->deskripsi }}</p>
        @endif

        @if ($titikWisata->video_embed_url)
            <div class="mt-5 aspect-video rounded-xl overflow-hidden">
                <iframe src="{{ $titikWisata->video_embed_url }}" class="w-full h-full" allowfullscreen
                        loading="lazy" title="Video narasi {{ $titikWisata->nama }}"></iframe>
            </div>
        @endif

        @if ($titikWisata->cerita_lokal)
            <div class="mt-5 bg-gray-50 rounded-xl p-4">
                <h2 class="font-semibold text-sm text-gray-500 uppercase tracking-wide">Cerita Lokal</h2>
                <p class="mt-2 text-gray-700 whitespace-pre-line">{{ $titikWisata->cerita_lokal }}</p>
            </div>
        @endif

        <div class="mt-5 text-sm text-gray-500">
            Koordinat: {{ $titikWisata->latitude }}, {{ $titikWisata->longitude }}
            &middot;
            <a href="https://www.openstreetmap.org/?mlat={{ $titikWisata->latitude }}&mlon={{ $titikWisata->longitude }}#map=17/{{ $titikWisata->latitude }}/{{ $titikWisata->longitude }}"
               target="_blank" rel="noopener" class="text-wisata-green-600 font-medium">buka di peta</a>
        </div>

        @if ($lainnya->isNotEmpty())
            <div class="mt-8">
                <h2 class="font-semibold text-sm text-gray-500 uppercase tracking-wide mb-3">Titik Wisata Lainnya</h2>
                <div class="grid grid-cols-2 gap-3">
                    @foreach ($lainnya as $titik)
                        <a href="{{ route('titik-wisata.show', $titik) }}" class="block rounded-xl border border-gray-200 p-3">
                            <span class="text-sm font-semibold">{{ $titik->nama }}</span>
                            <span class="block text-xs text-gray-500 mt-1">Dusun {{ $titik->dusun }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
