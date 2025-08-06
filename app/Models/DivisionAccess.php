<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DivisionAccess extends Model
{
    use HasFactory;

    protected $table = 'division_accesses';
    public $incrementing = false;
    protected $primaryKey = null;
    
    public $timestamps = true;

    protected $fillable = [
        'access_id',
        'division_id',
    ];

    public function access(): BelongsTo
    {
        return $this->belongsTo(Access::class, 'access_id', 'access_id');
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id', 'division_id');
    }
}
