<?php

namespace App\Filament\Dosen\Resources\RespondAssetDesaResource\Pages;

use App\Filament\Dosen\Resources\RespondAssetDesaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRespondAssetDesa extends ViewRecord
{
    protected static string $resource = RespondAssetDesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
