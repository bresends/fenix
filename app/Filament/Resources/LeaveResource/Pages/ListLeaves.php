<?php

namespace App\Filament\Resources\LeaveResource\Pages;

use App\Enums\StatusFoEnum;
use App\Filament\Resources\LeaveResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListLeaves extends ListRecords
{
    protected static string $resource = LeaveResource::class;

    public function getTabs(): array
    {
        return [
            'Todas' => Tab::make()
                ->icon('heroicon-s-queue-list'),
            'À deliberar' => Tab::make()
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', StatusFoEnum::EM_ANDAMENTO->value)),
            'Falta comprovante' => Tab::make()
                ->icon('heroicon-o-question-mark-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->where('status', "!=", StatusFoEnum::EM_ANDAMENTO->value)
                    ->whereNull('file')
                    ->where('paid', false)),
            'Comprovante anexado' => Tab::make()
                ->icon('heroicon-o-arrow-down-on-square')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->where('status', "!=", StatusFoEnum::EM_ANDAMENTO->value)
                    ->whereNotNull('file')
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
