<?php

$views = [
    'manajemen_aksi' => 'aksis',
    'manajemen_visit' => 'visits',
    'manajemen_canvasing' => 'canvasings',
    'manajemen_ho' => 'hos',
    'manajemen_ppd' => 'ppds'
];

foreach ($views as $view => $tableSuffix) {
    $path = "/var/www/html/monitoring-karyawan/resources/views/{$view}.blade.php";
    $content = file_get_contents($path);

    // 1. Add currentUserId
    $search1 = <<<JS
    let anggotaDatatable = null;

    const userDataStr = localStorage.getItem('user_data');
    if (userDataStr) {
        try {
            const ud = JSON.parse(userDataStr);
JS;
    $replace1 = <<<JS
    let anggotaDatatable = null;
    let currentUserId = null;

    const userDataStr = localStorage.getItem('user_data');
    if (userDataStr) {
        try {
            const ud = JSON.parse(userDataStr);
            currentUserId = ud.id;
JS;
    $content = str_replace($search1, $replace1, $content);

    // 2. Update initial DataTable URL
    $endpoint = str_replace('_', '-', $view);
    $search2 = "url: `\${apiUrl}/{$endpoint}`,";
    $replace2 = "url: isSuperAdminOrLeader ? `\${apiUrl}/{$endpoint}` : `\${apiUrl}/{$endpoint}/user/` + currentUserId,";
    $content = str_replace($search2, $replace2, $content);

    // 3. Update viewMemberData
    $search3 = <<<JS
window.viewMemberData = function(userId) {
    selectedUserId = userId;
    document.getElementById('anggota_list_container').style.display = 'none';
    document.getElementById('main_table_container').style.display = 'block';
    document.getElementById('btn_back_to_members').style.display = 'block';
    if (hasCrudAccess) {
        document.getElementById('btn_add_container').style.display = 'block';
    }
    $('#kt_table_{$tableSuffix}').DataTable().ajax.reload();
}
JS;
    $replace3 = <<<JS
window.viewMemberData = function(userId) {
    selectedUserId = userId;
    document.getElementById('anggota_list_container').style.display = 'none';
    document.getElementById('main_table_container').style.display = 'block';
    document.getElementById('btn_back_to_members').style.display = 'block';
    if (hasCrudAccess) {
        document.getElementById('btn_add_container').style.display = 'block';
    }
    $('#kt_table_{$tableSuffix}').DataTable().ajax.url(`\${apiUrl}/{$endpoint}/user/\${userId}`).load();
}
JS;
    $content = str_replace($search3, $replace3, $content);

    // 4. Update showMembersList
    $search4 = <<<JS
window.showMembersList = function() {
    selectedUserId = null;
    document.getElementById('main_table_container').style.display = 'none';
    document.getElementById('btn_back_to_members').style.display = 'none';
    document.getElementById('btn_add_container').style.display = 'none';
    document.getElementById('anggota_list_container').style.display = 'block';
    if (anggotaDatatable) {
        anggotaDatatable.ajax.reload();
    }
}
JS;
    $replace4 = <<<JS
window.showMembersList = function() {
    selectedUserId = null;
    document.getElementById('main_table_container').style.display = 'none';
    document.getElementById('btn_back_to_members').style.display = 'none';
    document.getElementById('btn_add_container').style.display = 'none';
    document.getElementById('anggota_list_container').style.display = 'block';
    $('#kt_table_{$tableSuffix}').DataTable().ajax.url(`\${apiUrl}/{$endpoint}`);
    if (anggotaDatatable) {
        anggotaDatatable.ajax.reload();
    }
}
JS;
    $content = str_replace($search4, $replace4, $content);

    // 5. Update exportData
    $search5 = <<<JS
    let url = `{{ url('/api/export/{$endpoint}') }}?`;
    if (selectedUserId) url += `user_id=\${selectedUserId}&`;
JS;
    $replace5 = <<<JS
    let url = `{{ url('/api/export/{$endpoint}') }}?`;
    if (isSuperAdminOrLeader) {
        if (selectedUserId) url += `user_id=\${selectedUserId}&`;
    } else {
        if (currentUserId) url += `user_id=\${currentUserId}&`;
    }
JS;
    $content = str_replace($search5, $replace5, $content);

    file_put_contents($path, $content);
    echo "Patched {$view}.blade.php\n";
}

?>
