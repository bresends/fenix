<?php

namespace App\Filament\Resources\MilitaryResource\Pages;

use App\Filament\Resources\MilitaryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMilitary extends CreateRecord
{
    protected static string $resource = MilitaryResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
