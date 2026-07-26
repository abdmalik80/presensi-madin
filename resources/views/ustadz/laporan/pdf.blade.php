<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi PDF</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; margin: 0; padding: 0; }
        .kop { width: 100%; border-bottom: 3px solid #000; padding-bottom: 5px; margin-bottom: 2px; }
        .kop td { vertical-align: middle; }
        .kop-logo { width: 80px; text-align: center; }
        .kop-logo img { width: 70px; }
        .kop-text { text-align: center; }
        .nama-pondok { font-size: 16px; font-weight: bold; text-transform: uppercase; font-family: 'Times New Roman', serif; }
        .nama-komplek { font-size: 20px; font-weight: bold; text-transform: uppercase; font-family: 'Times New Roman', serif; margin-top: 2px; }
        .alamat { font-size: 9px; margin-top: 4px; line-height: 1.3; }
        .garis-tipis { border-top: 1px solid #000; margin-bottom: 15px; }
        
        .judul-wrapper { text-align: center; margin-bottom: 15px; }
        .judul-wrapper h2 { font-size: 13px; text-transform: uppercase; text-decoration: underline; margin: 0 0 5px 0; }
        .sub-judul { font-size: 10px; color: #333; }
        
        .meta-table { width: auto; font-size: 10px; margin-bottom: 15px; }
        .meta-table td { padding: 2px 5px 2px 0; font-weight: bold; }
        
        .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .summary-table th { background: #1a3a2a; color: #fff; padding: 6px; font-size: 9px; border: 1px solid #000; }
        .summary-table td { padding: 6px; font-size: 12px; font-weight: bold; text-align: center; border: 1px solid #000; }
        
        .data-table { width: 100%; border-collapse: collapse; font-size: 9px; }
        .data-table th { background: #1a3a2a; color: #fff; padding: 5px; border: 1px solid #000; text-transform: uppercase; }
        .data-table td { padding: 5px; border: 1px solid #000; text-align: center; }
        .data-table td.left { text-align: left; }
        
        .ttd-table { width: 100%; margin-top: 30px; font-size: 11px; }
        .ttd-table td { width: 50%; text-align: center; vertical-align: top; }
        .ttd-jarak { height: 60px; }
        
        .page-footer { position: fixed; bottom: -20px; left: 0; right: 0; font-size: 8px; color: #666; border-top: 1px solid #ccc; padding-top: 3px; display: table; width: 100%; }
        .page-footer td { width: 50%; }
        .page-footer td.right { text-align: right; }
    </style>
</head>
<body>

    <table class="kop">
        <tr>
            <td class="kop-logo">
                {{-- Gunakan public_path agar dompdf bisa render gambar lokal --}}
                <img src="{{ public_path('images/logo-nq.png') }}" alt="Logo">
            </td>
            <td class="kop-text">
                <div class="nama-pondok">PPTQ AL-ASY'ARIYYAH</div>
                <div class="nama-komplek">KOMPLEK NAWWIR QULUUBANAA PUTRA</div>
                <div class="alamat">
                    Alamat: Dsn. Kemiri (Belakang Gedung Perpustakaan, UNISIQ) Ds. Sukorejo,<br>
                    Kec. Mojotengah, Kab. Wonosobo 56351, &nbsp; e-mail: <span style="color: blue;"><a href="mailto:[pp.nawwirquluubanaa@gmail.com]">[pp.nawwirquluubanaa@gmail.com]</a></span>
                </div>
            </td>
        </tr>
    </table>
    <div class="garis-tipis"></div>

    <div class="judul-wrapper">
        <h2>Laporan Rekapitulasi Presensi Santri</h2>
        <div class="sub-judul">
            Madrasah Diniyah Nawwir Quluubanaa
            @if($selectedMajelis ?? null)
                &nbsp;|&nbsp; Majelis: <strong>{{ $selectedMajelis->nama_majelis }}</strong>
            @endif
            @if($request->dari || $request->sampai)
                <br>Periode:
                <strong>{{ $request->dari ? \Carbon\Carbon::parse($request->dari)->translatedFormat('d F Y') : '—' }}</strong>
                s/d
                <strong>{{ $request->sampai ? \Carbon\Carbon::parse($request->sampai)->translatedFormat('d F Y') : '—' }}</strong>
            @endif
        </div>
    </div>

    <table class="meta-table">
        <tr>
            <td>Dicetak oleh</td>
            <td>:</td>
            <td>{{ auth()->user()->name }}</td>
        </tr>
        <tr>
            <td>Tanggal Cetak</td>
            <td>:</td>
            <td>{{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
        </tr>
        <tr>
            <td>Jumlah Santri</td>
            <td>:</td>
            <td>{{ $laporan->count() }} santri</td>
        </tr>
    </table>

    @if($summary)
    @php
        $totalSemua = $summary['hadir'] + $summary['izin'] + $summary['sakit'] + $summary['alpa'];
        $pctTotal   = $totalSemua > 0 ? round($summary['hadir'] / $totalSemua * 100, 1) : 0;
    @endphp
    <table class="summary-table">
        <thead>
            <tr>
                <th>Total Hadir</th>
                <th>Total Izin</th>
                <th>Total Sakit</th>
                <th>Total Alpha</th>
                <th>Rata-rata Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="color: #16a34a;">{{ $summary['hadir'] }}</td>
                <td style="color: #2563eb;">{{ $summary['izin'] }}</td>
                <td style="color: #d97706;">{{ $summary['sakit'] }}</td>
                <td style="color: #dc2626;">{{ $summary['alpa'] }}</td>
                <td>{{ $pctTotal }}%</td>
            </tr>
        </tbody>
    </table>
    @endif

    @if($laporan->isNotEmpty())
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:26px;">No</th>
                <th class="left">Nama Santri</th>
                <th class="left">NIS</th>
                <th class="left">Majelis</th>
                <th style="width:38px;">Hadir</th>
                <th style="width:32px;">Izin</th>
                <th style="width:32px;">Sakit</th>
                <th style="width:32px;">Alpha</th>
                <th style="width:36px;">Total</th>
                <th style="width:48px;">% Hadir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan as $i => $row)
            @php $pct = $row['total'] > 0 ? round(($row['hadir'] / $row['total']) * 100) : 0; @endphp
            <tr>
                <td style="color:#6b7280;">{{ $i + 1 }}</td>
                <td class="left" style="font-weight:bold;">{{ $row['nama'] }}</td>
                <td class="left" style="color:#555;">{{ $row['nis'] }}</td>
                <td class="left" style="color:#444;">{{ $row['majelis'] }}</td>
                <td style="color:#16a34a;font-weight:bold;">{{ $row['hadir'] }}</td>
                <td style="color:#2563eb;">{{ $row['izin'] }}</td>
                <td style="color:#d97706;">{{ $row['sakit'] }}</td>
                <td style="color:#dc2626;">{{ $row['alpa'] }}</td>
                <td>{{ $row['total'] }}</td>
                <td style="font-weight:bold; color: {{ $pct >= 75 ? '#16a34a' : ($pct >= 50 ? '#b45309' : '#dc2626') }}">
                    {{ $pct }}%
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="text-align:center;color:#9ca3af;padding:36px 0;font-style:italic;">
        Tidak ada data untuk ditampilkan.
    </p>
    @endif

    <table class="ttd-table">
        <tr>
            <td>
                <div>&nbsp;</div>
                <div>Pengurus / Lurah Pondok,</div>
                <div class="ttd-jarak"></div>
                <div>( _________________________ )</div>
            </td>
            <td>
                <div>Wonosobo, {{ now()->translatedFormat('d F Y') }}</div>
                <div>Pengasuh,</div>
                <div class="ttd-jarak"></div>
                <div>( _________________________ )</div>
            </td>
        </tr>
    </table>

    <table class="page-footer">
        <tr>
            <td>PPTQ Al-Asy'ariyyah — Komplek Nawwir Quluubana Putra</td>
            <td class="right">Dicetak: {{ now()->format('d/m/Y H:i') }} WIB</td>
        </tr>
    </table>

</body>
</html>
