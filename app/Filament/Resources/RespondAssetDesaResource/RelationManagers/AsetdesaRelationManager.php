<?php

namespace App\Filament\Resources\RespondAssetDesaResource\RelationManagers;

use App\Models\AssetDesa;
use App\Models\AssetDesaData;
use App\Models\AssetDesaSubJenis;
use App\Models\AssetDesaSubJenisData;
use App\Models\RespondAssetDesaDetail;
use App\Models\RespondAssetDesaData;
use App\Models\RespondAssetDesaSubJenisData;
use App\Models\RespondAssetDesaJenisKelamin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Tables\Enums\FiltersLayout;
use App\Filament\Imports\RegularAssetDataImporter;
use App\Filament\Imports\JenisKelaminAssetDataImporter;
use App\Filament\Imports\SubJenisAssetDataImporter;

class AsetdesaRelationManager extends RelationManager
{
    protected static string $relationship = 'details';
    
    protected static ?string $recordTitleAttribute = 'id';
    
    protected static ?string $title = 'Detail Asset Desa';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('assetdesa_id')
                    ->relationship('assetDesa', 'jenis', function (Builder $query) {
                        // Only show relevant asset types for this form
                        return $query;
                    })
                    ->required()
                    ->label('Jenis Asset')
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        $assetDesa = AssetDesa::find($state);
                        
                        if (!$assetDesa) {
                            return;
                        }
                        
                        $isJenisKelamin = $assetDesa->is_jenis_kelamin;
                        $isSubJenis = $assetDesa->is_data && $assetDesa->is_sub_jenis;
                        
                        $set('show_jenis_kelamin', $isJenisKelamin);
                        $set('show_sub_jenis', $isSubJenis);
                        
                        // Reset related fields
                        $set('assetdesa_data_id', null);
                        $set('assetdesa_sub_jenis_id', null);
                        $set('assetdesa_sub_jenis_data_id', null);
                    }),
                    
                Forms\Components\Hidden::make('show_jenis_kelamin')
                    ->default(false),
                    
                Forms\Components\Hidden::make('show_sub_jenis')
                    ->default(false),
                    
                // Show this section ALWAYS for both regular and sub jenis data
                Forms\Components\Section::make('Data Umum')
                    ->schema([
                        Forms\Components\Select::make('assetdesa_data_id')
                            ->options(function (callable $get) {
                                $assetId = $get('assetdesa_id');
                                if (!$assetId) return [];
                                
                                $asset = AssetDesa::find($assetId);
                                // Only show data for regular assets (not sub_jenis)
                                if (!$asset || !$asset->is_data || $asset->is_sub_jenis) return [];
                                
                                return AssetDesaData::where('assetdesa_id', $assetId)
                                    ->pluck('nama', 'id')
                                    ->toArray();
                            })
                            ->label('Data')
                            ->required(fn (callable $get) => !$get('show_jenis_kelamin') && !$get('show_sub_jenis')),
                            
                        // Regular data nilai field - always show for both regular and sub-jenis
                        Forms\Components\TextInput::make('nilai')
                            ->label('Jawaban')
                            ->required(fn (callable $get) => 
                                !$get('show_jenis_kelamin') && 
                                (!$get('show_sub_jenis') || ($get('show_sub_jenis') && !$get('assetdesa_sub_jenis_data_id')))
                            )
                    ])
                    ->visible(fn (callable $get) => !$get('show_jenis_kelamin') && !$get('show_sub_jenis')), // Only show for regular data
                    
                // Only show sub jenis section for sub jenis data
                Forms\Components\Section::make('Data Sub Jenis')
                    ->schema([
                        Forms\Components\Select::make('assetdesa_sub_jenis_id')
                            ->options(function (callable $get) {
                                $assetId = $get('assetdesa_id');
                                if (!$assetId) return [];
                                
                                return AssetDesaSubJenis::where('assetdesa_id', $assetId)
                                    ->pluck('subjenis', 'id')
                                    ->toArray();
                            })
                            ->label('Sub Jenis')
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('assetdesa_sub_jenis_data_id', null)),
                            
                        Forms\Components\Select::make('assetdesa_sub_jenis_data_id')
                            ->options(function (callable $get) {
                                $subJenisId = $get('assetdesa_sub_jenis_id');
                                if (!$subJenisId) return [];
                                
                                return AssetDesaSubJenisData::where('subjenis_id', $subJenisId)
                                    ->pluck('nama', 'id')
                                    ->toArray();
                            })
                            ->label('Data Sub Jenis')
                            ->required(fn (callable $get) => $get('show_sub_jenis')),
                            
                        // The sub jenis nilai field
                        Forms\Components\TextInput::make('nilai')
                            ->label('Jawaban Sub Jenis')
                            ->required(fn (callable $get) => 
                                $get('show_sub_jenis') && 
                                $get('assetdesa_sub_jenis_data_id')
                            )
                    ])
                    ->visible(fn (callable $get) => $get('show_sub_jenis')),
                    
                // Only show jenis kelamin section for jenis kelamin data
                Forms\Components\Section::make('Data Jenis Kelamin')
                    ->schema([
                        Forms\Components\Select::make('assetdesa_data_id')
                            ->options(function (callable $get) {
                                $assetId = $get('assetdesa_id');
                                if (!$assetId) return [];
                                
                                $asset = AssetDesa::find($assetId);
                                if (!$asset || !$asset->is_jenis_kelamin) return [];
                                
                                return AssetDesaData::where('assetdesa_id', $assetId)
                                    ->pluck('nama', 'id')
                                    ->toArray();
                            })
                            ->label('Data')
                            ->required(fn (callable $get) => $get('show_jenis_kelamin')),
                            
                        Forms\Components\TextInput::make('jawaban_laki_laki')
                            ->label('Laki-laki')
                            ->numeric()
                            ->required(fn (callable $get) => $get('show_jenis_kelamin')),
                            
                        Forms\Components\TextInput::make('jawaban_perempuan')
                            ->label('Perempuan')
                            ->numeric() 
                            ->required(fn (callable $get) => $get('show_jenis_kelamin')),
                    ])
                    ->visible(fn (callable $get) => $get('show_jenis_kelamin')),
            ]);
    }
    
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\TextEntry::make('assetDesa.jenis')
                    ->label('')
                    ->weight('bold')
                    ->size('xl'),
                    
                Components\Section::make('Detail Data')
                    ->schema([
                        Components\TextEntry::make('data_table')
                            ->label('')
                            ->html()
                            ->state(function ($record) {
                                // Check asset type
                                if (!$record->assetDesa) {
                                    return '<div class="text-red-500 font-bold">Data asset tidak ditemukan</div>';
                                }
                                
                                $output = ''; // Variable to store all HTML output
                                
                                // First, handle regular data if any
                                if ($record->assetDesa->is_data) {
                                    $responseData = RespondAssetDesaData::where('respond_assetdesa_detail_id', $record->id)->get();
                                    
                                    if ($responseData->isNotEmpty()) {
                                        $output .= '<div class="mb-8">';
                                        
                                        $output .= '<table class="w-full border-collapse border border-gray-300">';
                                        $output .= '<thead><tr>';
                                        $output .= '<th class="border border-gray-300 p-2 bg-gray-100">Data</th>';
                                        $output .= '<th class="border border-gray-300 p-2 bg-gray-100">Jawaban</th>';
                                        $output .= '</tr></thead>';
                                        $output .= '<tbody>';
                                        
                                        foreach ($responseData as $item) {
                                            $assetData = AssetDesaData::find($item->assetdesa_data_id);
                                            $dataName = $assetData ? $assetData->nama : 'Data tidak ditemukan';
                                            
                                            // Format jawaban if numeric
                                            $jawaban = $item->jawaban;
                                            $displayJawaban = is_numeric($jawaban) ? number_format(floatval($jawaban)) : $jawaban;
                                            
                                            $output .= '<tr>';
                                            $output .= '<td class="border border-gray-300 p-2">' . $dataName . '</td>';
                                            $output .= '<td class="border border-gray-300 p-2 ">' . $displayJawaban . '</td>';
                                            $output .= '</tr>';
                                        }
                                        
                                        $output .= '</tbody></table>';
                                        $output .= '</div>';
                                    }
                                }
                                
                                // Then, handle specific types
                                $isJenisKelamin = $record->assetDesa->is_jenis_kelamin;
                                $isSubJenis = $record->assetDesa->is_data && $record->assetDesa->is_sub_jenis;
                                
                                // Handle jenis kelamin data
                                if ($isJenisKelamin) {
                                    $jenisKelaminData = RespondAssetDesaJenisKelamin::where('respond_assetdesa_detail_id', $record->id)->get();
                                    
                                    if ($jenisKelaminData->isNotEmpty()) {
                                        $output .= '<div class="mb-6">';
                                        if (!empty($output)) {
                                            $output .= '<h3 class="text-lg font-bold mb-2">Data Jenis Kelamin</h3>';
                                        }
                                        
                                        // Prepare for totals
                                        $totalLaki = 0;
                                        $totalPerempuan = 0;
                                        $grandTotal = 0;
                                        
                                        $output .= '<table class="w-full border-collapse border border-gray-300">';
                                        $output .= '<thead><tr>';
                                        $output .= '<th class="border border-gray-300 p-2 bg-gray-100">Data</th>';
                                        $output .= '<th class="border border-gray-300 p-2 bg-gray-100">Laki-laki</th>';
                                        $output .= '<th class="border border-gray-300 p-2 bg-gray-100">Perempuan</th>';
                                        $output .= '<th class="border border-gray-300 p-2 bg-gray-100">Total</th>';
                                        $output .= '</tr></thead>';
                                        $output .= '<tbody>';
                                        
                                        foreach ($jenisKelaminData as $item) {
                                            $assetData = AssetDesaData::find($item->assetdesa_data_id);
                                            $dataName = $assetData ? $assetData->nama : 'Data tidak ditemukan';
                                            $laki = $item->jawaban_laki_laki ?? 0;
                                            $perempuan = $item->jawaban_perempuan ?? 0;
                                            $total = $laki + $perempuan;
                                            
                                            // Add to totals
                                            $totalLaki += $laki;
                                            $totalPerempuan += $perempuan;
                                            $grandTotal += $total;
                                            
                                            $output .= '<tr>';
                                            $output .= '<td class="border border-gray-300 p-2">' . $dataName . '</td>';
                                            $output .= '<td class="border border-gray-300 p-2 ">' . number_format($laki) . '</td>';
                                            $output .= '<td class="border border-gray-300 p-2 ">' . number_format($perempuan) . '</td>';
                                            $output .= '<td class="border border-gray-300 p-2  font-bold">' . number_format($total) . '</td>';
                                            $output .= '</tr>';
                                        }
                                        
                                        // Add total row
                                        $output .= '<tr class="bg-gray-50">';
                                        $output .= '<td class="border border-gray-300 p-2 font-bold">TOTAL</td>';
                                        $output .= '<td class="border border-gray-300 p-2  font-bold">' . number_format($totalLaki) . '</td>';
                                        $output .= '<td class="border border-gray-300 p-2  font-bold">' . number_format($totalPerempuan) . '</td>';
                                        $output .= '<td class="border border-gray-300 p-2  font-bold">' . number_format($grandTotal) . '</td>';
                                        $output .= '</tr>';
                                        
                                        $output .= '</tbody></table>';
                                        $output .= '</div>';
                                    }
                                } 
                                // Handle sub jenis data
                                elseif ($isSubJenis) {
                                    $subJenisData = RespondAssetDesaSubJenisData::where('respond_assetdesa_detail_id', $record->id)->get();
                                    
                                    if ($subJenisData->isNotEmpty()) {
                                        $output .= '<div class="mt-6 space-y-8">';
                                        // Group by sub jenis
                                        $groupedData = [];
                                        foreach ($subJenisData as $item) {
                                            $subJenisDataItem = AssetDesaSubJenisData::find($item->assetdesa_sub_jenis_data_id);
                                            if (!$subJenisDataItem) continue;
                                            
                                            $subJenis = AssetDesaSubJenis::find($subJenisDataItem->subjenis_id);
                                            $subJenisName = $subJenis ? $subJenis->subjenis : 'Sub Jenis Tidak Ditemukan';
                                            
                                            if (!isset($groupedData[$subJenisName])) {
                                                $groupedData[$subJenisName] = [];
                                            }
                                            
                                            $groupedData[$subJenisName][] = [
                                                'name' => $subJenisDataItem->nama,
                                                'value' => $item->jawaban
                                            ];
                                        }
                                        
                                        // Display each sub jenis as a separate table
                                        foreach ($groupedData as $subJenisName => $items) {
                                            $output .= '<div class="mb-4">';
                                            $output .= '<h3 class="text-lg font-bold mb-2">' . $subJenisName . '</h3>';
                                            $output .= '<table class="w-full border-collapse border border-gray-300">';
                                            $output .= '<thead><tr>';
                                            $output .= '<th class="border border-gray-300 p-2 bg-gray-100">Data</th>';
                                            $output .= '<th class="border border-gray-300 p-2 bg-gray-100">Jawaban</th>';
                                            $output .= '</tr></thead>';
                                            $output .= '<tbody>';
                                            
                                            foreach ($items as $item) {
                                                // Format jawaban if numeric
                                                $jawaban = $item['value'];
                                                $displayJawaban = is_numeric($jawaban) ? number_format(floatval($jawaban)) : $jawaban;
                                                
                                                $output .= '<tr>';
                                                $output .= '<td class="border border-gray-300 p-2">' . $item['name'] . '</td>';
                                                $output .= '<td class="border border-gray-300 p-2 ">' . $displayJawaban . '</td>';
                                                $output .= '</tr>';
                                            }
                                            
                                            $output .= '</tbody></table>';
                                            $output .= '</div>';
                                        }
                                        
                                        $output .= '</div>';
                                    }
                                }
                                
                                // If no data was added to output
                                if (empty($output)) {
                                    return '<div class="text-red-500 font-bold">Tidak ada data tersedia</div>';
                                }
                                
                                return $output;
                            }),
                    ])
                    ->collapsible(false)
            ]);
    }


    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('assetDesa.jenis')
                    ->label('Jenis Asset')
                    ->sortable()
                    ->searchable(),
                
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('assetdesa_id')
                    ->relationship('assetDesa', 'jenis')
                    ->label('Jenis Asset'),
                
                
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, $record) {
                        // Set appropriate flags based on asset type
                        if ($record->assetDesa) {
                            $isJenisKelamin = $record->assetDesa->is_jenis_kelamin;
                            $isSubJenis = $record->assetDesa->is_data && $record->assetDesa->is_sub_jenis;
                            
                            // Set flags for showing/hiding form sections
                            $data['show_jenis_kelamin'] = $isJenisKelamin;
                            $data['show_sub_jenis'] = $isSubJenis;
                            
                            // For jenis kelamin data
                            if ($isJenisKelamin) {
                                $jenisKelaminData = RespondAssetDesaJenisKelamin::where('respond_assetdesa_detail_id', $record->id)
                                    ->where('assetdesa_data_id', $record->assetdesa_data_id)
                                    ->first();
                                    
                                if ($jenisKelaminData) {
                                    $data['jawaban_laki_laki'] = $jenisKelaminData->jawaban_laki_laki;
                                    $data['jawaban_perempuan'] = $jenisKelaminData->jawaban_perempuan;
                                }
                            } 
                            // For sub jenis data
                            elseif ($isSubJenis && $record->assetdesa_sub_jenis_data_id) {
                                $subJenisData = RespondAssetDesaSubJenisData::where('respond_assetdesa_detail_id', $record->id)
                                    ->where('assetdesa_sub_jenis_data_id', $record->assetdesa_sub_jenis_data_id)
                                    ->first();
                                    
                                if ($subJenisData) {
                                    $data['nilai'] = $subJenisData->jawaban;
                                }
                            } 
                            // For regular data
                            elseif ($record->assetdesa_data_id) {
                                $regularData = RespondAssetDesaData::where('respond_assetdesa_detail_id', $record->id)
                                    ->where('assetdesa_data_id', $record->assetdesa_data_id)
                                    ->first();
                                    
                                if ($regularData) {
                                    $data['nilai'] = $regularData->jawaban;
                                }
                            }
                        }
                        
                        return $data;
                    })
                    ->after(function ($record, array $data) {
                        try {
                            // Update related data based on asset type
                            if ($record->assetDesa) {
                                $isJenisKelamin = $record->assetDesa->is_jenis_kelamin;
                                $isSubJenis = $record->assetDesa->is_data && $record->assetDesa->is_sub_jenis;
                                
                                if ($isJenisKelamin) {
                                    // Handle jenis kelamin data
                                    $existingJenisKelamin = RespondAssetDesaJenisKelamin::where('respond_assetdesa_detail_id', $record->id)
                                        ->where('assetdesa_data_id', $record->assetdesa_data_id)
                                        ->first();
                                        
                                    if ($existingJenisKelamin) {
                                        $existingJenisKelamin->update([
                                            'jawaban_laki_laki' => $data['jawaban_laki_laki'],
                                            'jawaban_perempuan' => $data['jawaban_perempuan']
                                        ]);
                                    } else {
                                        RespondAssetDesaJenisKelamin::create([
                                            'respond_assetdesa_detail_id' => $record->id,
                                            'assetdesa_data_id' => $record->assetdesa_data_id,
                                            'jawaban_laki_laki' => $data['jawaban_laki_laki'],
                                            'jawaban_perempuan' => $data['jawaban_perempuan']
                                        ]);
                                    }
                                } elseif ($isSubJenis && $record->assetdesa_sub_jenis_data_id) {
                                    // Handle sub jenis data
                                    $existingSubJenisData = RespondAssetDesaSubJenisData::where('respond_assetdesa_detail_id', $record->id)
                                        ->where('assetdesa_sub_jenis_data_id', $record->assetdesa_sub_jenis_data_id)
                                        ->first();
                                        
                                    if ($existingSubJenisData) {
                                        $existingSubJenisData->update(['jawaban' => $data['nilai']]);
                                    } else {
                                        RespondAssetDesaSubJenisData::create([
                                            'respond_assetdesa_detail_id' => $record->id,
                                            'assetdesa_sub_jenis_data_id' => $record->assetdesa_sub_jenis_data_id,
                                            'jawaban' => $data['nilai']
                                        ]);
                                    }
                                } elseif ($record->assetdesa_data_id) {
                                    // Handle regular data
                                    $existingResponse = RespondAssetDesaData::where('respond_assetdesa_detail_id', $record->id)
                                        ->where('assetdesa_data_id', $record->assetdesa_data_id)
                                        ->first();
                                        
                                    if ($existingResponse) {
                                        $existingResponse->update(['jawaban' => $data['nilai']]);
                                    } else {
                                        RespondAssetDesaData::create([
                                            'respond_assetdesa_detail_id' => $record->id,
                                            'assetdesa_data_id' => $record->assetdesa_data_id,
                                            'jawaban' => $data['nilai']
                                        ]);
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            // Log the error for debugging
                            Log::error('Error updating asset desa data', [
                                'record_id' => $record->id,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                        }
                    })
                    ->mutateRecordDataUsing(function (array $data, $record) {
                        // First, set the appropriate flags based on asset type
                        if ($record->assetDesa) {
                            $isJenisKelamin = $record->assetDesa->is_jenis_kelamin;
                            $isSubJenis = $record->assetDesa->is_data && $record->assetDesa->is_sub_jenis;
                            
                            // Log what type of record we're dealing with
                            Log::info('Editing record', [
                                'record_id' => $record->id,
                                'is_jenis_kelamin' => $isJenisKelamin,
                                'is_sub_jenis' => $isSubJenis,
                                'assetdesa_data_id' => $record->assetdesa_data_id,
                                'assetdesa_sub_jenis_data_id' => $record->assetdesa_sub_jenis_data_id
                            ]);
                            
                            // Set flags for showing/hiding form sections
                            $data['show_jenis_kelamin'] = $isJenisKelamin;
                            $data['show_sub_jenis'] = $isSubJenis;
                            
                            // For jenis kelamin data
                            if ($isJenisKelamin) {
                                $jenisKelaminData = RespondAssetDesaJenisKelamin::where('respond_assetdesa_detail_id', $record->id)->first();
                                    
                                if ($jenisKelaminData) {
                                    $data['jawaban_laki_laki'] = $jenisKelaminData->jawaban_laki_laki;
                                    $data['jawaban_perempuan'] = $jenisKelaminData->jawaban_perempuan;
                                    
                                    Log::info('Found jenis kelamin data', [
                                        'laki' => $jenisKelaminData->jawaban_laki_laki,
                                        'perempuan' => $jenisKelaminData->jawaban_perempuan
                                    ]);
                                }
                            }
                            // Handle both regular and sub-jenis data
                            else {
                                // First check for regular data regardless of sub-jenis status
                                if ($record->assetdesa_data_id) {
                                    $regularData = RespondAssetDesaData::where('respond_assetdesa_detail_id', $record->id)
                                        ->where('assetdesa_data_id', $record->assetdesa_data_id)
                                        ->first();
                                        
                                    if ($regularData) {
                                        $data['nilai'] = $regularData->jawaban;
                                        
                                        Log::info('Found regular data', [
                                            'data_id' => $regularData->id,
                                            'jawaban' => $regularData->jawaban
                                        ]);
                                    }
                                }
                                
                                // Then check for sub-jenis data if applicable
                                if ($isSubJenis && $record->assetdesa_sub_jenis_data_id) {
                                    $subJenisData = RespondAssetDesaSubJenisData::where('respond_assetdesa_detail_id', $record->id)
                                        ->where('assetdesa_sub_jenis_data_id', $record->assetdesa_sub_jenis_data_id)
                                        ->first();
                                        
                                    if ($subJenisData) {
                                        // For now, use a different field name to avoid conflicts
                                        $data['nilai_sub_jenis'] = $subJenisData->jawaban;
                                        
                                        Log::info('Found sub jenis data', [
                                            'data_id' => $subJenisData->id,
                                            'jawaban' => $subJenisData->jawaban
                                        ]);
                                    }
                                }
                            }
                        }
                        
                        return $data;
                    })
                    ->after(function ($record, array $data) {
                        try {
                            // Update related data based on asset type
                            if ($record->assetDesa) {
                                $isJenisKelamin = $record->assetDesa->is_jenis_kelamin;
                                $isSubJenis = $record->assetDesa->is_data && $record->assetDesa->is_sub_jenis;
                                
                                Log::info('Saving record data', [
                                    'record_id' => $record->id,
                                    'is_jenis_kelamin' => $isJenisKelamin,
                                    'is_sub_jenis' => $isSubJenis,
                                    'data' => $data
                                ]);
                                
                                if ($isJenisKelamin) {
                                    // Handle jenis kelamin data
                                    $existingJenisKelamin = RespondAssetDesaJenisKelamin::where('respond_assetdesa_detail_id', $record->id)
                                        ->first();
                                        
                                    if ($existingJenisKelamin) {
                                        $existingJenisKelamin->update([
                                            'jawaban_laki_laki' => $data['jawaban_laki_laki'] ?? 0,
                                            'jawaban_perempuan' => $data['jawaban_perempuan'] ?? 0
                                        ]);
                                        
                                        Log::info('Updated jenis kelamin data', [
                                            'id' => $existingJenisKelamin->id
                                        ]);
                                    } else {
                                        $newData = RespondAssetDesaJenisKelamin::create([
                                            'respond_assetdesa_detail_id' => $record->id,
                                            'assetdesa_data_id' => $record->assetdesa_data_id,
                                            'jawaban_laki_laki' => $data['jawaban_laki_laki'] ?? 0,
                                            'jawaban_perempuan' => $data['jawaban_perempuan'] ?? 0
                                        ]);
                                        
                                        Log::info('Created jenis kelamin data', [
                                            'id' => $newData->id
                                        ]);
                                    }
                                } else {
                                    // Always handle regular data first
                                    if ($record->assetdesa_data_id) {
                                        $existingResponse = RespondAssetDesaData::where('respond_assetdesa_detail_id', $record->id)
                                            ->where('assetdesa_data_id', $record->assetdesa_data_id)
                                            ->first();
                                            
                                        $value = $data['nilai'] ?? '';
                                            
                                        if ($existingResponse) {
                                            $existingResponse->update(['jawaban' => $value]);
                                            
                                            Log::info('Updated regular data', [
                                                'id' => $existingResponse->id,
                                                'value' => $value
                                            ]);
                                        } else {
                                            $newData = RespondAssetDesaData::create([
                                                'respond_assetdesa_detail_id' => $record->id,
                                                'assetdesa_data_id' => $record->assetdesa_data_id,
                                                'jawaban' => $value
                                            ]);
                                            
                                            Log::info('Created regular data', [
                                                'id' => $newData->id,
                                                'value' => $value
                                            ]);
                                        }
                                    }
                                    
                                    // Then handle sub jenis data if applicable
                                    if ($isSubJenis && $record->assetdesa_sub_jenis_data_id) {
                                        $existingSubJenisData = RespondAssetDesaSubJenisData::where('respond_assetdesa_detail_id', $record->id)
                                            ->where('assetdesa_sub_jenis_data_id', $record->assetdesa_sub_jenis_data_id)
                                            ->first();
                                            
                                        $value = $data['nilai_sub_jenis'] ?? $data['nilai'] ?? '';
                                            
                                        if ($existingSubJenisData) {
                                            $existingSubJenisData->update(['jawaban' => $value]);
                                            
                                            Log::info('Updated sub jenis data', [
                                                'id' => $existingSubJenisData->id,
                                                'value' => $value
                                            ]);
                                        } else {
                                            $newData = RespondAssetDesaSubJenisData::create([
                                                'respond_assetdesa_detail_id' => $record->id,
                                                'assetdesa_sub_jenis_data_id' => $record->assetdesa_sub_jenis_data_id,
                                                'jawaban' => $value
                                            ]);
                                            
                                            Log::info('Created sub jenis data', [
                                                'id' => $newData->id,
                                                'value' => $value
                                            ]);
                                        }
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            // Log the error for debugging
                            Log::error('Error updating asset desa data', [
                                'record_id' => $record->id,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                        }
                    })
                    ->mutateRecordDataUsing(function (array $data, $record) {
                        // First, set the appropriate flags based on asset type
                        if ($record->assetDesa) {
                            $isJenisKelamin = $record->assetDesa->is_jenis_kelamin;
                            $isSubJenis = $record->assetDesa->is_data && $record->assetDesa->is_sub_jenis;
                            
                            // Set flags for showing/hiding form sections
                            $data['show_jenis_kelamin'] = $isJenisKelamin;
                            $data['show_sub_jenis'] = $isSubJenis;
                            
                            // For jenis kelamin data
                            if ($isJenisKelamin) {
                                $jenisKelaminData = RespondAssetDesaJenisKelamin::where('respond_assetdesa_detail_id', $record->id)
                                    ->first();
                                    
                                if ($jenisKelaminData) {
                                    $data['jawaban_laki_laki'] = $jenisKelaminData->jawaban_laki_laki;
                                    $data['jawaban_perempuan'] = $jenisKelaminData->jawaban_perempuan;
                                }
                            }
                            // For sub jenis data with regular data
                            elseif ($isSubJenis) {
                                // First load regular data if any
                                if ($record->assetdesa_data_id) {
                                    $regularData = RespondAssetDesaData::where('respond_assetdesa_detail_id', $record->id)
                                        ->where('assetdesa_data_id', $record->assetdesa_data_id)
                                        ->first();
                                        
                                    if ($regularData) {
                                        $data['nilai'] = $regularData->jawaban;
                                    }
                                }
                                
                                // Then check for sub jenis specific data
                                if ($record->assetdesa_sub_jenis_data_id) {
                                    $subJenisData = RespondAssetDesaSubJenisData::where('respond_assetdesa_detail_id', $record->id)
                                        ->where('assetdesa_sub_jenis_data_id', $record->assetdesa_sub_jenis_data_id)
                                        ->first();
                                        
                                    if ($subJenisData) {
                                        // Only override nilai if we actually found sub jenis data
                                        $data['nilai'] = $subJenisData->jawaban;
                                    }
                                }
                            }
                            // For regular data only
                            else if ($record->assetdesa_data_id) {
                                $regularData = RespondAssetDesaData::where('respond_assetdesa_detail_id', $record->id)
                                    ->where('assetdesa_data_id', $record->assetdesa_data_id)
                                    ->first();
                                    
                                if ($regularData) {
                                    $data['nilai'] = $regularData->jawaban;
                                }
                            }
                        }
                        
                        return $data;
                    })
                    ->after(function ($record, array $data) {
                        try {
                            // Update related data based on asset type
                            if ($record->assetDesa) {
                                $isJenisKelamin = $record->assetDesa->is_jenis_kelamin;
                                $isSubJenis = $record->assetDesa->is_data && $record->assetDesa->is_sub_jenis;
                                
                                if ($isJenisKelamin) {
                                    // Handle jenis kelamin data
                                    $existingJenisKelamin = RespondAssetDesaJenisKelamin::where('respond_assetdesa_detail_id', $record->id)
                                        ->first();
                                        
                                    if ($existingJenisKelamin) {
                                        $existingJenisKelamin->update([
                                            'jawaban_laki_laki' => $data['jawaban_laki_laki'] ?? 0,
                                            'jawaban_perempuan' => $data['jawaban_perempuan'] ?? 0
                                        ]);
                                    } else {
                                        RespondAssetDesaJenisKelamin::create([
                                            'respond_assetdesa_detail_id' => $record->id,
                                            'assetdesa_data_id' => $record->assetdesa_data_id,
                                            'jawaban_laki_laki' => $data['jawaban_laki_laki'] ?? 0,
                                            'jawaban_perempuan' => $data['jawaban_perempuan'] ?? 0
                                        ]);
                                    }
                                } else {
                                    // For both regular data and sub jenis
                                    
                                    // First handle regular data if applicable
                                    if ($record->assetdesa_data_id) {
                                        $existingResponse = RespondAssetDesaData::where('respond_assetdesa_detail_id', $record->id)
                                            ->where('assetdesa_data_id', $record->assetdesa_data_id)
                                            ->first();
                                            
                                        if ($existingResponse) {
                                            $existingResponse->update(['jawaban' => $data['nilai'] ?? '']);
                                        } else {
                                            RespondAssetDesaData::create([
                                                'respond_assetdesa_detail_id' => $record->id,
                                                'assetdesa_data_id' => $record->assetdesa_data_id,
                                                'jawaban' => $data['nilai'] ?? ''
                                            ]);
                                        }
                                    }
                                    
                                    // Then handle sub jenis data if applicable
                                    if ($isSubJenis && $record->assetdesa_sub_jenis_data_id) {
                                        $existingSubJenisData = RespondAssetDesaSubJenisData::where('respond_assetdesa_detail_id', $record->id)
                                            ->where('assetdesa_sub_jenis_data_id', $record->assetdesa_sub_jenis_data_id)
                                            ->first();
                                            
                                        if ($existingSubJenisData) {
                                            $existingSubJenisData->update(['jawaban' => $data['nilai'] ?? '']);
                                        } else {
                                            RespondAssetDesaSubJenisData::create([
                                                'respond_assetdesa_detail_id' => $record->id,
                                                'assetdesa_sub_jenis_data_id' => $record->assetdesa_sub_jenis_data_id,
                                                'jawaban' => $data['nilai'] ?? ''
                                            ]);
                                        }
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            // Log the error for debugging
                            Log::error('Error updating asset desa data', [
                                'record_id' => $record->id,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                        }
                    })
                    ->mutateRecordDataUsing(function (array $data, $record) {
                        // First, set the appropriate flags based on asset type
                        if ($record->assetDesa) {
                            $isJenisKelamin = $record->assetDesa->is_jenis_kelamin;
                            $isSubJenis = $record->assetDesa->is_data && $record->assetDesa->is_sub_jenis;
                            
                            // Set flags for showing/hiding form sections
                            $data['show_jenis_kelamin'] = $isJenisKelamin;
                            $data['show_sub_jenis'] = $isSubJenis;
                            
                            // Log for debugging what type of asset this is
                            Log::info('Asset type', [
                                'record_id' => $record->id,
                                'asset_id' => $record->assetdesa_id,
                                'is_jenis_kelamin' => $isJenisKelamin,
                                'is_sub_jenis' => $isSubJenis,
                                'assetdesa_data_id' => $record->assetdesa_data_id,
                                'assetdesa_sub_jenis_data_id' => $record->assetdesa_sub_jenis_data_id
                            ]);
                            
                            // For jenis kelamin data
                            if ($isJenisKelamin) {
                                $jenisKelaminData = RespondAssetDesaJenisKelamin::where('respond_assetdesa_detail_id', $record->id)
                                    ->first();
                                    
                                if ($jenisKelaminData) {
                                    Log::info('Found jenis kelamin data', [
                                        'record_id' => $record->id,
                                        'jenis_kelamin_id' => $jenisKelaminData->id,
                                        'laki' => $jenisKelaminData->jawaban_laki_laki,
                                        'perempuan' => $jenisKelaminData->jawaban_perempuan
                                    ]);
                                    $data['jawaban_laki_laki'] = $jenisKelaminData->jawaban_laki_laki;
                                    $data['jawaban_perempuan'] = $jenisKelaminData->jawaban_perempuan;
                                }
                            }
                            // For sub jenis data
                            elseif ($isSubJenis) {
                                $subJenisData = RespondAssetDesaSubJenisData::where('respond_assetdesa_detail_id', $record->id)
                                    ->first();
                                    
                                if ($subJenisData) {
                                    Log::info('Found sub jenis data', [
                                        'record_id' => $record->id,
                                        'sub_jenis_id' => $subJenisData->id,
                                        'jawaban' => $subJenisData->jawaban
                                    ]);
                                    $data['nilai'] = $subJenisData->jawaban;
                                }
                            }
                            // For regular data
                            else {
                                $regularData = RespondAssetDesaData::where('respond_assetdesa_detail_id', $record->id)
                                    ->first();
                                    
                                if ($regularData) {
                                    Log::info('Found regular data', [
                                        'record_id' => $record->id,
                                        'regular_data_id' => $regularData->id,
                                        'jawaban' => $regularData->jawaban
                                    ]);
                                    $data['nilai'] = $regularData->jawaban;
                                }
                            }
                        }
                        
                        return $data;
                    })
                    ->after(function ($record, array $data) {
                        try {
                            // Update related data based on asset type
                            if ($record->assetDesa) {
                                $isJenisKelamin = $record->assetDesa->is_jenis_kelamin;
                                $isSubJenis = $record->assetDesa->is_data && $record->assetDesa->is_sub_jenis;
                                
                                Log::info('Processing edit after submit', [
                                    'record_id' => $record->id,
                                    'is_jenis_kelamin' => $isJenisKelamin,
                                    'is_sub_jenis' => $isSubJenis,
                                    'data_to_save' => $data
                                ]);
                                
                                if ($isJenisKelamin) {
                                    // Handle jenis kelamin data
                                    $existingJenisKelamin = RespondAssetDesaJenisKelamin::where('respond_assetdesa_detail_id', $record->id)
                                        ->first();
                                        
                                    if ($existingJenisKelamin) {
                                        $existingJenisKelamin->update([
                                            'jawaban_laki_laki' => $data['jawaban_laki_laki'] ?? 0,
                                            'jawaban_perempuan' => $data['jawaban_perempuan'] ?? 0
                                        ]);
                                        Log::info('Updated jenis kelamin data', ['id' => $existingJenisKelamin->id]);
                                    } else {
                                        $newData = RespondAssetDesaJenisKelamin::create([
                                            'respond_assetdesa_detail_id' => $record->id,
                                            'assetdesa_data_id' => $record->assetdesa_data_id,
                                            'jawaban_laki_laki' => $data['jawaban_laki_laki'] ?? 0,
                                            'jawaban_perempuan' => $data['jawaban_perempuan'] ?? 0
                                        ]);
                                        Log::info('Created jenis kelamin data', ['id' => $newData->id]);
                                    }
                                } elseif ($isSubJenis) {
                                    // Handle sub jenis data
                                    $existingSubJenisData = RespondAssetDesaSubJenisData::where('respond_assetdesa_detail_id', $record->id)
                                        ->where('assetdesa_sub_jenis_data_id', $record->assetdesa_sub_jenis_data_id)
                                        ->first();
                                        
                                    if ($existingSubJenisData) {
                                        $existingSubJenisData->update(['jawaban' => $data['nilai'] ?? '']);
                                        Log::info('Updated sub jenis data', ['id' => $existingSubJenisData->id]);
                                    } else {
                                        $newData = RespondAssetDesaSubJenisData::create([
                                            'respond_assetdesa_detail_id' => $record->id,
                                            'assetdesa_sub_jenis_data_id' => $record->assetdesa_sub_jenis_data_id,
                                            'jawaban' => $data['nilai'] ?? ''
                                        ]);
                                        Log::info('Created sub jenis data', ['id' => $newData->id]);
                                    }
                                } else {
                                    // Handle regular data
                                    $existingResponse = RespondAssetDesaData::where('respond_assetdesa_detail_id', $record->id)
                                        ->where('assetdesa_data_id', $record->assetdesa_data_id)
                                        ->first();
                                        
                                    if ($existingResponse) {
                                        $existingResponse->update(['jawaban' => $data['nilai'] ?? '']);
                                        Log::info('Updated regular data', ['id' => $existingResponse->id]);
                                    } else {
                                        $newData = RespondAssetDesaData::create([
                                            'respond_assetdesa_detail_id' => $record->id,
                                            'assetdesa_data_id' => $record->assetdesa_data_id,
                                            'jawaban' => $data['nilai'] ?? ''
                                        ]);
                                        Log::info('Created regular data', ['id' => $newData->id]);
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            // Log the error for debugging
                            Log::error('Error updating asset desa data', [
                                'record_id' => $record->id,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                        }
                    })
                    ->mutateRecordDataUsing(function (array $data, $record) {
                        // Set appropriate flags based on asset type
                        if ($record->assetDesa) {
                            $isJenisKelamin = $record->assetDesa->is_jenis_kelamin;
                            $isSubJenis = $record->assetDesa->is_data && $record->assetDesa->is_sub_jenis;
                            
                            // Set flags for showing/hiding form sections
                            $data['show_jenis_kelamin'] = $isJenisKelamin;
                            $data['show_sub_jenis'] = $isSubJenis;
                            
                            // For jenis kelamin data
                            if ($isJenisKelamin) {
                                $jenisKelaminData = RespondAssetDesaJenisKelamin::where('respond_assetdesa_detail_id', $record->id)
                                    ->where('assetdesa_data_id', $record->assetdesa_data_id)
                                    ->first();
                                    
                                if ($jenisKelaminData) {
                                    $data['jawaban_laki_laki'] = $jenisKelaminData->jawaban_laki_laki;
                                    $data['jawaban_perempuan'] = $jenisKelaminData->jawaban_perempuan;
                                }
                            } 
                            // For sub jenis data
                            elseif ($isSubJenis && $record->assetdesa_sub_jenis_data_id) {
                                // Important: We need to query by sub_jenis_data_id here
                                $subJenisData = RespondAssetDesaSubJenisData::where('respond_assetdesa_detail_id', $record->id)
                                    ->where('assetdesa_sub_jenis_data_id', $record->assetdesa_sub_jenis_data_id)
                                    ->first();
                                    
                                if ($subJenisData) {
                                    $data['nilai'] = $subJenisData->jawaban;
                                }
                            } 
                            // For regular data
                            elseif ($record->assetdesa_data_id) {
                                $regularData = RespondAssetDesaData::where('respond_assetdesa_detail_id', $record->id)
                                    ->where('assetdesa_data_id', $record->assetdesa_data_id)
                                    ->first();
                                    
                                if ($regularData) {
                                    $data['nilai'] = $regularData->jawaban;
                                }
                            }
                        }
                        
                        return $data;
                    })
                    ->after(function ($record, array $data) {
                        try {
                            // Update related data based on asset type
                            if ($record->assetDesa) {
                                $isJenisKelamin = $record->assetDesa->is_jenis_kelamin;
                                $isSubJenis = $record->assetDesa->is_data && $record->assetDesa->is_sub_jenis;
                                
                                if ($isJenisKelamin) {
                                    // Handle jenis kelamin data
                                    $existingJenisKelamin = RespondAssetDesaJenisKelamin::where('respond_assetdesa_detail_id', $record->id)
                                        ->where('assetdesa_data_id', $record->assetdesa_data_id)
                                        ->first();
                                        
                                    if ($existingJenisKelamin) {
                                        $existingJenisKelamin->update([
                                            'jawaban_laki_laki' => $data['jawaban_laki_laki'],
                                            'jawaban_perempuan' => $data['jawaban_perempuan']
                                        ]);
                                    } else {
                                        RespondAssetDesaJenisKelamin::create([
                                            'respond_assetdesa_detail_id' => $record->id,
                                            'assetdesa_data_id' => $record->assetdesa_data_id,
                                            'jawaban_laki_laki' => $data['jawaban_laki_laki'],
                                            'jawaban_perempuan' => $data['jawaban_perempuan']
                                        ]);
                                    }
                                } elseif ($isSubJenis && $record->assetdesa_sub_jenis_data_id) {
                                    // Handle sub jenis data
                                    $existingSubJenisData = RespondAssetDesaSubJenisData::where('respond_assetdesa_detail_id', $record->id)
                                        ->where('assetdesa_sub_jenis_data_id', $record->assetdesa_sub_jenis_data_id)
                                        ->first();
                                        
                                    if ($existingSubJenisData) {
                                        $existingSubJenisData->update(['jawaban' => $data['nilai']]);
                                    } else {
                                        RespondAssetDesaSubJenisData::create([
                                            'respond_assetdesa_detail_id' => $record->id,
                                            'assetdesa_sub_jenis_data_id' => $record->assetdesa_sub_jenis_data_id,
                                            'jawaban' => $data['nilai']
                                        ]);
                                    }
                                } elseif ($record->assetdesa_data_id) {
                                    // Handle regular data
                                    $existingResponse = RespondAssetDesaData::where('respond_assetdesa_detail_id', $record->id)
                                        ->where('assetdesa_data_id', $record->assetdesa_data_id)
                                        ->first();
                                        
                                    if ($existingResponse) {
                                        $existingResponse->update(['jawaban' => $data['nilai']]);
                                    } else {
                                        RespondAssetDesaData::create([
                                            'respond_assetdesa_detail_id' => $record->id,
                                            'assetdesa_data_id' => $record->assetdesa_data_id,
                                            'jawaban' => $data['nilai']
                                        ]);
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            // Log the error for debugging
                            Log::error('Error updating asset desa data', [
                                'record_id' => $record->id,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                        }
                    })
                    ->mutateRecordDataUsing(function (array $data, $record) {
                        // Set appropriate flags based on asset type
                        if ($record->assetDesa) {
                            $isJenisKelamin = $record->assetDesa->is_jenis_kelamin;
                            $isSubJenis = $record->assetDesa->is_data && $record->assetDesa->is_sub_jenis;
                            
                            // Set flags for showing/hiding form sections
                            $data['show_jenis_kelamin'] = $isJenisKelamin;
                            $data['show_sub_jenis'] = $isSubJenis;
                            
                            // For jenis kelamin data
                            if ($isJenisKelamin) {
                                $jenisKelaminData = RespondAssetDesaJenisKelamin::where('respond_assetdesa_detail_id', $record->id)
                                    ->first(); // Remove the data_id filter to find any record
                                    
                                if ($jenisKelaminData) {
                                    $data['jawaban_laki_laki'] = $jenisKelaminData->jawaban_laki_laki;
                                    $data['jawaban_perempuan'] = $jenisKelaminData->jawaban_perempuan;
                                }
                            } 
                            // For sub jenis data
                            elseif ($isSubJenis) {
                                $subJenisData = RespondAssetDesaSubJenisData::where('respond_assetdesa_detail_id', $record->id)
                                    ->first(); // Remove the data_id filter to find any record
                                    
                                if ($subJenisData) {
                                    $data['nilai'] = $subJenisData->jawaban;
                                }
                            } 
                            // For regular data
                            else {
                                $regularData = RespondAssetDesaData::where('respond_assetdesa_detail_id', $record->id)
                                    ->first(); // Remove the data_id filter to find any record
                                    
                                if ($regularData) {
                                    $data['nilai'] = $regularData->jawaban;
                                }
                            }
                        }
                        
                        return $data;
                    })
                    ->after(function ($record, array $data) {
                        try {
                            // Update related data based on asset type
                            if ($record->assetDesa) {
                                $isJenisKelamin = $record->assetDesa->is_jenis_kelamin;
                                $isSubJenis = $record->assetDesa->is_data && $record->assetDesa->is_sub_jenis;
                                
                                if ($isJenisKelamin) {
                                    // Handle jenis kelamin data
                                    $existingJenisKelamin = RespondAssetDesaJenisKelamin::where('respond_assetdesa_detail_id', $record->id)
                                        ->where('assetdesa_data_id', $record->assetdesa_data_id)
                                        ->first();
                                        
                                    if ($existingJenisKelamin) {
                                        $existingJenisKelamin->update([
                                            'jawaban_laki_laki' => $data['jawaban_laki_laki'],
                                            'jawaban_perempuan' => $data['jawaban_perempuan']
                                        ]);
                                    } else {
                                        RespondAssetDesaJenisKelamin::create([
                                            'respond_assetdesa_detail_id' => $record->id,
                                            'assetdesa_data_id' => $record->assetdesa_data_id,
                                            'jawaban_laki_laki' => $data['jawaban_laki_laki'],
                                            'jawaban_perempuan' => $data['jawaban_perempuan']
                                        ]);
                                    }
                                } elseif ($isSubJenis && $record->assetdesa_sub_jenis_data_id) {
                                    // Handle sub jenis data
                                    $existingSubJenisData = RespondAssetDesaSubJenisData::where('respond_assetdesa_detail_id', $record->id)
                                        ->where('assetdesa_sub_jenis_data_id', $record->assetdesa_sub_jenis_data_id)
                                        ->first();
                                        
                                    if ($existingSubJenisData) {
                                        $existingSubJenisData->update(['jawaban' => $data['nilai']]);
                                    } else {
                                        RespondAssetDesaSubJenisData::create([
                                            'respond_assetdesa_detail_id' => $record->id,
                                            'assetdesa_sub_jenis_data_id' => $record->assetdesa_sub_jenis_data_id,
                                            'jawaban' => $data['nilai']
                                        ]);
                                    }
                                } elseif ($record->assetdesa_data_id) {
                                    // Handle regular data
                                    $existingResponse = RespondAssetDesaData::where('respond_assetdesa_detail_id', $record->id)
                                        ->where('assetdesa_data_id', $record->assetdesa_data_id)
                                        ->first();
                                        
                                    if ($existingResponse) {
                                        $existingResponse->update(['jawaban' => $data['nilai']]);
                                    } else {
                                        RespondAssetDesaData::create([
                                            'respond_assetdesa_detail_id' => $record->id,
                                            'assetdesa_data_id' => $record->assetdesa_data_id,
                                            'jawaban' => $data['nilai']
                                        ]);
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            // Log the error for debugging
                            Log::error('Error updating asset desa data', [
                                'record_id' => $record->id,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                        }
                    })
                    ->mutateRecordDataUsing(function (array $data, $record) {
                        // Load related values based on asset type
                        if ($record->assetDesa) {
                            $isJenisKelamin = $record->assetDesa->is_jenis_kelamin;
                            $isSubJenis = $record->assetDesa->is_data && $record->assetDesa->is_sub_jenis;
                            
                            if ($isJenisKelamin) {
                                $jenisKelaminData = RespondAssetDesaJenisKelamin::where('respond_assetdesa_detail_id', $record->id)
                                    ->where('assetdesa_data_id', $record->assetdesa_data_id)
                                    ->first();
                                    
                                if ($jenisKelaminData) {
                                    $data['jawaban_laki_laki'] = $jenisKelaminData->jawaban_laki_laki;
                                    $data['jawaban_perempuan'] = $jenisKelaminData->jawaban_perempuan;
                                    $data['show_jenis_kelamin'] = true;
                                }
                            } elseif ($isSubJenis) {
                                $subJenisData = RespondAssetDesaSubJenisData::where('respond_assetdesa_detail_id', $record->id)
                                    ->where('assetdesa_sub_jenis_data_id', $record->assetdesa_sub_jenis_data_id)
                                    ->first();
                                    
                                if ($subJenisData) {
                                    $data['nilai'] = $subJenisData->jawaban;
                                }
                                $data['show_sub_jenis'] = true;
                            }
                        }
                        
                        return $data;
                    })
                    ->after(function ($record, array $data) {
                        // Update related data based on asset type
                        if ($record->assetDesa) {
                            $isJenisKelamin = $record->assetDesa->is_jenis_kelamin;
                            $isSubJenis = $record->assetDesa->is_data && $record->assetDesa->is_sub_jenis;
                            
                            if ($isJenisKelamin) {
                                // Handle jenis kelamin data
                                $existingJenisKelamin = RespondAssetDesaJenisKelamin::where('respond_assetdesa_detail_id', $record->id)
                                    ->where('assetdesa_data_id', $record->assetdesa_data_id)
                                    ->first();
                                    
                                if ($existingJenisKelamin) {
                                    $existingJenisKelamin->update([
                                        'jawaban_laki_laki' => $data['jawaban_laki_laki'],
                                        'jawaban_perempuan' => $data['jawaban_perempuan']
                                    ]);
                                } else {
                                    RespondAssetDesaJenisKelamin::create([
                                        'respond_assetdesa_detail_id' => $record->id,
                                        'assetdesa_data_id' => $record->assetdesa_data_id,
                                        'jawaban_laki_laki' => $data['jawaban_laki_laki'],
                                        'jawaban_perempuan' => $data['jawaban_perempuan']
                                    ]);
                                }
                            } elseif ($isSubJenis && $record->assetdesa_sub_jenis_data_id) {
                                // Handle sub jenis data
                                $existingSubJenisData = RespondAssetDesaSubJenisData::where('respond_assetdesa_detail_id', $record->id)
                                    ->where('assetdesa_sub_jenis_data_id', $record->assetdesa_sub_jenis_data_id)
                                    ->first();
                                    
                                if ($existingSubJenisData) {
                                    $existingSubJenisData->update(['jawaban' => $data['nilai']]);
                                } else {
                                    RespondAssetDesaSubJenisData::create([
                                        'respond_assetdesa_detail_id' => $record->id,
                                        'assetdesa_sub_jenis_data_id' => $record->assetdesa_sub_jenis_data_id,
                                        'jawaban' => $data['nilai']
                                    ]);
                                }
                            } elseif ($record->assetdesa_data_id) {
                                // Handle regular data
                                $existingResponse = RespondAssetDesaData::where('respond_assetdesa_detail_id', $record->id)
                                    ->where('assetdesa_data_id', $record->assetdesa_data_id)
                                    ->first();
                                    
                                if ($existingResponse) {
                                    $existingResponse->update(['jawaban' => $data['nilai']]);
                                } else {
                                    RespondAssetDesaData::create([
                                        'respond_assetdesa_detail_id' => $record->id,
                                        'assetdesa_data_id' => $record->assetdesa_data_id,
                                        'jawaban' => $data['nilai']
                                    ]);
                                }
                            }
                        }
                    }),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($record) {
                        // Delete all related data
                        RespondAssetDesaData::where('respond_assetdesa_detail_id', $record->id)->delete();
                        RespondAssetDesaJenisKelamin::where('respond_assetdesa_detail_id', $record->id)->delete();
                        RespondAssetDesaSubJenisData::where('respond_assetdesa_detail_id', $record->id)->delete();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                RespondAssetDesaData::where('respond_assetdesa_detail_id', $record->id)->delete();
                                RespondAssetDesaJenisKelamin::where('respond_assetdesa_detail_id', $record->id)->delete();
                                RespondAssetDesaSubJenisData::where('respond_assetdesa_detail_id', $record->id)->delete();
                            }
                        }),
                ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data) {
                        // Make sure we save nilai when it's a regular data type
                        if (!isset($data['show_jenis_kelamin']) || !$data['show_jenis_kelamin']) {
                            $data['nilai'] = $data['nilai'] ?? '';
                        }
                        return $data;
                    })
                    ->after(function ($record, array $data) {
                        // Check asset type
                        $asset = AssetDesa::find($record->assetdesa_id);
                        
                        if (!$asset) return;
                        
                        $isJenisKelamin = $asset->is_jenis_kelamin;
                        $isSubJenis = $asset->is_data && $asset->is_sub_jenis;
                        
                        if ($isJenisKelamin) {
                            // Create jenis_kelamin response
                            RespondAssetDesaJenisKelamin::create([
                                'respond_assetdesa_detail_id' => $record->id,
                                'assetdesa_data_id' => $record->assetdesa_data_id,
                                'jawaban_laki_laki' => $data['jawaban_laki_laki'] ?? 0,
                                'jawaban_perempuan' => $data['jawaban_perempuan'] ?? 0
                            ]);
                        } else if ($isSubJenis && $record->assetdesa_sub_jenis_data_id) {
                            // Create sub jenis data response
                            RespondAssetDesaSubJenisData::create([
                                'respond_assetdesa_detail_id' => $record->id,
                                'assetdesa_sub_jenis_data_id' => $record->assetdesa_sub_jenis_data_id,
                                'jawaban' => $record->nilai
                            ]);
                        } else if ($record->assetdesa_data_id) {
                            // Create regular data response
                            RespondAssetDesaData::create([
                                'respond_assetdesa_detail_id' => $record->id,
                                'assetdesa_data_id' => $record->assetdesa_data_id,
                                'jawaban' => $record->nilai
                            ]);
                        }
                    }),
                    Tables\Actions\ImportAction::make()
                        ->label('Import Data Reguler')
                        ->icon('heroicon-s-document-plus')
                        ->color('primary')
                        ->importer(RegularAssetDataImporter::class)
                        ->modalHeading('Import Data Reguler'),
                        
                    Tables\Actions\ImportAction::make('importJenisKelamin')
                        ->label('Import Data Jenis Kelamin')
                        ->icon('heroicon-s-users')
                        ->color('success')
                        ->importer(JenisKelaminAssetDataImporter::class)
                        ->modalHeading('Import Data Jenis Kelamin')
                        ->modalDescription('Upload CSV/Excel dengan data jenis kelamin.'),
                        
                    Tables\Actions\ImportAction::make('importSubJenis')
                        ->label('Import Data Sub Jenis')
                        ->icon('heroicon-s-table-cells')
                        ->color('warning')
                        ->importer(SubJenisAssetDataImporter::class)
                        ->modalHeading('Import Data Sub Jenis')
                        ->modalDescription('Upload CSV/Excel dengan data sub jenis.'),
            ]);
            
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
                'jenisKelamin',
                'assetDesaSubJenis',
                'assetDesaSubJenisData',
                'subJenisData' 
            ]); 
    }
}