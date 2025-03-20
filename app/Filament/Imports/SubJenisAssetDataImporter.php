<?php

namespace App\Filament\Imports;

use App\Models\AssetDesa;
use App\Models\AssetDesaSubJenis;
use App\Models\AssetDesaSubJenisData;
use App\Models\RespondAssetDesaDetail;
use App\Models\RespondAssetDesaSubJenisData;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;

class SubJenisAssetDataImporter extends Importer
{
    protected static ?string $model = RespondAssetDesaSubJenisData::class;
    
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('assetdesa_sub_jenis_data_id')
                ->label('Asset Sub Jenis Data ID')
                ->requiredMapping()
                ->example('1')
                ->rules(['required', 'integer', 'exists:assetdesa_sub_jenis_data,id']),
                
            ImportColumn::make('jawaban')
                ->label('Jawaban')
                ->requiredMapping()
                ->example('500')
                ->rules(['required', 'string']),

            ImportColumn::make('respond_assetdesa_detail_id')
                ->label('Respond Asset Desa Detail ID')
                ->requiredMapping()
                ->rules(['integer', 'exists:respond_assetdesa_detail,id']),
        ];
    }
    
    public static function getCsvContents(): string
    {
        $subJenisDataItems = AssetDesaSubJenisData::take(5)->get();
        $respondDetails = RespondAssetDesaDetail::take(1)->get();
        
        $headers = ['assetdesa_sub_jenis_data_id', 'jawaban', 'respond_assetdesa_detail_id'];
        $rows = [];
        
        $sampleRespondDetailId = $respondDetails->isNotEmpty() ? $respondDetails->first()->id : '1';
        
        if ($subJenisDataItems->isNotEmpty()) {
            foreach ($subJenisDataItems as $dataItem) {
                $rows[] = [
                    $dataItem->id,
                    'Contoh jawaban',
                    $sampleRespondDetailId
                ];
            }
        }
        
        if (empty($rows)) {
            $rows = [
                ['1', '500', $sampleRespondDetailId],
                ['2', '30', $sampleRespondDetailId],
                ['3', '300', $sampleRespondDetailId],
                ['4', '25', $sampleRespondDetailId]
            ];
        }
        
        $csv = implode(',', $headers) . "\n";
        foreach ($rows as $row) {
            $csv .= implode(',', $row) . "\n";
        }
        
        return $csv;
    }
    
    public function getValidationMessages(): array
    {
        return [
            'assetdesa_sub_jenis_data_id.required' => 'ID Data Asset Sub Jenis harus diisi',
            'assetdesa_sub_jenis_data_id.integer' => 'ID Data Asset Sub Jenis harus berupa angka',
            'assetdesa_sub_jenis_data_id.exists' => 'ID Data Asset Sub Jenis tidak ditemukan',
            'jawaban.required' => 'Jawaban harus diisi',
            'respond_assetdesa_detail_id.required' => 'ID Respond Asset Desa Detail harus diisi',
            'respond_assetdesa_detail_id.exists' => 'ID Respond Asset Desa Detail tidak ditemukan',
        ];
    }
    
    public function import(Import $import, array $data): void
    {
        try {
            if (empty($data['assetdesa_sub_jenis_data_id']) || empty($data['jawaban']) || empty($data['respond_assetdesa_detail_id'])) {
                Log::warning('Data tidak lengkap', $data);
                $import->skipRow();
                return;
            }
            
            $subJenisData = AssetDesaSubJenisData::find($data['assetdesa_sub_jenis_data_id']);
            if (!$subJenisData) {
                Log::warning('Data asset sub jenis tidak ditemukan', ['id' => $data['assetdesa_sub_jenis_data_id']]);
                $import->skipRow();
                return;
            }
            
            $respondDetail = RespondAssetDesaDetail::find($data['respond_assetdesa_detail_id']);
            if (!$respondDetail) {
                Log::warning('Respond asset desa detail tidak ditemukan', ['id' => $data['respond_assetdesa_detail_id']]);
                $import->skipRow();
                return;
            }
            
            $existingData = RespondAssetDesaSubJenisData::where('respond_assetdesa_detail_id', $data['respond_assetdesa_detail_id'])
                ->where('assetdesa_sub_jenis_data_id', $data['assetdesa_sub_jenis_data_id'])
                ->first();
                
            if ($existingData) {
                $existingData->update(['jawaban' => $data['jawaban']]);
                Log::info('Updated sub jenis data', ['id' => $existingData->id]);
            } else {
                $newData = RespondAssetDesaSubJenisData::create([
                    'respond_assetdesa_detail_id' => $data['respond_assetdesa_detail_id'],
                    'assetdesa_sub_jenis_data_id' => $data['assetdesa_sub_jenis_data_id'],
                    'jawaban' => $data['jawaban']
                ]);
                Log::info('Created sub jenis data', ['id' => $newData->id]);
            }
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            throw $e;
        }
    }
    
    public function resolveRecord(): ?RespondAssetDesaSubJenisData
    {
        return new RespondAssetDesaSubJenisData();
    }
    
    public static function getCompletedNotificationBody(Import $import): string
    {
        $count = $import->successful_rows;
        return "Berhasil mengimpor {$count} data sub jenis.";
    }
}