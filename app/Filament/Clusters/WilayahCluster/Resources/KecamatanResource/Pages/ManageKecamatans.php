<?php

namespace App\Filament\Clusters\WilayahCluster\Resources\KecamatanResource\Pages;

use App\Filament\Clusters\WilayahCluster\Resources\KecamatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageKecamatans extends ManageRecords
{
    protected static string $resource = KecamatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
