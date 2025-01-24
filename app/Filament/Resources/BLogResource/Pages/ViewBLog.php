<?php

namespace App\Filament\Resources\BLogResource\Pages;

use App\Filament\Resources\BLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBLog extends ViewRecord
{
    protected static string $resource = BLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
