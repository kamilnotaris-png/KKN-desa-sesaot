<?php

namespace App\Filament\Resources\TitikWisataResource\Pages;

use App\Filament\Resources\TitikWisataResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTitikWisata extends EditRecord
{
    protected static string $resource = TitikWisataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
