<?php

namespace App\Filament\Imports;

use App\Models\AssetDesa;
use App\Models\AssetDesaData;
use App\Models\RespondAssetDesaDetail;
use App\Models\RespondAssetDesaJenisKelamin;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;

class JenisKelaminAssetDataImporter extends Importer
{
    protected static ?string $model = RespondAssetDesaJenisKelamin::class;
    
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('assetdesa_data_id')
                ->label('assetdesa_data_id')
                ->requiredMapping()
                ->example('1')
                ->rules(['required', 'integer', 'exists:assetdesa_data,id']),
                
            ImportColumn::make('jawaban_laki_laki')
                ->label('Laki-laki')
                ->requiredMapping()
                ->example('5000')
                ->numeric()
                ->rules(['required', 'numeric']),
                
            ImportColumn::make('jawaban_perempuan')
                ->label('Perempuan')
                ->requiredMapping()
                ->example('4500')
                ->numeric()
                ->rules(['required', 'numeric']),

            ImportColumn::make('respond_assetdesa_detail_id')
                ->label('Respond Asset Desa Detail ID')
                ->requiredMapping() 
                ->rules(['integer', 'exists:respond_assetdesa_detail,id']),
        ];
    }
    
    public static function getCsvContents(): string
    {
        $assetDataList = AssetDesaData::whereHas('assetDesa', function($query) {
            $query->where('is_jenis_kelamin', true);
        })->take(3)->get();

        $headers = ['assetdesa_data_id', 'jawaban_laki_laki', 'jawaban_perempuan', 'respond_assetdesa_detail_id'];
        $rows = [];
        
        $sampleDetailId = RespondAssetDesaDetail::first()?->id ?? '1';
        
        if ($assetDataList->isNotEmpty()) {
            foreach ($assetDataList as $assetData) {
                $rows[] = [
                    $assetData->id,
                    mt_rand(1000, 5000),  
                    mt_rand(1000, 5000),  
                    $sampleDetailId
                ];
            }
        }
        
        if (empty($rows)) {
            $rows = [
                ['1', '2500', '2300', $sampleDetailId],
                ['2', '3500', '3200', $sampleDetailId],
                ['3', '8000', '7500', $sampleDetailId]
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
            'assetdesa_data_id.required' => 'Asset Desa Data ID harus diisi',
            'assetdesa_data_id.integer' => 'Asset Desa Data ID harus berupa angka',
            'assetdesa_data_id.exists' => 'Asset Desa Data ID tidak ditemukan',
            'jawaban_laki_laki.required' => 'Jumlah Laki-laki harus diisi',
            'jawaban_laki_laki.numeric' => 'Jumlah Laki-laki harus berupa angka',
            'jawaban_perempuan.required' => 'Jumlah Perempuan harus diisi',
            'jawaban_perempuan.numeric' => 'Jumlah Perempuan harus berupa angka',
            'respond_assetdesa_detail_id.required' => 'Respond Asset Desa Detail ID harus diisi',
            'respond_assetdesa_detail_id.exists' => 'Respond Asset Desa Detail ID tidak ditemukan',
        ];
    }
    
    public function import(Import $import, array $data): void
    {
        if (!empty($data['assetdesa_data_id']) && !empty($data['jawaban_laki_laki']) && 
            !empty($data['jawaban_perempuan']) && !empty($data['respond_assetdesa_detail_id'])) {
            try {
            
                $assetData = AssetDesaData::find($data['assetdesa_data_id']);
                
                if (!$assetData) {
                    Log::warning('Asset data tidak ditemukan', ['assetdesa_data_id' => $data['assetdesa_data_id']]);
                    $import->skipRow();
                    return;
                }
                
            
                $assetDesa = $assetData->assetDesa;
                if (!$assetDesa || !$assetDesa->is_jenis_kelamin) {
                    Log::warning('Asset bukan tipe jenis kelamin', ['assetdesa_id' => $assetDesa->id ?? 'unknown']);
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
                
           
                $existingData = RespondAssetDesaJenisKelamin::where('respond_assetdesa_detail_id', $data['respond_assetdesa_detail_id'])
                    ->where('assetdesa_data_id', $data['assetdesa_data_id'])
                    ->first();
                    
                if ($existingData) {
                    $existingData->update([
                        'jawaban_laki_laki' => $data['jawaban_laki_laki'],
                        'jawaban_perempuan' => $data['jawaban_perempuan']
                    ]);
                    Log::info('Updated jenis kelamin data', ['id' => $existingData->id]);
                } else {
                    $newData = RespondAssetDesaJenisKelamin::create([
                        'respond_assetdesa_detail_id' => $data['respond_assetdesa_detail_id'],
                        'assetdesa_data_id' => $data['assetdesa_data_id'],
                        'jawaban_laki_laki' => $data['jawaban_laki_laki'],
                        'jawaban_perempuan' => $data['jawaban_perempuan']
                    ]);
                    Log::info('Created jenis kelamin data', ['id' => $newData->id]);
                }
            } catch (\Exception $e) {
                Log::error('Import error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'data' => $data
                ]);
                throw $e;
            }
        } else {
            $import->skipRow();
            Log::info('Melewati baris yang tidak valid - data tidak lengkap');
        }
    }
    
    public function resolveRecord(): ?RespondAssetDesaJenisKelamin
    {
        return new RespondAssetDesaJenisKelamin();
    }
    
    public static function getCompletedNotificationBody(Import $import): string
    {
        $count = $import->successful_rows;
        return "Berhasil mengimpor {$count} data jenis kelamin.";
    }
}