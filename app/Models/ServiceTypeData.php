<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\SoftDeleteBoolean;

class ServiceTypeData extends Model
{
    use HasFactory, SoftDeleteBoolean;

    protected $fillable = ['field_id', 'service_id', 'value', 'is_deleted'];

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
