<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class WilayahCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationGroup = 'Data';
    
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Data Wilayah';
}
