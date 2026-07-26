import re

path = 'resources/views/santri/jadwal/show.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace('{{ ->hari }}', '{{ $jadwal->hari }}')
content = content.replace('->jam_mulai', '$jadwal->jam_mulai')
content = content.replace('->jam_selesai', '$jadwal->jam_selesai')
content = content.replace('->kitab->nama_kitab', '$jadwal->kitab->nama_kitab')
content = content.replace('->ustadz->user->name', '$jadwal->ustadz->user->name')
content = content.replace('->keterangan', '$jadwal->keterangan')
content = content.replace("{{ ['Hadir'] }}", "{{ $statistik['Hadir'] }}")
content = content.replace("{{ ['Izin'] }}", "{{ $statistik['Izin'] }}")
content = content.replace("{{ ['Sakit'] }}", "{{ $statistik['Sakit'] }}")
content = content.replace("{{ ['Alpha'] }}", "{{ $statistik['Alpha'] }}")

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Restored missing variable symbols.")
