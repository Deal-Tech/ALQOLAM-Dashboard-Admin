<?php

namespace App\Filament\Dosen\Resources\RespondSurveyResource\Pages;

use App\Filament\Dosen\Resources\RespondSurveyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRespondSurveys extends ListRecords
{
    protected static string $resource = RespondSurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
           
        ];
    }
}
