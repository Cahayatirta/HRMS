<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use App\Models\ServiceTypeField;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['serviceTypeData'] = $this->record->serviceTypeData->map(function ($serviceData) {
            return [
                'id' => $serviceData->id,
                'field_id' => $serviceData->field_id,
                'value' => $serviceData->value,
            ];
        })->toArray();

        $existingFieldIds = collect($data['serviceTypeData'])->pluck('field_id')->toArray();
        $allFields = ServiceTypeField::where('service_type_id', $this->record->service_type_id)->get();
        
        foreach ($allFields as $field) {
            if (!in_array($field->id, $existingFieldIds)) {
                $data['serviceTypeData'][] = [
                    'id' => null,
                    'field_id' => $field->id,
                    'value' => '',
                ];
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $serviceTypeData = $data['serviceTypeData'] ?? [];
        unset($data['serviceTypeData']);
        
        return $data;
    }

    protected function afterSave(): void
    {
        $data = $this->form->getState();
        
        if (isset($data['serviceTypeData'])) {
            // Get all current field IDs for the service type
            $currentFieldIds = ServiceTypeField::where('service_type_id', $this->record->service_type_id)->pluck('id')->toArray();
            
            // Delete data that doesn't belong to current service type
            $this->record->serviceTypeData()->whereNotIn('field_id', $currentFieldIds)->delete();
            
            foreach ($data['serviceTypeData'] as $serviceData) {
                if (isset($serviceData['id']) && $serviceData['id']) {
                    // Update existing
                    $this->record->serviceTypeData()->where('id', $serviceData['id'])->update([
                        'field_id' => $serviceData['field_id'],
                        'value' => $serviceData['value'],
                    ]);
                } else {
                    // Create new
                    $this->record->serviceTypeData()->create([
                        'field_id' => $serviceData['field_id'],
                        'value' => $serviceData['value'],
                    ]);
                }
            }
        }
    }
}
