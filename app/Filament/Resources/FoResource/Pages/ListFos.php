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
            'Aguardando Ciência' => Tab::make()
                ->icon('heroicon-o-chat-bubble-oval-left')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', StatusFoEnum::EM_ANDAMENTO->value)
                    ->whereNull('excuse')),
            'À deliberar' => Tab::make()
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', StatusFoEnum::EM_ANDAMENTO->value)
                    ->whereNotNull('excuse')),
            'Não cumpridos' => Tab::make()
                ->icon('heroicon-o-document-minus')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->where('type', FoEnum::Negativo->value)
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
