<?php

namespace App\Filament\Clusters\DimensiCluster\Resources;

use App\Filament\Clusters\DimensiCluster;
use App\Filament\Clusters\DimensiCluster\Resources\DimensiResource\Pages;
use App\Filament\Clusters\DimensiCluster\Resources\DimensiResource\RelationManagers;
use App\Models\Dimensi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DimensiResource extends Resource
{
    protected static ?string $model = Dimensi::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationLabel = 'Dimensi';

    protected static ?string $cluster = DimensiCluster::class;

    protected static ?string $slug = 'dimensi';

    public static function getLabel(): string
    {
        return 'Dimensi';
    }

    public static function getPluralLabel(): string
    {
        return 'Dimensi';
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Nama')
                    ->required(),
                Forms\Components\TextInput::make('deskripsi')
                    ->label('Deskripsi'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama'),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime(),
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
            'index' => Pages\ListDimensis::route('/'),
            'create' => Pages\CreateDimensi::route('/create'),
            'edit' => Pages\EditDimensi::route('/{record}/edit'),
        ];
    }
}
