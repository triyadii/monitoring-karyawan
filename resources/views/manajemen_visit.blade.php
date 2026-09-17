@extends('layouts.app')

@section('title', 'Manajemen Visit')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        
        <div class="card card-flush">
            <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <input type="text" data-kt-visit-table-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Cari Visit..." />
                    </div>
                </div>
                <div class="card-toolbar" id="btn_add_container" style="display: none;">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_visit" onclick="resetForm()">
                        <i class="ki-duotone ki-plus fs-2"></i> Tambah Visit
                    </button>
                </div>
            </div>
            
            <div class="card-body pt-0">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_visits">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-125px">Nama Client</th>
                            <th class="min-w-125px">Nomor Telepon</th>
                            <th class="min-w-150px">Kegiatan</th>
                            <th class="min-w-125px">Foto</th>
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

<!-- Modal Add/Edit Visit -->
<div class="modal fade" id="kt_modal_add_visit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_visit_header">
                <h2 class="fw-bold" id="modal_title">Tambah Visit</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_add_visit_form" class="form" action="#" enctype="multipart/form-data">
                    <input type="hidden" id="visit_uuid" name="uuid" value="" />

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
                        <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                            <span>Kegiatan</span>
                        </label>
                        <textarea class="form-control form-control-solid" rows="3" placeholder="Deskripsi Kegiatan" name="kegiatan" id="kegiatan"></textarea>
                    </div>

                    <div class="d-flex flex-column mb-7 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                            <span>Foto (Max 3, format: jpeg, png, jpg)</span>
                        </label>
                        <input type="file" class="form-control form-control-solid" name="foto[]" id="foto" accept=".jpg, .jpeg, .png" multiple />
                        <div class="text-muted fs-7 mt-2">Pilih maksimal 3 foto. Memilih foto baru akan menimpa foto yang ada sebelumnya (pada saat edit).</div>
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
                        <button type="submit" id="kt_modal_add_visit_submit" class="btn btn-primary">
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

<!-- Modal Detail Visit -->
<div class="modal fade" id="kt_modal_detail_visit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Detail Visit</h2>
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

                    <div class="d-flex flex-column mb-5">
                        <h4 class="text-gray-900 mb-1">Kegiatan</h4>
                        <div class="separator mb-3"></div>
                        <div class="bg-light-info rounded p-4 text-gray-800" id="detail_kegiatan" style="min-height: 80px; white-space: pre-wrap;"></div>
                    </div>

                    <div class="d-flex flex-column mb-5">
                        <h4 class="text-gray-900 mb-1">Lampiran Foto</h4>
                        <div class="separator mb-3"></div>
                        <div id="detail_foto" class="d-flex flex-wrap gap-4 mt-2">
                            <!-- Foto di-generate dari JS -->
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
    const baseUrl = '{{ env("APP_URL") }}';

    // Role-based logic
    let hasCrudAccess = false;
    const userDataStr = localStorage.getItem('user_data');
    if (userDataStr) {
        try {
            const ud = JSON.parse(userDataStr);
            const role = ud.role_name || (ud.role && ud.role.nama_role) || '';
            const roleLower = role.toLowerCase();
            
            if (roleLower === 'channeling' || roleLower === 'superadmin' || roleLower === 'leader') {
                hasCrudAccess = true;
                document.getElementById('btn_add_container').style.display = 'block';
            }
        } catch(e) {}
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
    var datatable = $('#kt_table_visits').DataTable({
        ajax: {
            url: `${apiUrl}/manajemen-visit`,
            type: 'GET',
            headers: { 'Authorization': `Bearer ${token}` },
            dataSrc: ''
        },
        columns: [
            { data: 'namaClient', defaultContent: '-' },
            { data: 'nomorTelepon', defaultContent: '-' },
            { data: 'kegiatan', defaultContent: '-', render: function(data) {
                if(!data) return '-';
                return data.length > 30 ? data.substr(0, 30) + '...' : data;
            }},
            { data: 'foto', orderable: false, render: function(data) {
                if (data && data.length > 0) {
                    let images = '';
                    data.forEach(img => {
                        images += `<img src="${baseUrl}/${img}" alt="Foto" class="w-35px h-35px rounded me-1" style="object-fit:cover;" />`;
                    });
                    return `<div class="d-flex align-items-center">${images}</div>`;
                }
                return '-';
            }},
            { data: 'user', defaultContent: null, render: function(data) {
                return data ? data.nama : '-';
            }},
            { data: 'status', defaultContent: null, render: function(data) {
                return data ? `<span class="badge badge-light-primary">${data.nama_status}</span>` : '-';
            }},
            { data: 'uuid', orderable: false, render: function(data, type, row) {
                let actions = `
                    <button class="btn btn-icon btn-sm btn-light-info me-2" onclick='showDetailVisit(${JSON.stringify(row).replace(/'/g, "&apos;")})' title="Detail">
                        <i class="ki-duotone ki-eye fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                    </button>`;
                
                if (hasCrudAccess) {
                    actions += `
                        <button class="btn btn-icon btn-sm btn-light-primary me-2" onclick='editVisit(${JSON.stringify(row).replace(/'/g, "&apos;")})' title="Edit">
                            <i class="ki-duotone ki-pencil fs-3"><span class="path1"></span><span class="path2"></span></i>
                        </button>
                        <button class="btn btn-icon btn-sm btn-light-danger" onclick="deleteVisit('${data}')" title="Delete">
                            <i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        </button>`;
                }
                return actions;
            }}
        ]
    });

    // Search functionality
    $('[data-kt-visit-table-filter="search"]').on('keyup', function () {
        datatable.search(this.value).draw();
    });

    // Handle Form Submit
    const form = document.getElementById('kt_modal_add_visit_form');
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const submitButton = document.getElementById('kt_modal_add_visit_submit');
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;

        const uuid = document.getElementById('visit_uuid').value;
        const method = 'POST'; // Since we're using multipart/form-data for files, always use POST
        let url = uuid ? `${apiUrl}/manajemen-visit/${uuid}` : `${apiUrl}/manajemen-visit`;

        let formData = new FormData();
        formData.append('namaClient', document.getElementById('namaClient').value);
        formData.append('nomorTelepon', document.getElementById('nomorTelepon').value);
        formData.append('alamat', document.getElementById('alamat').value);
        formData.append('status_id', document.getElementById('status_id').value);
        formData.append('kegiatan', document.getElementById('kegiatan').value);
        
        if (uuid) {
            // formData.append('_method', 'PUT'); // Tidak digunakan karena controller diset ke Route::post
        }

        const files = document.getElementById('foto').files;
        for (let i = 0; i < files.length; i++) {
            formData.append('foto[]', files[i]);
        }

        fetch(url, {
            method: method,
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
                // Do not set Content-Type, fetch will boundary automatically for FormData
            },
            body: formData
        })
        .then(response => {
            submitButton.removeAttribute('data-kt-indicator');
            submitButton.disabled = false;

            if (response.ok) {
                $('#kt_modal_add_visit').modal('hide');
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
    document.getElementById('kt_modal_add_visit_form').reset();
    document.getElementById('visit_uuid').value = "";
    document.getElementById('modal_title').innerText = "Tambah Visit";
}

window.editVisit = function(visit) {
    resetForm();
    document.getElementById('modal_title').innerText = "Edit Visit";
    document.getElementById('visit_uuid').value = visit.uuid;
    document.getElementById('namaClient').value = visit.namaClient;
    document.getElementById('nomorTelepon').value = visit.nomorTelepon;
    document.getElementById('alamat').value = visit.alamat;
    document.getElementById('kegiatan').value = visit.kegiatan || "";
    document.getElementById('status_id').value = visit.status_id;
    
    $('#kt_modal_add_visit').modal('show');
}

window.deleteVisit = function(uuid) {
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
            
            fetch(`${apiUrl}/manajemen-visit/${uuid}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    $('#kt_table_visits').DataTable().ajax.reload();
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

window.showDetailVisit = function(visit) {
    const baseUrl = '{{ env("APP_URL") }}';
    document.getElementById('detail_nama_client').innerText = visit.namaClient || '-';
    document.getElementById('detail_nomor_telepon').innerText = visit.nomorTelepon || '-';
    document.getElementById('detail_alamat').innerText = visit.alamat || '-';
    document.getElementById('detail_kegiatan').innerText = visit.kegiatan || '-';
    
    let pembuatName = visit.user ? visit.user.nama : '-';
    document.getElementById('detail_pembuat').innerText = pembuatName;
    document.getElementById('detail_pembuat_initial').innerText = pembuatName !== '-' ? pembuatName.charAt(0).toUpperCase() : '?';
    
    document.getElementById('detail_status').innerHTML = visit.status 
        ? `<span class="badge badge-light-primary">${visit.status.nama_status}</span>` 
        : `-`;
        
    const fotoContainer = document.getElementById('detail_foto');
    fotoContainer.innerHTML = '';
    if (visit.foto && visit.foto.length > 0) {
        visit.foto.forEach(img => {
            const a = document.createElement('a');
            a.href = `${baseUrl}/${img}`;
            a.target = '_blank';
            a.className = 'd-block border border-gray-300 rounded overflow-hidden';
            a.innerHTML = `<img src="${baseUrl}/${img}" alt="Foto" class="w-100px h-100px" style="object-fit:cover;" />`;
            fotoContainer.appendChild(a);
        });
    } else {
        fotoContainer.innerHTML = '<span class="text-gray-500">Tidak ada foto</span>';
    }

    $('#kt_modal_detail_visit').modal('show');
}
</script>
@endsection
