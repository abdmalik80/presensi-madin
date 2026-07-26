<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ustadz extends Model
{
    protected $fillable = ['user_id', 'mode_presensi'];

    public function user() { return $this->belongsTo(User::class); }
    public function jadwals() { return $this->hasMany(Jadwal::class); }
    public function presensis() { return $this->hasMany(Presensi::class, 'dicatat_oleh'); }
    public function dokumentasis() { return $this->hasMany(Dokumentasi::class, 'pengampu_id'); }
}
