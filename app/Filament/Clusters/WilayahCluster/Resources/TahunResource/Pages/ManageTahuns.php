<?php

namespace App\Filament\Clusters\WilayahCluster\Resources\TahunResource\Pages;

use App\Filament\Clusters\WilayahCluster\Resources\TahunResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTahuns extends ManageRecords
{
    protected static string $resource = TahunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
