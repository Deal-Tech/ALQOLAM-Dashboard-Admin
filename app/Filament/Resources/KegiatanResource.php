<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KegiatanResource\Pages;
use App\Filament\Resources\KegiatanResource\RelationManagers;
use App\Models\Kegiatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KegiatanResource extends Resource
{
    protected static ?string $model = Kegiatan::class;

    protected static ?string $navigationIcon = 'heroicon-c-calendar-days';

    protected static ?string $navigationGroup = 'Hasil';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Berita Kegiatan';

    protected static ?string $slug = 'kegiatan';

    public static function getLabel(): string
    {
        return 'Kegiatan';
    }

    public static function getPluralLabel(): string
    {
        return 'Kegiatan';
    }
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_kategori_kegiatan')
                    ->relationship('kategorikegiatan', 'nama')
                    ->required(),
                Forms\Components\TextInput::make('judul')
                    ->Label('Judul')
                    ->required(),
                Forms\Components\TextInput::make('imgfuture')
                    ->Label('Url Gambar')
                    ->required(),
                Forms\Components\FileUpload::make('lampiran')
                    ->label('Lampiran')
                    ->image()
                    ->hiddenLabel(),
                
                Forms\Components\TextInput::make('konten')
                    ->Label('Konten')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('kategorikegiatan.nama')
                    ->label('Kategori Kegiatan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('imgfuture')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Dibuat Pada'),
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
            'index' => Pages\ListKegiatans::route('/'),
            'create' => Pages\CreateKegiatan::route('/create'),
            'view' => Pages\ViewKegiatan::route('/{record}'),
            'edit' => Pages\EditKegiatan::route('/{record}/edit'),
        ];
    }
}
