<?php

namespace App\Filament\Dosen\Resources\DosenResource\Widgets;

use App\Models\Dimensi;
use App\Models\Variabel;
use App\Models\SubVariabel;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
       
        $dimensiCount = Dimensi::count();
        $variabelCount = Variabel::count();
        $subVariabelCount = SubVariabel::count();

        return [
            Stat::make('Dimensi', $dimensiCount)
                ->description('Total Dimensi')
                ->descriptionIcon('heroicon-o-document')
                ->color('success'),
            
            Stat::make('Variabel', $variabelCount)
                ->description('Total Variabel')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('primary'),
            
            Stat::make('Indikator', $subVariabelCount)
                ->description('Total Indikator')
                ->descriptionIcon('heroicon-o-cube')
                ->color('info'),
        ];
    }
}