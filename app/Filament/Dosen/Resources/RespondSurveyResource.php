<?php

namespace App\Filament\Dosen\Resources;

use App\Filament\Dosen\Resources\RespondSurveyResource\Pages;
use App\Filament\Dosen\Resources\RespondSurveyResource\RelationManagers;
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
            Forms\Components\Group::make()
            ->schema([
            Forms\Components\Section::make('Respon Survey')
                ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('mahasiswa', 'nama_kelompok')
                    ->required(),
                Forms\Components\TextInput::make('nama_ketua')
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
                        ->label('Konfirmasi')
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
        ->query(
            RespondSurvey::query()
                ->where('dosenpendamping_id', auth('dosen')->id())
        )
            ->columns([
                Tables\Columns\TextColumn::make('mahasiswa.nama_kelompok')->label('Kelompok'),
                Tables\Columns\TextColumn::make('kabupaten.nama')->label('Kabupaten'),
                Tables\Columns\TextColumn::make('kecamatan.nama')->label('Kecamatan'),
                Tables\Columns\TextColumn::make('desa.nama')
                    ->searchable()
                    ->label('Desa'),
                Tables\Columns\TextColumn::make('nama_ketua')->label('Nama Ketua'),
                Tables\Columns\ToggleColumn::make('is_compled')->label('Konfirmasi'),
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
                Tables\Filters\SelectFilter::make('is_compled')
                    ->options([
                        'true' => 'Sudah',
                        'false' => 'Belum',
                    ])
                    ->label('Konfirmasi'),

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                
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
            RelationManagers\ResponDetailRelationManager::class,
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
