<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BLogResource\Pages;
use App\Filament\Resources\BLogResource\RelationManagers;
use App\Models\Kegiatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BLogResource extends Resource
{
    protected static ?string $model = Kegiatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Blog';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'Post';

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
            'index' => Pages\ListBLogs::route('/'),
            'create' => Pages\CreateBLog::route('/create'),
            'view' => Pages\ViewBLog::route('/{record}'),
            'edit' => Pages\EditBLog::route('/{record}/edit'),
        ];
    }
}
