<?php

namespace App\Filament\Clusters\DimensiCluster\Resources\VariabelResource\Pages;

use App\Filament\Clusters\DimensiCluster\Resources\VariabelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVariabel extends EditRecord
{
    protected static string $resource = VariabelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
