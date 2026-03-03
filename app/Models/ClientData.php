<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\SoftDeleteBoolean;

class ClientData extends Model
{    
    use HasFactory, SoftDeleteBoolean;

    protected $fillable = ['account_type', 'account_credential', 'account_password'];

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
}
