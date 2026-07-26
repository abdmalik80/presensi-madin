<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $fillable = ['hari', 'jam_mulai', 'jam_selesai', 'majelis_id', 'kitab_id', 'ustadz_id', 'keterangan'];

    public function majelis()       { return $this->belongsTo(Majelis::class); }
    public function kitab()         { return $this->belongsTo(Kitab::class); }
    public function ustadz()        { return $this->belongsTo(Ustadz::class); }
    public function presensis()     { return $this->hasMany(Presensi::class); }
    public function dokumentasis()  { return $this->hasMany(Dokumentasi::class); }

    public function getHariAttribute($value)
    {
        return ucfirst(strtolower($value));
    }
}
