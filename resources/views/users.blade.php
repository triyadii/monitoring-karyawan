@extends('layouts.app')

@section('title', 'Manajemen User')

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
                        <input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Cari User..." />
                    </div>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_user" onclick="resetForm()">
                        <i class="ki-duotone ki-plus fs-2"></i> Tambah User
                    </button>
                </div>
            </div>
            
            <div class="card-body pt-0">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-125px">Nama</th>
                            <th class="min-w-125px">Username</th>
                            <th class="min-w-125px">Role</th>
                            <th class="min-w-125px">Status</th>
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

<!-- Modal Add/Edit User -->
<div class="modal fade" id="kt_modal_add_user" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_user_header">
                <h2 class="fw-bold" id="modal_title">Tambah User</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_add_user_form" class="form" action="#">
                    <input type="hidden" id="user_id" name="id" value="" />
                    
                    <div class="d-flex flex-column mb-7 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                            <span class="required">Nama Lengkap</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" placeholder="Nama Lengkap" name="nama" id="nama" />
                    </div>

                    <div class="d-flex flex-column mb-7 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                            <span class="required">Username</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" placeholder="Username" name="username" id="username" />
                    </div>

                    <div class="d-flex flex-column mb-7 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                            <span>Password</span>
                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Kosongkan jika tidak ingin merubah password saat edit"></i>
                        </label>
                        <input type="password" class="form-control form-control-solid" placeholder="Password" name="password" id="password" />
                    </div>

                    <div class="d-flex flex-column mb-7 fv-row">
                        <label class="required fs-6 fw-semibold form-label mb-2">Role</label>
                        <select name="role_id" id="role_id" data-control="select2" data-dropdown-parent="#kt_modal_add_user" data-placeholder="Pilih Role..." class="form-select form-select-solid">
                            <option value="">Pilih Role...</option>
                            <!-- Loaded via AJAX -->
                        </select>
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
                        <button type="submit" id="kt_modal_add_user_submit" class="btn btn-primary">
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

    // 1. Fetch Roles for Dropdown
    function loadRoles() {
        fetch(`${apiUrl}/roles`, {
            method: 'GET',
            headers: { 'Authorization': `Bearer ${token}` }
        })
        .then(response => response.json())
        .then(data => {
            const roleSelect = document.getElementById('role_id');
            // Clear existing except first
            while (roleSelect.options.length > 1) {
                roleSelect.remove(1);
            }
            data.forEach(role => {
                const option = document.createElement('option');
                option.value = role.id;
                option.text = role.nama_role;
                roleSelect.add(option);
            });
        })
        .catch(error => console.error('Error fetching roles:', error));
    }
    loadRoles();

    // 2. Initialize DataTable
    var datatable = $('#kt_table_users').DataTable({
        ajax: {
            url: `${apiUrl}/users`,
            type: 'GET',
            headers: { 'Authorization': `Bearer ${token}` },
            dataSrc: ''
        },
        columns: [
            { data: 'nama' },
            { data: 'username' },
            { data: 'role', render: function(data) {
                return data ? `<span class="badge badge-light-primary">${data.nama_role}</span>` : '-';
            }},
            { data: 'status', render: function(data) {
                return data == 1 
                    ? `<span class="badge badge-light-success">Aktif</span>` 
                    : `<span class="badge badge-light-danger">Non-Aktif</span>`;
            }},
            { data: 'id', orderable: false, render: function(data, type, row) {
                return `
                    <button class="btn btn-icon btn-sm btn-light-primary me-2" onclick='editUser(${JSON.stringify(row).replace(/'/g, "&apos;")})' title="Edit">
                        <i class="ki-duotone ki-pencil fs-3"><span class="path1"></span><span class="path2"></span></i>
                    </button>
                    <button class="btn btn-icon btn-sm btn-light-danger" onclick="deleteUser('${data}')" title="Delete">
                        <i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                    </button>
                `;
            }}
        ]
    });

    // Search functionality
    $('[data-kt-user-table-filter="search"]').on('keyup', function () {
        datatable.search(this.value).draw();
    });

    // 3. Handle Form Submit (Create & Update)
    const form = document.getElementById('kt_modal_add_user_form');
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const submitButton = document.getElementById('kt_modal_add_user_submit');
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;

        const id = document.getElementById('user_id').value;
        const method = id ? 'PUT' : 'POST';
        const url = id ? `${apiUrl}/users/${id}` : `${apiUrl}/users`;

        const payload = {
            nama: document.getElementById('nama').value,
            username: document.getElementById('username').value,
            role_id: document.getElementById('role_id').value,
            status: document.getElementById('status').value
        };

        const password = document.getElementById('password').value;
        if (password) {
            payload.password = password;
        }

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
                $('#kt_modal_add_user').modal('hide');
                datatable.ajax.reload();
                Swal.fire({
                    text: "Data user berhasil disimpan!",
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

    // Ensure Select2 handles the dynamic options correctly
    $('#kt_modal_add_user').on('shown.bs.modal', function () {
        $('[data-control="select2"]').select2({
            dropdownParent: $('#kt_modal_add_user')
        });
    });

});

// Global functions for inline onclick handlers
window.resetForm = function() {
    document.getElementById('kt_modal_add_user_form').reset();
    document.getElementById('user_id').value = "";
    document.getElementById('modal_title').innerText = "Tambah User";
    $('#role_id').val("").trigger('change');
}

window.editUser = function(user) {
    resetForm();
    document.getElementById('modal_title').innerText = "Edit User";
    document.getElementById('user_id').value = user.id;
    document.getElementById('nama').value = user.nama;
    document.getElementById('username').value = user.username;
    document.getElementById('status').value = user.status;
    
    // Select2 needs to be triggered to show the selected value
    if (user.role_id) {
        $('#role_id').val(user.role_id).trigger('change');
    }
    
    $('#kt_modal_add_user').modal('show');
}

window.deleteUser = function(id) {
    Swal.fire({
        text: "Apakah Anda yakin ingin menghapus user ini?",
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
            
            fetch(`${apiUrl}/users/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    $('#kt_table_users').DataTable().ajax.reload();
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
