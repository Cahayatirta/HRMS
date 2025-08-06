<?php

namespace App\Filament\Resources\DivisionResource\Pages;

use App\Filament\Resources\DivisionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDivision extends EditRecord
{
    protected static string $resource = DivisionResource::class;

    protected array $accessCategories = [
        'client_access',
        'service_access',
        'service_type_access',
        'division_access',
        'employee_access',
        'Workhour_plan_access',
        'attendance_access',
        'metting_access',
        'task_access',
        'user_access',
        'access_access',
    ];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ambil semua access yang terkait dengan division ini (langsung objeknya)
        $accesses = $this->record->accesses()->get(); // Ini ambil relasi Access

        // Inisialisasi data default
        foreach ($this->accessCategories as $category) {
            $data[$category] = [];
        }
        
        // Bagi access berdasarkan prefix
        foreach ($accesses as $access) {
            foreach ($this->accessCategories as $category) {
                $prefix = str_replace('_access', '', $category); // Misal: 'client'
                
                
                if (str_starts_with($access->name, $prefix . '.')) {
                    $data[$category][] = $access->id;
                    break;
                }
            }
        }
        
        dd($data);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        foreach ($this->accessCategories as $category) {
            unset($data[$category]);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $allAccessIds = [];

        foreach ($this->accessCategories as $category) {
            $allAccessIds = array_merge($allAccessIds, $this->form->getState()[$category] ?? []);
        }

        $this->record->accesses()->sync($allAccessIds);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->action(function ($record) {
                    $record->softDelete(request());
                })
                ->requiresConfirmation(),
        ];
    }
}
