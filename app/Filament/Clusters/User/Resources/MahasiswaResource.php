<?php

namespace App\Filament\Clusters\User\Resources;

use App\Filament\Clusters\User;
use App\Filament\Clusters\User\Resources\MahasiswaResource\Pages;
use App\Filament\Clusters\User\Resources\MahasiswaResource\RelationManagers;
use App\Models\Mahasiswa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MahasiswaResource extends Resource
{
    protected static ?string $model = Mahasiswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $cluster = User::class;

    protected static ?string $slug = 'mahasiswa';

    protected static ?int $navigationSort = 1;

    public static function getLabel(): string
    {
        return 'Mahasiswa';
    }

    public static function getPluralLabel(): string
    {
        return 'Mahasiswa';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->email()
                    ->label('Email'),
                Forms\Components\TextInput::make('password')
                    ->required()
                    ->password()
                    ->label('Password'),
                Forms\Components\TextInput::make('no_handphone')
                    ->label('No Handphone'),
                Forms\Components\TextInput::make('nama_kelompok')
                    ->required()
                    ->label('Nama Kelompok'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->label('Email'),
                Tables\Columns\TextColumn::make('nama_kelompok')
                    ->searchable()
                    ->label('Nama Kelompok'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Dibuat Pada'),
                
            ])
            ->filters([
                //
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
            'index' => Pages\ManageMahasiswas::route('/'),
        ];
    }
}
