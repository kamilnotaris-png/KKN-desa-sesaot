<?php

namespace App\Filament\Resources\TitikWisataResource\Pages;

use App\Filament\Resources\TitikWisataResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;

class ListTitikWisatas extends ListRecords
{
    use Translatable;

    protected static string $resource = TitikWisataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }
}
