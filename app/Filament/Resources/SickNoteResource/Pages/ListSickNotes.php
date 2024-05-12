<?php

namespace App\Filament\Resources\SickNoteResource\Pages;

use App\Filament\Resources\SickNoteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSickNotes extends ListRecords
{
    protected static string $resource = SickNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
