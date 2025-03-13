<?php

namespace App\Filament\Resources\RespondAssetDesaResource\RelationManagers;

use App\Models\AssetDesa;
use App\Models\AssetDesaData;
use App\Models\AssetDesaSubJenis;
use App\Models\AssetDesaSubJenisData;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AsetdesaRelationManager extends RelationManager
{
    protected static string $relationship = 'details';

    protected static ?string $recordTitleAttribute = 'nilai';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('assetdesa_id')
                    ->relationship('assetDesa', 'jenis')
                    ->required()
                    ->label('Jenis Asset')
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {
                        $set('assetdesa_data_id', null);
                        $set('assetdesa_sub_jenis_id', null);
                    }),

                Forms\Components\Select::make('assetdesa_data_id')
                    ->label('Data Asset')
                    ->options(function (callable $get) {
                        $assetId = $get('assetdesa_id');
                        if (!$assetId) {
                            return [];
                        }
                        
                        $asset = AssetDesa::find($assetId);
                        if (!$asset || !$asset->is_data || $asset->is_sub_jenis) {
                            return [];
                        }
                        
                        return AssetDesaData::where('assetdesa_id', $assetId)
                            ->pluck('nama', 'id')
                            ->toArray();
                    })
                    ->visible(function (callable $get) {
                        $assetId = $get('assetdesa_id');
                        if (!$assetId) return false;
                        
                        $asset = AssetDesa::find($assetId);
                        return $asset && $asset->is_data && !$asset->is_sub_jenis;
                    }),
                
                Forms\Components\Select::make('assetdesa_sub_jenis_id')
                    ->label('Sub Jenis')
                    ->options(function (callable $get) {
                        $assetId = $get('assetdesa_id');
                        if (!$assetId) {
                            return [];
                        }
                        
                        return AssetDesaSubJenis::where('assetdesa_id', $assetId)
                            ->pluck('subjenis', 'id')
                            ->toArray();
                    })
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {
                        $set('assetdesa_sub_jenis_data_id', null);
                    })
                    ->visible(function (callable $get) {
                        $assetId = $get('assetdesa_id');
                        if (!$assetId) return false;
                        
                        $asset = AssetDesa::find($assetId);
                        return $asset && $asset->is_data && $asset->is_sub_jenis;
                    }),
                
                Forms\Components\Select::make('assetdesa_sub_jenis_data_id')
                    ->label('Data Sub Jenis')
                    ->options(function (callable $get) {
                        $subJenisId = $get('assetdesa_sub_jenis_id');
                        if (!$subJenisId) {
                            return [];
                        }
                        
                        return AssetDesaSubJenisData::where('subjenis_id', $subJenisId)
                            ->pluck('nama', 'id')
                            ->toArray();
                    })
                    ->visible(function (callable $get) {
                        return !empty($get('assetdesa_sub_jenis_id'));
                    }),
                
                Forms\Components\TextInput::make('nilai')
                    ->label('Nilai/Jumlah')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('assetDesa.jenis')
                    ->label('Jenis Asset')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('assetDesaData.nama')
                    ->label('Data')
                    ->placeholder('-')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('assetDesaSubJenis.subjenis')
                    ->label('Sub Jenis')
                    ->placeholder('-')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('assetDesaSubJenisData.nama')
                    ->label('Data Sub Jenis')
                    ->placeholder('-'),
                
                Tables\Columns\TextColumn::make('nilai')
                    ->label('Nilai/Jumlah'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('assetdesa_id')
                    ->relationship('assetDesa', 'jenis')
                    ->label('Jenis Asset'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Detail'),
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
}