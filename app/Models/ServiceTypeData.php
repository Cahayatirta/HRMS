<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\SoftDeleteBoolean;

class ServiceTypeData extends Model
{
    use HasFactory, SoftDeleteBoolean;

    protected $fillable = ['field_id', 'service_id', 'value', 'is_deleted'];

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

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function field()
    {
        return $this->belongsTo(ServiceTypeField::class, 'field_id');
    }
    
    public function softDelete(Request $request)
    {
        $this->is_deleted = true;
        $this->save();
    }
}
