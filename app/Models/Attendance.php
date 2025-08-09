<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Adrianorosa\GeoLocation\GeoLocation;
use Illuminate\Database\Eloquent\Relations\HasMany;


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

    public function attendanceTasks(): HasMany
    {
        return $this->hasMany(AttendanceTask::class, 'attendance_id', 'attendance_id');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'attendance_tasks', 'attendance_id', 'task_id')->withTimestamps();
    }

    public function softDelete(Request $request)
    {
        $this->is_deleted = true;
        $this->save();
    }
 
    public function getCreatedDateAttribute()
    {
        return $this->created_at->toDateString(); // hasilnya format YYYY-MM-DD
    }

    public function GetLocationAttribute(Request $request): array
    {
        try {
            // $ipAddress = $request->ip() ?? '182.253.51.18';
            $ipAddress = '182.253.51.18';
            $geoDetails = Geolocation::lookup($ipAddress);

            return [
                'latitude' => $geoDetails->getLatitude(),
                'longitude' => $geoDetails->getLongitude(),
                'ip_address' => $ipAddress,
            ];
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Error getting location: ' . $e->getMessage());
            
            return [
                'latitude' => null,
                'longitude' => null,
                'ip_address' => $request->ip(),
            ];
        }
    }

    public function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000; // Radius bumi dalam meter
        
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLatRad = deg2rad($lat2 - $lat1);
        $deltaLonRad = deg2rad($lon2 - $lon1);
        
        $a = sin($deltaLatRad / 2) * sin($deltaLatRad / 2) +
            cos($lat1Rad) * cos($lat2Rad) *
            sin($deltaLonRad / 2) * sin($deltaLonRad / 2);
            
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }
}
