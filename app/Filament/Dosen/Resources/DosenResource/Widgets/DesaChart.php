<?php

namespace App\Filament\Dosen\Resources\DosenResource\Widgets;

use App\Models\Desa;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DesaChart extends ChartWidget
{
    protected static ?string $heading = 'Statistik Desa ';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $currentYear = date('Y');
        $startYear = $currentYear - 4;

        $years = range($startYear, $currentYear);
        $yearlyData = Desa::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as count')
        )
        ->whereYear('created_at', '>=', $startYear)
        ->groupBy('year')
        ->orderBy('year')
        ->pluck('count', 'year')
        ->toArray();

        
        $data = array_fill_keys($years, 0);
        foreach ($yearlyData as $year => $count) {
            $data[$year] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Desa',
                    'data' => array_values($data),
                    'borderColor' => '#9BD0F5',
                    'backgroundColor' => 'rgba(155, 208, 245, 0.2)',
                    'fill' => true,
                    'tension' => 0.4
                ],
            ],
            'labels' => array_map(function($year) {
                return "$year";
            }, $years),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1
                    ]
                ]
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ]
            ],
        ];
    }
}