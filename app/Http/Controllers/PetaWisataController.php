<?php

namespace App\Http\Controllers;

use App\Models\TitikWisata;

class PetaWisataController extends Controller
{
    public function index()
    {
        return view('peta.index');
    }

    public function show(TitikWisata $titikWisata)
    {
        abort_unless($titikWisata->is_active, 404);

        $lainnya = TitikWisata::active()
            ->where('id', '!=', $titikWisata->id)
            ->ordered()
            ->limit(4)
            ->get();

        return view('peta.show', [
            'titikWisata' => $titikWisata,
            'lainnya' => $lainnya,
        ]);
    }
}
