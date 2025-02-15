<?php

namespace App\Filament\Dosen\Resources\DosenResource\Widgets;

use App\Models\Kegiatan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KegiatanChart extends ChartWidget
{
    protected static ?string $heading = 'Statistik Kegiatan';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $currentYear = date('Y');
        $startYear = $currentYear - 4; 

        $years = range($startYear, $currentYear);
        $yearlyData = Kegiatan::select(
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
                    'label' => 'Total Kegiatan',
                    'data' => array_values($data),
                    'borderColor' => '#4CAF50',
                    'backgroundColor' => 'rgba(76, 175, 80, 0.2)',
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