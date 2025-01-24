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

    protected static ?string $navigationIcon = 'heroicon-c-arrows-pointing-out';

    protected static ?string $navigationLabel = 'Kecamatan';

    protected static ?string $cluster = WilayahCluster::class;

    protected static ?string $slug = 'kecamatan';

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
                Forms\Components\Select::make('kabupaten_id')
                    ->relationship('kabupaten', 'nama')
                    ->label('Kabupaten')
                    ->required(),
                
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->placeholder('Nama Kecamatan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kabupaten.nama')
                    ->label('Kabupaten'),
                
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Kecamatan'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Dibuat Pada'),
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
