<?php

namespace App\Filament\Resources\SwitchShiftResource\Pages;

use App\Filament\Resources\SwitchShiftResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSwitchShift extends EditRecord
{
    protected static string $resource = SwitchShiftResource::class;

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
}
