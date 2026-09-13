@extends('layouts.app')

@section('title', 'Manajemen Kegiatan Anggota')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        
        <!-- View Anggota (Khusus Superadmin/Leader) -->
        <div id="view_anggota" style="display: none;">
            <div class="card shadow-sm">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <h3 class="card-label">Daftar Anggota</h3>
                    </div>
                </div>
                <div class="card-body py-4">
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_anggota">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-150px">Nama Anggota</th>
                                <th class="min-w-125px">Username</th>
                                <th class="text-end min-w-100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            <!-- DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- View Kegiatan -->
        <div id="view_kegiatan" style="display: none;">
            <div class="card shadow-sm">
                <div class="card-header border-0 pt-6">
                    <div class="card-title d-flex align-items-center">
                        <button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary me-4" id="btn_back_to_anggota" style="display: none;" title="Kembali ke Daftar Anggota">
                            <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i>
                        </button>
                        <h3 class="card-label m-0" id="title_kegiatan">Daftar Kegiatan</h3>
                    </div>
                    <div class="card-toolbar d-flex flex-wrap gap-2">
                        <div class="d-flex align-items-center position-relative my-1 me-3">
                            <input type="date" class="form-control form-control-solid form-control-sm w-130px w-md-150px me-2" id="filter_start_date" title="Tanggal Awal Input" />
                            <span class="me-2 text-muted fw-bold">s/d</span>
                            <input type="date" class="form-control form-control-solid form-control-sm w-130px w-md-150px me-2" id="filter_end_date" title="Tanggal Akhir Input" />
                            <button type="button" class="btn btn-sm btn-light-primary" id="btn_filter_tanggal">
                                <i class="ki-duotone ki-filter fs-2"><span class="path1"></span><span class="path2"></span></i> Filter
                            </button>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary" id="btn_tambah_kegiatan" style="display: none;" data-bs-toggle="modal" data-bs-target="#kt_modal_add_kegiatan" onclick="resetForm()">
                            <i class="ki-duotone ki-plus fs-2"></i> Tambah Kegiatan
                        </button>
                    </div>
                </div>
                <div class="card-body py-4">
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_kegiatan" style="width: 100%;">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-150px">Kegiatan</th>
                                <th class="min-w-150px">Jenis Kegiatan</th>
                                <th class="min-w-125px">Tanggal</th>
                                <th class="min-w-125px">User</th>
                                <th class="min-w-100px">Foto</th>
                                <th class="text-end min-w-100px">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            <!-- DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>
</div>

<!-- Modal Tambah/Edit Kegiatan -->
<div class="modal fade" id="kt_modal_add_kegiatan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-900px">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_kegiatan_header">
                <h2 class="fw-bold" id="modal_title">Tambah Kegiatan</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_add_kegiatan_form" class="form" action="#" enctype="multipart/form-data">
                    <input type="hidden" id="kegiatan_id" />
                    
                    <div class="row">
                        <!-- Kolom Form -->
                        <div class="col-md-7 pe-md-5">
                            <div class="fv-row mb-7">
                                <label class="required fs-6 fw-semibold mb-2">Nama Kegiatan</label>
                                <input type="text" class="form-control form-control-solid" placeholder="Nama Kegiatan" id="nama_kegiatan_user" required />
                            </div>

                            <div class="fv-row mb-7">
                                <label class="required fs-6 fw-semibold mb-2">Jenis Kegiatan</label>
                                <select id="jenis_kegiatan_id" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#kt_modal_add_kegiatan" data-placeholder="Pilih Jenis Kegiatan..." required>
                                    <option value="">Pilih Jenis Kegiatan...</option>
                                </select>
                            </div>

                            <div class="fv-row mb-7">
                                <label class="fs-6 fw-semibold mb-2">Keterangan Kegiatan</label>
                                <textarea id="keterangan_kegiatan" class="form-control form-control-solid" rows="3" placeholder="Keterangan (Opsional)"></textarea>
                            </div>

                            <div class="fv-row mb-7">
                                <label class="required fs-6 fw-semibold mb-2">Tanggal Kegiatan</label>
                                <input type="datetime-local" class="form-control form-control-solid" id="tanggal_kegiatan" required />
                            </div>

                            <div class="fv-row mb-7">
                                <label class="fs-6 fw-semibold mb-2">Foto Dokumentasi</label>
                                <input type="file" class="form-control form-control-solid" id="foto" accept="image/*" />
                                <div class="text-muted fs-7 mt-2">Pilih gambar jika ada (Format: JPG, PNG, max 2MB).</div>
                            </div>
                        </div>

                        <!-- Kolom Preview Gambar -->
                        <div class="col-md-5 ps-md-5 d-flex flex-column align-items-center justify-content-center border-start border-gray-200">
                            <div id="foto_preview" class="w-100 text-center" style="display:none;">
                                <label class="fs-6 fw-semibold mb-4 d-block text-gray-700">Preview Foto</label>
                                <img src="" id="img_preview" class="img-fluid rounded shadow-sm" style="max-height: 400px; width: 100%; object-fit: cover;" />
                            </div>
                        </div>
                    </div>

                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="kt_modal_add_kegiatan_submit" class="btn btn-primary">
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

<!-- Modal View Foto -->
<div class="modal fade" id="kt_modal_view_foto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-800px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Foto Dokumentasi</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body text-center p-0">
                <img src="" id="img_modal_preview" style="max-width: 100%; max-height: 80vh; object-fit: contain; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;" />
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
    
    window.datatableKegiatan = null;
    
    const userData = JSON.parse(userDataStr);
    const apiUrl = '{{ env("APP_URL") }}' + '/api';
    
    let rawRole = userData.role_name || (userData.role && userData.role.nama_role) || '';
    const roleName = rawRole.toLowerCase();

    // Load Jenis Kegiatan for form dropdown
    fetch(`${apiUrl}/jenis-kegiatan`, { headers: { 'Authorization': `Bearer ${token}` } })
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('jenis_kegiatan_id');
            data.forEach(jk => {
                select.innerHTML += `<option value="${jk.id}">${jk.nama_jenis_kegiatan}</option>`;
            });
        });

    if (roleName === 'superadmin' || roleName === 'leader') {
        // Show view anggota first
        document.getElementById('view_anggota').style.display = 'block';

        $('#kt_table_anggota').DataTable({
            ajax: {
                url: `${apiUrl}/users`,
                type: 'GET',
                headers: { 'Authorization': `Bearer ${token}` },
                dataSrc: function(json) {
                    return json.filter(u => u.role && u.role.nama_role.toLowerCase() === 'anggota');
                }
            },
            columns: [
                { data: 'nama' },
                { data: 'username' },
                { data: 'id', orderable: false, render: function(data, type, row) {
                    return `
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-sm btn-light-primary" onclick="showKegiatanUser('${data}', '${row.nama.replace(/'/g, "\\'")}')">
                                <i class="ki-duotone ki-eye fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> Lihat Kegiatan
                            </button>
                        </div>
                    `;
                }}
            ]
        });

        // Setup back button
        document.getElementById('btn_back_to_anggota').style.display = 'inline-flex';
        document.getElementById('btn_back_to_anggota').addEventListener('click', function() {
            document.getElementById('view_kegiatan').style.display = 'none';
            document.getElementById('view_anggota').style.display = 'block';
        });

    } else if (roleName === 'anggota') {
        // Show direct activities
        document.getElementById('view_kegiatan').style.display = 'block';
        document.getElementById('title_kegiatan').innerText = 'Kegiatan Saya';
        
        const btnTambah = document.getElementById('btn_tambah_kegiatan');
        if(btnTambah) btnTambah.style.display = 'inline-flex';

        initDataTableKegiatan(`${apiUrl}/kegiatan-anggota/my-kegiatan`);
    }
    
    document.getElementById('btn_filter_tanggal').addEventListener('click', function() {
        if (window.datatableKegiatan) {
            window.datatableKegiatan.ajax.reload();
        }
    });

    window.showKegiatanUser = function(userId, userName) {
        document.getElementById('view_anggota').style.display = 'none';
        document.getElementById('view_kegiatan').style.display = 'block';
        document.getElementById('title_kegiatan').innerText = `Kegiatan: ${userName}`;
        initDataTableKegiatan(`${apiUrl}/kegiatan-anggota/user/${userId}`);
    };

    function initDataTableKegiatan(url) {
        if (window.datatableKegiatan) {
            window.datatableKegiatan.ajax.url(url).load();
        } else {
            window.datatableKegiatan = $('#kt_table_kegiatan').DataTable({
                ajax: {
                    url: url,
                    type: 'GET',
                    headers: { 'Authorization': `Bearer ${token}` },
                    data: function(d) {
                        d.start_date = document.getElementById('filter_start_date').value;
                        d.end_date = document.getElementById('filter_end_date').value;
                    },
                    dataSrc: ''
                },
                columns: [
                    { data: 'nama_kegiatan_user' },
                    { data: 'jenis_kegiatan', render: function(data) {
                        return data ? data.nama_jenis_kegiatan : '-';
                    }},
                    { data: 'tanggal_kegiatan', render: function(data) {
                        return new Date(data).toLocaleString('id-ID');
                    }},
                    { data: 'user', render: function(data) {
                        return data ? `<span class="badge badge-light-primary">${data.nama}</span>` : '-';
                    }},
                    { data: 'foto', render: function(data) {
                        if(data) {
                            let fullUrl = data.startsWith('http') ? data : '{{ env("APP_URL") }}/' + data;
                            return `<img src="${fullUrl}" style="height: 40px; border-radius: 4px; cursor: pointer;" onclick="viewFoto('${fullUrl}')" />`;
                        }
                        return '<span class="badge badge-light-secondary">No Image</span>';
                    }},
                    { data: 'id', orderable: false, render: function(data, type, row) {
                        return `
                            <div class="d-flex justify-content-end flex-shrink-0">
                                <button class="btn btn-icon btn-sm btn-light-primary me-2" onclick='editKegiatan(${JSON.stringify(row).replace(/'/g, "&apos;")})' title="Edit">
                                    <i class="ki-duotone ki-pencil fs-3"><span class="path1"></span><span class="path2"></span></i>
                                </button>
                                <button class="btn btn-icon btn-sm btn-light-danger" onclick="deleteKegiatan('${data}')" title="Delete">
                                    <i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                </button>
                            </div>
                        `;
                    }}
                ]
            });
        }
    }

    const form = document.getElementById('kt_modal_add_kegiatan_form');
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const submitButton = document.getElementById('kt_modal_add_kegiatan_submit');
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;

        const id = document.getElementById('kegiatan_id').value;
        const url = id ? `${apiUrl}/kegiatan-anggota/${id}` : `${apiUrl}/kegiatan-anggota`;
        
        let formData = new FormData();
        formData.append('nama_kegiatan_user', document.getElementById('nama_kegiatan_user').value);
        formData.append('jenis_kegiatan_id', document.getElementById('jenis_kegiatan_id').value);
        formData.append('keterangan_kegiatan', document.getElementById('keterangan_kegiatan').value);
        formData.append('tanggal_kegiatan', document.getElementById('tanggal_kegiatan').value);
        
        const foto = document.getElementById('foto').files[0];
        if(foto) formData.append('foto', foto);

        fetch(url, {
            method: 'POST', // Always POST, we use _method=PUT for updates using explicit post route in api.php
            headers: { 
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async response => {
            submitButton.removeAttribute('data-kt-indicator');
            submitButton.disabled = false;
            
            if (response.ok) {
                $('#kt_modal_add_kegiatan').modal('hide');
                if (window.datatableKegiatan) {
                    window.datatableKegiatan.ajax.reload();
                }
                Swal.fire({ text: "Berhasil menyimpan kegiatan!", icon: "success", customClass: { confirmButton: "btn btn-primary" } });
                form.reset();
            } else {
                const res = await response.json();
                Swal.fire({ text: res.message || "Terjadi kesalahan.", icon: "error", customClass: { confirmButton: "btn btn-danger" } });
            }
        })
        .catch(error => {
            submitButton.removeAttribute('data-kt-indicator');
            submitButton.disabled = false;
            console.error('Error:', error);
            Swal.fire({ text: "Terjadi kesalahan sistem.", icon: "error", customClass: { confirmButton: "btn btn-danger" } });
        });
    });
});

window.viewFoto = function(url) {
    document.getElementById('img_modal_preview').src = url;
    $('#kt_modal_view_foto').modal('show');
}

window.resetForm = function() {
    document.getElementById('kt_modal_add_kegiatan_form').reset();
    document.getElementById('kegiatan_id').value = "";
    document.getElementById('modal_title').innerText = "Tambah Kegiatan";
    $('#jenis_kegiatan_id').val("").trigger('change');
    document.getElementById('foto_preview').style.display = 'none';
}

// Handler saat file dipilih secara manual, untuk preview langsung
document.getElementById('foto').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        let reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('foto_preview').style.display = 'block';
            document.getElementById('img_preview').src = e.target.result;
        }
        reader.readAsDataURL(e.target.files[0]);
    } else {
        document.getElementById('foto_preview').style.display = 'none';
    }
});

window.editKegiatan = function(kegiatan) {
    resetForm();
    document.getElementById('modal_title').innerText = "Edit Kegiatan";
    document.getElementById('kegiatan_id').value = kegiatan.id;
    document.getElementById('nama_kegiatan_user').value = kegiatan.nama_kegiatan_user;
    document.getElementById('keterangan_kegiatan').value = kegiatan.keterangan_kegiatan || "";
    
    if(kegiatan.jenis_kegiatan_id) {
        $('#jenis_kegiatan_id').val(kegiatan.jenis_kegiatan_id).trigger('change');
    }
    
    // Format tanggal_kegiatan for datetime-local (YYYY-MM-DDThh:mm)
    if(kegiatan.tanggal_kegiatan) {
        let dt = new Date(kegiatan.tanggal_kegiatan);
        dt.setMinutes(dt.getMinutes() - dt.getTimezoneOffset());
        document.getElementById('tanggal_kegiatan').value = dt.toISOString().slice(0, 16);
    }
    
    if(kegiatan.foto) {
        let fullUrl = kegiatan.foto.startsWith('http') ? kegiatan.foto : '{{ env("APP_URL") }}/' + kegiatan.foto;
        document.getElementById('foto_preview').style.display = 'block';
        document.getElementById('img_preview').src = fullUrl;
    } else {
        document.getElementById('foto_preview').style.display = 'none';
    }

    $('#kt_modal_add_kegiatan').modal('show');
}

window.deleteKegiatan = function(id) {
    Swal.fire({
        text: "Hapus kegiatan ini?", icon: "warning", showCancelButton: true,
        confirmButtonText: "Ya", cancelButtonText: "Batal",
        customClass: { confirmButton: "btn btn-danger", cancelButton: "btn btn-active-light" }
    }).then(function (result) {
        if (result.value) {
            const token = localStorage.getItem('jwt_token');
            fetch(`{{ url('/api/kegiatan-anggota') }}/${id}`, {
                method: 'DELETE', headers: { 'Authorization': `Bearer ${token}` }
            }).then(response => {
                if (response.ok) {
                    if (window.datatableKegiatan) {
                        window.datatableKegiatan.ajax.reload();
                    }
                    Swal.fire({ text: "Berhasil dihapus.", icon: "success", customClass: { confirmButton: "btn btn-primary" } });
                }
            });
        }
    });
}
</script>
@endsection
