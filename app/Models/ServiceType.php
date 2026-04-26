<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\SoftDeleteBoolean;

class ServiceType extends Model
{
    use HasFactory, SoftDeleteBoolean;

    protected $fillable = ['name', 'description', 'status','is_deleted'];

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

    public function fields()
    {
        return $this->hasMany(ServiceTypeField::class, 'service_type_id');
    }
    
    public function softDelete(Request $request)
    {
        $this->is_deleted = true;
        $this->save();
    }
}
