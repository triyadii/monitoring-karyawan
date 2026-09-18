<?php

$views = [
    'manajemen_aksi' => ['tableSuffix' => 'aksis', 'endpoint' => 'manajemen-aksi', 'type' => 'aksi'],
    'manajemen_visit' => ['tableSuffix' => 'visits', 'endpoint' => 'manajemen-visit', 'type' => 'visit'],
    'manajemen_canvasing' => ['tableSuffix' => 'canvasings', 'endpoint' => 'manajemen-canvasing', 'type' => 'canvasing'],
    'manajemen_ho' => ['tableSuffix' => 'hos', 'endpoint' => 'manajemen-ho', 'type' => 'ho'],
    'manajemen_ppd' => ['tableSuffix' => 'ppds', 'endpoint' => 'manajemen-ppd', 'type' => 'ppd']
];

foreach ($views as $view => $config) {
    $tableSuffix = $config['tableSuffix'];
    $endpoint = $config['endpoint'];
    $type = $config['type'];
    
    $path = "/var/www/html/monitoring-karyawan/resources/views/{$view}.blade.php";
    $content = file_get_contents($path);

    // 1. Replace anggota_list_container with member_stats_cards
    $searchHtml = '/<div id="anggota_list_container" style="display: none;">.*?<\/div>\s*<\/div>\s*<\/div>/s';
    $replaceHtml = <<<HTML
<div class="row g-5 g-xl-8 mb-5" id="member_stats_cards" style="display: none;">
            <!-- Cards will be injected here via JS -->
        </div>
HTML;
    $content = preg_replace($searchHtml, $replaceHtml, $content);

    // 2. Remove btn_back_to_members
    $searchBackBtn = '/<button type="button" class="btn btn-sm btn-light" id="btn_back_to_members".*?<\/button>/s';
    $content = preg_replace($searchBackBtn, '', $content);

    // 3. Replace JS block for isSuperAdminOrLeader
    $searchJs = '/if \(isSuperAdminOrLeader\) \{\s*document\.getElementById\(\'main_table_container\'\)\.style\.display = \'none\';\s*document\.getElementById\(\'anggota_list_container\'\)\.style\.display = \'block\';\s*anggotaDatatable = \$\(\'#kt_table_anggota\'\)\.DataTable\(\{\s*ajax: \{\s*url: `\$\{apiUrl\}\/member-stats\/' . $type . '`,\s*type: \'GET\',\s*headers: \{ \'Authorization\': `Bearer \$\{token\}` \},\s*dataSrc: \'\'\s*\},.*?\s*\}\);\s*\} else \{/s';
    
    $replaceJs = <<<JS
if (isSuperAdminOrLeader) {
        document.getElementById('member_stats_cards').style.display = 'flex';
        fetch(`\${apiUrl}/member-stats/{$type}`, {
            headers: { 'Authorization': `Bearer \${token}` }
        })
        .then(response => response.json())
        .then(data => {
            let cardsHtml = `
                <div class="col-xl-3 col-md-4 col-sm-6">
                    <div class="card card-flush h-md-100 mb-5 cursor-pointer member-card border border-primary bg-light-primary" onclick="showAllMembersData(this)">
                        <div class="card-header pt-4 pb-2">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-primary me-2 lh-1 ls-n2">ALL</span>
                                <span class="text-gray-600 pt-1 fw-semibold fs-6">Semua Anggota</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            data.forEach(member => {
                let roleLabel = member.role !== '-' ? member.role : member.jenis_pegawai;
                cardsHtml += `
                    <div class="col-xl-3 col-md-4 col-sm-6">
                        <div class="card card-flush h-md-100 mb-5 cursor-pointer member-card border" onclick="viewMemberData('\${member.id}', this)">
                            <div class="card-header pt-4 pb-2">
                                <div class="card-title d-flex flex-column" style="width: 100%;">
                                    <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">\${member.total_input}</span>
                                    <span class="text-gray-500 pt-1 fw-semibold fs-6 text-truncate" style="max-width: 100%; display: inline-block;" title="\${member.nama}">\${member.nama}</span>
                                    <span class="text-muted fs-8">\${roleLabel}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            document.getElementById('member_stats_cards').innerHTML = cardsHtml;
        });
        
        if (hasCrudAccess) {
            document.getElementById('btn_add_container').style.display = 'block';
        }
    } else {
JS;
    $content = preg_replace($searchJs, $replaceJs, $content);

    // 4. Update viewMemberData and remove showMembersList
    $searchViewMemberData = '/window\.viewMemberData = function\(userId\) \{.*?\}\s*window\.showMembersList = function\(\) \{.*?\}/s';
    
    $replaceViewMemberData = <<<JS
window.viewMemberData = function(userId, el) {
    selectedUserId = userId;
    $('.member-card').removeClass('border-primary bg-light-primary');
    if (el) $(el).addClass('border-primary bg-light-primary');
    $('#kt_table_{$tableSuffix}').DataTable().ajax.url(`\${apiUrl}/{$endpoint}/user/\${userId}`).load();
}

window.showAllMembersData = function(el) {
    selectedUserId = null;
    $('.member-card').removeClass('border-primary bg-light-primary');
    if (el) $(el).addClass('border-primary bg-light-primary');
    $('#kt_table_{$tableSuffix}').DataTable().ajax.url(`\${apiUrl}/{$endpoint}`).load();
}
JS;
    $content = preg_replace($searchViewMemberData, $replaceViewMemberData, $content);

    file_put_contents($path, $content);
    echo "Patched {$view}.blade.php\n";
}

?>
