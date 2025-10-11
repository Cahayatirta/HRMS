<?php

namespace App\Filament\Resources\DivisionResource\Pages;

use App\Filament\Resources\DivisionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDivision extends CreateRecord
{
    protected static string $resource = DivisionResource::class;

    // protected array $accessCategories = [
    //     'client_access',
    //     'service_access',
    //     'service_type_access',
    //     'division_access',
    //     'employee_access',
    //     'Workhour_plan_access',
    //     'attendance_access',
    //     'metting_access',
    //     'task_access',
    //     'user_access',
    //     'access_access',
    // ];

    // protected array $collectedAccessIds = [];

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     // Ambil semua access dari field kategori
    //     foreach ($this->accessCategories as $category) {
    //         $this->collectedAccessIds = array_merge(
    //             $this->collectedAccessIds,
    //             $data[$category] ?? []
    //         );

    //         // Hapus dari data agar tidak masuk ke kolom division
    //         unset($data[$category]);
    //     }

    //     return $data;
    // }

    protected function afterCreate(): void
    {
        // Simpan ke pivot table division_accesses
        // $this->record->accesses()->sync($this->collectedAccessIds);
    }
}
