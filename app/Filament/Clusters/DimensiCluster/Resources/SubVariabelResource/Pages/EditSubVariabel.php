<?php

namespace App\Filament\Clusters\DimensiCluster\Resources\SubVariabelResource\Pages;

use App\Filament\Clusters\DimensiCluster\Resources\SubVariabelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubVariabel extends EditRecord
{
    protected static string $resource = SubVariabelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
