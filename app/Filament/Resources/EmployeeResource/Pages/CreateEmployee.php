<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;
use Spatie\Permission\Models\Role;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    // Handle data sebelum disimpan
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove user_role dari data employee karena bukan field di table employees
        $userRole = $data['user_role'] ?? null;
        unset($data['user_role']);

        // Store role untuk digunakan di afterCreate
        $this->userRole = $userRole;

        return $data;
    }

    // Handle setelah employee dibuat
    protected function afterCreate(): void
    {
        // Assign role ke user jika ada
        if (isset($this->userRole) && $this->userRole) {
            $employee = $this->getRecord();
            $user = $employee->user;

            if ($user && Role::where('name', $this->userRole)->exists()) {
                // Remove existing roles (kecuali super_admin)
                $user->roles()->where('name', '!=', 'super_admin')->detach();
                
                // Assign new role
                $user->assignRole($this->userRole);

                // Notification
                $this->getCreatedNotification()
                    ?->title('Employee created and role assigned successfully!')
                    ?->send();
            }
        }
    }

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->success()
            ->title('Employee created')
            ->body('Employee has been created and role has been assigned successfully.');
    }
}