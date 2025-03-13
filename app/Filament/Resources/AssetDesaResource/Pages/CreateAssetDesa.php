<?php

namespace App\Filament\Resources\AssetDesaResource\Pages;

use App\Filament\Resources\AssetDesaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAssetDesa extends CreateRecord
{
    protected static string $resource = AssetDesaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Return only the main model data
        return [
            'jenis' => $data['jenis'],
            'is_data' => $data['is_data'],
            'is_sub_jenis' => $data['is_sub_jenis'],
            'is_jenis_kelamin' => $data['is_jenis_kelamin'],
        ];
    }

    protected function afterCreate(): void
    {
        $formData = $this->form->getState();
        $record = $this->record;
    
        try {
            // Handle simple data
            if ($record->is_data && !$record->is_sub_jenis && isset($formData['temp_data_simple'])) {
                // Delete existing simple data
                $record->data()->delete();
                
                // Add new simple data with multiple answer flag
                foreach ($formData['temp_data_simple'] as $item) {
                    $record->data()->create([
                        'nama' => $item['item'],
                        'is_multiple_answer' => $item['is_multiple_answer'] ?? false
                    ]);
                }
            }
            
            // Handle data with sub
            if ($record->is_data && $record->is_sub_jenis && isset($formData['temp_data_with_sub'])) {
                // Delete existing sub jenis and its data
                foreach ($record->subJenis as $subJenis) {
                    $subJenis->data()->delete();
                }
                $record->subJenis()->delete();
                
                // Add new sub jenis and data with multiple answer flag
                foreach ($formData['temp_data_with_sub'] as $subItem) {
                    $subJenis = $record->subJenis()->create([
                        'subjenis' => $subItem['nama_subjenis']
                    ]);
                    
                    foreach ($subItem['data'] ?? [] as $dataItem) {
                        $subJenis->data()->create([
                            'nama' => $dataItem['item'],
                            'is_multiple_answer' => $dataItem['is_multiple_answer'] ?? false
                        ]);
                    }
                }
            }
        
        // Handle jenis kelamin
        if ($record->is_jenis_kelamin) {
            // Add new jenis kelamin
            foreach ($formData['temp_jenis_kelamin'] ?? [] as $item) {
                $record->jenisKelamin()->create(['value' => $item['item']]);
            }
        }
    }
    catch (\Exception $e) {
        Actions\Toast::make()->message($e->getMessage())->show();
    }
}        
}