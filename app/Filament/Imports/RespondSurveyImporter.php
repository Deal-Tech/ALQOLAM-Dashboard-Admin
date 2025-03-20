<?php

namespace App\Filament\Imports;

use App\Models\ResponDetail;
use App\Models\Survey;
use App\Models\Mahasiswa;
use App\Models\RespondSurvey;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RespondSurveyImporter extends Importer
{
    protected static ?string $model = ResponDetail::class;
    
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('survey_id')
                ->label('Survey ID')
                ->requiredMapping()
                ->example('1')
                ->rules(['integer', 'exists:survey,id']),
                
            ImportColumn::make('jawaban')
                ->label('Jawaban')
                ->requiredMapping()
                ->example('Jawaban survey')
                ->rules(['required', 'string']),

            ImportColumn::make('respondsurvey_id')
                ->label('Respond Survey ID')
                ->requiredMapping()
                ->example('1')
                ->rules(['integer', 'exists:respondsurvey,id']),
        ];
    }
    
    protected function validateRequiredSurveys(int $respondSurveyId): void
    {
        $requiredSurveys = Survey::where('is_required', true)->pluck('id')->toArray();
        
        $existingAnswers = ResponDetail::where('respondsurvey_id', $respondSurveyId)
            ->pluck('survey_id')
            ->toArray();
        
        $allAnsweredSurveys = array_unique(array_merge($existingAnswers, $this->importedSurveyIds[$respondSurveyId] ?? []));
        
        
        $missingSurveys = array_diff($requiredSurveys, $allAnsweredSurveys);
        
        if (count($missingSurveys) > 0) {
            $missingIds = implode(', ', $missingSurveys);
            $missingSurveyTitles = Survey::whereIn('id', $missingSurveys)->pluck('title')->implode(', ');
            
            throw new ValidationException(
                validator([], [])
                    ->errors()
                    ->add('import', "Ada survey belum dijawab: {$missingSurveyTitles} (ID: {$missingIds})")
            );
        }
    }
    

    protected array $importedSurveyIds = [];
    
    public function import(Import $import, array $data): void
    {
        if (!empty($data['survey_id']) && !empty($data['jawaban']) && !empty($data['respondsurvey_id'])) {
            try {

                $survey = Survey::find($data['survey_id']);
                
                if (!$survey) {
                    \Log::warning('Survey tidak ditemukan', ['survey_id' => $data['survey_id']]);
                    $import->skipRow();
                    return;
                }

                $respondSurvey = RespondSurvey::find($data['respondsurvey_id']);
                
                if (!$respondSurvey) {
                    \Log::warning('RespondSurvey tidak ditemukan', ['respondsurvey_id' => $data['respondsurvey_id']]);
                    $import->skipRow();
                    return;
                }
                
                $existingAnswer = ResponDetail::where('respondsurvey_id', $data['respondsurvey_id'])
                    ->where('survey_id', $data['survey_id'])
                    ->first();
                
                if ($existingAnswer) {
                    \Log::warning('Jawaban survey sudah ada. Melakukan update.', [
                        'id' => $existingAnswer->id,
                        'survey_id' => $data['survey_id']
                    ]);
                    
                    $existingAnswer->jawaban = $data['jawaban'];
                    $existingAnswer->save();
                } else {
                   
                    $detailRecord = ResponDetail::create([
                        'respondsurvey_id' => $data['respondsurvey_id'],
                        'survey_id' => $data['survey_id'],
                        'jawaban' => $data['jawaban']
                    ]);
                    
                    
                    if (!isset($this->importedSurveyIds[$data['respondsurvey_id']])) {
                        $this->importedSurveyIds[$data['respondsurvey_id']] = [];
                    }
                    $this->importedSurveyIds[$data['respondsurvey_id']][] = $data['survey_id'];
                    
                    \Log::info('Berhasil membuat record respondetail', [
                        'id' => $detailRecord->id,
                        'survey_id' => $data['survey_id'],
                        'jawaban' => $data['jawaban']
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Gagal membuat record respondetail: ' . $e->getMessage(), [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e; 
            }
        } else {
            $import->skipRow();
            \Log::info('Melewati baris yang tidak valid - data tidak lengkap');
        }
    }
    
    public function afterImport(Import $import): void
    {
        
        try {
            foreach ($this->importedSurveyIds as $respondSurveyId => $surveyIds) {
                $this->validateRequiredSurveys($respondSurveyId);
            }
        } catch (ValidationException $e) {
            \Log::error('Validasi gagal setelah import: ' . $e->getMessage());
            throw $e;
        }
        
        \Log::info('Import selesai dengan validasi semua pertanyaan wajib telah dijawab');
    }
    
    public function resolveRecord(): ?ResponDetail
    {
        return new ResponDetail();
    }
    
    public static function getCompletedNotificationBody(Import $import): string
    {
        $count = $import->successful_rows;
        return "Berhasil mengimpor {$count} jawaban survey.";
    }
}