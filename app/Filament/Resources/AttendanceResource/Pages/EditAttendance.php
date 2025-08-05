<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttendance extends EditRecord
{
    protected static string $resource = AttendanceResource::class;

    public int|null $employee_id = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->action(function ($record) {
                    $record->softDelete(request());
                })
                ->requiresConfirmation(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ambil data task yang sudah terhubung ke attendance
        $attendance = $this->record;

        // Ambil tasks dengan relasi pivot (attendance_tasks)
        $existingTasks = $attendance->tasks()->withPivot('attendance_id')->get();

        // Debug hasil
        // dd($existingTasks);

        $completedTasks = $existingTasks->map(function ($task) {
            return [
                'attendance_task_id' => $task->id, // ID task
                'task_id' => $task->id, // bisa pakai yang sama
                'task_info' => $task->task_description ?? 'No description', // langsung dari task
            ];
        })->toArray();

        $data['completedTasks'] = $completedTasks;

        return $data;
    }


    protected function afterSave(): void
    {
        $attendance = $this->record;
        $completedTasks = $this->data['completedTasks'] ?? [];

        // Delete existing attendance tasks
        \App\Models\AttendanceTask::where('attendance_id', $attendance->id)->delete();

        // dd($attendance);
        // Save new attendance tasks
        foreach ($completedTasks as $taskData) {
            if (isset($taskData['task_id']) && $taskData['task_id']) {
                \App\Models\AttendanceTask::create([
                    'attendance_id' => $attendance->id,
                    'task_id' => $taskData['task_id'],
                ]);
            }
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove completedTasks from main data to prevent it from being saved to attendance table
        unset($data['completedTasks']);
        
        // dd($data);

        return $data;
    }
}
