<?php

namespace App\Filament\Clusters\DimensiCluster\Resources;

use App\Filament\Clusters\DimensiCluster;
use App\Filament\Clusters\DimensiCluster\Resources\VariabelResource\Pages;
use App\Filament\Clusters\DimensiCluster\Resources\VariabelResource\RelationManagers;
use App\Models\Variabel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VariabelResource extends Resource
{
    protected static ?string $model = Variabel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Variabel';

    protected static ?string $cluster = DimensiCluster::class;

    public static function getLabel(): string
    {
        return 'Variabel';
    }

    public static function getPluralLabel(): string
    {
        return 'Variabel';
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
            'index' => Pages\ListVariabels::route('/'),
            'create' => Pages\CreateVariabel::route('/create'),
            'edit' => Pages\EditVariabel::route('/{record}/edit'),
        ];
    }
}
