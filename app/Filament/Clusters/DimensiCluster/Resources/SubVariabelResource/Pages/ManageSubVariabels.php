<?php

namespace App\Filament\Clusters\DimensiCluster\Resources\SubVariabelResource\Pages;

use App\Filament\Clusters\DimensiCluster\Resources\SubVariabelResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSubVariabels extends ManageRecords
{
    protected static string $resource = SubVariabelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
