<?php

namespace App\Filament\Clusters\DimensiCluster\Resources\DimensiResource\Pages;

use App\Filament\Clusters\DimensiCluster\Resources\DimensiResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDimensis extends ManageRecords
{
    protected static string $resource = DimensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
