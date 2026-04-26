<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\SoftDeleteBoolean;

class Service extends Model
{
    use HasFactory, SoftDeleteBoolean;

    protected $fillable = [
        'client_id', 'service_type_id', 'status', 'price', 'start_time', 'expired_time', 'is_deleted'
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

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function serviceTypeData()
    {
        return $this->hasMany(ServiceTypeData::class, 'service_id');
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }

    public function softCascades()
    {
        return ['serviceTypeData'];
    }
    
    public function softDelete(Request $request)
    {
        $this->is_deleted = true;
        $this->save();
    }
}
