@extends('layouts.app')

@section('content')
							<!--begin::Content-->
							<div id="kt_app_content" class="app-content flex-column-fluid">
								<!--begin::Content container-->
								<div id="kt_app_content_container" class="app-container container-xxl">
									<!--begin::Row-->
									<div class="row g-5 gx-xl-10 mb-5 mb-xl-10">
										<div class="col-12">
											<div class="card shadow-sm">
												<div class="card-body p-10 text-center">
													<i class="ki-duotone ki-element-11 fs-5x text-primary mb-5">
														<span class="path1"></span>
														<span class="path2"></span>
														<span class="path3"></span>
														<span class="path4"></span>
													</i>
													<h1 class="text-gray-900 fw-bold mb-3">Selamat Datang di Monitoring Karyawan!</h1>
												</div>
											</div>
										</div>
									</div>
									<!--end::Row-->

                                    <!--begin::Row for Statistics-->
									<div class="row gy-5 gx-xl-10 mb-xl-10" id="main_stats_container" style="display: none;">
										<div class="col-sm-6 col-xl-2 col-lg-4 mb-5 mb-xl-0">
											<div role="button" onclick="showDashboardModal('aksi')" class="card card-flush h-md-100 bg-primary hover-elevate-up cursor-pointer">
												<div class="card-body py-9">
													<div class="d-flex flex-stack mb-5">
														<div class="fw-semibold text-white fs-6">Manajemen Aksi</div>
													</div>
													<div class="fw-bolder text-white fs-1 mb-2" id="stat_aksi">-</div>
												</div>
											</div>
										</div>
										<div class="col-sm-6 col-xl-2 col-lg-4 mb-5 mb-xl-0">
											<div role="button" onclick="showDashboardModal('visit')" class="card card-flush h-md-100 bg-success hover-elevate-up cursor-pointer">
												<div class="card-body py-9">
													<div class="d-flex flex-stack mb-5">
														<div class="fw-semibold text-white fs-6">Manajemen Visit</div>
													</div>
													<div class="fw-bolder text-white fs-1 mb-2" id="stat_visit">-</div>
												</div>
											</div>
										</div>
										<div class="col-sm-6 col-xl-3 col-lg-4 mb-5 mb-xl-0">
											<div role="button" onclick="showDashboardModal('canvasing')" class="card card-flush h-md-100 bg-info hover-elevate-up cursor-pointer">
												<div class="card-body py-9">
													<div class="d-flex flex-stack mb-5">
														<div class="fw-semibold text-white fs-6">Manajemen Canvasing</div>
													</div>
													<div class="fw-bolder text-white fs-1 mb-2" id="stat_canvasing">-</div>
												</div>
											</div>
										</div>
										<div class="col-sm-6 col-xl-2 col-lg-6 mb-5 mb-xl-0">
											<div role="button" onclick="showDashboardModal('ho')" class="card card-flush h-md-100 bg-warning hover-elevate-up cursor-pointer">
												<div class="card-body py-9">
													<div class="d-flex flex-stack mb-5">
														<div class="fw-semibold text-white fs-6">Manajemen HO</div>
													</div>
													<div class="fw-bolder text-white fs-1 mb-2" id="stat_ho">-</div>
												</div>
											</div>
										</div>
										<div class="col-sm-6 col-xl-3 col-lg-6 mb-5 mb-xl-0">
											<div role="button" onclick="showDashboardModal('ppd')" class="card card-flush h-md-100 bg-danger hover-elevate-up cursor-pointer">
												<div class="card-body py-9">
													<div class="d-flex flex-stack mb-5">
														<div class="fw-semibold text-white fs-6">Manajemen PPD</div>
													</div>
													<div class="fw-bolder text-white fs-1 mb-2" id="stat_ppd">-</div>
												</div>
											</div>
										</div>
									</div>
                                    <!--end::Row for Statistics-->

                                    <!--begin::Row for Status Stats-->
                                    <div class="row g-5 gx-xl-10 mb-5 mb-xl-10" id="status_stats_container">
                                        <!-- Status stats will be loaded here via JS -->
                                    </div>
                                    <!--end::Row for Status Stats-->
								</div>
								<!--end::Content container-->
							</div>
							<!--end::Content-->

                            <!-- Modal Dashboard List -->
                            <div class="modal fade" id="kt_modal_dashboard_list" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h2 class="fw-bold" id="modal_dashboard_title">List Data</h2>
                                            <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                                                <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                                            </div>
                                        </div>
                                        <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_dashboard" style="width: 100%">
                                                <thead>
                                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                        <th class="min-w-50px">No</th>
                                                        <th class="min-w-200px" id="th_nama">Nama / ID</th>
                                                        <th class="min-w-150px">Dibuat Oleh</th>
                                                        <th class="min-w-150px">Tanggal</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="fw-semibold text-gray-600">
                                                    <!-- Data will be loaded via JS -->
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer flex-center">
                                            <a href="#" id="btn_go_to_menu" class="btn btn-primary">Buka Menu Lengkap</a>
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const token = localStorage.getItem('jwt_token');
    const userDataStr = localStorage.getItem('user_data');
    if (!token || !userDataStr) return;

    const userData = JSON.parse(userDataStr);
    let rawRole = userData.role_name || (userData.role && userData.role.nama_role) || '';
    let roleName = rawRole.toLowerCase();

    // Only for superadmin and leader
    if (roleName === 'superadmin' || roleName === 'leader') {
        const mainStats = document.getElementById('main_stats_container');
        if (mainStats) {
            mainStats.style.display = 'flex';
        }

        fetch('{{ url('/api/dashboard/stats') }}', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data) {
                document.getElementById('stat_aksi').innerText = data.aksi !== undefined ? data.aksi : '-';
                document.getElementById('stat_visit').innerText = data.visit !== undefined ? data.visit : '-';
                document.getElementById('stat_canvasing').innerText = data.canvasing !== undefined ? data.canvasing : '-';
                document.getElementById('stat_ho').innerText = data.ho !== undefined ? data.ho : '-';
                document.getElementById('stat_ppd').innerText = data.ppd !== undefined ? data.ppd : '-';
            }
        })
        .catch(error => console.error('Error fetching dashboard stats:', error));
    }
});

let dashboardDataTable = null;

window.showDashboardModal = function(module) {
    const titleEl = document.getElementById('modal_dashboard_title');
    const thNama = document.getElementById('th_nama');
    const btnGo = document.getElementById('btn_go_to_menu');
    let apiUrl = '';
    
    if(module === 'aksi') {
        titleEl.innerText = "List Data Aksi";
        thNama.innerText = "Nama Aksi";
        apiUrl = '/api/manajemen-aksi';
        btnGo.href = "{{ route('manajemen-aksi') }}";
    } else if(module === 'visit') {
        titleEl.innerText = "List Data Visit";
        thNama.innerText = "Nama Client";
        apiUrl = '/api/manajemen-visit';
        btnGo.href = "{{ route('manajemen-visit') }}";
    } else if(module === 'canvasing') {
        titleEl.innerText = "List Data Canvasing";
        thNama.innerText = "Nama Client";
        apiUrl = '/api/manajemen-canvasing';
        btnGo.href = "{{ route('manajemen-canvasing') }}";
    } else if(module === 'ho') {
        titleEl.innerText = "List Data HO";
        thNama.innerText = "Nama Client";
        apiUrl = '/api/manajemen-ho';
        btnGo.href = "{{ route('manajemen-ho') }}";
    } else if(module === 'ppd') {
        titleEl.innerText = "List Data PPD";
        thNama.innerText = "Nama Client";
        apiUrl = '/api/manajemen-ppd';
        btnGo.href = "{{ route('manajemen-ppd') }}";
    }

    if(dashboardDataTable) {
        dashboardDataTable.destroy();
        $('#kt_table_dashboard tbody').empty();
    }

    const token = localStorage.getItem('jwt_token');

    dashboardDataTable = $('#kt_table_dashboard').DataTable({
        ajax: {
            url: `{{ url('') }}${apiUrl}`,
            type: 'GET',
            headers: { 'Authorization': `Bearer ${token}` },
            dataSrc: ''
        },
        columns: [
            { data: null, orderable: false, render: function(data, type, row, meta) {
                return meta.row + 1;
            }},
            { data: null, render: function(data, type, row) {
                return module === 'aksi' ? (row.namaAksi || '-') : (row.namaClient || '-');
            }},
            { data: 'user', render: function(data) {
                return data ? data.nama : '-';
            }},
            { data: 'created_at', render: function(data) {
                if(!data) return '-';
                const d = new Date(data);
                return d.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
            }}
        ]
    });

    $('#kt_modal_dashboard_list').modal('show');
}
</script>
@endpush
@endsection
