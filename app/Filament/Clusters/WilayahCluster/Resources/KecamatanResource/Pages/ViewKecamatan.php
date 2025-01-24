<?php

namespace App\Filament\Clusters\WilayahCluster\Resources\KecamatanResource\Pages;

use App\Filament\Clusters\WilayahCluster\Resources\KecamatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewKecamatan extends ViewRecord
{
    protected static string $resource = KecamatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
