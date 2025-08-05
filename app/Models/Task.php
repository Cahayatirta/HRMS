<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\SoftDeleteBoolean;

class Task extends Model
{
    use HasFactory, SoftDeleteBoolean;

    protected $fillable = [
        'task_name', 'task_description', 'deadline', 'status',
        'parent_task_id', 'note', 'is_deleted'
    ];

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_tasks', 'task_id', 'employee_id')->withTimestamps();
    }

    public function attendances()
    {
        return $this->belongsToMany(Attendance::class, 'attendance_tasks', 'task_id', 'attendance_id')->withTimestamps();
    }

    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function softCascades()
    {
        return ['subtasks'];
    }

    public function getEmployeeListAttribute()
    {
        return $this->employee->map(function ($employee) {
            return ['name' => $employee->name];
        });
    }

    public function softDelete(Request $request)
    {
        $this->is_deleted = true;
        $this->save();
    }
}
