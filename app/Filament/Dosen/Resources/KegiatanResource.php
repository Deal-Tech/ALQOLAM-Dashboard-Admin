<?php

namespace App\Filament\Dosen\Resources;

use App\Filament\Dosen\Resources\KegiatanResource\Pages;
use App\Filament\Dosen\Resources\KegiatanResource\RelationManagers;
use App\Models\Kegiatan;
use Filament\Forms;
use Filament\Forms\Form;
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
                
                Forms\Components\RichEditor::make('konten')
                    ->Label('Konten')
                    ->columnspan(2)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                // Get currently logged-in dosen's ID
                $dosenId = auth('dosen')->id();
                
                // Get user_ids (kelompok) associated with this dosen
                $kelompokIds = \App\Models\RespondSurvey::where('dosenpendamping_id', $dosenId)
                    ->pluck('user_id')
                    ->unique()
                    ->toArray();
                
                // Filter kegiatan to only those created by associated kelompok
                return Kegiatan::query()
                    ->whereIn('user_id', $kelompokIds);
                    
            })
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        $words = explode(' ', $state);
                        return implode(' ', array_slice($words, 0, 5)) . (count($words) > 10 ? '...' : '');
                    }),
                
                Tables\Columns\TextColumn::make('kategorikegiatan.nama')
                    ->label('Kategori Kegiatan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('imgfuture')
                    ->label('Gambar Utama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('status')->label('Publikasikan'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Dibuat Pada'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKegiatans::route('/'),
            'view' => Pages\ViewKegiatan::route('/{record}'),
            
        ];
    }
}
