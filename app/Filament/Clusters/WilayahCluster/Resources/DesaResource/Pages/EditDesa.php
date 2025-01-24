<?php

namespace App\Filament\Clusters\WilayahCluster\Resources\DesaResource\Pages;

use App\Filament\Clusters\WilayahCluster\Resources\DesaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDesa extends EditRecord
{
    protected static string $resource = DesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
