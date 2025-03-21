<?php

namespace App\Filament\Exports;

use App\Models\AssetDesa;
use App\Models\AssetDesaData;
use App\Models\AssetDesaSubJenis;
use App\Models\AssetDesaSubJenisData;
use App\Models\AssetDesaJenisKelamin;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AssetDesaExporter extends Exporter
{
    protected static ?string $model = AssetDesa::class;
    
    protected static string $format = 'xlsx';

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
                
            ExportColumn::make('jenis')
                ->label('Jenis Asset'),
                
            ExportColumn::make('is_data')
                ->label('Is Data')
                ->formatStateUsing(fn ($state) => $state ? 'Ya' : 'Tidak'),
                
            ExportColumn::make('is_sub_jenis')
                ->label('Is Sub Jenis')
                ->formatStateUsing(fn ($state) => $state ? 'Ya' : 'Tidak'),
                
            ExportColumn::make('is_jenis_kelamin')
                ->label('Is Jenis Kelamin')
                ->formatStateUsing(fn ($state) => $state ? 'Ya' : 'Tidak'),
                
            ExportColumn::make('regular_data')
                ->label('Data Reguler')
                ->state(function (AssetDesa $record): string {
                    if (!$record->is_data || $record->is_sub_jenis) {
                        return '';
                    }
                    
                    $dataItems = AssetDesaData::where('assetdesa_id', $record->id)
                        ->orderBy('id')
                        ->get();
                    
                    if ($dataItems->isEmpty()) {
                        return '';
                    }
                    
                    $output = [];
                    foreach ($dataItems as $item) {
                        $multiple = $item->is_multiple_answer ? ' (Multiple)' : '';
                        $nama = str_replace(['"', ':', '\n', '\r'], ['', '-', ' ', ' '], $item->nama);
                        
                        $lineText = "ID {$item->id} - {$nama}{$multiple}";
                        if (str_starts_with(trim($lineText), '=') || str_starts_with(trim($lineText), '-')) {
                            $lineText = "' " . $lineText;
                        }
                        
                        $output[] = $lineText;
                    }
                    
                    return implode("\n", $output);
                }),
                
            ExportColumn::make('sub_jenis_data')
                ->label('Data Sub Jenis')
                ->state(function (AssetDesa $record): string {
                    if (!$record->is_data || !$record->is_sub_jenis) {
                        return '';
                    }
                    
                    $subJenisItems = DB::table('assetdesa_sub_jenis')
                        ->where('assetdesa_id', $record->id)
                        ->orderBy('id')
                        ->get();
                    
                    if ($subJenisItems->isEmpty()) {
                        return 'Tidak ada sub jenis untuk asset ini';
                    }
                    
                    $output = [];
                    foreach ($subJenisItems as $subJenis) {
                        $subjenis_text = str_replace(['"', ':', '\n', '\r'], ['', '-', ' ', ' '], $subJenis->subjenis);
                        
                        $headerText = "*** Sub Jenis {$subjenis_text} (ID {$subJenis->id}) ***";
                        if (str_starts_with(trim($headerText), '=') || str_starts_with(trim($headerText), '-')) {
                            $headerText = "' " . $headerText;
                        }
                        
                        $output[] = $headerText;
                        
                        $dataItems = DB::table('assetdesa_sub_jenis_data')
                            ->where('subjenis_id', $subJenis->id)
                            ->orderBy('id')
                            ->get();
                        
                        if ($dataItems->isEmpty()) {
                            $output[] = "Tidak ada data";
                        } else {
                            foreach ($dataItems as $item) {
                                $nama = str_replace(['"', ':', '\n', '\r'], ['', '-', ' ', ' '], $item->nama);
                                
                                $lineText = "ID {$item->id} - {$nama}";
                                if (str_starts_with(trim($lineText), '=') || str_starts_with(trim($lineText), '-')) {
                                    $lineText = "' " . $lineText;
                                }
                                
                                $output[] = $lineText;
                            }
                        }
                        
                        $output[] = ""; 
                    }
                    
                    return implode("\n", $output);
                }),
                
            ExportColumn::make('jenis_kelamin_data')
                ->label('Data Jenis Kelamin')
                ->state(function (AssetDesa $record): string {
                    if (!$record->is_jenis_kelamin) {
                        return '';
                    }
                    
                    $jenisKelaminItems = AssetDesaJenisKelamin::where('assetdesa_id', $record->id)
                        ->orderBy('id')
                        ->get();
                    
                    if ($jenisKelaminItems->isEmpty()) {
                        return '';
                    }
                    
                    $output = [];
                    foreach ($jenisKelaminItems as $item) {
                        $nama = str_replace(['"', ':', '\n', '\r'], ['', '-', ' ', ' '], $item->nama);
                        
                        $lineText = "ID {$item->id} - {$nama}";
                        if (str_starts_with(trim($lineText), '=') || str_starts_with(trim($lineText), '-')) {
                            $lineText = "' " . $lineText;
                        }
                        
                        $output[] = $lineText;
                    }
                    
                    return implode("\n", $output);
                }),
            
            ExportColumn::make('created_at')
                ->label('Dibuat Pada'),
                
            ExportColumn::make('updated_at')
                ->label('Diperbarui Pada'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->select([
                'id', 
                'jenis', 
                'is_data', 
                'is_sub_jenis', 
                'is_jenis_kelamin',
                'created_at',
                'updated_at'
            ])
            ->orderBy('jenis');
    }
    
    public static function getExportName(): string
    {
        return 'Data Asset Desa - ' . now()->format('d-m-Y');
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $count = $export->successful_rows;
        return "Berhasil mengekspor {$count} data aset desa.";
    }
}