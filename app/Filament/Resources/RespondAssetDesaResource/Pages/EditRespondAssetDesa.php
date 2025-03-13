<?php

namespace App\Filament\Resources\RespondAssetDesaResource\Pages;

use App\Filament\Resources\RespondAssetDesaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRespondAssetDesa extends EditRecord
{
    protected static string $resource = RespondAssetDesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
