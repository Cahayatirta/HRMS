<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Traits\SoftDeleteBoolean;

class Meeting extends Model
{
    use HasFactory, SoftDeleteBoolean;

    protected $fillable = ['meeting_title', 'meeting_note', 'start_time', 'end_time', 'is_deleted'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_deleted' => 'boolean',
    ];

    /**
     * Prepare a date for array / JSON serialization.
     * This ensures all datetime fields use ISO 8601 format with microseconds
     */
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return Carbon::instance($date)->format('Y-m-d H:i:s');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'meeting_users', 'meeting_id', 'user_id')->withTimestamps();
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'meeting_clients', 'meeting_id', 'client_id')->withTimestamps();
    }

    public function softDelete(Request $request)
    {
        $this->is_deleted = true;
        $this->save();
    }
}
