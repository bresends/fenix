<?php

namespace App\Filament\Resources\ExamAppealResource\Pages;

use App\Filament\Resources\ExamAppealResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExamAppeal extends CreateRecord
{
    protected static string $resource = ExamAppealResource::class;

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
