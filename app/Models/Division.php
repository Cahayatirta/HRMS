<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\SoftDeleteBoolean;

class Division extends Model
{
    use HasFactory, SoftDeleteBoolean;

    protected $fillable = ['division_name', 'required_workhours', 'is_deleted'];

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

    public function employees()
    {
        return $this->hasMany(Employee::class, 'division_id');
    }

    public function accesses()
    {
        return $this->belongsToMany(Access::class, 'division_accesses', 'division_id', 'access_id')->withTimestamps();
    }

    // Untuk kompatibilitas dengan Shield, kita akan buat method helper
    public function getPermissionsAttribute()
    {
        // Konversi dari sistem Access lama ke permission Spatie
        return $this->accesses->map(function($access) {
            return $access->access_name;
        });
    }

    public function softDelete(Request $request)
    {
        $this->is_deleted = true;
        $this->save();
    }
}
