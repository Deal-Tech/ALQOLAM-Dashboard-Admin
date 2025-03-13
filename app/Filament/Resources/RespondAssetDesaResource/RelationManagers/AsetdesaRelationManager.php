<?php

namespace App\Filament\Resources\RespondAssetDesaResource\RelationManagers;

use App\Models\AssetDesa;
use App\Models\AssetDesaData;
use App\Models\AssetDesaJenisKelamin; // This was also missing
use App\Models\AssetDesaSubJenis;
use App\Models\AssetDesaSubJenisData;
use App\Models\RespondAssetDesaDetail; // Add this import
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model; // This was missing for the group function
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Filament\Tables\Grouping\Group;


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
                // This column will show the display with question & answer combined
                Tables\Columns\TextColumn::make('data_display')
                    ->label('')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$record || !$record->assetDesa) return '-';
                        
                        $namaData = '';
                        $jawaban = '';
                        
                        // Data reguler (is_data=true, is_sub_jenis=false)
                        if ($record->assetDesa->is_data && !$record->assetDesa->is_sub_jenis) {
                            $namaData = $record->assetDesaData?->nama ?? 'Tidak ada nama data';
                            $dataResponse = $record->data->first();
                            $jawaban = $dataResponse ? $dataResponse->jawaban : $record->nilai;
                        } 
                        // Data sub jenis (is_data=true, is_sub_jenis=true)
                        elseif ($record->assetDesa->is_data && $record->assetDesa->is_sub_jenis) {
                            $subJenisName = $record->assetDesaSubJenis?->subjenis ?? '';
                            $dataName = $record->assetDesaSubJenisData?->nama ?? 'Tidak ada nama data';
                            $namaData = $subJenisName ? "$subJenisName: $dataName" : $dataName;
                            
                            $subJenisData = $record->subJenisData->first();
                            $jawaban = $subJenisData ? $subJenisData->jawaban : $record->nilai;
                        } 
                        // Jenis kelamin (is_jenis_kelamin=true)
                        elseif ($record->assetDesa->is_jenis_kelamin) {
                            // Get appropriate name
                            $jenisKelaminMaster = AssetDesaJenisKelamin::where('assetdesa_id', $record->assetdesa_id)->first();
                            $namaData = $jenisKelaminMaster->nama ?? 'Jenis Kelamin';
                            
                            // Get data name if applicable
                            if ($record->assetDesaData) {
                                $namaData .= ': ' . $record->assetDesaData->nama;
                            }
                            
                            $jenisKelamin = $record->jenisKelamin->first();
                            if ($jenisKelamin) {
                                $lakilaki = number_format($jenisKelamin->jawaban_laki_laki ?? 0);
                                $perempuan = number_format($jenisKelamin->jawaban_perempuan ?? 0);
                                $total = number_format(($jenisKelamin->jawaban_laki_laki ?? 0) + ($jenisKelamin->jawaban_perempuan ?? 0));
                                $jawaban = "L: $lakilaki, P: $perempuan, Total: $total";
                            } else {
                                $jawaban = 'Tidak ada data';
                            }
                        }
                        
                        // Format as: name >> answer
                        return new HtmlString(
                            "<div class='flex items-center gap-2'>
                                <span class='font-medium text-gray-900'>$namaData</span>
                                <span class='text-gray-400 mx-1'>»</span>
                                <span class='text-primary-600 font-medium'>$jawaban</span>
                            </div>"
                        );
                    }),
                    
                // Hidden column just for the purpose of having something to group by
                Tables\Columns\TextColumn::make('assetDesa.jenis')
                    ->label('Jenis Asset')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('assetDesa.jenis')
                    ->label('Jenis Asset')
                    ->collapsible(),
            ])
            ->defaultGroup('assetDesa.jenis')
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
            ])
            ->striped();
    }
    
    protected function getTableQuery(): Builder
    {
        $owner = $this->getOwnerRecord();
        
        if (!$owner) {
            return RespondAssetDesaDetail::query()->whereRaw('1 = 0');
        }
        
        return RespondAssetDesaDetail::query()
            ->where('respond_assetdesa_id', $owner->id)
            ->with([
                'assetDesa',
                'assetDesaData',
                'assetDesaSubJenis',
                'assetDesaSubJenisData',
                'data',
                'subJenisData',
                'jenisKelamin',
            ]);
    }

}