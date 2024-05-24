<?php

namespace App\Filament\Resources\ExamAppealResource\Pages;

use App\Filament\Resources\ExamAppealResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExamAppeals extends ListRecords
{
    protected static string $resource = ExamAppealResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
