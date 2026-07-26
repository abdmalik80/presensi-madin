{{-- Cetak laporan ustadz — sama format dengan admin --}}
@php
    $selectedMajelis = $majelis ?? null;
    // reuse summary var if missing
    if(!isset($summary)) $summary = null;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Presensi — Nawwir Quluubana</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; font-size: 12px; color: #1a1a1a; background: #fff; }
        .header { text-align: center; border-bottom: 2px solid #14532d; padding-bottom: 12px; margin-bottom: 16px; }
        .header h1 { font-size: 18px; font-weight: bold; color: #14532d; }
        .header p  { font-size: 11px; color: #555; margin-top: 2px; }
        .meta { display: flex; justify-content: space-between; margin-bottom: 16px; font-size: 11px; color: #555; }
        .summary { display: grid; grid-template-columns: repeat(4,1fr); gap: 10px; margin-bottom: 16px; }
        .summary-card { border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px 12px; text-align: center; }
        .summary-card .val { font-size: 20px; font-weight: bold; }
        .summary-card .lbl { font-size: 10px; color: #777; }
        .val-hadir { color: #16a34a; } .val-izin  { color: #2563eb; }
        .val-sakit { color: #ca8a04; } .val-alpa  { color: #dc2626; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        thead tr { background: #14532d; color: #fff; }
        th { padding: 7px 10px; text-align: left; font-weight: 600; font-size: 10px; text-transform: uppercase; }
        th.center, td.center { text-align: center; }
        td { padding: 6px 10px; border-bottom: 1px solid #f0f0f0; }
        tr:nth-child(even) td { background: #f9fafb; }
        .badge { display: inline-block; padding: 1px 6px; border-radius: 20px; font-size: 9px; font-weight: bold; }
        .badge-green { background:#dcfce7;color:#16a34a; } .badge-yellow { background:#fef9c3;color:#ca8a04; } .badge-red { background:#fee2e2;color:#dc2626; }
        .footer { margin-top: 20px; display: flex; justify-content: flex-end; font-size: 11px; color: #555; }
        .ttd { text-align: center; width: 200px; }
        .ttd .line { border-top: 1px solid #333; margin-top: 50px; padding-top: 4px; font-weight: bold; }
        @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } .no-print { display: none !important; } }
    </style>
</head>
<body>
    <div class="no-print" style="padding:12px; background:#f3f4f6; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; gap:10px;">
        <button onclick="window.print()" style="background:#14532d;color:#fff;padding:6px 16px;border:none;border-radius:6px;cursor:pointer;font-size:12px;">🖨️ Cetak Dokumen</button>
        <button onclick="window.close()" style="background:#e5e7eb;color:#374151;padding:6px 16px;border:none;border-radius:6px;cursor:pointer;font-size:12px;">✕ Tutup</button>
    </div>

    <div style="padding: 24px 32px;">
        <div class="header">
            <h1>LAPORAN REKAPITULASI PRESENSI</h1>
            <p>Madrasah Diniyah Nawwir Quluubana</p>
            @if($selectedMajelis)<p>Majelis: <strong>{{ $selectedMajelis->nama_majelis }}</strong></p>@endif
            @if($request->dari || $request->sampai)
            <p>Periode: {{ $request->dari ? \Carbon\Carbon::parse($request->dari)->translatedFormat('d M Y') : '—' }} s/d {{ $request->sampai ? \Carbon\Carbon::parse($request->sampai)->translatedFormat('d M Y') : '—' }}</p>
            @endif
        </div>
        <div class="meta">
            <span>Pengampu: {{ auth()->user()->name }}</span>
            <span>Dicetak: {{ now()->translatedFormat('d M Y, H:i') }} WIB</span>
        </div>
        @if($laporan->isNotEmpty())
        <table>
            <thead>
                <tr>
                    <th>No</th><th>Nama Santri</th><th>Majelis</th>
                    <th class="center">Hadir</th><th class="center">Izin</th><th class="center">Sakit</th>
                    <th class="center">Alpha</th><th class="center">Total</th><th class="center">% Hadir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laporan as $i => $row)
                @php $pct = $row['total'] > 0 ? round(($row['hadir']/$row['total'])*100) : 0; @endphp
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $row['nama'] }}</td>
                    <td>{{ $row['majelis'] }}</td>
                    <td class="center" style="color:#16a34a;font-weight:600;">{{ $row['hadir'] }}</td>
                    <td class="center" style="color:#2563eb;">{{ $row['izin'] }}</td>
                    <td class="center" style="color:#ca8a04;">{{ $row['sakit'] }}</td>
                    <td class="center" style="color:#dc2626;">{{ $row['alpa'] }}</td>
                    <td class="center">{{ $row['total'] }}</td>
                    <td class="center">
                        <span class="badge {{ $pct >= 75 ? 'badge-green' : ($pct >= 50 ? 'badge-yellow' : 'badge-red') }}">{{ $pct }}%</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p style="text-align:center;color:#9ca3af;padding:32px 0;">Tidak ada data untuk ditampilkan.</p>
        @endif
        <div class="footer">
            <div class="ttd">
                <p>Pengampu,</p>
                <div class="line">( {{ auth()->user()->name }} )</div>
            </div>
        </div>
    </div>
</body>
</html>
