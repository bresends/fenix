<?php

namespace App\Filament\Resources\SwitchShiftResource\Pages;

use App\Enums\StatusFoEnum;
use App\Filament\Resources\SwitchShiftResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSwitchShifts extends ListRecords
{
    protected static string $resource = SwitchShiftResource::class;

    public function getTabs(): array
    {
        return [
            'Todas' => Tab::make()
                ->icon('heroicon-s-queue-list'),
            'À deliberar' => Tab::make()
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->where('accepted', true)
                    ->where('status', StatusFoEnum::EM_ANDAMENTO->value)),
            'Faltando informar às OBMs' => Tab::make()
                ->icon('heroicon-o-chat-bubble-oval-left-ellipsis')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->where('status', "!=", StatusFoEnum::EM_ANDAMENTO->value)
                    ->where('paid', false)),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
