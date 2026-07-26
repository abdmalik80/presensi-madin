<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPresensi extends Model
{
    protected $fillable = ['presensi_id', 'santri_id', 'status_kehadiran', 'keterangan'];

    public function presensi() { return $this->belongsTo(Presensi::class); }
    public function santri() { return $this->belongsTo(Santri::class); }
}
