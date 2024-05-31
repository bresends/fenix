<?php

namespace App\Filament\Resources\MilitaryResource\Pages;

use App\Filament\Resources\MilitaryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMilitary extends EditRecord
{
    protected static string $resource = MilitaryResource::class;

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
