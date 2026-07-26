<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'username', 'email', 'password', 'role',
        'jenis_kelamin', 'foto_profil', 'no_hp', 'alamat', 'status_aktif', 'notif_read_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status_aktif' => 'boolean',
        ];
    }

    public function santri()
    {
        return $this->hasOne(Santri::class);
    }

    public function ustadz()
    {
        return $this->hasOne(Ustadz::class);
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isUstadz(): bool { return $this->role === 'ustadz'; }
    public function isSantri(): bool { return $this->role === 'santri'; }
}
