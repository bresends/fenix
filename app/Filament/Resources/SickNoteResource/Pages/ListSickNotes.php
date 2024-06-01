<?php

namespace App\Filament\Resources\SickNoteResource\Pages;

use App\Filament\Resources\SickNoteResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSickNotes extends ListRecords
{
    protected static string $resource = SickNoteResource::class;

    public function getTabs(): array
    {
        return [
            'Todos' => Tab::make()
                ->icon('heroicon-s-queue-list'),
            'À deliberar' => Tab::make()
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('received', false)),
            'À anexar no SEI' => Tab::make()
                ->icon('heroicon-o-arrow-down-on-square')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->where('received', true)
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
