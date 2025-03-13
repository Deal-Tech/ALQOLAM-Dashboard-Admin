<?php

namespace App\Filament\Resources\AssetDesaResource\Pages;

use App\Filament\Resources\AssetDesaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;


class EditAssetDesa extends EditRecord
{
    protected static string $resource = AssetDesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Debugging data
        try {
            // Log data for debugging
            \Illuminate\Support\Facades\Log::info('Record data', [
                'record' => $this->record->toArray(),
                'relationships' => [
                    'data' => $this->record->data->count(),
                    'subJenis' => $this->record->subJenis->count(),
                    'jenisKelamin' => $this->record->jenisKelamin->count(),
                ]
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in EditAssetDesa', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $data;
    }

    protected function afterSave(): void
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
            if ($record->is_jenis_kelamin && isset($formData['temp_jenis_kelamin'])) {
                // Delete existing jenis kelamin
                $record->jenisKelamin()->delete();
                
                // Add new jenis kelamin
                foreach ($formData['temp_jenis_kelamin'] as $item) {
                    $record->jenisKelamin()->create(['value' => $item['item']]);
                }
            }

            Notification::make()
                ->title('Data tersimpan')
                ->success()
                ->send();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in afterSave', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            Notification::make()
                ->title('Error menyimpan data')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}