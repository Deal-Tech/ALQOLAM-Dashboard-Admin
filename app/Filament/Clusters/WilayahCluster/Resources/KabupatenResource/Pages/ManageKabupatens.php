<?php

namespace App\Filament\Clusters\WilayahCluster\Resources\KabupatenResource\Pages;

use App\Filament\Clusters\WilayahCluster\Resources\KabupatenResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageKabupatens extends ManageRecords
{
    protected static string $resource = KabupatenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
