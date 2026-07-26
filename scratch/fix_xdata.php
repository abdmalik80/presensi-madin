<?php

// Fix Ustadz
$file = 'd:\PRESENSI MADIN\resources\views\admin\ustadz\index.blade.php';
$content = file_get_contents($file);

$oldXData = 'x-data="{ showFilterModal: false, filterStatus: \'{{ request(\'status_aktif\', \'\') }}\' }"';
$newXData = <<<'EOD'
x-data="{ 
        showFilterModal: false, 
        filterStatus: '{{ request('status_aktif', '') }}',
        selectedIds: [],
        selectAllPages: false,
        isPageAllChecked: false,
        allIds: {{ json_encode($ustadzs->pluck('id')) }},
        pageIds: {{ json_encode($ustadzs->pluck('id')) }},
        
        togglePageAll(checked) {
            this.isPageAllChecked = checked;
            if (checked) {
                this.pageIds.forEach(id => {
                    if (!this.selectedIds.includes(id)) this.selectedIds.push(id);
                });
            } else {
                this.selectedIds = this.selectedIds.filter(id => !this.pageIds.includes(id));
                this.selectAllPages = false;
            }
        },
        selectEveryPage() {
            this.selectedIds = [...this.allIds];
            this.selectAllPages = true;
            this.isPageAllChecked = true;
        },
        clearAll() {
            this.selectedIds = [];
            this.selectAllPages = false;
            this.isPageAllChecked = false;
        }
    }"
EOD;
if (strpos($content, 'selectedIds: []') === false) {
    $content = str_replace($oldXData, $newXData, $content);
    file_put_contents($file, $content);
    echo "Fixed Ustadz x-data.\n";
}

// Fix Pengguna
$file2 = 'd:\PRESENSI MADIN\resources\views\admin\pengguna\index.blade.php';
$content2 = file_get_contents($file2);

$oldXData2 = 'x-data="{ showFilterModal: false }"';
$newXData2 = <<<'EOD'
x-data="{ 
        showFilterModal: false,
        selectedIds: [],
        selectAllPages: false,
        isPageAllChecked: false,
        allIds: {{ json_encode($pengguna->pluck('id')) }},
        pageIds: {{ json_encode($pengguna->pluck('id')) }},
        
        togglePageAll(checked) {
            this.isPageAllChecked = checked;
            if (checked) {
                this.pageIds.forEach(id => {
                    if (!this.selectedIds.includes(id)) this.selectedIds.push(id);
                });
            } else {
                this.selectedIds = this.selectedIds.filter(id => !this.pageIds.includes(id));
                this.selectAllPages = false;
            }
        },
        selectEveryPage() {
            this.selectedIds = [...this.allIds];
            this.selectAllPages = true;
            this.isPageAllChecked = true;
        },
        clearAll() {
            this.selectedIds = [];
            this.selectAllPages = false;
            this.isPageAllChecked = false;
        }
    }"
EOD;

if (strpos($content2, 'selectedIds: []') === false) {
    $content2 = str_replace($oldXData2, $newXData2, $content2);
    file_put_contents($file2, $content2);
    echo "Fixed Pengguna x-data.\n";
}

echo "Done fixing.";
