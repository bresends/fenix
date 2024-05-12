<?php

namespace App\Filament\Widgets;

use App\Models\Fo;
use App\Models\Military;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FoOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total de Militares', Military::all()->count()),
            Stat::make('Total de FOs', Fo::all()->count()),
        ];
    }
}
