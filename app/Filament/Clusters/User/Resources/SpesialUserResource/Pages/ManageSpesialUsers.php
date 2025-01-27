<?php

namespace App\Filament\Clusters\User\Resources\SpesialUserResource\Pages;

use App\Filament\Clusters\User\Resources\SpesialUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSpesialUsers extends ManageRecords
{
    protected static string $resource = SpesialUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
