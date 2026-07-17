@extends('layouts.app')

@section('title', 'Peta Wisata Digital Desa Sesaot')
@section('main-class', 'overflow-hidden')

@section('content')
    <div class="relative h-full">
        <div id="peta-wisata"></div>

        <div class="arah-panel">
            <button type="button" id="arah-toggle" class="arah-toggle">🧭 Cara ke Sini</button>
            <div id="arah-list" class="arah-list hidden"></div>
        </div>
    </div>
@endsection
