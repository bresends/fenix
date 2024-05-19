<?php

namespace App\Filament\Resources\MakeUpExamResource\Pages;

use App\Filament\Resources\MakeUpExamResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMakeUpExams extends ListRecords
{
    protected static string $resource = MakeUpExamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
