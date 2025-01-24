<?php

namespace App\Filament\Clusters\WilayahCluster\Resources;

use App\Filament\Clusters\WilayahCluster;
use App\Filament\Clusters\WilayahCluster\Resources\KabupatenResource\Pages;
use App\Filament\Clusters\WilayahCluster\Resources\KabupatenResource\RelationManagers;
use App\Models\Kabupaten;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KabupatenResource extends Resource
{
    protected static ?string $model = Kabupaten::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $cluster = WilayahCluster::class;

    protected static ?string $slug = 'kabupaten';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                ->required()
                ->label('Nama Kabupaten'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')->label('Nama Kabupaten'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Dibuat Pada'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageKabupatens::route('/'),
        ];
    }
}
