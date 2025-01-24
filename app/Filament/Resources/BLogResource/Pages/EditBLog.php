<?php

namespace App\Filament\Resources\BLogResource\Pages;

use App\Filament\Resources\BLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBLog extends EditRecord
{
    protected static string $resource = BLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
