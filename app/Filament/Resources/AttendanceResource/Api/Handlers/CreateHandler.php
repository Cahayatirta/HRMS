<?php
namespace App\Filament\Resources\AttendanceResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\AttendanceResource;
use App\Filament\Resources\AttendanceResource\Api\Requests\CreateAttendanceRequest;
use App\Models\AttendanceTask;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = AttendanceResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    public function handler(CreateAttendanceRequest $request)
    {
        $model = new (static::getModel());
        
        // Extract completed tasks before filling main data
        $completedTasks = $request->input('completed_task_ids', []);
        $data = $request->except('completed_task_ids');
        $data['work_location'] = strtolower($data['work_location']);

        // Fill and save main attendance data
        $model->fill($data);
        $model->save();

        // Create attendance tasks
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

        return static::sendSuccessResponse($model, "Successfully Create Attendance");
    }
}