<?php

namespace App\Filament\Clusters\User\Resources\DosenPendampingResource\Pages;

use App\Filament\Clusters\User\Resources\DosenPendampingResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDosenPendampings extends ManageRecords
{
    protected static string $resource = DosenPendampingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
