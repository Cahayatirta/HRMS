<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\SoftDeleteBoolean;
use Illuminate\Http\Request;

class WorkhourPlan extends Model
{
    use HasFactory, SoftDeleteBoolean;

    protected $fillable = [
        'employee_id', 'plan_date', 'planned_starttime', 'planned_endtime',
        'work_location', 'is_deleted'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function softDelete(Request $request)
    {
        $this->is_deleted = true;
        $this->save();
        // $ipAddress = $request->ip();
        // dd($ipAddress);
    }
}
