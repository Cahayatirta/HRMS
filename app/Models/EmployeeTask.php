<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeTask extends Model
{
    protected $table = 'employee_tasks';
    
    protected $fillable = [
        'employee_id',
        'task_id',
        'is_deleted'
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

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
