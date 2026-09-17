@extends('layouts.app')

@section('title', 'Manajemen Role')

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
                        <input type="text" data-kt-role-table-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Cari Role..." />
                    </div>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_role" onclick="resetForm()">
                        <i class="ki-duotone ki-plus fs-2"></i> Tambah Role
                    </button>
                </div>
            </div>
            
            <div class="card-body pt-0">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_roles">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-125px">Nama Role</th>
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

<!-- Modal Add/Edit Role -->
<div class="modal fade" id="kt_modal_add_role" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_role_header">
                <h2 class="fw-bold" id="modal_title">Tambah Role</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_add_role_form" class="form" action="#">
                    <input type="hidden" id="role_id" name="id" value="" />
                    
                    <div class="d-flex flex-column mb-7 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                            <span class="required">Nama Role</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" placeholder="Nama Role" name="nama_role" id="nama_role" />
                    </div>

                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="kt_modal_add_role_submit" class="btn btn-primary">
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
    const apiUrl = '{{ env("APP_URL") }}' + '/api';

    // 1. Initialize DataTable
    var datatable = $('#kt_table_roles').DataTable({
        ajax: {
            url: `${apiUrl}/roles`,
            type: 'GET',
            headers: { 'Authorization': `Bearer ${token}` },
            dataSrc: ''
        },
        columns: [
            { data: 'nama_role' },
            { data: 'id', orderable: false, render: function(data, type, row) {
                return `
                    <button class="btn btn-icon btn-sm btn-light-primary me-2" onclick='editRole(${JSON.stringify(row).replace(/'/g, "&apos;")})' title="Edit">
                        <i class="ki-duotone ki-pencil fs-3"><span class="path1"></span><span class="path2"></span></i>
                    </button>
                    <button class="btn btn-icon btn-sm btn-light-danger" onclick="deleteRole('${data}')" title="Delete">
                        <i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                    </button>
                `;
            }}
        ]
    });

    // Search functionality
    $('[data-kt-role-table-filter="search"]').on('keyup', function () {
        datatable.search(this.value).draw();
    });

    // 2. Handle Form Submit (Create & Update)
    const form = document.getElementById('kt_modal_add_role_form');
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const submitButton = document.getElementById('kt_modal_add_role_submit');
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;

        const id = document.getElementById('role_id').value;
        const method = id ? 'PUT' : 'POST';
        const url = id ? `${apiUrl}/roles/${id}` : `${apiUrl}/roles`;

        const payload = {
            nama_role: document.getElementById('nama_role').value
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
                $('#kt_modal_add_role').modal('hide');
                datatable.ajax.reload();
                Swal.fire({
                    text: "Data role berhasil disimpan!",
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
    document.getElementById('kt_modal_add_role_form').reset();
    document.getElementById('role_id').value = "";
    document.getElementById('modal_title').innerText = "Tambah Role";
}

window.editRole = function(role) {
    resetForm();
    document.getElementById('modal_title').innerText = "Edit Role";
    document.getElementById('role_id').value = role.id;
    document.getElementById('nama_role').value = role.nama_role;
    
    $('#kt_modal_add_role').modal('show');
}

window.deleteRole = function(id) {
    Swal.fire({
        text: "Apakah Anda yakin ingin menghapus role ini?",
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
            
            fetch(`${apiUrl}/roles/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    $('#kt_table_roles').DataTable().ajax.reload();
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
