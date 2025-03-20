<?php

namespace App\Filament\Imports;

use App\Models\AssetDesa;
use App\Models\AssetDesaData;
use App\Models\RespondAssetDesaDetail;
use App\Models\RespondAssetDesaJenisKelamin;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;

class JenisKelaminAssetDataImporter extends Importer
{
    protected static ?string $model = RespondAssetDesaDetail::class;
    
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('jenis_asset')
                ->label('Jenis Asset')
                ->requiredMapping()
                ->example('Penduduk Berdasarkan Jenis Kelamin')
                ->rules(['required', 'string']),
                
            ImportColumn::make('nama_data')
                ->label('Nama Data')
                ->requiredMapping()
                ->example('Dewasa')
                ->rules(['required', 'string']),
                
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

            ImportColumn::make('respondsurvey_id')
                ->label('Respond Survey ID')
                ->required()
                ->hidden()
                ->rules(['integer', 'exists:respond_assetdesa,id']),
        ];
    }
    
    public function import(Import $import, array $data): void
    {
        try {
            // Find the Asset Desa by jenis
            $assetDesa = AssetDesa::where('jenis', $data['jenis_asset'])
                ->where('is_jenis_kelamin', true)
                ->first();
            
            if (!$assetDesa) {
                \Log::warning('Jenis kelamin asset not found: ' . $data['jenis_asset']);
                $import->skipRow();
                return;
            }
            
            // Find the asset data by name
            $assetData = AssetDesaData::where('assetdesa_id', $assetDesa->id)
                ->where('nama', $data['nama_data'])
                ->first();
                
            if (!$assetData) {
                \Log::warning('Asset data not found: ' . $data['nama_data']);
                $import->skipRow();
                return;
            }
            
            // Find or create the respond detail
            $respondDetail = RespondAssetDesaDetail::firstOrCreate(
                [
                    'respond_assetdesa_id' => $data['respondsurvey_id'],
                    'assetdesa_id' => $assetDesa->id,
                    'assetdesa_data_id' => $assetData->id,
                ]
            );
            
            // Find or create the jenis kelamin data
            $existingData = RespondAssetDesaJenisKelamin::where('respond_assetdesa_detail_id', $respondDetail->id)
                ->where('assetdesa_data_id', $assetData->id)
                ->first();
                
            if ($existingData) {
                $existingData->update([
                    'jawaban_laki_laki' => $data['jawaban_laki_laki'],
                    'jawaban_perempuan' => $data['jawaban_perempuan']
                ]);
                \Log::info('Updated jenis kelamin data', ['id' => $existingData->id]);
            } else {
                $newData = RespondAssetDesaJenisKelamin::create([
                    'respond_assetdesa_detail_id' => $respondDetail->id,
                    'assetdesa_data_id' => $assetData->id,
                    'jawaban_laki_laki' => $data['jawaban_laki_laki'],
                    'jawaban_perempuan' => $data['jawaban_perempuan']
                ]);
                \Log::info('Created jenis kelamin data', ['id' => $newData->id]);
            }
        } catch (\Exception $e) {
            \Log::error('Import error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            throw $e;
        }
    }
    
    public static function getCompletedNotificationBody(Import $import): string
    {
        $count = $import->successful_rows;
        return "Berhasil mengimpor {$count} data jenis kelamin.";
    }
}