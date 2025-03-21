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

    protected static ?string $navigationLabel = 'Indikator';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = DimensiCluster::class;

    public static function getLabel(): string
    {
        return 'Indikator';
    }

    public static function getPluralLabel(): string
    {
        return 'Indikator';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('variabel_id')
                    ->relationship('variabel', 'nama'),
                
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->placeholder('Nama Indikator'),
                Forms\Components\TextInput::make('bobot')
                    ->required()
                    ->placeholder('Bobot'),

                Forms\Components\Textarea::make('deskripsi')
                    ->placeholder('Deskripsi Indikator'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable()
                    ->label('Nama Indikator')
                    ->formatStateUsing(function ($state) {
                        $words = explode(' ', $state);
                        return implode(' ', array_slice($words, 0, 5)) . (count($words) > 10 ? '...' : '');
                    }),

                Tables\Columns\TextColumn::make('variabel.nama')
                    ->label('Variabel')
                    ->formatStateUsing(function ($state) {
                        $words = explode(' ', $state);
                        return implode(' ', array_slice($words, 0, 5)) . (count($words) > 10 ? '...' : '');
                    }),

                Tables\Columns\TextColumn::make('bobot')
                    ->label('Bobot'),

                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Dibuat Pada'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('variabel_id')
                    ->relationship('variabel', 'nama'),

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
            'index' => Pages\ManageSubVariabels::route('/'),
        ];
    }
}
