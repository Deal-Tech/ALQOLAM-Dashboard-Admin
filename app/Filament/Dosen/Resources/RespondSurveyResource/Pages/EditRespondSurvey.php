<?php

namespace App\Filament\Dosen\Resources\RespondSurveyResource\Pages;

use App\Filament\Dosen\Resources\RespondSurveyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRespondSurvey extends EditRecord
{
    protected static string $resource = RespondSurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
