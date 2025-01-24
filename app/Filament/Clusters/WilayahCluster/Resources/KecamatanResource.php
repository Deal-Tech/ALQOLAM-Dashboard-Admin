<?php

namespace App\Filament\Clusters\WilayahCluster\Resources;

use App\Filament\Clusters\WilayahCluster;
use App\Filament\Clusters\WilayahCluster\Resources\KecamatanResource\Pages;
use App\Filament\Clusters\WilayahCluster\Resources\KecamatanResource\RelationManagers;
use App\Models\Kecamatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KecamatanResource extends Resource
{
    protected static ?string $model = Kecamatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Kecamatan';

    protected static ?string $cluster = WilayahCluster::class;

    public static function getLabel(): string
    {
        return 'Kecamatan';
    }

    public static function getPluralLabel(): string
    {
        return 'Kecamatan';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKecamatans::route('/'),
            'create' => Pages\CreateKecamatan::route('/create'),
            'view' => Pages\ViewKecamatan::route('/{record}'),
            'edit' => Pages\EditKecamatan::route('/{record}/edit'),
        ];
    }
}
