<?php

namespace App\Filament\Clusters\User\Resources\MahasiswaResource\Pages;

use App\Filament\Clusters\User\Resources\MahasiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMahasiswas extends ManageRecords
{
    protected static string $resource = MahasiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
