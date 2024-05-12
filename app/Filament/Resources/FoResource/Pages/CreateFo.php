<?php

namespace App\Filament\Resources\FoResource\Pages;

use App\Filament\Resources\FoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFo extends CreateRecord
{
    protected static string $resource = FoResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
