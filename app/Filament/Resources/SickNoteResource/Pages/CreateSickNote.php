<?php

namespace App\Filament\Resources\SickNoteResource\Pages;

use App\Filament\Resources\SickNoteResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSickNote extends CreateRecord
{
    protected static string $resource = SickNoteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
