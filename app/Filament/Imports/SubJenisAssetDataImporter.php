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

class SubJenisAssetDataImporter extends Importer
{
    protected static ?string $model = RespondAssetDesaDetail::class;
    
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('jenis_asset')
                ->label('Jenis Asset')
                ->requiredMapping()
                ->example('Pendidikan')
                ->rules(['required', 'string']),
                
            ImportColumn::make('nama_subjenis')
                ->label('Sub Jenis')
                ->requiredMapping()
                ->example('SD')
                ->rules(['required', 'string']),
                
            ImportColumn::make('nama_data')
                ->label('Nama Data')
                ->requiredMapping()
                ->example('Jumlah Siswa')
                ->rules(['required', 'string']),
                
            ImportColumn::make('jawaban')
                ->label('Jawaban')
                ->requiredMapping()
                ->example('500')
                ->rules(['required', 'string']),

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
                ->where('is_data', true)
                ->where('is_sub_jenis', true)
                ->first();
            
            if (!$assetDesa) {
                \Log::warning('Sub jenis asset not found: ' . $data['jenis_asset']);
                $import->skipRow();
                return;
            }
            
            // Find the sub jenis by name
            $subJenis = AssetDesaSubJenis::where('assetdesa_id', $assetDesa->id)
                ->where('subjenis', $data['nama_subjenis'])
                ->first();
                
            if (!$subJenis) {
                \Log::warning('Sub jenis not found: ' . $data['nama_subjenis']);
                $import->skipRow();
                return;
            }
            
            // Find the sub jenis data by name
            $subJenisData = AssetDesaSubJenisData::where('subjenis_id', $subJenis->id)
                ->where('nama', $data['nama_data'])
                ->first();
                
            if (!$subJenisData) {
                \Log::warning('Sub jenis data not found: ' . $data['nama_data']);
                $import->skipRow();
                return;
            }
            
            // Find or create the respond detail
            $respondDetail = RespondAssetDesaDetail::firstOrCreate(
                [
                    'respond_assetdesa_id' => $data['respondsurvey_id'],
                    'assetdesa_id' => $assetDesa->id,
                    'assetdesa_sub_jenis_id' => $subJenis->id,
                    'assetdesa_sub_jenis_data_id' => $subJenisData->id,
                ]
            );
            
            // Find or create the sub jenis data response
            $existingData = RespondAssetDesaSubJenisData::where('respond_assetdesa_detail_id', $respondDetail->id)
                ->where('assetdesa_sub_jenis_data_id', $subJenisData->id)
                ->first();
                
            if ($existingData) {
                $existingData->update(['jawaban' => $data['jawaban']]);
                \Log::info('Updated sub jenis data', ['id' => $existingData->id]);
            } else {
                $newData = RespondAssetDesaSubJenisData::create([
                    'respond_assetdesa_detail_id' => $respondDetail->id,
                    'assetdesa_sub_jenis_data_id' => $subJenisData->id,
                    'jawaban' => $data['jawaban']
                ]);
                \Log::info('Created sub jenis data', ['id' => $newData->id]);
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
        return "Berhasil mengimpor {$count} data sub jenis.";
    }
}