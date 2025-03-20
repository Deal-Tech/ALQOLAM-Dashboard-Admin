<?php

namespace App\Filament\Imports;

use App\Models\RespondAssetDesaDetail;
use App\Models\AssetDesa;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RespondAsetDesaDetailImporter extends Importer
{
    protected static ?string $model = RespondAssetDesaDetail::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('id')
                ->label('ID')
                ->requiredMapping()
                ->example('1010320251')
                ->helperText('Format: user_id + tanggal + bulan + tahun + 1 dan seterusnya')
                ->rules(['required', 'integer']),
                
            ImportColumn::make('respond_assetdesa_id')
                ->label('Respond Asset Desa ID')
                ->requiredMapping()
                ->example('1')
                ->rules(['required', 'integer', 'exists:respond_assetdesa,id']),
                
            ImportColumn::make('assetdesa_id')
                ->label('Asset Desa ID')
                ->requiredMapping()
                ->example('5')
                ->rules(['required', 'integer', 'exists:assetdesa,id']),
        ];
    }


    public static function getCsvContents(): string
    {
        $currentDate = date('dmY');
        $sampleUserId = 1;
        
        $sampleId1 = $sampleUserId . $currentDate . '1';
        $sampleId2 = $sampleUserId . $currentDate . '2';
        $sampleId3 = $sampleUserId . $currentDate . '3';
        
        $headers = ['id', 'respond_assetdesa_id', 'assetdesa_id'];
        $rows = [
            [$sampleId1, '1', '5'],
            [$sampleId2, '1', '8'],
            [$sampleId3, '2', '5'],
        ];
        
        $csv = "# Format ID: user_id + tanggal + bulan + tahun + nomor urut\n";
        $csv .= "# Contoh: 101032025 (user_id=1, tanggal=01/03/2025, nomor=1)\n\n";
        $csv .= implode(',', $headers) . "\n";
        foreach ($rows as $row) {
            $csv .= implode(',', $row) . "\n";
        }
        
        return $csv;
    }

    public function getValidationMessages(): array
    {
        return [
            'id.required' => 'ID harus diisi',
            'id.integer' => 'ID harus berupa angka',
            'respond_assetdesa_id.required' => 'Respond Asset Desa ID harus diisi',
            'respond_assetdesa_id.exists' => 'Respond Asset Desa ID tidak ditemukan dalam database',
            'assetdesa_id.required' => 'Asset Desa ID harus diisi',
            'assetdesa_id.exists' => 'Asset Desa ID tidak ditemukan dalam database',
        ];
    }

    protected array $importedRecords = [];
    
    public function import(Import $import, array $data): void
    {
        if (!empty($data['id']) && !empty($data['respond_assetdesa_id']) && !empty($data['assetdesa_id'])) {
            try {
                $assetDesa = AssetDesa::find($data['assetdesa_id']);
                
                if (!$assetDesa) {
                    Log::warning('Asset desa tidak ditemukan', ['assetdesa_id' => $data['assetdesa_id']]);
                    $import->skipRow();
                    return;
                }
                
                $uniqueId = $data['id'];
                
                $existingRecord = RespondAssetDesaDetail::where('respond_assetdesa_id', $data['respond_assetdesa_id'])
                    ->where('assetdesa_id', $data['assetdesa_id'])
                    ->first();
                
                if ($existingRecord) {
                    Log::warning('Record respond asset desa detail sudah ada. Melewati baris.', [
                        'respond_assetdesa_id' => $data['respond_assetdesa_id'],
                        'assetdesa_id' => $data['assetdesa_id']
                    ]);
                    $import->skipRow();
                    return;
                }
                
                $existingId = RespondAssetDesaDetail::find($uniqueId);
                if ($existingId) {
                    Log::warning('ID sudah digunakan.', [
                        'id' => $uniqueId
                    ]);
                    $import->skipRow();
                    return;
                }
                
                try {
                    $detail = new RespondAssetDesaDetail();
                    $detail->id = $uniqueId;
                    $detail->respond_assetdesa_id = $data['respond_assetdesa_id'];
                    $detail->assetdesa_id = $data['assetdesa_id'];
                    $detail->save();
                    
                    $key = $data['respond_assetdesa_id'] . '-' . $data['assetdesa_id'];
                    $this->importedRecords[$key] = true;
                    
                    Log::info('Berhasil membuat record respond asset desa detail', [
                        'id' => $uniqueId,
                        'respond_assetdesa_id' => $data['respond_assetdesa_id'],
                        'assetdesa_id' => $data['assetdesa_id']
                    ]);
                } catch (\Exception $innerException) {
                    Log::warning('Failed with Eloquent, trying raw query: ' . $innerException->getMessage());
                    
                    DB::statement('
                        INSERT INTO respond_assetdesa_detail 
                        (id, respond_assetdesa_id, assetdesa_id, created_at, updated_at) 
                        VALUES (?, ?, ?, NOW(), NOW())
                    ', [$uniqueId, $data['respond_assetdesa_id'], $data['assetdesa_id']]);
                    
                    Log::info('Berhasil membuat record respond asset desa detail dengan raw query', [
                        'id' => $uniqueId,
                        'respond_assetdesa_id' => $data['respond_assetdesa_id'],
                        'assetdesa_id' => $data['assetdesa_id']
                    ]);
                }
                
            } catch (\Exception $e) {
                Log::error('Gagal membuat record respond asset desa detail: ' . $e->getMessage(), [
                    'error' => $e->getMessage(),
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
    
    public function resolveRecord(): ?RespondAssetDesaDetail
    {
        return new RespondAssetDesaDetail();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $count = $import->successful_rows;
        return "Berhasil mengimpor {$count} data respond asset desa detail.";
    }
}