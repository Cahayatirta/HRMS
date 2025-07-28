<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\SoftDeleteBoolean;

class Client extends Model
{
    use HasFactory, SoftDeleteBoolean;

    protected $fillable = ['name', 'phone_number', 'email', 'address', 'is_deleted'];

    public function services()
    {
        return $this->hasMany(Service::class, 'client_id');
    }

    public function meetings()
    {
        return $this->belongsToMany(Meeting::class, 'meeting_clients', 'client_id', 'meeting_id')->withTimestamps();
    }

    public function softCascades()
    {
        return ['services'];
    }
}
