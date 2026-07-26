$files = @(
    'resources\views\admin\ustadz\index.blade.php',
    'resources\views\admin\santri\index.blade.php',
    'resources\views\admin\pengguna\index.blade.php',
    'resources\views\admin\kitab\index.blade.php',
    'resources\views\admin\jadwal\index.blade.php',
    'resources\views\admin\izin\index.blade.php',
    'resources\views\admin\dokumentasi\index.blade.php'
)

foreach ($f in $files) {
    if (Test-Path $f) {
        $content = Get-Content $f -Raw
        
        # Remove onchange
        $content = $content -replace ' onchange="this\.form\.submit\(\)"', ''
        
        # Make sure there is a Terapkan button
        if ($content -notmatch '>Terapkan</button>') {
            $content = $content -replace '<button type="button" @click="window\.location\.href=''([^'']+)''" class="w-full ([^"]+)">Reset Filter</button>', '<div class="flex gap-2"><button type="button" @click="window.location.href=''''" class="flex-1 ">Reset Filter</button><button type="submit" class="flex-1 py-2.5 text-xs font-semibold text-white bg-[#187a41] hover:bg-green-700 rounded-xl transition-colors shadow-sm">Terapkan</button></div>'
        }
        
        Set-Content -Path $f -Value $content
        Write-Host "Processed $f"
    }
}
