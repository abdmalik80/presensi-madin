<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kitab extends Model
{
    protected $fillable = ['nama_kitab', 'bidang_ilmu', 'pengarang'];

    public function jadwals() { return $this->hasMany(Jadwal::class); }
}
