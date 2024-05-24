<?php

namespace App\Filament\Resources\SwitchShiftResource\Pages;

use App\Filament\Resources\SwitchShiftResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSwitchShift extends CreateRecord
{
    protected static string $resource = SwitchShiftResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
