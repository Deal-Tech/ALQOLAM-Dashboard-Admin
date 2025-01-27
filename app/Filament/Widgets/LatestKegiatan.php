<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Kegiatan;

class LatestKegiatan extends BaseWidget
{

    protected static ?int $sort = 4;

    protected static ?string $heading = 'Kegiatan terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Kegiatan::query()
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('judul')->label('Judul Kegiatan'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Dibuat Pada'),
            ]);
    }
}
