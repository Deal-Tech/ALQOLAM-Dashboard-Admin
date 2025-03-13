<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RespondAssetDesaResource\Pages;
use App\Filament\Resources\RespondAssetDesaResource\RelationManagers;
use App\Models\RespondAssetDesa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class RespondAssetDesaResource extends Resource
{
    protected static ?string $model = RespondAssetDesa::class;

    protected static ?string $navigationIcon = 'heroicon-m-rectangle-group';

    protected static ?string $navigationGroup = 'Hasil';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Hasil Aset Desa';

    public static function getLabel(): string
    {
        return 'Hasil Aset Desa';   
    }

    public static function getPluralLabel(): string
    {
        return 'Hasil Aset Desa';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Informasi Survei')
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->relationship('user', 'nama_kelompok')
                                    ->label('Kelompok')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                
                                Forms\Components\Select::make('desa_id')
                                    ->relationship('desa', 'nama')
                                    ->label('Desa')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                
                                Forms\Components\Toggle::make('is_completed')
                                    ->label('Survey Selesai')
                                    ->default(false),
                                
                                Forms\Components\Toggle::make('is_published')
                                    ->label('Publikasikan')
                                    ->default(false),
                            ])
                            ->columns(2)
                    ])
                    ->columnSpan(['lg' => 2]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.nama_kelompok')
                    ->searchable()
                    ->sortable()
                    ->label('Kelompok'),
                
                Tables\Columns\TextColumn::make('desa.nama')
                    ->searchable()
                    ->sortable()
                    ->label('Desa'),
                
                Tables\Columns\TextColumn::make('desa.kecamatan.nama')
                    ->searchable()
                    ->sortable()
                    ->label('Kecamatan'),
                
                Tables\Columns\TextColumn::make('desa.kecamatan.kabupaten.nama')
                    ->searchable()
                    ->sortable()
                    ->label('Kabupaten'),
                
                Tables\Columns\IconColumn::make('is_completed')
                    ->boolean()
                    ->label('Selesai'),
                
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean()
                    ->label('Dipublikasi'),
                
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
                        '1' => 'Dipublikasi',
                        '0' => 'Draft',
                    ])
                    ->label('Status Publikasi'),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('user.nama_kelompok')
                    ->label('Kelompok'),
                
                Infolists\Components\TextEntry::make('desa.nama')
                    ->label('Desa'),
                
                Infolists\Components\TextEntry::make('desa.kecamatan.nama')
                    ->label('Kecamatan'),
                
                Infolists\Components\TextEntry::make('desa.kecamatan.kabupaten.nama')
                    ->label('Kabupaten'),
                
                Infolists\Components\IconEntry::make('is_completed')
                    ->boolean()
                    ->label('Status Pengisian'),
                
                Infolists\Components\IconEntry::make('is_published')
                    ->boolean()
                    ->label('Status Publikasi'),
                
                Infolists\Components\TextEntry::make('created_at')
                    ->dateTime()
                    ->label('Dibuat Pada'),
                
                Infolists\Components\TextEntry::make('updated_at')
                    ->dateTime()
                    ->label('Diperbarui Pada'),
                
                Infolists\Components\Section::make('Detail Aset Desa')
                    ->schema([
                        // Additional component to display asset details will go here
                        // This would typically be a custom component or a repeatable entry
                        // showing the asset data responses
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AsetdesaRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRespondAssetDesas::route('/'),
            'create' => Pages\CreateRespondAssetDesa::route('/create'),
            'view' => Pages\ViewRespondAssetDesa::route('/{record}'),
            'edit' => Pages\EditRespondAssetDesa::route('/{record}/edit'),
        ];
    }
}