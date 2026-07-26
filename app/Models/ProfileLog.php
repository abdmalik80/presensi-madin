<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileLog extends Model
{
    protected $fillable = ['user_id', 'keterangan', 'is_read'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
