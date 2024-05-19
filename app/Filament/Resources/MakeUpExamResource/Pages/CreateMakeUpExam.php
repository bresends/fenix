<?php

namespace App\Filament\Resources\MakeUpExamResource\Pages;

use App\Filament\Resources\MakeUpExamResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMakeUpExam extends CreateRecord
{
    protected static string $resource = MakeUpExamResource::class;

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
