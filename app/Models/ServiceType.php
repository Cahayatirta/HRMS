<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\SoftDeleteBoolean;

class ServiceType extends Model
{
    use HasFactory, SoftDeleteBoolean;

    protected $fillable = ['name', 'description', 'status','is_deleted'];

    public function fields()
    {
        return $this->hasMany(ServiceTypeField::class, 'service_type_id');
    }
}
