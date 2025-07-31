<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\SoftDeleteBoolean;

class ServiceTypeField extends Model
{
    use HasFactory, SoftDeleteBoolean;

    protected $fillable = ['service_type_id', 'field_name', 'is_deleted'];

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }

    public function data()
    {
        return $this->hasMany(ServiceTypeData::class, 'field_id');
    }

    public function softDelete(Request $request)
    {
        $this->is_deleted = true;
        $this->save();
    }
}
