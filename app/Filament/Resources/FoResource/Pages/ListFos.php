<?php

namespace App\Filament\Resources\FoResource\Pages;

use App\Enums\FoEnum;
use App\Enums\StatusFoEnum;
use App\Filament\Resources\FoResource;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
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

            ActionGroup::make([
                Action::make('download-cfo')
                    ->icon('heroicon-o-document-text')
                    ->label('CFO')
                    ->url(route('fos.cfo'))
                    ->openUrlInNewTab(),
                Action::make('download-cfp')
                    ->icon('heroicon-o-document-text')
                    ->label('CFP')
                    ->url(route('fos.cfp'))
                    ->openUrlInNewTab(),
            ])
                ->label('Gerar OS')
                ->button()
                ->color(Color::Zinc)
                ->icon('heroicon-o-arrow-down-tray')
                ->hidden(!auth()->user()->hasAnyRole(['super_admin', 'admin'])),
        ];
    }
}
