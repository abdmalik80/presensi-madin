<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Majelis extends Model
{
    protected $fillable = ['nama_majelis', 'tempat', 'koordinator', 'keterangan'];

    public function santris() { return $this->hasMany(Santri::class)->orderBy('id', 'asc'); }
    public function jadwals() { return $this->hasMany(Jadwal::class); }
    public function dokumentasis() { return $this->hasMany(Dokumentasi::class); }
}
