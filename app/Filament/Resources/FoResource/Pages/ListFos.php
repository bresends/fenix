<?php

namespace App\Filament\Resources\FoResource\Pages;

use App\Filament\Resources\FoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFos extends ListRecords
{
    protected static string $resource = FoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
