<?php

namespace App\Filament\Resources\BLogResource\Pages;

use App\Filament\Resources\BLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBLogs extends ListRecords
{
    protected static string $resource = BLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
