<?php

namespace App\Filament\Exports;

use App\Models\Survey;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;

class SurveyExporter extends Exporter
{
    protected static ?string $model = Survey::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('pertanyaan')
                ->label('Pertanyaan'),
            ExportColumn::make('dimensi.nama')
                ->label('Dimensi'),
            ExportColumn::make('variabel.nama')
                ->label('Variabel'),
            ExportColumn::make('subvariabel.nama')
                ->label('Indikator'),
            ExportColumn::make('opsi_jawaban_1')
                ->label('Opsi Jawaban 1'),
            ExportColumn::make('opsi_jawaban_2')
                ->label('Opsi Jawaban 2'),
            ExportColumn::make('opsi_jawaban_3')
                ->label('Opsi Jawaban 3'),
            ExportColumn::make('opsi_jawaban_4')
                ->label('Opsi Jawaban 4'),
            ExportColumn::make('opsi_jawaban_5')
                ->label('Opsi Jawaban 5'),
            ExportColumn::make('created_at')
                ->label('Dibuat Pada'),
            ExportColumn::make('updated_at')
                ->label('Diperbarui Pada'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $count = $export->successful_rows;
        return "Ekspor {$count} data survei telah selesai.";
    }
}