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

    public static function getLabel(): string
    {
        return 'Hasil Survei';
    }

    public static function getPluralLabel(): string
    {
        return 'Hasil Survei';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('kabupaten_id')
                    ->relationship('kabupaten', 'nama')
                    ->required(),
                Forms\Components\Select::make('kecamatan_id')
                    ->relationship('kecamatan', 'nama')
                    ->required(),
                Forms\Components\Select::make('desa_id')
                    ->relationship('desa', 'nama')
                    ->required(),
                Forms\Components\Select::make('survey_id')
                    ->relationship('survey', 'pertanyaan')
                    ->required(),
                Forms\Components\Textarea::make('jawaban')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('User'),
                Tables\Columns\TextColumn::make('kabupaten.nama')->label('Kabupaten'),
                Tables\Columns\TextColumn::make('kecamatan.nama')->label('Kecamatan'),
                Tables\Columns\TextColumn::make('desa.nama')->label('Desa'),
                Tables\Columns\TextColumn::make('survey.pertanyaan')->label('Pertanyaan Survei'),
                Tables\Columns\TextColumn::make('jawaban')->label('Jawaban'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Dibuat Pada'),
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