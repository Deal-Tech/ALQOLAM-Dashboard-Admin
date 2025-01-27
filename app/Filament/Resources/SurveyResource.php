<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SurveyResource\Pages;
use App\Filament\Resources\SurveyResource\RelationManagers;
use App\Models\Survey;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Enums\FiltersLayout;

class SurveyResource extends Resource
{
    protected static ?string $model = Survey::class;

    protected static ?string $navigationIcon = 'heroicon-c-bars-arrow-up';

    protected static ?string $navigationGroup = 'Data';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Pertanyaan Survei';

    public static function getLabel(): string
    {
        return 'Survei';
    }

    public static function getPluralLabel(): string
    {
        return 'Survei';
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Group::make()
            ->schema([
                Forms\Components\Section::make('Pertanyaan')
                ->schema([
                    Forms\Components\Textarea::make('pertanyaan')
                        ->required()
                        ->rows(4)
                        ->required()
                        ->columnSpan('full'),
            
                ]),

                Forms\Components\Section::make('Jawaban')
                ->schema([
                    Forms\Components\Textarea::make('opsi_jawaban_1')
                        ->required(),
                    Forms\Components\Textarea::make('opsi_jawaban_2')
                        ->required(),
                    Forms\Components\Textarea::make('opsi_jawaban_3'),
                    Forms\Components\Textarea::make('opsi_jawaban_4'),
                    Forms\Components\Textarea::make('opsi_jawaban_5'),
            ])
            ->columns(2)
            ])
            
            ->columns(2)
            ->columnSpan(['lg' => 2]),

            Forms\Components\Group::make()
            ->schema([
                Forms\Components\Section::make('Pilih IDM')
                ->schema([    
                    Forms\Components\Select::make('dimensi_id')
                        ->relationship('dimensi', 'nama')
                        ->required(),
                    Forms\Components\Select::make('variabel_id')
                        ->relationship('variabel', 'nama')
                        ->required(),
                    Forms\Components\Select::make('sub_variabel_id')
                        ->relationship('subvariabel', 'nama')
                        ->label('Sub Variabel')
                        ->required(),

                ])
            ])
            
            ->columns(1)
            ->columnSpan(['lg' => 1]),
        ])
        ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('dimensi.nama')->label('Dimensi'),
                Tables\Columns\TextColumn::make('variabel.nama')->label('Variabel'),
                Tables\Columns\TextColumn::make('subvariabel.nama')->label('Sub Variabel'),
                Tables\Columns\TextColumn::make('pertanyaan'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
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
            'index' => Pages\ListSurveys::route('/'),
            'create' => Pages\CreateSurvey::route('/create'),
            'view' => Pages\ViewSurvey::route('/{record}'),
            'edit' => Pages\EditSurvey::route('/{record}/edit'),
        ];
    }
}