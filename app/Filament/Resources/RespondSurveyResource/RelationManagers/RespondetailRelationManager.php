<?php

namespace App\Filament\Resources\RespondSurveyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Enums\FiltersLayout;


class RespondetailRelationManager extends RelationManager
{
    protected static string $relationship = 'respondetail';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('jawaban')
                    ->options(function ($get) {
                        $surveyId = $get('survey_id');
                        if (!$surveyId) return [];
                        
                        $survey = \App\Models\Survey::find($surveyId);
                        if (!$survey) return [];
    
                        $options = [];
                        for ($i = 1; $i <= 5; $i++) {
                            $field = "opsi_jawaban_$i";
                            if (!empty($survey->$field)) {
                                $options[$survey->$field] = $survey->$field;
                            }
                        }
                        return $options;
                    })
                    ->required(),
                
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('respondsurvey_id')
            ->columns([
                Tables\Columns\TextColumn::make('survey_id'),
                Tables\Columns\TextColumn::make('survey.pertanyaan')
                    ->formatStateUsing(function ($state) {
                        $words = explode(' ', $state);
                        return implode(' ', array_slice($words, 0, 5)) . (count($words) > 10 ? '...' : '');
                    })
                    ->Searchable(),
                    
                Tables\Columns\TextColumn::make('jawaban')
                    ->formatStateUsing(function ($state) {
                        $words = explode(' ', $state);
                        return implode(' ', array_slice($words, 0, 5)) . (count($words) > 10 ? '...' : '');
                    }),
                
                Tables\Columns\TextColumn::make('survey.dimensi.nama'),
                Tables\Columns\TextColumn::make('survey.variabel.nama')
                    ->formatStateUsing(function ($state) {
                        $words = explode(' ', $state);
                        return implode(' ', array_slice($words, 0, 5)) . (count($words) > 10 ? '...' : '');
                    }),
                Tables\Columns\TextColumn::make('survey.subvariabel.nama')
                    ->formatStateUsing(function ($state) {
                        $words = explode(' ', $state);
                        return implode(' ', array_slice($words, 0, 5)) . (count($words) > 10 ? '...' : '');
                    })
                    ->Label('Indikator'),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('dimensi_id')
                    ->relationship('survey.dimensi', 'nama')
                    ->label('Dimensi'),
                Tables\Filters\SelectFilter::make('variabel_id')
                    ->relationship('survey.variabel', 'nama')
                    ->label('Variabel'),
                Tables\Filters\SelectFilter::make('subvariabel_id')
                    ->relationship('survey.subvariabel', 'nama')
                    ->label('Indikator'),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
