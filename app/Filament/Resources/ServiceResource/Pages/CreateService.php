<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use App\Models\ServiceTypeField;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove the serviceTypeData from main data to handle separately
        $serviceTypeData = $data['serviceTypeData'] ?? [];
        unset($data['serviceTypeData']);
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $data = $this->form->getState();
        
        if (isset($data['serviceTypeData'])) {
            foreach ($data['serviceTypeData'] as $serviceData) {
                $this->record->serviceTypeData()->create([
                    'field_id' => $serviceData['field_id'],
                    'value' => $serviceData['value'],
                ]);
            }
        }
    }
}
