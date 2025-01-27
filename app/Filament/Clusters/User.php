<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class User extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Data';
    
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Data User';
}
