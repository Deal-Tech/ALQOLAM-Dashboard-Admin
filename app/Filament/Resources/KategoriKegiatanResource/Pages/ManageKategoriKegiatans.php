<?php

namespace App\Filament\Resources\KategoriKegiatanResource\Pages;

use App\Filament\Resources\KategoriKegiatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageKategoriKegiatans extends ManageRecords
{
    protected static string $resource = KategoriKegiatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
