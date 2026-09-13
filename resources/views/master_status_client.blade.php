@extends('layouts.app')

@section('title', 'Manajemen Status Client')

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
                        <input type="text" data-kt-status-client-table-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Cari Status Client..." />
                    </div>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_status_client" onclick="resetForm()">
                        <i class="ki-duotone ki-plus fs-2"></i> Tambah Status Client
                    </button>
                </div>
            </div>
            
            <div class="card-body pt-0">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_status_client">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-125px">Nama Status</th>
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

<!-- Modal Add/Edit Status Client -->
<div class="modal fade" id="kt_modal_add_status_client" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_status_client_header">
                <h2 class="fw-bold" id="modal_title">Tambah Status Client</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_add_status_client_form" class="form" action="#">
                    <input type="hidden" id="status_client_id" name="id" value="" />
                    
                    <div class="d-flex flex-column mb-7 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                            <span class="required">Nama Status Client</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" placeholder="Nama Status" name="nama_status" id="nama_status" required />
                    </div>

                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="kt_modal_add_status_client_submit" class="btn btn-primary">
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
        window.location.href = '/login';
        return;
    }

    const userData = JSON.parse(userDataStr);
    const roleName = userData.role_name ? userData.role_name.toLowerCase() : '';
    if (roleName !== 'superadmin' && roleName !== 'leader') {
        window.location.href = '/dashboard';
        return;
    }

    const apiUrl = '{{ env("APP_URL") }}' + '/api';

    // 1. Initialize DataTable
    var datatable = $('#kt_table_status_client').DataTable({
        ajax: {
            url: `${apiUrl}/master-status-clients`,
            type: 'GET',
            headers: { 'Authorization': `Bearer ${token}` },
            dataSrc: ''
        },
        columns: [
            { data: 'nama_status' },
            { data: 'id', orderable: false, render: function(data, type, row) {
                return `
                    <div class="d-flex justify-content-end flex-shrink-0">
                        <button class="btn btn-icon btn-sm btn-light-primary me-2" onclick='editStatusClient(${JSON.stringify(row).replace(/'/g, "&apos;")})' title="Edit">
                            <i class="ki-duotone ki-pencil fs-3"><span class="path1"></span><span class="path2"></span></i>
                        </button>
                        <button class="btn btn-icon btn-sm btn-light-danger" onclick="deleteStatusClient('${data}')" title="Delete">
                            <i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        </button>
                    </div>
                `;
            }}
        ]
    });

    // Search functionality
    $('[data-kt-status-client-table-filter="search"]').on('keyup', function () {
        datatable.search(this.value).draw();
    });

    // 2. Handle Form Submit (Create & Update)
    const form = document.getElementById('kt_modal_add_status_client_form');
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const submitButton = document.getElementById('kt_modal_add_status_client_submit');
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;

        const id = document.getElementById('status_client_id').value;
        const method = id ? 'PUT' : 'POST';
        const url = id ? `${apiUrl}/master-status-clients/${id}` : `${apiUrl}/master-status-clients`;

        const payload = {
            nama_status: document.getElementById('nama_status').value
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
                $('#kt_modal_add_status_client').modal('hide');
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
    document.getElementById('kt_modal_add_status_client_form').reset();
    document.getElementById('status_client_id').value = "";
    document.getElementById('modal_title').innerText = "Tambah Status Client";
}

window.editStatusClient = function(data) {
    resetForm();
    document.getElementById('modal_title').innerText = "Edit Status Client";
    document.getElementById('status_client_id').value = data.id;
    document.getElementById('nama_status').value = data.nama_status;
    
    $('#kt_modal_add_status_client').modal('show');
}

window.deleteStatusClient = function(id) {
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
            
            fetch(`${apiUrl}/master-status-clients/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    $('#kt_table_status_client').DataTable().ajax.reload();
                    Swal.fire({
                        text: "Data berhasil dihapus.",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: { confirmButton: "btn btn-primary" }
                    });
                } else {
                    response.json().then(err => {
                        Swal.fire({
                            text: err.message || "Gagal menghapus data.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: { confirmButton: "btn btn-danger" }
                        });
                    });
                }
            });
        }
    });
}
</script>
@endsection
