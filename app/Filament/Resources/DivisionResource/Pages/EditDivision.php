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
        'workhour_plan_access', // Fixed: consistent naming
        'attendance_access',
        'metting_access',
        'task_access',
        'user_access',
        'access_access',
    ];

    // Mapping untuk matching yang lebih akurat
    protected array $accessMapping = [
        'client_access' => ['client'],
        'service_access' => ['service'], // Exact match, tidak termasuk "service type"
        'service_type_access' => ['service type'],
        'division_access' => ['division'],
        'employee_access' => ['employee'],
        'workhour_plan_access' => ['workhour plan'],
        'attendance_access' => ['attendance'],
        'metting_access' => ['metting'],
        'task_access' => ['task'],
        'user_access' => ['user'],
        'access_access' => ['access'],
    ];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ambil semua access yang terkait dengan division ini
        $accesses = $this->record->accesses()->get();

        // Inisialisasi data default
        foreach ($this->accessCategories as $category) {
            $data[$category] = [];
        }

        // Bagi access berdasarkan mapping yang lebih spesifik
        foreach ($accesses as $access) {
            $accessNameLower = strtolower($access->access_name);
            
            foreach ($this->accessMapping as $category => $keywords) {
                foreach ($keywords as $keyword) {
                    // Menggunakan strategi matching yang lebih spesifik
                    if ($this->isAccessMatch($accessNameLower, strtolower($keyword))) {
                        $data[$category][] = $access->id;
                        break 2; // Break both loops setelah match
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Fungsi untuk matching access yang lebih akurat
     */
    private function isAccessMatch(string $accessName, string $keyword): bool
    {
        // Untuk "service" - pastikan bukan "service type" 
        if ($keyword === 'service') {
            return str_contains($accessName, 'service') && !str_contains($accessName, 'service type');
        }
        
        // Untuk keyword lainnya, gunakan contains biasa
        return str_contains($accessName, $keyword);
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