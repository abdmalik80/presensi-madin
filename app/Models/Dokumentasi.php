<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dokumentasi extends Model
{
    protected $fillable = [
        'judul', 'deskripsi', 'tanggal',
        'majelis_id', 'jadwal_id',
        'upload_file', 'tipe_file', 'nama_file',
        'pengampu_id', 'kitab_id',
    ];

    public function majelis()   { return $this->belongsTo(Majelis::class); }
    public function jadwal()    { return $this->belongsTo(Jadwal::class); }
    public function kitab()     { return $this->belongsTo(Kitab::class); }
    public function pengampu()  { return $this->belongsTo(Ustadz::class, 'pengampu_id'); }

    /** Icon FontAwesome berdasarkan tipe file */
    public function getFileIconAttribute(): string
    {
        return match($this->tipe_file) {
            'pdf'   => 'fa-file-pdf text-red-500',
            'doc'   => 'fa-file-word text-blue-600',
            'xls'   => 'fa-file-excel text-green-600',
            'ppt'   => 'fa-file-powerpoint text-orange-500',
            'image' => 'fa-file-image text-purple-500',
            default => 'fa-file-alt text-gray-500',
        };
    }

    /** Label tipe file */
    public function getTipeLabelAttribute(): string
    {
        return match($this->tipe_file) {
            'pdf'   => 'PDF',
            'doc'   => 'Word',
            'xls'   => 'Excel',
            'ppt'   => 'PowerPoint',
            'image' => 'Gambar',
            default => 'Berkas',
        };
    }
}
