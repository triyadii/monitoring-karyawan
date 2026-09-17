@extends('layouts.app')

@section('title', 'Manajemen Jenis Pegawai')

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
                        <input type="text" data-kt-jenis-pegawai-table-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Cari Jenis Pegawai..." />
                    </div>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_jenis_pegawai" onclick="resetForm()">
                        <i class="ki-duotone ki-plus fs-2"></i> Tambah Jenis Pegawai
                    </button>
                </div>
            </div>
            
            <div class="card-body pt-0">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_jenis_pegawai">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-125px">Nama Jenis Pegawai</th>
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

<!-- Modal Add/Edit Jenis Pegawai -->
<div class="modal fade" id="kt_modal_add_jenis_pegawai" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_jenis_pegawai_header">
                <h2 class="fw-bold" id="modal_title">Tambah Jenis Pegawai</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_add_jenis_pegawai_form" class="form" action="#">
                    <input type="hidden" id="jenis_pegawai_uuid" name="uuid" value="" />
                    
                    <div class="d-flex flex-column mb-7 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                            <span class="required">Nama Jenis Pegawai</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" placeholder="Nama Jenis Pegawai" name="jenisPegawai" id="jenisPegawai" required />
                    </div>

                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="kt_modal_add_jenis_pegawai_submit" class="btn btn-primary">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const token = localStorage.getItem('jwt_token');
    const userDataStr = localStorage.getItem('user_data');
    
    if (!token || !userDataStr) {
        window.location.href = '{{ route('login') }}';
        return;
    }

    const userData = JSON.parse(userDataStr);
    const roleName = userData.role_name ? userData.role_name.toLowerCase() : '';
    if (roleName !== 'superadmin' && roleName !== 'leader') {
        window.location.href = '{{ route('dashboard') }}';
        return;
    }

    const apiUrl = '{{ env("APP_URL") }}' + '/api';

    // 1. Initialize DataTable
    var datatable = $('#kt_table_jenis_pegawai').DataTable({
        ajax: {
            url: `${apiUrl}/jenis-pegawai`,
            type: 'GET',
            headers: { 'Authorization': `Bearer ${token}` },
            dataSrc: ''
        },
        columns: [
            { data: 'jenisPegawai' },
            { data: 'uuid', orderable: false, render: function(data, type, row) {
                return `
                    <div class="d-flex justify-content-end flex-shrink-0">
                        <button class="btn btn-icon btn-sm btn-light-primary me-2" onclick='editJenisPegawai(${JSON.stringify(row).replace(/'/g, "&apos;")})' title="Edit">
                            <i class="ki-duotone ki-pencil fs-3"><span class="path1"></span><span class="path2"></span></i>
                        </button>
                        <button class="btn btn-icon btn-sm btn-light-danger" onclick="deleteJenisPegawai('${data}')" title="Delete">
                            <i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        </button>
                    </div>
                `;
            }}
        ]
    });

    // Search functionality
    $('[data-kt-jenis-pegawai-table-filter="search"]').on('keyup', function () {
        datatable.search(this.value).draw();
    });

    // 2. Handle Form Submit (Create & Update)
    const form = document.getElementById('kt_modal_add_jenis_pegawai_form');
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const submitButton = document.getElementById('kt_modal_add_jenis_pegawai_submit');
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;

        const uuid = document.getElementById('jenis_pegawai_uuid').value;
        const method = uuid ? 'PUT' : 'POST';
        const url = uuid ? `${apiUrl}/jenis-pegawai/${uuid}` : `${apiUrl}/jenis-pegawai`;

        const payload = {
            jenisPegawai: document.getElementById('jenisPegawai').value
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
                $('#kt_modal_add_jenis_pegawai').modal('hide');
                datatable.ajax.reload();
                Swal.fire({
                    text: "Data berhasil disimpan!",
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: { confirmButton: "btn btn-primary" }
                });
            } else {
                response.json().then(err => {
                    Swal.fire({
                        text: err.message || "Terjadi kesalahan sistem.",
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
            console.error('Error:', error);
        });
    });

});

// Global functions for inline onclick handlers
window.resetForm = function() {
    document.getElementById('kt_modal_add_jenis_pegawai_form').reset();
    document.getElementById('jenis_pegawai_uuid').value = "";
    document.getElementById('modal_title').innerText = "Tambah Jenis Pegawai";
}

window.editJenisPegawai = function(data) {
    resetForm();
    document.getElementById('modal_title').innerText = "Edit Jenis Pegawai";
    document.getElementById('jenis_pegawai_uuid').value = data.uuid;
    document.getElementById('jenisPegawai').value = data.jenisPegawai;
    
    $('#kt_modal_add_jenis_pegawai').modal('show');
}

window.deleteJenisPegawai = function(uuid) {
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
            
            fetch(`${apiUrl}/jenis-pegawai/${uuid}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    $('#kt_table_jenis_pegawai').DataTable().ajax.reload();
                    Swal.fire({
                        text: "Data berhasil dihapus.",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
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
</script>
@endsection
