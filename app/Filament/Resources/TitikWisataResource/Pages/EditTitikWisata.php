<?php

namespace App\Filament\Resources\TitikWisataResource\Pages;

use App\Filament\Resources\TitikWisataResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;

class EditTitikWisata extends EditRecord
{
    use Translatable;

    protected static string $resource = TitikWisataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
