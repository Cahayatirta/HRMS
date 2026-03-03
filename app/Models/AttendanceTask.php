<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceTask extends Model
{
    use HasFactory;

    protected $table = 'attendance_tasks';
    
    protected $fillable = [
        'task_id',
        'attendance_id'
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    /**
     * Prepare a date for array / JSON serialization.
     * This ensures all datetime fields use ISO 8601 format with microseconds
     */
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return \Illuminate\Support\Carbon::instance($date)->format('Y-m-d H:i:s');
    }

    public $timestamps = false;

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id', 'task_id');
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class, 'attendance_id', 'attendance_id');
    }
}