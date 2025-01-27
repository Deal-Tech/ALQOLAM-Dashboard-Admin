<?php

namespace App\Filament\Clusters\User\Resources;

use App\Filament\Clusters\User;
use App\Filament\Clusters\User\Resources\SpesialUserResource\Pages;
use App\Filament\Clusters\User\Resources\SpesialUserResource\RelationManagers;
use App\Models\SpesialUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SpesialUserResource extends Resource
{
    protected static ?string $model = SpesialUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $cluster = User::class;

    protected static ?string $slug = 'spesial-user';

    protected static ?int $navigationSort = 3;

    public static function getLabel(): string
    {
        return 'Spesial User';
    }

    public static function getPluralLabel(): string
    {
        return 'Spesial User';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_lengkap')
                    ->required()
                    ->label('Nama Lengkap'),
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->email()
                    ->label('Email'),
                Forms\Components\TextInput::make('no_handphone')
                    ->label('No Handphone'),
                Forms\Components\TextInput::make('password')
                    ->required()
                    ->password()
                    ->label('Password'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->searchable()
                    ->label('Nama Lengkap'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->label('Email'),
                Tables\Columns\TextColumn::make('no_handphone')
                    ->searchable()
                    ->label('No Handphone'),
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
            'index' => Pages\ManageSpesialUsers::route('/'),
        ];
    }
}
