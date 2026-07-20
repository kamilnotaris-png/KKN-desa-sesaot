@extends('layouts.app')

@section('title', __('peta.judul_situs'))
@section('main-class', 'overflow-hidden')

@push('structured-data')
    <script type="application/ld+json">{!! $structuredData !!}</script>
@endpush

@section('content')
    <script>
        window.PETA_I18N = {
            caraKeSini: @json(__('peta.cara_ke_sini')),
            lihatDetail: @json(__('peta.lihat_detail')),
            petunjukArah: @json(__('peta.petunjuk_arah')),
            layerPeta: @json(__('peta.layer_peta')),
            layerSatelit: @json(__('peta.layer_satelit')),
            dusun: @json(__('peta.dusun')),
            titikAsal: @json(collect(__('peta.titik_asal'))->values()),
        };
    </script>

    <div class="relative h-full">
        <div id="peta-wisata"></div>

        <div class="arah-panel">
            <button type="button" id="arah-toggle" class="arah-toggle">🧭 {{ __('peta.cara_ke_sini') }}</button>
            <div id="arah-list" class="arah-list hidden"></div>
        </div>
    </div>
@endsection
