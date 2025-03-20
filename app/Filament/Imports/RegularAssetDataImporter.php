<?php

namespace App\Filament\Imports;

use App\Models\AssetDesa;
use App\Models\AssetDesaData;
use App\Models\RespondAssetDesaDetail;
use App\Models\RespondAssetDesaData;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;

class RegularAssetDataImporter extends Importer
{
    protected static ?string $model = RespondAssetDesaData::class;
    
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('assetdesa_data_id')
                ->label('Asset Data ID')
                ->requiredMapping()
                ->example('1')
                ->rules(['integer', 'exists:assetdesa_data,id']),
                
            ImportColumn::make('jawaban')
                ->label('Jawaban')
                ->requiredMapping()
                ->example('Jawaban')
                ->rules(['required', 'string']),

            ImportColumn::make('respond_assetdesa_detail_id')
                ->label('Respond Asset Desa Detail ID')
                ->requiredMapping()
                ->example('1')
                ->rules(['integer', 'exists:respond_assetdesa_detail,id']),
        ];
    }
    
    protected array $importedResponses = [];
    
    public static function getExampleFileContent(): string
    {
        $headers = ['assetdesa_data_id', 'jawaban', 'respond_assetdesa_detail_id'];
        $rows = [
            ['1', 'Contoh jawaban data 1', '1'],
            ['2', 'Contoh jawaban data 2', '1'],
            ['3', 'Contoh jawaban data 3', '1'],
        ];
        
        $csv = implode(',', $headers) . "\n";
        foreach ($rows as $row) {
            $csv .= implode(',', $row) . "\n";
        }
        
        return $csv;
    }
    
    public function import(Import $import, array $data): void
    {
        if (!empty($data['assetdesa_data_id']) && !empty($data['jawaban']) && !empty($data['respond_assetdesa_detail_id'])) {
            try {
                $assetData = AssetDesaData::find($data['assetdesa_data_id']);
                
                if (!$assetData) {
                    Log::warning('Asset data tidak ditemukan', ['assetdesa_data_id' => $data['assetdesa_data_id']]);
                    $import->skipRow();
                    return;
                }
                
                $respondDetail = RespondAssetDesaDetail::find($data['respond_assetdesa_detail_id']);
                
                if (!$respondDetail) {
                    Log::warning('Respond asset desa detail tidak ditemukan', [
                        'respond_assetdesa_detail_id' => $data['respond_assetdesa_detail_id']
                    ]);
                    $import->skipRow();
                    return;
                }
                
                $existingResponse = RespondAssetDesaData::where('respond_assetdesa_detail_id', $data['respond_assetdesa_detail_id'])
                    ->where('assetdesa_data_id', $data['assetdesa_data_id'])
                    ->first();
                
                if ($existingResponse) {
                    Log::warning('Jawaban data asset sudah ada. Melakukan update.', [
                        'id' => $existingResponse->id,
                        'assetdesa_data_id' => $data['assetdesa_data_id']
                    ]);
                    
                    $existingResponse->jawaban = $data['jawaban'];
                    $existingResponse->save();
                } else {
                    $newResponse = RespondAssetDesaData::create([
                        'respond_assetdesa_detail_id' => $data['respond_assetdesa_detail_id'],
                        'assetdesa_data_id' => $data['assetdesa_data_id'],
                        'jawaban' => $data['jawaban']
                    ]);
                    
                    $respondAssetDesaId = $respondDetail->respond_assetdesa_id;
                    if (!isset($this->importedResponses[$respondAssetDesaId])) {
                        $this->importedResponses[$respondAssetDesaId] = [];
                    }
                    $this->importedResponses[$respondAssetDesaId][] = $data['assetdesa_data_id'];
                    
                    Log::info('Berhasil membuat data jawaban asset', [
                        'id' => $newResponse->id,
                        'assetdesa_data_id' => $data['assetdesa_data_id'],
                        'jawaban' => $data['jawaban']
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Gagal membuat record jawaban asset: ' . $e->getMessage(), [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        } else {
            $import->skipRow();
            Log::info('Melewati baris yang tidak valid - data tidak lengkap');
        }
    }
    
    public function resolveRecord(): ?RespondAssetDesaData
    {
        return new RespondAssetDesaData();
    }

    public function getValidationMessages(): array
    {
        return [
            'assetdesa_data_id.required' => 'ID data aset harus diisi',
            'assetdesa_data_id.exists' => 'ID data aset tidak ditemukan dalam database',
            'jawaban.required' => 'Jawaban harus diisi',
            'respond_assetdesa_detail_id.required' => 'ID detail respond aset desa harus diisi',
            'respond_assetdesa_detail_id.exists' => 'ID detail respond aset desa tidak ditemukan dalam database',
        ];
    }
    
    public static function getCompletedNotificationBody(Import $import): string
    {
        $count = $import->successful_rows;
        return "Berhasil mengimpor {$count} jawaban data aset.";
    }
}