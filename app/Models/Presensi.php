<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $fillable = ['tanggal', 'jadwal_id', 'dicatat_oleh', 'status', 'jam_buka', 'jam_tutup'];

    public function jadwal() { return $this->belongsTo(Jadwal::class); }
    public function pencatat() { return $this->belongsTo(Ustadz::class, 'dicatat_oleh'); }
    public function details() { return $this->hasMany(DetailPresensi::class); }
}
