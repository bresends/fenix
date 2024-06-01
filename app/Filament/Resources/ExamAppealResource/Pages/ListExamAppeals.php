<?php

namespace App\Filament\Resources\ExamAppealResource\Pages;

use App\Enums\StatusExamEnum;
use App\Enums\StatusFoEnum;
use App\Filament\Resources\ExamAppealResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListExamAppeals extends ListRecords
{
    protected static string $resource = ExamAppealResource::class;

    public function getTabs(): array
    {
        return [
            'Todos' => Tab::make()
                ->icon('heroicon-s-queue-list'),
            'À deliberar' => Tab::make()
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', StatusExamEnum::EM_ANDAMENTO->value)),
            'Falta encaminhamento' => Tab::make()
                ->icon('heroicon-o-arrow-right-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->where('status', "!=", StatusExamEnum::EM_ANDAMENTO->value)
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
