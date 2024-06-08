<?php

namespace App\Filament\Widgets;

use App\Enums\StatusEnum;
use App\Enums\StatusFoEnum;
use App\Models\Fo;
use App\Models\Leave;
use App\Models\Military;
use App\Models\SwitchShift;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FoOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Militares', Military::all()->count())
                ->descriptionIcon('heroicon-o-user-group')
                ->description('total de militares cadastrados'),
            Stat::make('FOs', Fo::where('status', StatusFoEnum::EM_ANDAMENTO->value)->count())
                ->descriptionIcon('heroicon-o-check-circle')
                ->description('aguardando parecer'),
            Stat::make('Dispensas', Leave::where('status', StatusEnum::EM_ANDAMENTO->value)->count())
                ->descriptionIcon('heroicon-o-check-circle')
                ->description('aguardando parecer'),
            Stat::make('Trocas de Serviço', SwitchShift::where('status', StatusEnum::EM_ANDAMENTO->value)->count())
                ->descriptionIcon('heroicon-o-check-circle')
                ->description('aguardando parecer'),
        ];
    }
}
