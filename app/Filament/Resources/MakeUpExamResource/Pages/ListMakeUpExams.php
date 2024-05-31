<?php

namespace App\Filament\Resources\MakeUpExamResource\Pages;

use App\Enums\StatusFoEnum;
use App\Filament\Resources\MakeUpExamResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListMakeUpExams extends ListRecords
{
    protected static string $resource = MakeUpExamResource::class;
    public function getTabs(): array
    {
        return [
            'Todas' => Tab::make()
                ->icon('heroicon-s-queue-list'),
            'À deliberar' => Tab::make()
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', StatusFoEnum::EM_ANDAMENTO->value)),
            'Falta encaminhamento' => Tab::make()
                ->icon('heroicon-o-arrow-right-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->where('status', "!=", StatusFoEnum::EM_ANDAMENTO->value)
                    ->where('archived', false)),
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
