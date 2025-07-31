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

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function serviceTypeData()
    {
        return $this->hasMany(ServiceTypeData::class, 'service_id');
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
