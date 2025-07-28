<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\SoftDeleteBoolean;

class Division extends Model
{
    use HasFactory, SoftDeleteBoolean;

    protected $fillable = ['division_name', 'required_workhours', 'is_deleted'];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'division_id');
    }

    public function accesses()
    {
        return $this->belongsToMany(Access::class, 'division_accesses', 'division_id', 'access_id')->withTimestamps();
    }
}
