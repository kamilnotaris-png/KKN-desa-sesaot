<?php

namespace App\Filament\Resources\TitikWisataResource\Pages;

use App\Filament\Resources\TitikWisataResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTitikWisatas extends ListRecords
{
    protected static string $resource = TitikWisataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
