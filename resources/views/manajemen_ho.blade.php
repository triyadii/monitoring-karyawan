@extends('layouts.app')

@section('title', 'Manajemen HO')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        
        <div id="anggota_list_container" style="display: none;">
            <div class="card card-flush">
                <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                    <div class="card-title">
                        <h3 class="fw-bold">Daftar Anggota Retention</h3>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_anggota">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-150px">Nama Anggota</th>
                                <th class="min-w-150px">Username</th>
                                <th class="min-w-125px">Role / Jabatan</th>
                                <th class="min-w-125px">Total Data Input</th>
                                <th class="text-end min-w-100px">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="main_table_container">
            <div class="card card-flush">
                <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                    <div class="card-title d-flex align-items-center gap-3">
                        <button type="button" class="btn btn-sm btn-light" id="btn_back_to_members" style="display: none;" onclick="showMembersList()">
                            <i class="ki-duotone ki-arrow-left fs-2"></i> Kembali
                        </button>
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" data-kt-ho-table-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Cari HO..." />
                        </div>
                        <div class="d-flex align-items-center gap-2 my-1 ms-3">
                            <input type="date" id="filter_tanggal" class="form-control form-control-solid w-150px" title="Filter Tanggal" />
                            <input type="text" id="filter_nama" class="form-control form-control-solid w-200px" placeholder="Filter Nama..." />
                            <button type="button" class="btn btn-primary btn-sm" onclick="applyFilters()">Filter</button>
                            <button type="button" class="btn btn-light btn-sm" onclick="resetFilters()">Reset</button>
                        </div>
                    </div>
                    <div class="card-toolbar" id="btn_add_container" style="display: none;">
                        <button type="button" class="btn btn-success me-3" onclick="exportData('ho')">
                            <i class="ki-duotone ki-file-down fs-2"></i> Export Data
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_ho" onclick="resetForm()">
                            <i class="ki-duotone ki-plus fs-2"></i> Tambah HO
                        </button>
                    </div>
                </div>
                
                <div class="card-body pt-0">
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_hos" style="width: 100%">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-125px">Nama Client</th>
                                <th class="min-w-125px">Nomor Telepon</th>
                                <th class="min-w-200px">Alamat</th>
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

<!-- Modal Add/Edit HO -->
<div class="modal fade" id="kt_modal_add_ho" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_ho_header">
                <h2 class="fw-bold" id="modal_title">Tambah HO</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_add_ho_form" class="form" action="#">
                    <input type="hidden" id="ho_uuid" name="uuid" value="" />

                    <div class="d-flex flex-column mb-7 fv-row">
                        <label class="required d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                            <span>Nama Client</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" placeholder="Nama Client" name="namaClient" id="namaClient" required />
                    </div>

                    <div class="d-flex flex-column mb-7 fv-row">
                        <label class="required d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                            <span>Nomor Telepon</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" placeholder="Nomor Telepon" name="nomorTelepon" id="nomorTelepon" required />
                    </div>

                    <div class="d-flex flex-column mb-7 fv-row">
                        <label class="required d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                            <span>Alamat</span>
                        </label>
                        <textarea class="form-control form-control-solid" rows="3" placeholder="Alamat Lengkap" name="alamat" id="alamat" required></textarea>
                    </div>

                    <div class="d-flex flex-column mb-7 fv-row">
                        <label class="required fs-6 fw-semibold form-label mb-2">Status</label>
                        <select name="status_id" id="status_id" class="form-select form-select-solid" required>
                            <option value="">Pilih Status...</option>
                            <!-- Options loaded dynamically -->
                        </select>
                    </div>

                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="kt_modal_add_ho_submit" class="btn btn-primary">
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

<!-- Modal Detail HO -->
<div class="modal fade" id="kt_modal_detail_ho" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Detail HO</h2>
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
                            <div class="col-sm-4 text-gray-500 fw-bold">Nama Client</div>
                            <div class="col-sm-8 text-gray-800 fw-bold" id="detail_nama_client"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 text-gray-500 fw-bold">Nomor Telepon</div>
                            <div class="col-sm-8 text-gray-800" id="detail_nomor_telepon"></div>
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
                        <h4 class="text-gray-900 mb-1">Alamat</h4>
                        <div class="separator mb-3"></div>
                        <div class="bg-light rounded p-4 text-gray-700" id="detail_alamat" style="min-height: 80px; white-space: pre-wrap;"></div>
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

    const userDataStr = localStorage.getItem('user_data');
    if (userDataStr) {
        try {
            const ud = JSON.parse(userDataStr);
            const role = ud.role_name || (ud.role && ud.role.nama_role) || '';
            const roleLower = role.toLowerCase();
            
            if (roleLower === 'retention' || roleLower === 'superadmin' || roleLower === 'leader') {
                hasCrudAccess = true;
            }
            if (roleLower === 'superadmin' || roleLower === 'leader') {
                isSuperAdminOrLeader = true;
            }
        } catch(e) {}
    }

    if (isSuperAdminOrLeader) {
        document.getElementById('main_table_container').style.display = 'none';
        document.getElementById('anggota_list_container').style.display = 'block';

        anggotaDatatable = $('#kt_table_anggota').DataTable({
            ajax: {
                url: `${apiUrl}/member-stats/ho`,
                type: 'GET',
                headers: { 'Authorization': `Bearer ${token}` },
                dataSrc: ''
            },
            columns: [
                { data: 'nama', defaultContent: '-' },
                { data: 'username', defaultContent: '-' },
                { data: 'role', defaultContent: '-', render: function(data, type, row) {
                    return data !== '-' ? data : row.jenis_pegawai;
                }},
                { data: 'total_input', defaultContent: '0' },
                { data: 'id', orderable: false, render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-light-primary" onclick="viewMemberData('${data}')">
                            <i class="ki-duotone ki-eye fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> Lihat Data
                        </button>
                    `;
                }}
            ]
        });
    } else {
        if (hasCrudAccess) {
            document.getElementById('btn_add_container').style.display = 'block';
        }
    }

    // Load Master Status Client for dropdown
    fetch(`${apiUrl}/master-status-clients`, {
        headers: { 'Authorization': `Bearer ${token}` }
    })
    .then(response => response.json())
    .then(data => {
        const select = document.getElementById('status_id');
        data.forEach(status => {
            const option = document.createElement('option');
            option.value = status.id;
            option.text = status.nama_status;
            select.appendChild(option);
        });
    });

    // Initialize DataTable
    var datatable = $('#kt_table_hos').DataTable({
        ajax: {
            url: `${apiUrl}/manajemen-ho`,
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
            { data: 'namaClient', defaultContent: '-' },
            { data: 'nomorTelepon', defaultContent: '-' },
            { data: 'alamat', defaultContent: '-', render: function(data) {
                return data.length > 50 ? data.substr(0, 50) + '...' : data;
            }},
            { data: 'user', defaultContent: null, render: function(data) {
                return data ? data.nama : '-';
            }},
            { data: 'status', defaultContent: null, render: function(data) {
                return data ? `<span class="badge badge-light-primary">${data.nama_status}</span>` : '-';
            }},
            { data: 'uuid', orderable: false, render: function(data, type, row) {
                let actions = `
                    <button class="btn btn-icon btn-sm btn-light-info me-2" onclick='showDetailHO(${JSON.stringify(row).replace(/'/g, "&apos;")})' title="Detail">
                        <i class="ki-duotone ki-eye fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                    </button>`;
                
                if (hasCrudAccess) {
                    actions += `
                        <button class="btn btn-icon btn-sm btn-light-primary me-2" onclick='editHO(${JSON.stringify(row).replace(/'/g, "&apos;")})' title="Edit">
                            <i class="ki-duotone ki-pencil fs-3"><span class="path1"></span><span class="path2"></span></i>
                        </button>
                        <button class="btn btn-icon btn-sm btn-light-danger" onclick="deleteHO('${data}')" title="Delete">
                            <i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        </button>`;
                }
                return actions;
            }}
        ]
    });

    // Search functionality
    $('[data-kt-ho-table-filter="search"]').on('keyup', function () {
        datatable.search(this.value).draw();
    });

    // Handle Form Submit
    const form = document.getElementById('kt_modal_add_ho_form');
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const submitButton = document.getElementById('kt_modal_add_ho_submit');
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;

        const uuid = document.getElementById('ho_uuid').value;
        const method = uuid ? 'PUT' : 'POST';
        let url = uuid ? `${apiUrl}/manajemen-ho/${uuid}` : `${apiUrl}/manajemen-ho`;

        let data = {
            namaClient: document.getElementById('namaClient').value,
            nomorTelepon: document.getElementById('nomorTelepon').value,
            alamat: document.getElementById('alamat').value,
            status_id: document.getElementById('status_id').value
        };

        fetch(url, {
            method: method,
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            submitButton.removeAttribute('data-kt-indicator');
            submitButton.disabled = false;

            if (response.ok) {
                $('#kt_modal_add_ho').modal('hide');
                datatable.ajax.reload();
                resetForm();
                
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

});

window.resetForm = function() {
    document.getElementById('kt_modal_add_ho_form').reset();
    document.getElementById('ho_uuid').value = "";
    document.getElementById('modal_title').innerText = "Tambah HO";
}

window.editHO = function(ho) {
    resetForm();
    document.getElementById('modal_title').innerText = "Edit HO";
    document.getElementById('ho_uuid').value = ho.uuid;
    document.getElementById('namaClient').value = ho.namaClient;
    document.getElementById('nomorTelepon').value = ho.nomorTelepon;
    document.getElementById('alamat').value = ho.alamat;
    document.getElementById('status_id').value = ho.status_id;
    
    $('#kt_modal_add_ho').modal('show');
}

window.deleteHO = function(uuid) {
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
            
            fetch(`${apiUrl}/manajemen-ho/${uuid}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    $('#kt_table_hos').DataTable().ajax.reload();
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

window.showDetailHO = function(ho) {
    document.getElementById('detail_nama_client').innerText = ho.namaClient || '-';
    document.getElementById('detail_nomor_telepon').innerText = ho.nomorTelepon || '-';
    document.getElementById('detail_alamat').innerText = ho.alamat || '-';
    
    let pembuatName = ho.user ? ho.user.nama : '-';
    document.getElementById('detail_pembuat').innerText = pembuatName;
    document.getElementById('detail_pembuat_initial').innerText = pembuatName !== '-' ? pembuatName.charAt(0).toUpperCase() : '?';
    
    document.getElementById('detail_status').innerHTML = ho.status 
        ? `<span class="badge badge-light-primary">${ho.status.nama_status}</span>` 
        : `-`;

    $('#kt_modal_detail_ho').modal('show');
}

window.applyFilters = function() {
    $('#kt_table_hos').DataTable().ajax.reload();
}

window.resetFilters = function() {
    document.getElementById('filter_tanggal').value = '';
    document.getElementById('filter_nama').value = '';
    $('#kt_table_hos').DataTable().ajax.reload();
}

window.exportData = function(module) {
    const token = localStorage.getItem('jwt_token');
    const filterTanggal = document.getElementById('filter_tanggal')?.value || '';
    const filterNama = document.getElementById('filter_nama')?.value || '';
    
    let url = `{{ url('/api/export/manajemen-ho') }}?`;
    if (selectedUserId) url += `user_id=${selectedUserId}&`;
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
        a.download = 'manajemen_ho.xlsx';
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

window.viewMemberData = function(userId) {
    selectedUserId = userId;
    document.getElementById('anggota_list_container').style.display = 'none';
    document.getElementById('main_table_container').style.display = 'block';
    document.getElementById('btn_back_to_members').style.display = 'block';
    if (hasCrudAccess) {
        document.getElementById('btn_add_container').style.display = 'block';
    }
    $('#kt_table_hos').DataTable().ajax.reload();
}

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
</script>
@endsection
