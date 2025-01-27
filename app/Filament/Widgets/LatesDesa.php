<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Desa;

class LatesDesa extends BaseWidget
{

    protected static ?int $sort = 3;

    protected static ?string $heading = 'Desa baru ditambah';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Desa::query()
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama')->label('Nama Desa'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Dibuat Pada'),
            ]);
    }
}
