<?php

namespace App\Filament\Resources\AssetDesaResource\Pages;

use App\Filament\Resources\AssetDesaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetDesa extends EditRecord
{
    protected static string $resource = AssetDesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
