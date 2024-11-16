<?php

namespace App\Filament\Resources\FoResource\Pages;

use App\Filament\Resources\FoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFo extends EditRecord
{
    protected static string $resource = FoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['excuse']) && $this->record->excuse !== $data['excuse']) {
            $data['excuse_timestamp'] = now();
        }

        return $data;
    }
}
