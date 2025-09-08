<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\SoftDeleteBoolean;

class Access extends Model
{
    use HasFactory;

    protected $fillable = ['access_name', 'access_description',
        'is_deleted'];
    protected $casts = [
        'is_deleted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function divisions()
    {
        return $this->belongsToMany(Division::class, 'division_accesses', 'access_id', 'division_id')->withTimestamps();
    }

    public function softDelete(Request $request)
    {
        $this->is_deleted = true;
        $this->save();
    }

    // Scope untuk record aktif
    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }

    // Scope untuk record yang dihapus
    public function scopeDeleted($query)
    {
        return $query->where('is_deleted', true);
    }
}
