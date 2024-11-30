<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action as TableAction;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download-school')
                ->icon('heroicon-o-document-text')
                ->label('Gerar Relatório')
                ->url(route('absent-excel'))
                ->label('Gerar relatório de ausências')
                ->button()
                ->color(Color::Zinc)
                ->icon('heroicon-o-arrow-down-tray')
                ->hidden(!auth()->user()->hasAnyRole(['super_admin', 'admin']))
                ->openUrlInNewTab()
        ];
    }
}
