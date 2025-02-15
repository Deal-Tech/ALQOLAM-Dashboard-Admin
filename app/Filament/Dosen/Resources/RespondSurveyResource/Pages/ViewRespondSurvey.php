<?php

namespace App\Filament\Dosen\Resources\RespondSurveyResource\Pages;

use App\Filament\Dosen\Resources\RespondSurveyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRespondSurvey extends ViewRecord
{
    protected static string $resource = RespondSurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
