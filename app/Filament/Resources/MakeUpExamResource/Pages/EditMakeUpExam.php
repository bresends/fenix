<?php

namespace App\Filament\Resources\MakeUpExamResource\Pages;

use App\Filament\Resources\MakeUpExamResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMakeUpExam extends EditRecord
{
    protected static string $resource = MakeUpExamResource::class;

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
