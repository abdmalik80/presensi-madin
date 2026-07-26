<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    protected $fillable = [
        'user_id', 'nis', 'tempat_lahir', 'tanggal_lahir',
        'nama_wali', 'no_hp_wali', 'angkatan', 'majelis_id',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function majelis() { return $this->belongsTo(Majelis::class); }
    public function detailPresensis() { return $this->hasMany(DetailPresensi::class); }
    public function izins() { return $this->hasMany(Izin::class); }
}
