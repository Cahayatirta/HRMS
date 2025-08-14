<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\User;
use Spatie\Permission\Models\Role;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // Load existing role ke form
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $employee = $this->getRecord();
        $user = $employee->user;

        // Get current role (exclude super_admin)
        if ($user) {
            $currentRole = $user->roles()->where('name', '!=', 'super_admin')->first();
            if ($currentRole) {
                $data['user_role'] = $currentRole->name;
            }
        }

        return $data;
    }

    // Handle data sebelum disimpan
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove user_role dari data employee
        $userRole = $data['user_role'] ?? null;
        unset($data['user_role']);

        // Store role untuk digunakan di afterSave
        $this->userRole = $userRole;

        return $data;
    }

    // Handle setelah employee diupdate
    protected function afterSave(): void
    {
        // Update role user jika ada perubahan
        if (isset($this->userRole)) {
            $employee = $this->getRecord();
            $user = $employee->user;

            if ($user) {
                // Remove existing roles (kecuali super_admin)
                $user->roles()->where('name', '!=', 'super_admin')->detach();
                
                // Assign new role jika ada
                if ($this->userRole && Role::where('name', $this->userRole)->exists()) {
                    $user->assignRole($this->userRole);
                }

                // Notification
                $this->getSavedNotification()
                    ?->title('Employee updated and role assigned successfully!')
                    ?->send();
            }
        }
    }

    protected function getSavedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->success()
            ->title('Employee updated')
            ->body('Employee has been updated and role has been assigned successfully.');
    }
}