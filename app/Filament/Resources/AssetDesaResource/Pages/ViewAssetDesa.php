<?php

namespace App\Filament\Resources\AssetDesaResource\Pages;

use App\Filament\Resources\AssetDesaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAssetDesa extends ViewRecord
{
    protected static string $resource = AssetDesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
