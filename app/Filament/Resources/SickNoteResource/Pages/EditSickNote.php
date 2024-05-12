<?php

namespace App\Filament\Resources\SickNoteResource\Pages;

use App\Filament\Resources\SickNoteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSickNote extends EditRecord
{
    protected static string $resource = SickNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
