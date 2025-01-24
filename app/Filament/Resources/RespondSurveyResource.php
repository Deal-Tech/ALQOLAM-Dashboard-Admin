<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RespondSurveyResource\Pages;
use App\Filament\Resources\RespondSurveyResource\RelationManagers;
use App\Models\RespondSurvey;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RespondSurveyResource extends Resource
{
    protected static ?string $model = RespondSurvey::class;

    protected static ?string $navigationIcon = 'heroicon-m-rectangle-group';

    protected static ?string $navigationGroup = 'Hasil';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Hasil Survei';

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
            'index' => Pages\ListRespondSurveys::route('/'),
            'create' => Pages\CreateRespondSurvey::route('/create'),
            'view' => Pages\ViewRespondSurvey::route('/{record}'),
            'edit' => Pages\EditRespondSurvey::route('/{record}/edit'),
        ];
    }
}
