<?php

namespace App\Filament\Dosen\Resources\KegiatanResource\Pages;

use App\Filament\Dosen\Resources\KegiatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewKegiatan extends ViewRecord
{
    protected static string $resource = KegiatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
