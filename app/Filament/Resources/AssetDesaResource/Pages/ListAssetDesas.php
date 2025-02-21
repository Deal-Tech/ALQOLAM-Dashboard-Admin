<?php

namespace App\Filament\Resources\AssetDesaResource\Pages;

use App\Filament\Resources\AssetDesaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssetDesas extends ListRecords
{
    protected static string $resource = AssetDesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
