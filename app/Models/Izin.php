<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{
    protected $fillable = ['santri_id', 'jadwal_id', 'tanggal_izin', 'alasan', 'upload_bukti', 'status_validasi', 'catatan_pengampu'];

    public function santri() { return $this->belongsTo(Santri::class); }
    public function jadwal() { return $this->belongsTo(Jadwal::class); }
}
