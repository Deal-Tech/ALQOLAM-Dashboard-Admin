<?php

namespace App\Filament\Clusters\WilayahCluster\Resources;

use App\Filament\Clusters\WilayahCluster;
use App\Filament\Clusters\WilayahCluster\Resources\DesaResource\Pages;
use App\Filament\Clusters\WilayahCluster\Resources\DesaResource\RelationManagers;
use App\Models\Desa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DesaResource extends Resource
{
    protected static ?string $model = Desa::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    
    protected static ?string $slug = 'desa';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = WilayahCluster::class;

    public static function getLabel(): string
    {
        return 'Desa';
    }

    public static function getPluralLabel(): string
    {
        return 'Desa';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('kecamatan_id')
                    ->relationship('kecamatan', 'nama')
                    ->label('Kecamatan')
                    ->required(),
                
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->placeholder('Nama Desa'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable()    
                    ->label('Nama'),

                Tables\Columns\TextColumn::make('kecamatan.nama')
                    ->label('Kecamatan'),

                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->label('Dibuat Pada'),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kecamatan_id')
                    ->relationship('kecamatan', 'nama')
                    ->label('Kecamatan'),
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
            'index' => Pages\ManageDesas::route('/'),
        ];
    }
}
