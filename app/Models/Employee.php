<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\SoftDeleteBoolean;

class Employee extends Model
{
    use HasFactory, SoftDeleteBoolean;

    protected $fillable = [
        'user_id', 'division_id', 'full_name', 'gender', 'birth_date', 'phone_number', 'address', 'image_path', 'status', 'is_deleted'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }

    public function softCascades()
    {
        return ['attendances'];
    }
}
