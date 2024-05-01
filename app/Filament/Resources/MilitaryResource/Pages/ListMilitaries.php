<?php

namespace App\Filament\Resources\MilitaryResource\Pages;

use App\Filament\Resources\MilitaryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMilitaries extends ListRecords
{
    protected static string $resource = MilitaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
