<?php

namespace App\Filament\Resources\AttendanceResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\AttendanceResource;
use App\Filament\Resources\AttendanceResource\Api\Requests\UpdateAttendanceRequest;
use App\Models\AttendanceTask;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = AttendanceResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    public function handler(UpdateAttendanceRequest $request)
    {
        $id = $request->route('id');
        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        // Extract completed tasks before filling main data
        $completedTasks = $request->input('completed_task_ids', []);
        $data = $request->except('completed_task_ids');

        $data['work_location'] = strtolower($data['work_location']);

        // Update main attendance data
        $model->fill($data);
        $model->save();

        // Delete existing attendance tasks
        AttendanceTask::where('attendance_id', $model->id)->delete();

        // Create new attendance tasks
        foreach ($completedTasks as $taskId) {
            if ($taskId) {
                AttendanceTask::create([
                    'attendance_id' => $model->id,
                    'task_id' => $taskId,
                ]);
            }
        }

        // Load relationships for response
        $model->load(['employee', 'attendanceTasks.task']);

        return static::sendSuccessResponse($model, "Successfully Updated Attendance");
    }
}