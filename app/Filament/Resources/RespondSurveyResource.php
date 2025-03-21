<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RespondSurveyResource\Pages;
use App\Filament\Resources\RespondSurveyResource\RelationManagers;
use App\Filament\Imports\RespondSurveyImporter;
use App\Models\RespondSurvey;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;


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
            Forms\Components\Group::make()
            ->schema([
            Forms\Components\Section::make('Respon Survey')
                ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('mahasiswa', 'nama_kelompok')
                    ->required(),
                Forms\Components\TextInput::make('nama_ketua')
                    ->required(),
                forms\Components\Select::make('dosenpendamping_id')
                    ->relationship('dosenpendamping', 'nama_lengkap')
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
                
                    
            ])
            ->columns(2)
            ])
            ->columns(2)
            ->columnSpan(['lg' => 2]),

            Forms\Components\Group::make()
            ->schema([
                Forms\Components\Section::make('Status')
                ->schema([    
                    Forms\Components\Toggle::make('is_compled')
                        ->label('Review Dosen')
                        ->required(),
                    Forms\Components\Toggle::make('is_published')
                        ->label('Dipublikasi')
                        ->required(),
                    DatePicker::make('created_at')
                        ->label('Tanggal Survey')
                        ->native(false),
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
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('mahasiswa.nama_kelompok')->label('Kelompok'),
                Tables\Columns\TextColumn::make('dosenpendamping.nama_lengkap')
                    ->label('Dosen Pendamping'),
                Tables\Columns\TextColumn::make('kabupaten.nama')->label('Kabupaten'),
                Tables\Columns\TextColumn::make('kecamatan.nama')->label('Kecamatan'),
                Tables\Columns\TextColumn::make('desa.nama')
                    ->searchable()
                    ->label('Desa'),
                Tables\Columns\TextColumn::make('nama_ketua')->label('Nama Ketua'),

                Tables\Columns\IconColumn::make('is_compled')
                    ->label('Konfirmasi Dosen')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publikasikan')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Dibuat Pada'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Diperbarui Pada'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('desa_id')
                    ->relationship('desa', 'nama')
                    ->label('Desa')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('is_completed')
                    ->options([
                        '1' => 'Selesai',
                        '0' => 'Belum Selesai',
                    ])
                    ->label('Review Dosen'),
                
                Tables\Filters\SelectFilter::make('is_published')
                    ->options([
                        '1' => 'Publikasi',
                        '0' => 'Draft',
                    ])
                    ->label('Status Publikasi'),
            ], layout: FiltersLayout::AboveContent)
            ->defaultSort('created_at', 'desc')
            ->filtersFormColumns(3)
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Data')
                    ->icon('heroicon-o-plus'),
                Tables\Actions\ImportAction::make()
                    ->importer(RespondSurveyImporter::class)
                    ->label('Import Data')
                    ->icon('heroicon-o-document-plus'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            RelationManagers\RespondetailRelationManager::class,
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