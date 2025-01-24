<?php

namespace App\Filament\Clusters\DimensiCluster\Resources;

use App\Filament\Clusters\DimensiCluster;
use App\Filament\Clusters\DimensiCluster\Resources\SubVariabelResource\Pages;
use App\Filament\Clusters\DimensiCluster\Resources\SubVariabelResource\RelationManagers;
use App\Models\SubVariabel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubVariabelResource extends Resource
{
    protected static ?string $model = SubVariabel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Sub Variabel';

    protected static ?string $cluster = DimensiCluster::class;

    public static function getLabel(): string
    {
        return 'Sub Variabel';
    }

    public static function getPluralLabel(): string
    {
        return 'Sub Variabel';
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
            'index' => Pages\ListSubVariabels::route('/'),
            'create' => Pages\CreateSubVariabel::route('/create'),
            'edit' => Pages\EditSubVariabel::route('/{record}/edit'),
        ];
    }
}
