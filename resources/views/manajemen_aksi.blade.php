@extends('layouts.app')

@section('title', 'Manajemen Aksi')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        
        <div class="row g-5 g-xl-8 mb-5" id="member_stats_cards" style="display: none;">
            <!-- Cards will be injected here via JS -->
        </div>

        <div id="main_table_container">
            <div class="card card-flush">
                <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                    <div class="card-title d-flex align-items-center gap-3">
                        
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" data-kt-aksi-table-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Cari Aksi..." />
                        </div>
                        <div class="d-flex align-items-center gap-2 my-1 ms-3">
                            <input type="date" id="filter_tanggal" class="form-control form-control-solid w-150px" title="Filter Tanggal" />
                            <input type="text" id="filter_nama" class="form-control form-control-solid w-200px" placeholder="Filter Nama..." />
                            <button type="button" class="btn btn-primary btn-sm" onclick="applyFilters()">Filter</button>
                            <button type="button" class="btn btn-light btn-sm" onclick="resetFilters()">Reset</button>
                        </div>
                    </div>
                    <div class="card-toolbar" id="btn_add_container" style="display: none;">
                        <button type="button" class="btn btn-success me-3" onclick="exportData('aksi')">
                            <i class="ki-duotone ki-file-down fs-2"></i> Export Data
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_aksi" onclick="resetForm()">
                            <i class="ki-duotone ki-plus fs-2"></i> Tambah Aksi
                        </button>
                    </div>
                </div>
                
                <div class="card-body pt-0">
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_aksis" style="width: 100%">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-125px">ID Aksi</th>
                                <th class="min-w-125px">Nama Aksi</th>
                                <th class="min-w-200px">Kegiatan</th>
                                <th class="min-w-150px">Foto</th>
                                <th class="min-w-125px">Dibuat Oleh</th>
                                <th class="min-w-100px">Status</th>
                                <th class="text-end min-w-100px">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                            <!-- Data loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Add/Edit Aksi -->
<div class="modal fade" id="kt_modal_add_aksi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_aksi_header">
                <h2 class="fw-bold" id="modal_title">Tambah Aksi</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_add_aksi_form" class="form" action="#">
                    <input type="hidden" id="aksi_uuid" name="uuid" value="" />
                    
                    <div class="d-flex flex-column mb-7 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                            <span>ID Aksi</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" placeholder="ID Aksi (Opsional)" name="idAksi" id="idAksi" />
                    </div>

                    <div class="d-flex flex-column mb-7 fv-row">
                        <label class="required d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                            <span>Nama Aksi</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" placeholder="Nama Aksi" name="namaAksi" id="namaAksi" required />
                    </div>

                    <div class="d-flex flex-column mb-7 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                            <span>Kegiatan</span>
                        </label>
                        <textarea class="form-control form-control-solid" rows="3" placeholder="Deskripsi Kegiatan" name="kegiatan" id="kegiatan"></textarea>
                    </div>

                    <div class="d-flex flex-column mb-7 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                            <span>Foto (Max 3, format: jpeg, png, jpg)</span>
                        </label>
                        <input type="file" class="form-control form-control-solid" name="foto" id="foto" accept=".jpg, .jpeg, .png" multiple />
                        <div class="text-muted fs-7 mt-2">Pilih maksimal 3 foto. Memilih foto baru akan menimpa foto yang ada sebelumnya (pada saat edit).</div>
                    </div>

                    <div class="d-flex flex-column mb-7 fv-row">
                        <label class="required fs-6 fw-semibold form-label mb-2">Status</label>
                        <select name="status" id="status" class="form-select form-select-solid">
                            <option value="1">Aktif</option>
                            <option value="0">Non-Aktif</option>
                        </select>
                    </div>

                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="kt_modal_add_aksi_submit" class="btn btn-primary">
                            <span class="indicator-label">Simpan</span>
                            <span class="indicator-progress">Please wait... 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Aksi -->
<div class="modal fade" id="kt_modal_detail_aksi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Detail Aksi</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <div class="d-flex flex-column gap-5">
                    <div class="d-flex flex-column mb-5">
                        <h4 class="text-gray-900 mb-1">Informasi Umum</h4>
                        <div class="separator mb-3"></div>
                        <div class="row mb-3">
                            <div class="col-sm-4 text-gray-500 fw-bold">ID Aksi</div>
                            <div class="col-sm-8 text-gray-800" id="detail_id_aksi"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 text-gray-500 fw-bold">Nama Aksi</div>
                            <div class="col-sm-8 text-gray-800 fw-bold" id="detail_nama_aksi"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 text-gray-500 fw-bold">Status</div>
                            <div class="col-sm-8" id="detail_status"></div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-sm-4 text-gray-500 fw-bold">Dibuat Oleh</div>
                            <div class="col-sm-8 text-gray-800 d-flex align-items-center">
                                <div class="symbol symbol-35px symbol-circle me-3">
                                    <div class="symbol-label bg-light-primary text-primary fw-bold fs-6" id="detail_pembuat_initial"></div>
                                </div>
                                <span id="detail_pembuat" class="fw-semibold"></span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column mb-5">
                        <h4 class="text-gray-900 mb-1">Deskripsi Kegiatan</h4>
                        <div class="separator mb-3"></div>
                        <div class="bg-light rounded p-4 text-gray-700" id="detail_kegiatan" style="min-height: 80px; white-space: pre-wrap;"></div>
                    </div>

                    <div class="d-flex flex-column">
                        <h4 class="text-gray-900 mb-1">Lampiran Foto</h4>
                        <div class="separator mb-3"></div>
                        <div id="detail_foto" class="d-flex flex-wrap gap-4 mt-2">
                            <!-- Foto detail render here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer flex-center">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const token = localStorage.getItem('jwt_token');
    const apiUrl = '{{ env("APP_URL") }}' + '/api';

    // Role-based logic
    let hasCrudAccess = false;
    let isSuperAdminOrLeader = false;
    let selectedUserId = null;
    let anggotaDatatable = null;
    let currentUserId = null;

    const userDataStr = localStorage.getItem('user_data');
    if (userDataStr) {
        try {
            const ud = JSON.parse(userDataStr);
            currentUserId = ud.id;
            const role = ud.role_name || (ud.role && ud.role.nama_role) || '';
            const roleLower = role.toLowerCase();
            
            if (roleLower === 'channeling' || roleLower === 'superadmin' || roleLower === 'leader') {
                hasCrudAccess = true;
            }
            if (roleLower === 'superadmin' || roleLower === 'leader') {
                isSuperAdminOrLeader = true;
            }
        } catch(e) {}
    }

    if (isSuperAdminOrLeader) {
        document.getElementById('member_stats_cards').style.display = 'flex';
        fetch(`${apiUrl}/member-stats/aksi`, {
            headers: { 'Authorization': `Bearer ${token}` }
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
                        <div class="card card-flush h-md-100 mb-5 cursor-pointer member-card border" onclick="viewMemberData('${member.id}', this)">
                            <div class="card-header pt-4 pb-2">
                                <div class="card-title d-flex flex-column" style="width: 100%;">
                                    <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">${member.total_input}</span>
                                    <span class="text-gray-500 pt-1 fw-semibold fs-6 text-truncate" style="max-width: 100%; display: inline-block;" title="${member.nama}">${member.nama}</span>
                                    <span class="text-muted fs-8">${roleLabel}</span>
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
        if (hasCrudAccess) {
            document.getElementById('btn_add_container').style.display = 'block';
        }
    }

    // Initialize DataTable
    var datatable = $('#kt_table_aksis').DataTable({
        ajax: {
            url: isSuperAdminOrLeader ? `${apiUrl}/manajemen-aksi` : `${apiUrl}/manajemen-aksi/user/` + currentUserId,
            type: 'GET',
            headers: { 'Authorization': `Bearer ${token}` },
            data: function(d) {
                if (selectedUserId) {
                    d.user_id = selectedUserId;
                }
                const filterTanggal = document.getElementById('filter_tanggal');
                const filterNama = document.getElementById('filter_nama');
                if (filterTanggal) d.filter_tanggal = filterTanggal.value;
                if (filterNama) d.filter_nama = filterNama.value;
            },
            dataSrc: ''
        },
        columns: [
            { data: 'idAksi', defaultContent: '-', render: function(data) {
                return data ? data : '-';
            }},
            { data: 'namaAksi', defaultContent: '-' },
            { data: 'kegiatan', defaultContent: '-', render: function(data) {
                return data ? data : '-';
            }},
            { data: 'foto', defaultContent: null, render: function(data) {
                if (data && Array.isArray(data) && data.length > 0) {
                    let html = '<div class="d-flex align-items-center gap-2">';
                    data.forEach(img => {
                        html += `<a href="{{ env('APP_URL') }}/${img}" target="_blank">
                                    <img src="{{ env('APP_URL') }}/${img}" class="rounded w-35px h-35px object-fit-cover" alt="foto">
                                 </a>`;
                    });
                    html += '</div>';
                    return html;
                }
                return '<span class="text-muted">Tidak ada foto</span>';
            }},
            { data: 'user', defaultContent: null, render: function(data) {
                return data ? data.nama : '-';
            }},
            { data: 'status', defaultContent: 1, render: function(data) {
                return data == 1 
                    ? `<span class="badge badge-light-success">Aktif</span>` 
                    : `<span class="badge badge-light-danger">Non-Aktif</span>`;
            }},
            { data: 'uuid', orderable: false, render: function(data, type, row) {
                let actions = `
                    <button class="btn btn-icon btn-sm btn-light-info me-2" onclick='showDetailAksi(${JSON.stringify(row).replace(/'/g, "&apos;")})' title="Detail">
                        <i class="ki-duotone ki-eye fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                    </button>`;
                
                if (hasCrudAccess) {
                    actions += `
                        <button class="btn btn-icon btn-sm btn-light-primary me-2" onclick='editAksi(${JSON.stringify(row).replace(/'/g, "&apos;")})' title="Edit">
                            <i class="ki-duotone ki-pencil fs-3"><span class="path1"></span><span class="path2"></span></i>
                        </button>
                        <button class="btn btn-icon btn-sm btn-light-danger" onclick="deleteAksi('${data}')" title="Delete">
                            <i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        </button>`;
                }
                return actions;
            }}
        ]
    });

    // Search functionality
    $('[data-kt-aksi-table-filter="search"]').on('keyup', function () {
        datatable.search(this.value).draw();
    });

    // Handle Form Submit
    const form = document.getElementById('kt_modal_add_aksi_form');
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const fotoInput = document.getElementById('foto');
        if (fotoInput.files.length > 3) {
            Swal.fire({
                text: "Maksimal 3 foto yang diizinkan.",
                icon: "warning",
                buttonsStyling: false,
                confirmButtonText: "Mengerti",
                customClass: { confirmButton: "btn btn-primary" }
            });
            return;
        }

        const submitButton = document.getElementById('kt_modal_add_aksi_submit');
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;

        const uuid = document.getElementById('aksi_uuid').value;
        const method = uuid ? 'POST' : 'POST';
        let url = uuid ? `${apiUrl}/manajemen-aksi/${uuid}` : `${apiUrl}/manajemen-aksi`;

        let formData = new FormData();
        
        formData.append('idAksi', document.getElementById('idAksi').value);
        formData.append('namaAksi', document.getElementById('namaAksi').value);
        formData.append('kegiatan', document.getElementById('kegiatan').value);
        formData.append('status', document.getElementById('status').value);

        if (fotoInput.files.length > 0) {
            for (let i = 0; i < fotoInput.files.length; i++) {
                formData.append('foto[]', fotoInput.files[i]);
            }
        }

        fetch(url, {
            method: method,
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
                // Jangan set Content-Type karena browser akan men-set otomatis dengan boundary untuk multipart/form-data
            },
            body: formData
        })
        .then(response => {
            submitButton.removeAttribute('data-kt-indicator');
            submitButton.disabled = false;

            if (response.ok) {
                $('#kt_modal_add_aksi').modal('hide');
                datatable.ajax.reload();
                Swal.fire({
                    text: "Data berhasil disimpan!",
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "Ok",
                    customClass: { confirmButton: "btn btn-primary" }
                });
            } else {
                response.json().then(err => {
                    Swal.fire({
                        text: err.message || "Terjadi kesalahan sistem.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: { confirmButton: "btn btn-danger" }
                    });
                });
            }
        })
        .catch(error => {
            submitButton.removeAttribute('data-kt-indicator');
            submitButton.disabled = false;
            console.error('Error:', error);
        });
    });

window.resetForm = function() {
    document.getElementById('kt_modal_add_aksi_form').reset();
    document.getElementById('aksi_uuid').value = "";
    document.getElementById('modal_title').innerText = "Tambah Aksi";
}

window.editAksi = function(aksi) {
    resetForm();
    document.getElementById('modal_title').innerText = "Edit Aksi";
    document.getElementById('aksi_uuid').value = aksi.uuid;
    document.getElementById('idAksi').value = aksi.idAksi || "";
    document.getElementById('namaAksi').value = aksi.namaAksi;
    document.getElementById('kegiatan').value = aksi.kegiatan || "";
    document.getElementById('status').value = (aksi.status == true || aksi.status == 1) ? "1" : "0";
    
    $('#kt_modal_add_aksi').modal('show');
}

window.deleteAksi = function(uuid) {
    Swal.fire({
        text: "Apakah Anda yakin ingin menghapus data ini?",
        icon: "warning",
        showCancelButton: true,
        buttonsStyling: false,
        confirmButtonText: "Ya, Hapus!",
        cancelButtonText: "Batal",
        customClass: {
            confirmButton: "btn btn-danger",
            cancelButton: "btn btn-active-light"
        }
    }).then(function (result) {
        if (result.value) {
            const token = localStorage.getItem('jwt_token');
            const apiUrl = '{{ env("APP_URL") }}' + '/api';
            
            fetch(`${apiUrl}/manajemen-aksi/${uuid}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    $('#kt_table_aksis').DataTable().ajax.reload();
                    Swal.fire({
                        text: "Data berhasil dihapus.",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: { confirmButton: "btn btn-primary" }
                    });
                } else {
                    Swal.fire({
                        text: "Gagal menghapus data.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: { confirmButton: "btn btn-danger" }
                    });
                }
            });
        }
    });
}

window.showDetailAksi = function(aksi) {
    document.getElementById('detail_id_aksi').innerText = aksi.idAksi || '-';
    document.getElementById('detail_nama_aksi').innerText = aksi.namaAksi || '-';
    document.getElementById('detail_kegiatan').innerText = aksi.kegiatan || '-';
    
    let pembuatName = aksi.user ? aksi.user.nama : '-';
    document.getElementById('detail_pembuat').innerText = pembuatName;
    document.getElementById('detail_pembuat_initial').innerText = pembuatName !== '-' ? pembuatName.charAt(0).toUpperCase() : '?';
    
    document.getElementById('detail_status').innerHTML = aksi.status == 1 
        ? `<span class="badge badge-light-success">Aktif</span>` 
        : `<span class="badge badge-light-danger">Non-Aktif</span>`;
        
    let fotoContainer = document.getElementById('detail_foto');
    fotoContainer.innerHTML = '';
    if (aksi.foto && Array.isArray(aksi.foto) && aksi.foto.length > 0) {
        aksi.foto.forEach(img => {
            fotoContainer.innerHTML += `
                <a href="{{ env('APP_URL') }}/${img}" target="_blank">
                    <img src="{{ env('APP_URL') }}/${img}" class="rounded shadow-sm w-150px h-150px object-fit-cover" alt="foto" style="border: 1px solid #ddd;">
                </a>
            `;
        });
    } else {
        fotoContainer.innerHTML = '<span class="text-muted">Tidak ada foto terlampir.</span>';
    }

    $('#kt_modal_detail_aksi').modal('show');
}

window.applyFilters = function() {
    $('#kt_table_aksis').DataTable().ajax.reload();
}

window.resetFilters = function() {
    document.getElementById('filter_tanggal').value = '';
    document.getElementById('filter_nama').value = '';
    $('#kt_table_aksis').DataTable().ajax.reload();
}

window.exportData = function(module) {
    const token = localStorage.getItem('jwt_token');
    const filterTanggal = document.getElementById('filter_tanggal')?.value || '';
    const filterNama = document.getElementById('filter_nama')?.value || '';
    
    let url = `{{ url('/api/export/manajemen-aksi') }}?`;
    if (isSuperAdminOrLeader) {
        if (selectedUserId) url += `user_id=${selectedUserId}&`;
    } else {
        if (currentUserId) url += `user_id=${currentUserId}&`;
    }
    if (filterTanggal) url += `filter_tanggal=${filterTanggal}&`;
    if (filterNama) url += `filter_nama=${filterNama}&`;

    fetch(url, {
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.blob();
    })
    .then(blob => {
        const downloadUrl = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = downloadUrl;
        a.download = 'manajemen_aksi.xlsx';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(downloadUrl);
        a.remove();
    })
    .catch(error => {
        console.error('Error exporting data:', error);
        Swal.fire('Error', 'Gagal mengekspor data.', 'error');
    });
}

window.viewMemberData = function(userId, el) {
    selectedUserId = userId;
    $('.member-card').removeClass('border-primary bg-light-primary');
    if (el) $(el).addClass('border-primary bg-light-primary');
    $('#kt_table_aksis').DataTable().ajax.url(`${apiUrl}/manajemen-aksi/user/${userId}`).load();
}

window.showAllMembersData = function(el) {
    selectedUserId = null;
    $('.member-card').removeClass('border-primary bg-light-primary');
    if (el) $(el).addClass('border-primary bg-light-primary');
    $('#kt_table_aksis').DataTable().ajax.url(`${apiUrl}/manajemen-aksi`).load();
}
});
</script>
@endsection
