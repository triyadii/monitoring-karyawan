@extends('layouts.app')

@section('title', 'Manajemen PPD')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        
        <div id="anggota_list_container" style="display: none;">
            <div class="card card-flush">
                <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                    <div class="card-title">
                        <h3 class="fw-bold">Daftar Anggota PPD</h3>
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
                            <input type="text" data-kt-ppd-table-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Cari PPD..." />
                        </div>
                        <div class="d-flex align-items-center gap-2 my-1 ms-3">
                            <input type="date" id="filter_tanggal" class="form-control form-control-solid w-150px" title="Filter Tanggal" />
                            <input type="text" id="filter_nama" class="form-control form-control-solid w-200px" placeholder="Filter Nama..." />
                            <button type="button" class="btn btn-primary btn-sm" onclick="applyFilters()">Filter</button>
                            <button type="button" class="btn btn-light btn-sm" onclick="resetFilters()">Reset</button>
                        </div>
                    </div>
                    <div class="card-toolbar" id="btn_add_container" style="display: none;">
                        <button type="button" class="btn btn-success me-3" onclick="exportData('ppd')">
                            <i class="ki-duotone ki-file-down fs-2"></i> Export Data
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_ppd" onclick="resetForm()">
                            <i class="ki-duotone ki-plus fs-2"></i> Tambah PPD
                        </button>
                    </div>
                </div>
                
                <div class="card-body pt-0">
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_ppds" style="width: 100%">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-125px">Nama Client</th>
                                <th class="min-w-125px">Nomor Telepon</th>
                                <th class="min-w-125px">Nomor Kontrak</th>
                                <th class="min-w-100px">KTP</th>
                                <th class="min-w-125px">Dibuat Oleh</th>
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

<!-- Modal Add/Edit PPD -->
<div class="modal fade" id="kt_modal_add_ppd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-800px">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_ppd_header">
                <h2 class="fw-bold" id="modal_title">Tambah PPD</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_add_ppd_form" class="form" action="#" enctype="multipart/form-data">
                    <input type="hidden" id="ppd_uuid" name="uuid" value="" />

                    <div class="row g-5">
                        <div class="col-md-6 d-flex flex-column mb-5 fv-row">
                            <label class="required d-flex align-items-center fs-6 fw-semibold form-label mb-2">Nama Client</label>
                            <input type="text" class="form-control form-control-solid" placeholder="Nama Client" name="namaClient" id="namaClient" required />
                        </div>
                        <div class="col-md-6 d-flex flex-column mb-5 fv-row">
                            <label class="required d-flex align-items-center fs-6 fw-semibold form-label mb-2">Nomor Telepon</label>
                            <input type="text" class="form-control form-control-solid" placeholder="Nomor Telepon" name="nomorTelepon" id="nomorTelepon" required />
                        </div>
                    </div>

                    <div class="d-flex flex-column mb-5 fv-row">
                        <label class="required d-flex align-items-center fs-6 fw-semibold form-label mb-2">Alamat</label>
                        <textarea class="form-control form-control-solid" rows="2" placeholder="Alamat Lengkap" name="alamat" id="alamat" required></textarea>
                    </div>

                    <div class="row g-5">
                        <div class="col-md-6 d-flex flex-column mb-5 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">Nomor Kontrak</label>
                            <input type="text" class="form-control form-control-solid" placeholder="Nomor Kontrak" name="nomorKontrak" id="nomorKontrak" />
                        </div>
                        <div class="col-md-6 d-flex flex-column mb-5 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">Tenor</label>
                            <input type="text" class="form-control form-control-solid" placeholder="Misal: 12 Bulan" name="tenor" id="tenor" />
                        </div>
                    </div>

                    <div class="row g-5">
                        <div class="col-md-6 d-flex flex-column mb-5 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">Angsuran</label>
                            <input type="text" class="form-control form-control-solid" placeholder="Misal: Rp 1.500.000" name="angsuran" id="angsuran" />
                        </div>
                        <div class="col-md-6 d-flex flex-column mb-5 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">Pinjaman</label>
                            <input type="text" class="form-control form-control-solid" placeholder="Misal: Rp 15.000.000" name="pinjaman" id="pinjaman" />
                        </div>
                    </div>

                    <div class="row g-5">
                        <div class="col-md-4 d-flex flex-column mb-5 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">Merk</label>
                            <input type="text" class="form-control form-control-solid" placeholder="Merk Kendaraan" name="merk" id="merk" />
                        </div>
                        <div class="col-md-4 d-flex flex-column mb-5 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">Type</label>
                            <input type="text" class="form-control form-control-solid" placeholder="Tipe Kendaraan" name="type" id="type" />
                        </div>
                        <div class="col-md-4 d-flex flex-column mb-5 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">Jenis Kendaraan</label>
                            <input type="text" class="form-control form-control-solid" placeholder="Jenis Kendaraan" name="jenisKendaraan" id="jenisKendaraan" />
                        </div>
                    </div>

                    <div class="row g-5">
                        <div class="col-md-6 d-flex flex-column mb-5 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">Jatuh Tempo</label>
                            <input type="date" class="form-control form-control-solid" name="jatuhTempo" id="jatuhTempo" />
                        </div>
                        <div class="col-md-6 d-flex flex-column mb-5 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">Foto KTP</label>
                            <input type="file" class="form-control form-control-solid" name="ktp" id="ktp" accept=".jpg, .jpeg, .png" />
                        </div>
                    </div>



                    <div class="text-center pt-10">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="kt_modal_add_ppd_submit" class="btn btn-primary">
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

<!-- Modal Detail PPD -->
<div class="modal fade" id="kt_modal_detail_ppd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-800px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Detail PPD</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <div class="row mb-5">
                    <div class="col-md-6">
                        <h4 class="text-gray-900 mb-1">Informasi Client</h4>
                        <div class="separator mb-3"></div>
                        <div class="row mb-2">
                            <div class="col-sm-5 text-gray-500 fw-bold">Nama Client</div>
                            <div class="col-sm-7 text-gray-800 fw-bold" id="detail_nama_client"></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-5 text-gray-500 fw-bold">Nomor Telepon</div>
                            <div class="col-sm-7 text-gray-800" id="detail_nomor_telepon"></div>
                        </div>
                        <div class="row mb-2">
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-5 text-gray-500 fw-bold">Dibuat Oleh</div>
                            <div class="col-sm-7 text-gray-800" id="detail_pembuat"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h4 class="text-gray-900 mb-1">Data Pinjaman</h4>
                        <div class="separator mb-3"></div>
                        <div class="row mb-2">
                            <div class="col-sm-5 text-gray-500 fw-bold">No. Kontrak</div>
                            <div class="col-sm-7 text-gray-800" id="detail_nomor_kontrak"></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-5 text-gray-500 fw-bold">Tenor</div>
                            <div class="col-sm-7 text-gray-800" id="detail_tenor"></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-5 text-gray-500 fw-bold">Angsuran</div>
                            <div class="col-sm-7 text-gray-800" id="detail_angsuran"></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-5 text-gray-500 fw-bold">Pinjaman</div>
                            <div class="col-sm-7 text-gray-800" id="detail_pinjaman"></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-5 text-gray-500 fw-bold">Jatuh Tempo</div>
                            <div class="col-sm-7 text-gray-800" id="detail_jatuh_tempo"></div>
                        </div>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-md-12">
                        <h4 class="text-gray-900 mb-1">Data Kendaraan & Alamat</h4>
                        <div class="separator mb-3"></div>
                        <div class="row mb-2">
                            <div class="col-sm-3 text-gray-500 fw-bold">Merk</div>
                            <div class="col-sm-9 text-gray-800" id="detail_merk"></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-3 text-gray-500 fw-bold">Type</div>
                            <div class="col-sm-9 text-gray-800" id="detail_type"></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-3 text-gray-500 fw-bold">Jenis Kendaraan</div>
                            <div class="col-sm-9 text-gray-800" id="detail_jenis_kendaraan"></div>
                        </div>
                        <div class="row mb-2 mt-4">
                            <div class="col-sm-3 text-gray-500 fw-bold">Alamat</div>
                            <div class="col-sm-9 text-gray-800 bg-light p-3 rounded" id="detail_alamat"></div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column mb-5">
                    <h4 class="text-gray-900 mb-1">Foto KTP</h4>
                    <div class="separator mb-3"></div>
                    <div id="detail_ktp" class="d-flex flex-wrap gap-4 mt-2">
                        <!-- Foto di-generate dari JS -->
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
    const baseUrl = '{{ env("APP_URL") }}';

    // Role-based logic for PPD
    let hasCrudAccess = false;
    let isSuperAdminOrLeader = false;
    let selectedUserId = null;
    let anggotaDatatable = null;

    const userDataStr = localStorage.getItem('user_data');
    if (userDataStr) {
        try {
            const ud = JSON.parse(userDataStr);
            const role = ud.role_name || (ud.role && ud.role.nama_role) || '';
            const jp = ud.jenis_pegawai_name || (ud.jenisPegawai && ud.jenisPegawai.jenisPegawai) || '';
            const roleLower = String(role).toLowerCase().trim();
            const jpLower = String(jp).toLowerCase().trim();
            
            if (roleLower === 'superadmin' || roleLower === 'leader' || jpLower === 'cco' || jpLower === 'cro') {
                hasCrudAccess = true;
            }
            if (roleLower === 'superadmin' || roleLower === 'leader') {
                isSuperAdminOrLeader = true;
            }
        } catch(e) {
            console.error("Error parsing user data:", e);
        }
    }

    if (isSuperAdminOrLeader) {
        document.getElementById('main_table_container').style.display = 'none';
        document.getElementById('anggota_list_container').style.display = 'block';

        anggotaDatatable = $('#kt_table_anggota').DataTable({
            ajax: {
                url: `${apiUrl}/member-stats/ppd`,
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

    // Initialize DataTable
    var datatable = $('#kt_table_ppds').DataTable({
        ajax: {
            url: `${apiUrl}/manajemen-ppd`,
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
            { data: 'nomorKontrak', defaultContent: '-' },
            { data: 'ktp', orderable: false, render: function(data) {
                if (data) {
                    return `<a href="${baseUrl}/${data}" target="_blank">
                                <img src="${baseUrl}/${data}" alt="KTP" class="w-40px h-40px rounded" style="object-fit:cover;" />
                            </a>`;
                }
                return '-';
            }},
            { data: 'user', defaultContent: null, render: function(data) {
                return data ? data.nama : '-';
            }},
            { data: 'uuid', orderable: false, render: function(data, type, row) {
                let actions = `
                    <button class="btn btn-icon btn-sm btn-light-info me-2" onclick='showDetailPPD(${JSON.stringify(row).replace(/'/g, "&apos;")})' title="Detail">
                        <i class="ki-duotone ki-eye fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                    </button>`;
                
                if (hasCrudAccess) {
                    actions += `
                        <button class="btn btn-icon btn-sm btn-light-primary me-2" onclick='editPPD(${JSON.stringify(row).replace(/'/g, "&apos;")})' title="Edit">
                            <i class="ki-duotone ki-pencil fs-3"><span class="path1"></span><span class="path2"></span></i>
                        </button>
                        <button class="btn btn-icon btn-sm btn-light-danger" onclick="deletePPD('${data}')" title="Delete">
                            <i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        </button>`;
                }
                return actions;
            }}
        ]
    });

    // Search functionality
    $('[data-kt-ppd-table-filter="search"]').on('keyup', function () {
        datatable.search(this.value).draw();
    });

    // Handle Form Submit
    const form = document.getElementById('kt_modal_add_ppd_form');
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const submitButton = document.getElementById('kt_modal_add_ppd_submit');
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;

        const uuid = document.getElementById('ppd_uuid').value;
        const method = 'POST'; // Since multipart is used, API is set up to handle POST for updates
        let url = uuid ? `${apiUrl}/manajemen-ppd/${uuid}` : `${apiUrl}/manajemen-ppd`;

        let formData = new FormData();
        formData.append('namaClient', document.getElementById('namaClient').value);
        formData.append('nomorTelepon', document.getElementById('nomorTelepon').value);
        formData.append('alamat', document.getElementById('alamat').value);
        formData.append('nomorKontrak', document.getElementById('nomorKontrak').value);
        formData.append('tenor', document.getElementById('tenor').value);
        formData.append('angsuran', document.getElementById('angsuran').value);
        formData.append('merk', document.getElementById('merk').value);
        formData.append('type', document.getElementById('type').value);
        formData.append('jenisKendaraan', document.getElementById('jenisKendaraan').value);
        formData.append('pinjaman', document.getElementById('pinjaman').value);
        formData.append('jatuhTempo', document.getElementById('jatuhTempo').value);
        
        const ktpFile = document.getElementById('ktp').files[0];
        if (ktpFile) {
            formData.append('ktp', ktpFile);
        }

        fetch(url, {
            method: method,
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            submitButton.removeAttribute('data-kt-indicator');
            submitButton.disabled = false;

            if (response.ok) {
                $('#kt_modal_add_ppd').modal('hide');
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
    document.getElementById('kt_modal_add_ppd_form').reset();
    document.getElementById('ppd_uuid').value = "";
    document.getElementById('modal_title').innerText = "Tambah PPD";
}

window.editPPD = function(ppd) {
    resetForm();
    document.getElementById('modal_title').innerText = "Edit PPD";
    document.getElementById('ppd_uuid').value = ppd.uuid;
    document.getElementById('namaClient').value = ppd.namaClient || "";
    document.getElementById('nomorTelepon').value = ppd.nomorTelepon || "";
    document.getElementById('alamat').value = ppd.alamat || "";
    document.getElementById('nomorKontrak').value = ppd.nomorKontrak || "";
    document.getElementById('tenor').value = ppd.tenor || "";
    document.getElementById('angsuran').value = ppd.angsuran || "";
    document.getElementById('merk').value = ppd.merk || "";
    document.getElementById('type').value = ppd.type || "";
    document.getElementById('jenisKendaraan').value = ppd.jenisKendaraan || "";
    document.getElementById('pinjaman').value = ppd.pinjaman || "";
    document.getElementById('jatuhTempo').value = ppd.jatuhTempo ? ppd.jatuhTempo.split('T')[0] : "";
    
    $('#kt_modal_add_ppd').modal('show');
}

window.deletePPD = function(uuid) {
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
            
            fetch(`${apiUrl}/manajemen-ppd/${uuid}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    $('#kt_table_ppds').DataTable().ajax.reload();
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

window.showDetailPPD = function(ppd) {
    const baseUrl = '{{ env("APP_URL") }}';
    
    document.getElementById('detail_nama_client').innerText = ppd.namaClient || '-';
    document.getElementById('detail_nomor_telepon').innerText = ppd.nomorTelepon || '-';
    document.getElementById('detail_alamat').innerText = ppd.alamat || '-';
    
    document.getElementById('detail_nomor_kontrak').innerText = ppd.nomorKontrak || '-';
    document.getElementById('detail_tenor').innerText = ppd.tenor || '-';
    document.getElementById('detail_angsuran').innerText = ppd.angsuran || '-';
    document.getElementById('detail_pinjaman').innerText = ppd.pinjaman || '-';
    document.getElementById('detail_jatuh_tempo').innerText = ppd.jatuhTempo || '-';
    
    document.getElementById('detail_merk').innerText = ppd.merk || '-';
    document.getElementById('detail_type').innerText = ppd.type || '-';
    document.getElementById('detail_jenis_kendaraan').innerText = ppd.jenisKendaraan || '-';
    
    document.getElementById('detail_pembuat').innerText = ppd.user ? ppd.user.nama : '-';
        
    const ktpContainer = document.getElementById('detail_ktp');
    if (ppd.ktp) {
        ktpContainer.innerHTML = `
            <a href="${baseUrl}/${ppd.ktp}" target="_blank" class="d-block border border-gray-300 rounded overflow-hidden">
                <img src="${baseUrl}/${ppd.ktp}" alt="KTP" class="w-200px h-150px" style="object-fit:cover;" />
            </a>
        `;
    } else {
        ktpContainer.innerHTML = '<span class="text-gray-500">Tidak ada KTP terlampir</span>';
    }

    $('#kt_modal_detail_ppd').modal('show');
}

window.applyFilters = function() {
    $('#kt_table_ppds').DataTable().ajax.reload();
}

window.resetFilters = function() {
    document.getElementById('filter_tanggal').value = '';
    document.getElementById('filter_nama').value = '';
    $('#kt_table_ppds').DataTable().ajax.reload();
}

window.exportData = function(module) {
    const token = localStorage.getItem('jwt_token');
    const filterTanggal = document.getElementById('filter_tanggal')?.value || '';
    const filterNama = document.getElementById('filter_nama')?.value || '';
    
    let url = `{{ url('/api/export/manajemen-ppd') }}?`;
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
        a.download = 'manajemen_ppd.xlsx';
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
    $('#kt_table_ppds').DataTable().ajax.reload();
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
