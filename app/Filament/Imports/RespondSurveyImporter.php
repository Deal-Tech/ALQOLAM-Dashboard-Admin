<?php

namespace App\Filament\Imports;

use App\Models\RespondSurvey;
use App\Models\RespondSurveyDetail;
use App\Models\User;
use App\Models\Desa;
use App\Models\DosenPendamping;
use App\Models\Survey;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;

class RespondSurveyImporter extends Importer
{
    protected static ?string $model = RespondSurvey::class;
    
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('user_id')
                ->label('User ID')
                ->requiredMapping()
                ->example('1')
                ->rules(['integer', 'exists:mahasiswa,id']),
                
            ImportColumn::make('kabupaten_id')
                ->label('Kab ID')
                ->requiredMapping()
                ->example('1')
                ->rules(['integer', 'exists:kabupaten,id']),

            ImportColumn::make('kecamatan_id')
                ->label('Kec ID')
                ->requiredMapping()
                ->example('1')
                ->rules(['integer', 'exists:kecamatan,id']),

            ImportColumn::make('desa_id')
                ->label('Desa ID')
                ->requiredMapping()
                ->example('1')
                ->rules(['integer', 'exists:desa,id']),
                
            ImportColumn::make('dosenpendamping_id')
                ->label('Dosen Pendamping ID')
                ->requiredMapping()
                ->example('1')
                ->rules(['integer', 'exists:dosenpendamping,id']),
                
            ImportColumn::make('nama_ketua')
                ->label('Nama Ketua')
                ->requiredMapping()
                ->example('Budi Santoso')
                ->rules(['string', 'max:255']),
                
            ImportColumn::make('review_dosen')
                ->label('Review Dosen')
                ->example('1')
                ->fillRecordUsing(function (array $data, array $row) {
                    $review = strtolower($data['review_dosen'] ?? '');
                    $data['is_compled'] = in_array($review, ['ya', 'y', 'true', '1', 'yes']);
                    
                    return $data;
                }),
                
            ImportColumn::make('publikasi')
                ->label('Publikasi')
                ->example('0')
                ->fillRecordUsing(function (array $data, array $row) {
                    $publikasi = strtolower($data['publikasi'] ?? '');
                    $data['is_published'] = in_array($publikasi, ['ya', 'y', 'true', '1', 'yes']);
                    
                    return $data;
                }),
            
            // Untuk detail jawaban
            ImportColumn::make('survey_id')
                ->label('Survey ID (letakkan di sel A6)')
                ->requiredMapping(false)
                ->example('1')
                ->rules(['integer', 'exists:survey,id'])
                ->helperText('Letakkan ID pertanyaan survey di sel A6 pada template Excel'),

            ImportColumn::make('jawaban')
                ->label('Jawaban (letakkan di sel B6)')
                ->requiredMapping(false)
                ->example('Jawaban survey')
                ->helperText('Letakkan jawaban survey di sel B6 pada template Excel'),
        ];
    }
    
    
    public function import(Import $import, array $data): void
    {
       
        $surveyId = $data['survey_id'] ?? null;
        $jawaban = $data['jawaban'] ?? null;
        

        unset($data['survey_id']);
        unset($data['jawaban']);
        

        $record = $this->resolveRecord();
        $record->fill($data);
        $record->save();
        

        if (!empty($surveyId) && !empty($jawaban)) {
            $survey = Survey::find($surveyId);
            
            if ($survey) {
                RespondSurveyDetail::create([
                    'respondsurvey_id' => $record->id,
                    'survey_id' => $survey->id,
                    'jawaban' => $jawaban,
                ]);
            }
        }
    }
    
    public function afterCreate(Import $import, RespondSurvey $record, array $data): void
    {

        $surveyId = $data['survey_id'] ?? null;
        $jawaban = $data['jawaban'] ?? null;
        
        if (!empty($surveyId) && !empty($jawaban)) {
            $survey = Survey::find($surveyId);
            
            if ($survey) {
                RespondSurveyDetail::create([
                    'respondsurvey_id' => $record->id,
                    'survey_id' => $survey->id,
                    'jawaban' => $jawaban,
                ]);
            }
        }
    }
    
    public function resolveRecord(): ?RespondSurvey
    {
        return new RespondSurvey();
    }
    
    public static function getCompletedNotificationBody(Import $import): string
    {
        $count = $import->successful_rows;
        return "Berhasil mengimpor {$count} hasil survei dengan detailnya.";
    }
}