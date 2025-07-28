<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'start_time', 'end_time', 'work_location',
        'longitude', 'latitude', 'image_path', 'task_link', 'is_deleted'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'attendance_tasks', 'attendance_id', 'task_id')->withTimestamps();
    }
}
