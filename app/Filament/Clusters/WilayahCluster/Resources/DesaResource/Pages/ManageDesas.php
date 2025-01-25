<?php

namespace App\Filament\Clusters\WilayahCluster\Resources\DesaResource\Pages;

use App\Filament\Clusters\WilayahCluster\Resources\DesaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDesas extends ManageRecords
{
    protected static string $resource = DesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
