<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use App\Models\Kitab;
use App\Models\Majelis;
use App\Models\User;
use App\Models\Ustadz;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class MadinDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Setup Majelis
        $majelisData = [
            ['nama_majelis' => 'Majlis 1', 'tempat' => 'Lantai 1', 'koordinator' => 'Fatimatuz Zahro', 'keterangan' => 'Angkatan 22 Keatas'],
            ['nama_majelis' => 'Majlis 2', 'tempat' => 'Lantai 1', 'koordinator' => 'Zulfa Nailal Kamila', 'keterangan' => 'Angkatan 23'],
            ['nama_majelis' => 'Majlis 3', 'tempat' => 'Lantai 2', 'koordinator' => 'Dina Amelia', 'keterangan' => 'Angkatan 24'],
            ['nama_majelis' => 'Majlis 4', 'tempat' => 'Lantai 1', 'koordinator' => 'Shofa Nala Salamah', 'keterangan' => 'Angkatan 25'],
        ];

        $majelisMap = [];
        foreach ($majelisData as $m) {
            $majelisMap[$m['nama_majelis']] = Majelis::firstOrCreate(['nama_majelis' => $m['nama_majelis']], $m)->id;
        }

        // 2. Setup Ustadz
        $ustadzNames = [
            "Ustadz Husni Mahbub",
            "Ibu Nyai Nurul Azizah",
            "Ustadz Irham & Ustadzah Idza",
            "Abah KH. Mas'udan Asy'ari",
            "Ustadz Munir",
            "Ustadz M. Fajrun & Ustadz Dino",
            "Ustadz Sawaun",
            "Ustadzah Makhil & Zulfa",
            "Ustadz Asep Sunarko",
            "Ustadz M. Fajrun",
            "Ustadz Anwari",
        ];

        $ustadzMap = [];
        foreach ($ustadzNames as $name) {
            $username = Str::slug($name);
            $user = User::firstOrCreate(
                ['username' => $username],
                [
                    'name' => $name,
                    'email' => $username . '@example.com',
                    'password' => Hash::make('password'),
                    'role' => 'ustadz',
                ]
            );

            $ustadz = Ustadz::firstOrCreate(['user_id' => $user->id]);
            $ustadzMap[$name] = $ustadz->id;
        }

        // 3. Setup Kitab
        $kitabNames = [
            "Nurul Yaqin",
            "Fathul Qorib",
            "Tajwid / Ghorib",
            "Tafsir Jalalain",
            "Al 'Ilmu wal 'Amal",
            "Durrotun Nasihin",
            "Al-Jurumiyyah & Tajwid",
            "At-Tibyan",
            "Yanbu'a Jilid 7",
            "Fathul Mu'in",
            "Tijan Durori",
            "Risalatul M & Tafsir Jalalain",
            "Jauharotuttauhid",
            "Al-Jurumiyyah"
        ];

        $kitabMap = [];
        foreach ($kitabNames as $name) {
            $kitabMap[$name] = Kitab::firstOrCreate(['nama_kitab' => $name])->id;
        }

        // 4. Setup Jadwal
        $jadwalData = [
            // AHAD
            ['hari' => 'ahad', 'jam_mulai' => '20:00:00', 'jam_selesai' => '21:00:00', 'majelis' => 'Majlis 1', 'kitab' => 'Nurul Yaqin', 'ustadz' => "Ustadz Husni Mahbub"],
            ['hari' => 'ahad', 'jam_mulai' => '20:00:00', 'jam_selesai' => '21:00:00', 'majelis' => 'Majlis 2', 'kitab' => 'Fathul Qorib', 'ustadz' => "Ibu Nyai Nurul Azizah"],
            ['hari' => 'ahad', 'jam_mulai' => '20:00:00', 'jam_selesai' => '21:00:00', 'majelis' => 'Majlis 3', 'kitab' => 'Tajwid / Ghorib', 'ustadz' => "Ustadz Irham & Ustadzah Idza"],
            ['hari' => 'ahad', 'jam_mulai' => '20:00:00', 'jam_selesai' => '21:00:00', 'majelis' => 'Majlis 4', 'kitab' => 'Tafsir Jalalain', 'ustadz' => "Abah KH. Mas'udan Asy'ari"],

            // SENIN
            ['hari' => 'senin', 'jam_mulai' => '20:00:00', 'jam_selesai' => '21:00:00', 'majelis' => 'Majlis 1', 'kitab' => "Al 'Ilmu wal 'Amal", 'ustadz' => "Ustadz Munir"],
            ['hari' => 'senin', 'jam_mulai' => '20:00:00', 'jam_selesai' => '21:00:00', 'majelis' => 'Majlis 2', 'kitab' => 'Durrotun Nasihin', 'ustadz' => "Abah KH. Mas'udan Asy'ari"],
            ['hari' => 'senin', 'jam_mulai' => '20:00:00', 'jam_selesai' => '21:00:00', 'majelis' => 'Majlis 3', 'kitab' => 'Tafsir Jalalain', 'ustadz' => "Ibu Nyai Nurul Azizah"],
            ['hari' => 'senin', 'jam_mulai' => '20:00:00', 'jam_selesai' => '21:00:00', 'majelis' => 'Majlis 4', 'kitab' => 'Al-Jurumiyyah & Tajwid', 'ustadz' => "Ustadz M. Fajrun & Ustadz Dino"],

            // SELASA
            ['hari' => 'selasa', 'jam_mulai' => '20:00:00', 'jam_selesai' => '21:00:00', 'majelis' => 'Majlis 1', 'kitab' => 'Durrotun Nasihin', 'ustadz' => "Abah KH. Mas'udan Asy'ari"],
            ['hari' => 'selasa', 'jam_mulai' => '20:00:00', 'jam_selesai' => '21:00:00', 'majelis' => 'Majlis 2', 'kitab' => 'At-Tibyan', 'ustadz' => "Ustadz Sawaun"],
            ['hari' => 'selasa', 'jam_mulai' => '20:00:00', 'jam_selesai' => '21:00:00', 'majelis' => 'Majlis 3', 'kitab' => 'Fathul Qorib', 'ustadz' => "Ustadz Munir"],
            ['hari' => 'selasa', 'jam_mulai' => '20:00:00', 'jam_selesai' => '21:00:00', 'majelis' => 'Majlis 4', 'kitab' => "Yanbu'a Jilid 7", 'ustadz' => "Ustadzah Makhil & Zulfa"],

            // RABU
            ['hari' => 'rabu', 'jam_mulai' => '20:00:00', 'jam_selesai' => '21:00:00', 'majelis' => 'Majlis 1', 'kitab' => "Fathul Mu'in", 'ustadz' => "Ustadz Asep Sunarko"],
            ['hari' => 'rabu', 'jam_mulai' => '20:00:00', 'jam_selesai' => '21:00:00', 'majelis' => 'Majlis 2', 'kitab' => 'Tafsir Jalalain', 'ustadz' => "Ustadz M. Fajrun"],
            ['hari' => 'rabu', 'jam_mulai' => '20:00:00', 'jam_selesai' => '21:00:00', 'majelis' => 'Majlis 3', 'kitab' => 'Tijan Durori', 'ustadz' => "Ustadz Anwari"],
            ['hari' => 'rabu', 'jam_mulai' => '20:00:00', 'jam_selesai' => '21:00:00', 'majelis' => 'Majlis 4', 'kitab' => 'Durrotun Nasihin', 'ustadz' => "Ustadz Husni Mahbub"],

            // JUMAT
            ['hari' => 'jumat', 'jam_mulai' => '20:00:00', 'jam_selesai' => '21:00:00', 'majelis' => 'Majlis 1', 'kitab' => 'Risalatul M & Tafsir Jalalain', 'ustadz' => "Ibu Nyai Nurul Azizah"],
            ['hari' => 'jumat', 'jam_mulai' => '20:00:00', 'jam_selesai' => '21:00:00', 'majelis' => 'Majlis 2', 'kitab' => 'Jauharotuttauhid', 'ustadz' => "Ustadz Husni Mahbub"],
            ['hari' => 'jumat', 'jam_mulai' => '20:00:00', 'jam_selesai' => '21:00:00', 'majelis' => 'Majlis 3', 'kitab' => 'Al-Jurumiyyah', 'ustadz' => "Ustadz M. Fajrun"],
            ['hari' => 'jumat', 'jam_mulai' => '20:00:00', 'jam_selesai' => '21:00:00', 'majelis' => 'Majlis 4', 'kitab' => 'Fathul Qorib', 'ustadz' => "Ustadz Anwari"],
        ];

        foreach ($jadwalData as $jadwal) {
            Jadwal::firstOrCreate([
                'hari' => $jadwal['hari'],
                'majelis_id' => $majelisMap[$jadwal['majelis']],
                'kitab_id' => $kitabMap[$jadwal['kitab']],
            ], [
                'jam_mulai' => $jadwal['jam_mulai'],
                'jam_selesai' => $jadwal['jam_selesai'],
                'ustadz_id' => $ustadzMap[$jadwal['ustadz']],
            ]);
        }
    }
}
