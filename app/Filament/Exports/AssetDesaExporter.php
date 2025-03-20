<?php

namespace App\Filament\Exports;

use App\Models\AssetDesa;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;

class AssetDesaExporter extends Exporter
{
    protected static ?string $model = AssetDesa::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('jenis')
                ->label('Jenis Asset'),
            ExportColumn::make('is_data')
                ->label('Is Data')
                ->formatStateUsing(fn (bool $state) => $state ? 'Ya' : 'Tidak'),
            ExportColumn::make('is_sub_jenis')
                ->label('Is Sub Jenis')
                ->formatStateUsing(fn (bool $state) => $state ? 'Ya' : 'Tidak'),
            ExportColumn::make('is_jenis_kelamin')
                ->label('Is Jenis Kelamin')
                ->formatStateUsing(fn (bool $state) => $state ? 'Ya' : 'Tidak'),
            
            // Add detailed data information
            ExportColumn::make('data_details')
                ->label('Data Detail')
                ->state(function (AssetDesa $record): string {
                    if (!$record->is_data) {
                        return 'Tidak ada data';
                    }
                    
                    // For regular data (not sub jenis)
                    if (!$record->is_sub_jenis) {
                        $dataItems = $record->data->map(function ($item) {
                            return $item->nama . ($item->is_multiple_answer ? ' (Multiple)' : '');
                        })->implode(', ');
                        
                        return $dataItems ?: 'Tidak ada data';
                    }
                    
                    // For data with sub jenis
                    $subJenisData = [];
                    foreach ($record->subJenis as $subJenis) {
                        $dataItems = $subJenis->data->map(function ($item) {
                            return $item->nama . ($item->is_multiple_answer ? ' (Multiple)' : '');
                        })->implode(', ');
                        
                        $subJenisData[] = $subJenis->subjenis . ': ' . ($dataItems ?: 'Tidak ada data');
                    }
                    
                    return implode(' | ', $subJenisData) ?: 'Tidak ada data sub jenis';
                }),
            
            // Add jenis kelamin information
            ExportColumn::make('jenis_kelamin_data')
                ->label('Jenis Kelamin')
                ->state(function (AssetDesa $record): string {
                    if (!$record->is_jenis_kelamin) {
                        return 'Bukan data jenis kelamin';
                    }
                    
                    $jenisKelamin = $record->jenisKelamin->pluck('nama')->implode(', ');
                    return $jenisKelamin ?: 'Tidak ada data jenis kelamin';
                }),
            
            ExportColumn::make('created_at')
                ->label('Dibuat Pada'),
            ExportColumn::make('updated_at')
                ->label('Diperbarui Pada'),
        ];
    }

    // Customize the behavior to include relationships
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['data', 'subJenis.data', 'jenisKelamin']);
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $count = $export->successful_rows;
        return "Ekspor {$count} data aset desa beserta detailnya telah selesai.";
    }
}