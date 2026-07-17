<?php

namespace App\Filament\Resources\TitikWisataResource\Pages;

use App\Filament\Resources\TitikWisataResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateTitikWisata extends CreateRecord
{
    use Translatable;

    protected static string $resource = TitikWisataResource::class;
}
