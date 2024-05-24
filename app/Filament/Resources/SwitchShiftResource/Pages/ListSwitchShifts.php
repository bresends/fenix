<?php

namespace App\Filament\Resources\SwitchShiftResource\Pages;

use App\Filament\Resources\SwitchShiftResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSwitchShifts extends ListRecords
{
    protected static string $resource = SwitchShiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
