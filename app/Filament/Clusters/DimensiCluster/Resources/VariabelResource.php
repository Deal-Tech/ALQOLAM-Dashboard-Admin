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

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Variabel';

    protected static ?int $navigationSort = 2;

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
                Forms\Components\Select::make('dimensi_id')
                    ->relationship('dimensi', 'nama'),
                
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->placeholder('Nama Variabel'),
                    
                Forms\Components\Textarea::make('deskripsi')
                    ->placeholder('Deskripsi Variabel'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('dimensi.nama')
                    ->label('Dimensi'),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Variabel'),

                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi Variabel'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Dibuat Pada'),
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
            'index' => Pages\ManageVariabels::route('/'),
        ];
    }
}
