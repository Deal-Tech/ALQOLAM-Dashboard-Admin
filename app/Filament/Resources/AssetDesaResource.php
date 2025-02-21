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
use Illuminate\Database\Eloquent\Model;


class AssetDesaResource extends Resource
{
    protected static ?string $model = AssetDesa::class;

    protected static ?string $navigationIcon = 'heroicon-m-ellipsis-horizontal';

    protected static ?string $navigationGroup = 'Asset Desa';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Data Asset';

    protected static ?string $slug = 'asset-desa';

    public static function getLabel(): string
    {
        return 'Data Asset';
    }

    public static function getPluralLabel(): string
    {
        return 'Data Asset';
    }

    public static function form(Form $form): Form 
{
    return $form
        ->schema([
            Card::make()
                ->schema([
                TextInput::make('temp_jenis')
                ->required()
                ->label('Jenis Asset')
                ->default(function (?Model $record) {
                    if (!$record) return '';
                    
                    $data = is_string($record->data) ? json_decode($record->data, true) : $record->data;
                    return $data['jenis'] ?? '';
                })
                ->live()
                ->afterStateHydrated(function ($component, $state, ?Model $record) {
                    if (!$record) return;
                    
                    $data = is_string($record->data) ? json_decode($record->data, true) : $record->data;
                    $component->state($data['jenis'] ?? '');
                })
                ->afterStateUpdated(function (string $state, callable $get, callable $set) {
                    $currentData = json_decode($get('data') ?? '{}', true);
                    $currentData['jenis'] = $state;
                    $set('data', json_encode($currentData));
                }),

                Repeater::make('temp_data_simple')
                        ->schema([
                            TextInput::make('item')
                                    ->required()
                                    ->label('Data')
                            ])
                            ->label('Data Tanpa Sub')
                            ->addActionLabel('Tambah Data')
                            ->createItemButtonLabel('Tambah Data Baru')
                            ->defaultItems(0)
                            ->dehydrated(false)
                            ->afterStateHydrated(function (Repeater $component, $state, ?Model $record) {
                                if (!$record) return;
                                
                                $data = is_string($record->data) ? json_decode($record->data, true) : $record->data;
                                $simpleData = collect($data['data'] ?? [])
                                    ->filter(fn($item) => !is_array($item))
                                    ->map(fn($item) => ['item' => $item])
                                    ->toArray();
                                    
                                $component->state($simpleData);
                            })
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $currentData = json_decode($get('data') ?? '{}', true);
                                
                                $subData = collect($currentData['data'] ?? [])
                                    ->filter(fn($item) => is_array($item) && isset($item['nama_subjenis']))
                                    ->values()
                                    ->toArray();
                                
                                $currentData['data'] = array_values(array_merge(
                                    collect($state)->pluck('item')->toArray(),
                                    $subData
                                ));
                                
                                $set('data', json_encode($currentData));
                            }),

                Repeater::make('temp_data_with_sub')
                    ->schema([
                        TextInput::make('nama_subjenis')
                            ->required()
                            ->label('Nama Sub Jenis'),
                        Repeater::make('data')
                            ->schema([
                                TextInput::make('item')
                                    ->required()
                                    ->label('Data')
                            ])
                            ->defaultItems(0)
                            ->collapsible()
                    ])
                    ->label('Data Dengan Sub')
                    ->addActionLabel('Tambah Sub Jenis')
                    ->createItemButtonLabel('Tambah Sub Jenis Baru')
                    ->defaultItems(0)
                    ->dehydrated(false)
                    ->afterStateHydrated(function (Repeater $component, $state, ?Model $record) {
                        if (!$record) return;
                        
                        $data = is_string($record->data) ? json_decode($record->data, true) : $record->data;
                        $subData = collect($data['data'] ?? [])
                            ->filter(fn($item) => is_array($item) && isset($item['nama_subjenis']))
                            ->map(function ($item) {
                                return [
                                    'nama_subjenis' => $item['nama_subjenis'],
                                    'data' => collect($item['data'] ?? [])->map(fn($d) => ['item' => $d])->toArray()
                                ];
                            })
                            ->toArray();
                            
                        $component->state($subData);
                    })
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $currentData = json_decode($get('data') ?? '{}', true);
                        
                        $simpleData = collect($currentData['data'] ?? [])
                            ->filter(fn($item) => !is_array($item))
                            ->values()
                            ->toArray();
                        
                        $currentData['data'] = array_values(array_merge(
                        $simpleData,
                        collect($state)->map(function ($item) {
                            return [
                                'nama_subjenis' => $item['nama_subjenis'],
                                'data' => collect($item['data'] ?? [])->pluck('item')->toArray()
                                ];
                            })->toArray()
                        ));
                        
                        $set('data', json_encode($currentData));
                    }),

                        Forms\Components\Hidden::make('data'),
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
                    ->formatStateUsing(function ($state) {
                        return $state ?? '-';
                    })
                    ->searchable(),
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
            TextEntry::make('data')
                ->label('Jenis Asset')
                ->formatStateUsing(fn ($state) => 
                    is_string($state) 
                        ? json_decode($state, true)['jenis'] ?? '-' 
                        : $state['jenis'] ?? '-'
                ),
            TextEntry::make('data')
                ->label('Data')
                ->formatStateUsing(function ($state) {
                    if (is_string($state)) {
                        $state = json_decode($state, true);
                    }
                    if (!isset($state['data'])) return '-';
                    
                    $result = [];
                    foreach ($state['data'] as $item) {
                        if (is_array($item) && isset($item['nama_subjenis'])) {
                            $subData = implode(', ', $item['data'] ?? []);
                            $result[] = "{$item['nama_subjenis']}: {$subData}";
                        } else {
                            $result[] = $item;
                        }
                    }
                    
                    return implode("\n", $result);
                })
                ->markdown(),
            TextEntry::make('data')
                ->label('Jenis Kelamin')
                ->formatStateUsing(function ($state) {
                    $data = is_string($state) ? json_decode($state, true) : $state;
                    $jenisKelamin = $data['jenis_kelamin'] ?? [];
                    return !empty($jenisKelamin) ? implode(', ', $jenisKelamin) : null;
                })
                ->visible(function ($state) {
                    $data = is_string($state) ? json_decode($state, true) : $state;
                    return !empty($data['jenis_kelamin'] ?? []);
                })
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