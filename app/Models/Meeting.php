<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\SoftDeleteBoolean;

class Meeting extends Model
{
    use HasFactory, SoftDeleteBoolean;

    protected $fillable = ['meeting_title', 'meeting_note', 'date', 'start_time', 'end_time', 'is_deleted'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'meeting_users', 'meeting_id', 'user_id')->withTimestamps();
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'meeting_clients', 'meeting_id', 'client_id')->withTimestamps();
    }
}
