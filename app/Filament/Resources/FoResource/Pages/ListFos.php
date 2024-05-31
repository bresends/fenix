<?php

namespace App\Filament\Resources\FoResource\Pages;

use App\Enums\FoEnum;
use App\Enums\StatusFoEnum;
use App\Filament\Resources\FoResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListFos extends ListRecords
{
    protected static string $resource = FoResource::class;

    public function getTabs(): array
    {
        return [
            'Todos' => Tab::make()
                ->icon('heroicon-s-queue-list'),
            'À deliberar' => Tab::make()
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', StatusFoEnum::EM_ANDAMENTO->value)),
            'Não cumpridos' => Tab::make()
                ->icon('heroicon-o-document-minus')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->where('type', FoEnum::Negativo->value)
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
