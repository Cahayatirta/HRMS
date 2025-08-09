<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\SoftDeleteBoolean;

class ClientData extends Model
{    
    use HasFactory, SoftDeleteBoolean;

    protected $fillable = ['account_type', 'account_credential', 'account_password'];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
