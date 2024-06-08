<?php

namespace App\Filament\Widgets;

use App\Models\Fo;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class FoNumbersChart extends ChartWidget
{
    protected static ?string $heading = 'FOs';

    protected static ?int $sort = 2;

    //color
    protected static string $color = 'warning';

    protected function getData(): array
    {
        $data = Trend::model(Fo::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Nº de FOs no período',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
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
