<?php

namespace App\Filament\Resources\InformationResource\Pages;

use App\Filament\Resources\InformationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInformation extends ViewRecord
{
    protected static string $resource = InformationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
