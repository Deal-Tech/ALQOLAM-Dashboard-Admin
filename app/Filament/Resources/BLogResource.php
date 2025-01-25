<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Filament\Resources\BlogResource\RelationManagers;
use App\Models\Kegiatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BlogResource extends Resource
{
    protected static ?string $model = Kegiatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Blog';

    protected static ?int $navigationSort = 7;

    protected static ?string $slug = 'post';

    protected static ?string $navigationLabel = 'Post';

    public static function getLabel(): string
    {
        return 'Post';
    }

    public static function getPluralLabel(): string
    {
        return 'Post';
    }


    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make()    
            ->schema([
                Forms\Components\Textinput::make('judul')
                    ->required()
                    ->label('Judul'),
                Forms\Components\Select::make('id_kategori_kegiatan')
                    ->relationship('kategorikegiatan', 'nama')
                    ->required()
                    ->label('Kategori Kegiatan'),
                Forms\Components\MarkdownEditor::make('konten')
                    ->required()
                    ->columnSpan('full')
                    ->label('Konten'),
                
                Forms\Components\DatePicker::make('created_at')
                    ->label('Tanggal'),
                Forms\Components\FileUpload::make('imgfuture')
                    ->label('Gambar Utama'),
                
                ])
                ->columns(2),

            Forms\Components\Section::make()
                ->schema([
                Forms\Components\FileUpload::make('lampiran')
                    ->label('Lampiran')
                    ->multiple()
                    ->image(),
                
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->searchable()
                    ->sortable()
                    ->label('Judul')
                    ->formatStateUsing(function ($state) {
                        $words = explode(' ', $state);
                        return implode(' ', array_slice($words, 0, 5)) . (count($words) > 10 ? '...' : '');
                    }),
                Tables\Columns\TextColumn::make('kategoriKegiatan.nama')
                    ->searchable()
                    ->sortable()
                    ->label('Kategori Kegiatan'),
                Tables\Columns\ImageColumn::make('imgfuture')
                    ->searchable()
                    ->sortable()
                    ->label('Gambar'),
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
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'view' => Pages\ViewBlog::route('/{record}'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }
}