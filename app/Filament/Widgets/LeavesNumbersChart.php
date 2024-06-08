<?php

namespace App\Filament\Widgets;

use App\Models\Leave;
use App\Models\SickNote;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;


class LeavesNumbersChart extends ChartWidget
{
    protected static ?string $heading = 'Atestados e Dispensas';

    protected static ?int $sort = 2;

    protected static string $color = 'success';

    protected function getData(): array
    {
        $leaves = Trend::model(Leave::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        $sick = Trend::model(SickNote::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Dispensas',
                    'data' => $leaves->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',

                ], [
                    'label' => 'Atestados Médicos',
                    'data' => $sick->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
