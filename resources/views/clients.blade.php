@extends('layouts.app')

@section('title', 'Manajemen Client')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        
        <div class="card card-flush">
            <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                <div class="card-title">
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" data-kt-client-table-filter="search" class="form-control form-control-solid w-200px ps-12" placeholder="Cari Client..." />
                        </div>
                        <div class="d-flex align-items-center position-relative my-1">
                            <select id="filter_status_client" class="form-select form-select-solid w-200px" data-placeholder="Semua Status">
                                <option value="">Semua Status</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center position-relative my-1" id="filter_anggota_container" style="display: none;">
                            <select id="filter_anggota" class="form-select form-select-solid w-200px" data-placeholder="Semua Anggota">
                                <option value="">Semua Anggota</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-success me-3" id="btn_bulk_disposisi" style="display: none;" onclick="openBulkDisposisiModal()">
                        <i class="ki-duotone ki-send fs-2"></i> Disposisi Terpilih (<span id="selected_count">0</span>)
                    </button>
                    <button type="button" class="btn btn-icon btn-light-success me-3" id="btn_export_excel" title="Export Excel">
                        <i class="ki-duotone ki-document fs-2"><span class="path1"></span><span class="path2"></span></i>
                    </button>
                    <button type="button" class="btn btn-icon btn-light-danger me-3" id="btn_export_pdf" title="Export PDF">
                        <i class="ki-duotone ki-document fs-2"><span class="path1"></span><span class="path2"></span></i>
                    </button>
                    <button type="button" class="btn btn-icon btn-light-primary me-3" id="btn_import_excel" style="display: none;" data-bs-toggle="modal" data-bs-target="#kt_modal_import_client" title="Import Excel">
                        <i class="ki-duotone ki-file-up fs-2"><span class="path1"></span><span class="path2"></span></i>
                    </button>
                    <button type="button" class="btn btn-icon btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_client" onclick="resetForm()" title="Tambah Client">
                        <i class="ki-duotone ki-plus fs-2"></i>
                    </button>
                </div>
            </div>
            
            <div class="card-body pt-0 overflow-auto">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_clients">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-10px pe-2">
                                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_clients .form-check-input" value="1" />
                                </div>
                            </th>
                            <th class="min-w-125px">Nama</th>
                            <th class="min-w-150px">Alamat</th>
                            <th class="min-w-125px">Kontak</th>
                            <th class="min-w-100px">Sumber Data</th>
                            <th class="min-w-100px">User (Disposisi)</th>
                            <th class="min-w-100px">Penginput</th>
                            <th class="min-w-100px">Tanggal Input</th>
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

<!-- Modal Add/Edit Client -->
<div class="modal fade" id="kt_modal_add_client" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-800px">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_client_header">
                <h2 class="fw-bold" id="modal_title">Tambah Client</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_add_client_form" class="form" action="#">
                    <input type="hidden" id="client_id" name="id" value="" />
                    
                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Nama Client</label>
                            <input type="text" class="form-control form-control-solid" placeholder="Nama Client" id="nama" required />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Nomor Telepon</label>
                            <input type="text" class="form-control form-control-solid" placeholder="Nomor Telepon" id="nomor_telepon" />
                        </div>
                    </div>

                    <div class="fv-row mb-8">
                        <label class="fs-6 fw-semibold mb-2">Alamat Lengkap</label>
                        <textarea class="form-control form-control-solid" rows="3" placeholder="Alamat" id="alamat"></textarea>
                    </div>

                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Provinsi</label>
                            <select id="provinsi" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#kt_modal_add_client" data-placeholder="Pilih Provinsi"></select>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Kabupaten</label>
                            <select id="kabupaten" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#kt_modal_add_client" data-placeholder="Pilih Kabupaten" disabled></select>
                        </div>
                    </div>
                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Kecamatan</label>
                            <select id="kecamatan" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#kt_modal_add_client" data-placeholder="Pilih Kecamatan" disabled></select>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Kelurahan</label>
                            <select id="kelurahan" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#kt_modal_add_client" data-placeholder="Pilih Kelurahan" disabled></select>
                        </div>
                    </div>

                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Sumber Data</label>
                            <select id="sumber_data" class="form-select form-select-solid" required>
                                <option value="1">1 - HO</option>
                                <option value="2">2 - Anggota</option>
                            </select>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Status Client</label>
                            <select id="status_client" class="form-select form-select-solid" required>
                                <option value="">Pilih Status...</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="kt_modal_add_client_submit" class="btn btn-primary">
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

<!-- Modal Bulk Disposisi -->
<div class="modal fade" id="kt_modal_bulk_disposisi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Disposisi Massal</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_bulk_disposisi_form" class="form" action="#">
                    <div class="fv-row mb-8">
                        <label class="required fs-6 fw-semibold mb-2">Pilih User untuk Disposisi</label>
                        <select id="disposisi_user_id" data-control="select2" data-dropdown-parent="#kt_modal_bulk_disposisi" data-placeholder="Pilih User..." class="form-select form-select-solid" required>
                            <option value="">Pilih User...</option>
                            <!-- Loaded via AJAX -->
                        </select>
                    </div>
                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="kt_modal_bulk_disposisi_submit" class="btn btn-success">
                            <span class="indicator-label">Terapkan Disposisi</span>
                            <span class="indicator-progress">Please wait... 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import Client -->
<div class="modal fade" id="kt_modal_import_client" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Import Client dari Excel</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_import_client_form" class="form" action="#">
                    <div class="fv-row mb-8">
                        <label class="required fs-6 fw-semibold mb-2">Pilih File Excel</label>
                        <input type="file" id="import_file" class="form-control form-control-solid" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required />
                        <div class="text-muted mt-2">Format yang didukung: .xls, .xlsx, .csv. <br><a href="{{ asset('format_import_client.csv') }}" download class="text-primary fw-bold"><i class="ki-duotone ki-file-down fs-4"></i> Unduh Format Contoh CSV</a></div>
                    </div>
                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="kt_modal_import_client_submit" class="btn btn-success">
                            <span class="indicator-label">Import Data</span>
                            <span class="indicator-progress">Please wait... 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Client -->
<div class="modal fade" id="kt_modal_detail_client" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Detail Client</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <div class="d-flex flex-column gap-7">
                    <!-- Header with Icon -->
                    <div class="d-flex align-items-center mb-2">
                        <div class="symbol symbol-50px me-5">
                            <span class="symbol-label bg-light-primary">
                                <i class="ki-duotone ki-briefcase text-primary fs-2x"><span class="path1"></span><span class="path2"></span></i>
                            </span>
                        </div>
                        <div class="d-flex flex-column">
                            <h2 class="fw-bold text-gray-900 mb-1" id="detail_nama">Nama Client</h2>
                            <span id="detail_status"></span>
                        </div>
                    </div>

                    <!-- Informasi Utama & Sistem -->
                    <div class="row g-5">
                        <div class="col-sm-6">
                            <div class="border border-dashed border-gray-300 rounded p-4 h-100">
                                <div class="fs-6 fw-bold text-gray-900 mb-4">Informasi Kontak & Disposisi</div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="ki-duotone ki-phone fs-4 me-3 text-gray-500"><span class="path1"></span><span class="path2"></span></i>
                                    <span class="fs-6 text-gray-700 fw-semibold" id="detail_nomor_telepon"></span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-user fs-4 me-3 text-gray-500"><span class="path1"></span><span class="path2"></span></i>
                                    <span class="fs-6 text-gray-700 fw-semibold" id="detail_user"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="border border-dashed border-gray-300 rounded p-4 h-100">
                                <div class="fs-6 fw-bold text-gray-900 mb-4">Sumber Data</div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-data fs-3 me-3 text-gray-500"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                    <span class="fs-6 text-gray-700 fw-semibold" id="detail_sumber_data"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div class="border border-dashed border-gray-300 rounded p-5">
                        <div class="d-flex align-items-center mb-4">
                            <i class="ki-duotone ki-geolocation fs-2x me-3 text-danger"><span class="path1"></span><span class="path2"></span></i>
                            <div class="fs-5 fw-bold text-gray-900">Alamat Lengkap</div>
                        </div>
                        <div class="fs-6 text-gray-700 fw-semibold mb-5 pb-5 border-bottom border-gray-200" id="detail_alamat"></div>
                        <div class="row text-gray-700 fs-6">
                            <div class="col-sm-4 mb-3 mb-sm-0">
                                <span class="text-muted d-block fw-semibold mb-1 fs-7 text-uppercase">Kelurahan</span>
                                <span class="fw-bold text-gray-800" id="detail_kelurahan"></span>
                            </div>
                            <div class="col-sm-4 mb-3 mb-sm-0">
                                <span class="text-muted d-block fw-semibold mb-1 fs-7 text-uppercase">Kecamatan</span>
                                <span class="fw-bold text-gray-800" id="detail_kecamatan"></span>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-muted d-block fw-semibold mb-1 fs-7 text-uppercase">Kabupaten</span>
                                <span class="fw-bold text-gray-800" id="detail_kabupaten"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Status -->
<div class="modal fade" id="kt_modal_update_status" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Update Status Client</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_update_status_form" class="form" action="#">
                    <input type="hidden" id="update_status_client_id" value="" />
                    
                    <div class="d-flex flex-column mb-7 fv-row">
                        <label class="required fs-6 fw-semibold mb-2">Pilih Status Baru</label>
                        <select id="new_status_client" class="form-select form-select-solid" required>
                            <option value="">Pilih Status...</option>
                        </select>
                    </div>

                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="kt_modal_update_status_submit" class="btn btn-primary">
                            <span class="indicator-label">Update</span>
                            <span class="indicator-progress">Please wait... 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const token = localStorage.getItem('jwt_token');
    const userDataStr = localStorage.getItem('user_data');
    let roleName = '';
    if (userDataStr) {
        const userData = JSON.parse(userDataStr);
        let rawRole = userData.role_name || (userData.role && userData.role.nama_role) || '';
        roleName = rawRole.toLowerCase();

        if (roleName === 'superadmin' || roleName === 'leader') {
            const btnImport = document.getElementById('btn_import_excel');
            if (btnImport) btnImport.style.display = 'inline-block';
        }
    }
    const apiUrl = '{{ env("APP_URL") }}' + '/api';

    // 1. Fetch Users for Disposisi Dropdown
    function loadUsers() {
        fetch(`${apiUrl}/users`, { headers: { 'Authorization': `Bearer ${token}` } })
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('disposisi_user_id');
            const filterAnggota = document.getElementById('filter_anggota');
            data.forEach(user => {
                const rName = user.role && user.role.nama_role ? user.role.nama_role.toLowerCase() : '';
                if (rName === 'anggota') {
                    const opt = document.createElement('option');
                    opt.value = user.id;
                    opt.textContent = user.nama;
                    select.appendChild(opt);

                    const optFilter = document.createElement('option');
                    optFilter.value = user.id;
                    optFilter.textContent = user.nama;
                    filterAnggota.appendChild(optFilter);
                }
            });
        })
        .catch(error => console.error('Error fetching users:', error));
    }
    loadUsers();

    function loadStatuses() {
        fetch(`${apiUrl}/master-status-clients`, { headers: { 'Authorization': `Bearer ${token}` } })
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('status_client');
            const selectUpdate = document.getElementById('new_status_client');
            const filterStatus = document.getElementById('filter_status_client');
            
            data.forEach(status => {
                const opt = document.createElement('option');
                opt.value = status.id;
                opt.textContent = status.nama_status;
                select.appendChild(opt);

                const opt2 = document.createElement('option');
                opt2.value = status.id;
                opt2.textContent = status.nama_status;
                selectUpdate.appendChild(opt2);
                
                const opt3 = document.createElement('option');
                opt3.value = status.id;
                opt3.textContent = status.nama_status;
                filterStatus.appendChild(opt3);
            });
        })
        .catch(error => console.error('Error fetching statuses:', error));
    }
    loadStatuses();

    // 2. Initialize DataTable
    var datatable = $('#kt_table_clients').DataTable({
        ajax: {
            url: `${apiUrl}/clients`,
            type: 'GET',
            headers: { 'Authorization': `Bearer ${token}` },
            data: function (d) {
                d.status_client = $('#filter_status_client').val();
                if (roleName !== 'anggota') {
                    d.user_id = $('#filter_anggota').val();
                }
            },
            dataSrc: ''
        },
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Data Clients',
                exportOptions: {
                    columns: [ 1, 2, 3, 4, 5, 6, 7, 8 ]
                }
            },
            {
                extend: 'pdfHtml5',
                title: 'Data Clients',
                exportOptions: {
                    columns: [ 1, 2, 3, 4, 5, 6, 7, 8 ]
                }
            }
        ],
        order: [],
        columns: [
            { 
                data: 'id', 
                orderable: false, 
                visible: roleName === 'leader',
                render: function (data) {
                    return `
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input client-checkbox" type="checkbox" value="${data}" />
                        </div>`;
                }
            },
            { data: 'nama' },
            { data: null, render: function(data, type, row) {
                let address = row.alamat || '';
                if(row.kelurahan) address += `, Kel. ${row.kelurahan}`;
                if(row.kecamatan) address += `, Kec. ${row.kecamatan}`;
                if(row.kabupaten) address += `, Kab. ${row.kabupaten}`;
                return address || '-';
            }},
            { data: 'nomor_telepon', render: function(data) { return data || '-'; } },
            { data: 'sumber_data', render: function(data) {
                return data == 1 ? '<span class="badge badge-light-info">HO</span>' : '<span class="badge badge-light-warning">Anggota</span>';
            }},
            { data: 'user', render: function(data) {
                return data ? `<span class="badge badge-light-primary">${data.nama}</span>` : '<span class="badge badge-light-secondary">Belum ada</span>';
            }},
            { data: 'penginput', render: function(data) {
                return data ? `<span class="badge badge-light-info">${data.nama}</span>` : '<span class="badge badge-light-secondary">Import Data</span>';
            }},
            { data: 'created_at', render: function(data) {
                if (!data) return '-';
                const date = new Date(data);
                return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute:'2-digit' });
            }},
            { data: 'status', render: function(data) {
                return data 
                    ? `<span class="badge badge-light-primary">${data.nama_status}</span>` 
                    : `<span class="badge badge-light-secondary">Belum ada</span>`;
            }},
            { data: 'id', orderable: false, render: function(data, type, row) {
                let buttons = `
                    <button class="btn btn-icon btn-sm btn-light-info me-2" onclick='detailClient(${JSON.stringify(row).replace(/'/g, "&apos;")})' title="Detail">
                        <i class="ki-duotone ki-eye fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                    </button>
                    <button class="btn btn-icon btn-sm btn-light-success me-2" onclick='updateStatusClient("${data}", "${row.status_client}")' title="Update Status">
                        <i class="ki-duotone ki-arrows-circle fs-3"><span class="path1"></span><span class="path2"></span></i>
                    </button>
                `;

                if (roleName !== 'anggota') {
                    buttons += `
                    <button class="btn btn-icon btn-sm btn-light-primary me-2" onclick='editClient(${JSON.stringify(row).replace(/'/g, "&apos;")})' title="Edit">
                        <i class="ki-duotone ki-pencil fs-3"><span class="path1"></span><span class="path2"></span></i>
                    </button>
                    <button class="btn btn-icon btn-sm btn-light-danger" onclick="deleteClient('${data}')" title="Delete">
                        <i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                    </button>
                    `;
                }
                
                return buttons;
            }}
        ]
    });

    // Search and Filter functionality
    $('[data-kt-client-table-filter="search"]').on('keyup', function () {
        datatable.search(this.value).draw();
    });

    $('#btn_export_excel').on('click', function() {
        datatable.button('.buttons-excel').trigger();
    });

    $('#btn_export_pdf').on('click', function() {
        datatable.button('.buttons-pdf').trigger();
    });

    $('#filter_status_client').on('change', function () {
        datatable.ajax.reload();
    });

    $('#filter_anggota').on('change', function () {
        datatable.ajax.reload();
    });

    // Show filter anggota if superadmin or leader
    if (roleName === 'superadmin' || roleName === 'leader') {
        document.getElementById('filter_anggota_container').style.display = 'flex';
    }

    // Handle checkboxes for bulk actions
    datatable.on('draw', function () {
        initCheckboxes();
    });

    function initCheckboxes() {
        const checkboxes = document.querySelectorAll('.client-checkbox');
        const selectAll = document.querySelector('[data-kt-check="true"]');
        
        checkboxes.forEach(c => {
            c.addEventListener('change', updateBulkActionBtn);
        });

        if(selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(c => {
                    c.checked = selectAll.checked;
                });
                updateBulkActionBtn();
            });
        }
    }

    function updateBulkActionBtn() {
        const checked = document.querySelectorAll('.client-checkbox:checked');
        const btn = document.getElementById('btn_bulk_disposisi');
        const countSpan = document.getElementById('selected_count');
        
        if (checked.length > 0) {
            btn.style.display = 'inline-block';
            countSpan.innerText = checked.length;
        } else {
            btn.style.display = 'none';
        }
    }

    // 3. Handle Form Submit (Create & Update)
    const form = document.getElementById('kt_modal_add_client_form');
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const submitButton = document.getElementById('kt_modal_add_client_submit');
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;

        const id = document.getElementById('client_id').value;
        const method = id ? 'PUT' : 'POST';
        const url = id ? `${apiUrl}/clients/${id}` : `${apiUrl}/clients`;

        const payload = {
            nama: document.getElementById('nama').value,
            alamat: document.getElementById('alamat').value,
            kelurahan: document.getElementById('kelurahan').value,
            kecamatan: document.getElementById('kecamatan').value,
            kabupaten: document.getElementById('kabupaten').value,
            nomor_telepon: document.getElementById('nomor_telepon').value,
            sumber_data: document.getElementById('sumber_data').value,
            status_client: document.getElementById('status_client').value
        };

        fetch(url, {
            method: method,
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => {
            submitButton.removeAttribute('data-kt-indicator');
            submitButton.disabled = false;

            if (response.ok) {
                $('#kt_modal_add_client').modal('hide');
                datatable.ajax.reload();
                Swal.fire({ text: "Data client berhasil disimpan!", icon: "success", buttonsStyling: false, confirmButtonText: "Ok", customClass: { confirmButton: "btn btn-primary" } });
            } else {
                response.json().then(err => {
                    Swal.fire({ text: err.message || "Terjadi kesalahan.", icon: "error", buttonsStyling: false, confirmButtonText: "Ok", customClass: { confirmButton: "btn btn-danger" } });
                });
            }
        })
        .catch(error => {
            submitButton.removeAttribute('data-kt-indicator');
            submitButton.disabled = false;
            console.error('Error:', error);
        });
    });

    // 4. Handle Bulk Disposisi Submit
    const bulkForm = document.getElementById('kt_modal_bulk_disposisi_form');
    bulkForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const checked = document.querySelectorAll('.client-checkbox:checked');
        if(checked.length === 0) return;

        const clientIds = Array.from(checked).map(c => c.value);
        const userId = document.getElementById('disposisi_user_id').value;

        if(!userId) {
            Swal.fire("Peringatan", "Pilih user terlebih dahulu", "warning");
            return;
        }

        const submitButton = document.getElementById('kt_modal_bulk_disposisi_submit');
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;

        fetch(`${apiUrl}/clients/bulk-disposisi`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                client_ids: clientIds,
                user_id: userId
            })
        })
        .then(response => {
            submitButton.removeAttribute('data-kt-indicator');
            submitButton.disabled = false;

            if (response.ok) {
                $('#kt_modal_bulk_disposisi').modal('hide');
                document.querySelector('[data-kt-check="true"]').checked = false;
                document.getElementById('btn_bulk_disposisi').style.display = 'none';
                datatable.ajax.reload();
                Swal.fire({ text: "Disposisi massal berhasil!", icon: "success", buttonsStyling: false, confirmButtonText: "Ok", customClass: { confirmButton: "btn btn-primary" } });
            } else {
                Swal.fire({ text: "Gagal memproses disposisi.", icon: "error", buttonsStyling: false, confirmButtonText: "Ok", customClass: { confirmButton: "btn btn-danger" } });
            }
        });
    });

    // 5. Handle Import Submit
    const importForm = document.getElementById('kt_modal_import_client_form');
    if (importForm) {
        importForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const fileInput = document.getElementById('import_file');
            if (fileInput.files.length === 0) {
                Swal.fire("Peringatan", "Pilih file excel terlebih dahulu", "warning");
                return;
            }

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);

            const submitButton = document.getElementById('kt_modal_import_client_submit');
            submitButton.setAttribute('data-kt-indicator', 'on');
            submitButton.disabled = true;

            fetch(`${apiUrl}/clients/import`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                body: formData
            })
            .then(response => {
                submitButton.removeAttribute('data-kt-indicator');
                submitButton.disabled = false;

                if (response.ok) {
                    $('#kt_modal_import_client').modal('hide');
                    importForm.reset();
                    datatable.ajax.reload();
                    Swal.fire({ text: "Data client berhasil diimport!", icon: "success", buttonsStyling: false, confirmButtonText: "Ok", customClass: { confirmButton: "btn btn-primary" } });
                } else {
                    response.json().then(err => {
                        Swal.fire({ text: err.message || "Gagal mengimport data.", icon: "error", buttonsStyling: false, confirmButtonText: "Ok", customClass: { confirmButton: "btn btn-danger" } });
                    }).catch(() => {
                        Swal.fire({ text: "Gagal mengimport data.", icon: "error", buttonsStyling: false, confirmButtonText: "Ok", customClass: { confirmButton: "btn btn-danger" } });
                    });
                }
            })
            .catch(error => {
                submitButton.removeAttribute('data-kt-indicator');
                submitButton.disabled = false;
                console.error('Error:', error);
                Swal.fire({ text: "Gagal memproses import.", icon: "error", buttonsStyling: false, confirmButtonText: "Ok", customClass: { confirmButton: "btn btn-danger" } });
            });
        });
    }

    $('#kt_modal_bulk_disposisi').on('shown.bs.modal', function () {
        $('#disposisi_user_id').select2({ dropdownParent: $('#kt_modal_bulk_disposisi') });
    });


    // 1. Fetch Provinces
    fetch(`${wilayahApiBase}/provinces`)
        .then(response => response.json())
        .then(json => {
            window.wilayahProvinces = json.data;
            fillDropdown($('#provinsi'), json.data, "Pilih Provinsi", $('#provinsi').val());
        })
        .catch(error => console.error("Error loading provinces:", error));

    // 2. On Province Select
    $('#provinsi').on('change select2:select', function (e) {
        const provName = $(this).val();
    if (!provName) {
        resetDropdown($('#kabupaten'), "Pilih Kabupaten");
        resetDropdown($('#kecamatan'), "Pilih Kecamatan");
        resetDropdown($('#kelurahan'), "Pilih Kelurahan");
        return;
    }
    
    if (!window.wilayahProvinces) {
        if (typeof Swal !== 'undefined') Swal.fire('Error', 'Data provinsi belum selesai dimuat', 'error');
        return;
    }
    
    const prov = window.wilayahProvinces.find(r => r.name.toUpperCase().trim() === provName.trim());
    if (!prov) {
        if (typeof Swal !== 'undefined') Swal.fire('Error', 'Data provinsi tidak cocok: ' + provName, 'error');
        return;
    }

    resetDropdown($('#kabupaten'), "Loading...");
    $('#kabupaten').prop('disabled', false); // visually enable while loading
    
    fetch(`${wilayahApiBase}/regencies/${prov.code}`)
        .then(response => {
            if (!response.ok) throw new Error("HTTP " + response.status);
            return response.json();
        })
        .then(json => {
            window.wilayahRegencies = json.data;
            fillDropdown($('#kabupaten'), json.data, "Pilih Kabupaten", $('#kabupaten').val());
        })
        .catch(error => {
            console.error("Error loading regencies:", error);
            if (typeof Swal !== 'undefined') Swal.fire('Error API', 'Gagal memuat kabupaten: ' + error, 'error');
        });
    });

    // 3. On Regency Select
    $('#kabupaten').on('change select2:select', function (e) {
        const regencyName = $(this).val();
    if (!regencyName) {
        resetDropdown($('#kecamatan'), "Pilih Kecamatan");
        resetDropdown($('#kelurahan'), "Pilih Kelurahan");
        return;
    }
    
    if (!window.wilayahRegencies) return;
    
    const regency = window.wilayahRegencies.find(r => r.name.toUpperCase().trim() === regencyName.trim());
    if (!regency) {
        if (typeof Swal !== 'undefined') Swal.fire('Error', 'Data kabupaten tidak cocok: ' + regencyName, 'error');
        return;
    }

    resetDropdown($('#kecamatan'), "Loading...");
    $('#kecamatan').prop('disabled', false);
    
    fetch(`${wilayahApiBase}/districts/${regency.code}`)
        .then(response => {
            if (!response.ok) throw new Error("HTTP " + response.status);
            return response.json();
        })
        .then(json => {
            window.wilayahDistricts = json.data;
            fillDropdown($('#kecamatan'), json.data, "Pilih Kecamatan", $('#kecamatan').val());
        })
        .catch(error => {
            console.error("Error loading districts:", error);
            if (typeof Swal !== 'undefined') Swal.fire('Error API', 'Gagal memuat kecamatan: ' + error, 'error');
        });
    });

    // 4. On District Select
    $('#kecamatan').on('change select2:select', function (e) {
        const districtName = $(this).val();
    if (!districtName) {
        resetDropdown($('#kelurahan'), "Pilih Kelurahan");
        return;
    }
    
    if (!window.wilayahDistricts) return;
    
    const district = window.wilayahDistricts.find(d => d.name.toUpperCase().trim() === districtName.trim());
    if (!district) {
        if (typeof Swal !== 'undefined') Swal.fire('Error', 'Data kecamatan tidak cocok: ' + districtName, 'error');
        return;
    }

    resetDropdown($('#kelurahan'), "Loading...");
    $('#kelurahan').prop('disabled', false);
    
    fetch(`${wilayahApiBase}/villages/${district.code}`)
        .then(response => {
            if (!response.ok) throw new Error("HTTP " + response.status);
            return response.json();
        })
        .then(json => {
            window.wilayahVillages = json.data;
            fillDropdown($('#kelurahan'), json.data, "Pilih Kelurahan", $('#kelurahan').val());
        })
        .catch(error => {
            console.error("Error loading villages:", error);
            if (typeof Swal !== 'undefined') Swal.fire('Error API', 'Gagal memuat kelurahan: ' + error, 'error');
        });
    });

    // Handle Update Status Submit
    const formUpdateStatus = document.getElementById('kt_modal_update_status_form');
    formUpdateStatus.addEventListener('submit', function (e) {
        e.preventDefault();
        
        const submitButton = document.getElementById('kt_modal_update_status_submit');
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;

        const id = document.getElementById('update_status_client_id').value;
        const payload = {
            status_client: document.getElementById('new_status_client').value
        };

        fetch(`${apiUrl}/clients/${id}/status`, {
            method: 'PATCH',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => {
            submitButton.removeAttribute('data-kt-indicator');
            submitButton.disabled = false;
            
            if (response.ok) {
                $('#kt_modal_update_status').modal('hide');
                datatable.ajax.reload(null, false); // reload without resetting pagination
                Swal.fire({
                    text: "Status berhasil diupdate!",
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: { confirmButton: "btn btn-primary" }
                });
            } else {
                response.json().then(err => {
                    Swal.fire({
                        text: err.message || "Gagal mengupdate status.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: { confirmButton: "btn btn-danger" }
                    });
                });
            }
        })
        .catch(error => {
            submitButton.removeAttribute('data-kt-indicator');
            submitButton.disabled = false;
            console.error('Error updating status:', error);
        });
    });

});

// Global functions for inline onclick handlers
window.updateStatusClient = function(id, currentStatus) {
    document.getElementById('update_status_client_id').value = id;
    
    let statusValue = currentStatus;
    if (statusValue === "null" || statusValue === "undefined" || !statusValue) statusValue = "";
    $('#new_status_client').val(statusValue).trigger('change');
    $('#kt_modal_update_status').modal('show');
}

window.resetForm = function() {
    document.getElementById('kt_modal_add_client_form').reset();
    document.getElementById('client_id').value = "";
    document.getElementById('modal_title').innerText = "Tambah Client";
    document.getElementById('status_client').value = "";
    document.getElementById('sumber_data').value = "1";
    $('#provinsi').val(null).trigger('change.select2');
    resetDropdown($('#kabupaten'), "Pilih Kabupaten");
    resetDropdown($('#kecamatan'), "Pilih Kecamatan");
    resetDropdown($('#kelurahan'), "Pilih Kelurahan");
}

window.editClient = function(client) {
    resetForm();
    document.getElementById('modal_title').innerText = "Edit Client";
    document.getElementById('client_id').value = client.id;
    document.getElementById('nama').value = client.nama;
    document.getElementById('alamat').value = client.alamat || '';
    
    if (client.kabupaten) {
        $('#kabupaten').empty().append('<option></option>').append(new Option(client.kabupaten, client.kabupaten, true, true)).prop('disabled', false);

        $('#kabupaten').trigger('change.select2');
        
        $('#kecamatan').empty().append('<option></option>').append(new Option(client.kecamatan, client.kecamatan, true, true)).prop('disabled', false);

        $('#kecamatan').trigger('change.select2');
        
        $('#kelurahan').empty().append('<option></option>').append(new Option(client.kelurahan, client.kelurahan, true, true)).prop('disabled', false);
        $('#kelurahan').trigger('change.select2');
    }

    document.getElementById('nomor_telepon').value = client.nomor_telepon || '';
    document.getElementById('sumber_data').value = client.sumber_data;
    
    let statusClient = client.status_client;
    if (statusClient === "null" || statusClient === "undefined" || !statusClient) statusClient = "";
    $('#status_client').val(statusClient).trigger('change');
    
    $('#kt_modal_add_client').modal('show');
}

window.deleteClient = function(id) {
    Swal.fire({
        text: "Hapus client ini?", icon: "warning", showCancelButton: true,
        confirmButtonText: "Ya", cancelButtonText: "Batal",
        customClass: { confirmButton: "btn btn-danger", cancelButton: "btn btn-active-light" }
    }).then(function (result) {
        if (result.value) {
            const token = localStorage.getItem('jwt_token');
            const apiUrl = '{{ env("APP_URL") }}' + '/api';
            fetch(`${apiUrl}/clients/${id}`, {
                method: 'DELETE', headers: { 'Authorization': `Bearer ${token}` }
            }).then(response => {
                if (response.ok) {
                    $('#kt_table_clients').DataTable().ajax.reload();
                    Swal.fire({ text: "Berhasil dihapus.", icon: "success", customClass: { confirmButton: "btn btn-primary" } });
                }
            });
        }
    });
}

window.openBulkDisposisiModal = function() {
    $('#disposisi_user_id').val("").trigger('change');
    $('#kt_modal_bulk_disposisi').modal('show');
}

window.detailClient = function(client) {
    document.getElementById('detail_nama').innerText = client.nama || '-';
    document.getElementById('detail_nomor_telepon').innerText = client.nomor_telepon || '-';
    document.getElementById('detail_alamat').innerText = client.alamat || '-';
    document.getElementById('detail_kelurahan').innerText = client.kelurahan || '-';
    document.getElementById('detail_kecamatan').innerText = client.kecamatan || '-';
    document.getElementById('detail_kabupaten').innerText = client.kabupaten || '-';
    document.getElementById('detail_sumber_data').innerText = client.sumber_data == 1 ? '1 - HO' : '2 - Anggota';
    document.getElementById('detail_status').innerHTML = client.status && client.status.nama_status
        ? `<span class="badge badge-light-primary">${client.status.nama_status}</span>`
        : `<span class="badge badge-light-secondary">Belum ada</span>`;
    document.getElementById('detail_user').innerText = client.user ? client.user.nama : 'Belum didisposisikan';
    
    $('#kt_modal_detail_client').modal('show');
}

// API Wilayah Indonesia (Lokal)
const wilayahApiBase = '{{ url('/api/wilayah') }}';

function resetDropdown($el, placeholderText) {
    $el.empty().append('<option></option>').val(null);
    $el.prop('disabled', true);
    $el.trigger('change.select2');
}

function fillDropdown($el, dataArray, placeholderText, selectedValue) {
    $el.empty().append('<option></option>');
    dataArray.forEach(item => {
        const name = item.name.toUpperCase().trim();
        $el.append(new Option(name, name, false, false));
    });
    
    $el.prop('disabled', false);
    if (selectedValue && $el.find(`option[value="${selectedValue}"]`).length > 0) {
        $el.val(selectedValue);
    } else {
        $el.val(null);
    }
    
    $el.trigger('change.select2');
}

</script>
@endsection
