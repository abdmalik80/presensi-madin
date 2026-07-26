<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotifState extends Model
{
    protected $fillable = ['user_id', 'notif_id', 'is_read', 'is_hidden'];
}
