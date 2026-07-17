<?php

use App\Http\Controllers\Api\TitikWisataController;
use App\Http\Controllers\PetaWisataController;
use Illuminate\Support\Facades\Route;

Route::middleware('public-locale')->group(function () {
    Route::get('/', [PetaWisataController::class, 'index'])->name('peta.index');
    Route::get('/peta/{titikWisata}', [PetaWisataController::class, 'show'])->name('titik-wisata.show');

    Route::get('/api/titik-wisata', [TitikWisataController::class, 'index'])->name('api.titik-wisata');
});
