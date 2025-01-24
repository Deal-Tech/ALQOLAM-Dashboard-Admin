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

    protected static ?string $navigationIcon = 'heroicon-o-cube';

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
                Forms\Components\Select::make('variabel_id')
                    ->relationship('variabel', 'nama'),
                
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->placeholder('Nama Sub Variabel'),

                Forms\Components\Textarea::make('deskripsi')
                    ->placeholder('Deskripsi Sub Variabel'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('variabel.nama')
                    ->label('Variabel'),
                
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Sub Variabel'),
                
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Dibuat Pada'),
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
