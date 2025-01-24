<?php

namespace App\Filament\Clusters\DimensiCluster\Resources\DimensiResource\Pages;

use App\Filament\Clusters\DimensiCluster\Resources\DimensiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDimensi extends EditRecord
{
    protected static string $resource = DimensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
