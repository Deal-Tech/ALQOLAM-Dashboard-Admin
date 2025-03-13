<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetDesaResource\Pages;
use App\Models\AssetDesa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\AssetDesaResource\Pages\CreateAssetDesa;
use App\Filament\Resources\AssetDesaResource\Pages\EditAssetDesa;
use Filament\Support\Enums\FontWeight;

class AssetDesaResource extends Resource
{
    protected static ?string $model = AssetDesa::class;

    protected static ?string $navigationIcon = 'heroicon-m-ellipsis-horizontal';
    protected static ?string $navigationGroup = 'Asset Desa';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Data Asset';
    protected static ?string $slug = 'asset-desa';

    public static function form(Form $form): Form 
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('jenis')
                            ->required()
                            ->label('Jenis Asset'),
                            
                        Toggle::make('is_data')
                            ->label('Is Data')
                            ->default(true),
                            
                        Toggle::make('is_sub_jenis')
                            ->label('Is Sub Jenis')
                            ->default(false),
                            
                        Toggle::make('is_jenis_kelamin')
                            ->label('Is Jenis Kelamin')
                            ->default(false),
                            
                        Repeater::make('temp_data_simple')
                            ->schema([
                                TextInput::make('item')
                                    ->required()
                                    ->label('Data'),
                                Checkbox::make('is_multiple_answer')
                                    ->label('Multiple Answer')
                                    ->default(false)
                            ])
                            ->label('Data Tanpa Sub')
                            ->addActionLabel('Tambah Data')
                            ->createItemButtonLabel('Tambah Data Baru')
                            ->visible(fn (callable $get) => $get('is_data') && !$get('is_sub_jenis'))
                            ->reactive()
                            ->afterStateHydrated(function (Repeater $component, $state, ?Model $record) {
                                if (!$record) return;
                                
                                // Get existing simple data
                                $simpleData = $record->data->map(function($item) {
                                    return [
                                        'item' => $item->nama,
                                        'is_multiple_answer' => (bool)$item->is_multiple_answer
                                    ];
                                })->toArray();
                                
                                $component->state($simpleData);
                            })
                            ->dehydrated(false),
                            
                        Repeater::make('temp_data_with_sub')
                            ->schema([
                                TextInput::make('nama_subjenis')
                                    ->required()
                                    ->label('Nama Sub Jenis'),
                                Repeater::make('data')
                                    ->schema([
                                        TextInput::make('item')
                                            ->required()
                                            ->label('Data'),
                                        Checkbox::make('is_multiple_answer')
                                            ->label('Multiple Answer')
                                            ->default(false)
                                    ])
                                    ->label('Data')
                                    ->addActionLabel('Tambah Data')
                                    ->createItemButtonLabel('Tambah Data Baru')
                                    ->collapsible()
                            ])
                            ->label('Data Dengan Sub')
                            ->addActionLabel('Tambah Sub Jenis')
                            ->createItemButtonLabel('Tambah Sub Jenis Baru')
                            ->visible(fn (callable $get) => $get('is_data') && $get('is_sub_jenis'))
                            ->reactive()
                            ->afterStateHydrated(function (Repeater $component, $state, ?Model $record) {
                                if (!$record) return;
                                
                                // Get existing sub data
                                $subData = $record->subJenis->map(function($subJenis) {
                                    return [
                                        'nama_subjenis' => $subJenis->subjenis,
                                        'data' => $subJenis->data->map(function($data) {
                                            return [
                                                'item' => $data->nama,
                                                'is_multiple_answer' => (bool)$data->is_multiple_answer
                                            ];
                                        })->toArray()
                                    ];
                                })->toArray();
                                
                                $component->state($subData);
                            })
                            ->dehydrated(false),
                            
                        Repeater::make('temp_jenis_kelamin')
                            ->schema([
                                TextInput::make('item')
                                    ->required()
                                    ->label('Jenis Kelamin')
                            ])
                            ->label('Jenis Kelamin')
                            ->addActionLabel('Tambah Jenis Kelamin')
                            ->createItemButtonLabel('Tambah Opsi Jenis Kelamin')
                            ->visible(fn (callable $get) => $get('is_jenis_kelamin'))
                            ->reactive()
                            ->afterStateHydrated(function (Repeater $component, $state, ?Model $record) {
                                if (!$record) return;
                                
                                try {
                                    // Get existing jenis kelamin
                                    $jenisKelamin = $record->jenisKelamin->map(function($item) {
                                        return ['item' => $item->nama];
                                    })->toArray();
                                    
                                    $component->state($jenisKelamin);
                                } catch (\Exception $e) {
                                    // Log error or handle it
                                    // For debugging purposes
                                }
                            })
                            ->dehydrated(false),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis Asset')
                    ->searchable()->formatStateUsing(function ($state) {
                        $words = explode(' ', $state);
                        return implode(' ', array_slice($words, 0, 6)) . (count($words) > 10 ? '...' : '');
                    }),
                Tables\Columns\IconColumn::make('is_data')
                    ->boolean()
                    ->label('Is Data'),
                Tables\Columns\IconColumn::make('is_sub_jenis')
                    ->boolean()
                    ->label('Is Sub'),
                Tables\Columns\IconColumn::make('is_jenis_kelamin')
                    ->boolean()
                    ->label('Is Jenis Kelamin'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('jenis')
                    ->label('Jenis Asset')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold)
                    ->columnSpanFull(),
    
                TextEntry::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->columnSpanFull(),
                    
                \Filament\Infolists\Components\Section::make('Data')
                    ->visible(fn ($record) => $record->is_data && !$record->is_sub_jenis)
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('data')
                            ->label('')
                            ->schema([
                                TextEntry::make('nama')
                                    ->label('Nama Data'),
                                TextEntry::make('is_multiple_answer')
                                    ->label('Multiple Answer')
                                    ->badge()
                                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                                    ->formatStateUsing(fn (bool $state) => $state ? 'Ya' : 'Tidak'),
                            ])
                            ->columns(2)
                            ->grid(2)
                            ->state(fn ($record) => $record->data ?? [])
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
                    
                \Filament\Infolists\Components\Section::make('Data dengan Sub Jenis')
                    ->visible(fn ($record) => $record->is_data && $record->is_sub_jenis)
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('subJenis')
                            ->schema([
                                TextEntry::make('subjenis')
                                    ->label('Nama Sub Jenis')
                                    ->weight(FontWeight::Bold)
                                    ->size(TextEntry\TextEntrySize::Large),
                                    
                                \Filament\Infolists\Components\RepeatableEntry::make('data')
                                    ->label('Data')
                                    ->schema([
                                        TextEntry::make('nama')
                                            ->label('Nama Data'),
                                        TextEntry::make('is_multiple_answer')
                                            ->label('Multiple Answer')
                                            ->badge()
                                            ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                                            ->formatStateUsing(fn (bool $state) => $state ? 'Ya' : 'Tidak'),
                                    ])
                                    ->columns(2)
                                    ->grid(2)
                                    ->state(fn ($record) => $record->data ?? [])
                            ])
                            // Remove the collapsible() method from RepeatableEntry as it doesn't exist
                            ->state(fn ($record) => $record->subJenis ?? [])
                    ])
                    ->collapsible() // Keep collapsible on the Section
                    ->columnSpanFull(),
                    
                \Filament\Infolists\Components\Section::make('Jenis Kelamin')
                    ->visible(fn ($record) => $record->is_jenis_kelamin)
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('jenisKelamin')
                            ->schema([
                                TextEntry::make('nama')
                                    ->label(''),
                            ])
                            ->columns(1)
                            ->state(fn ($record) => $record->jenisKelamin ?? [])
                    ])
                    ->collapsible() // Keep collapsible on the Section
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssetDesas::route('/'),
            'create' => Pages\CreateAssetDesa::route('/create'),
            'view' => Pages\ViewAssetDesa::route('/{record}'),
            'edit' => Pages\EditAssetDesa::route('/{record}/edit'),
        ];
    }
}