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
										<div class="col-sm-6 col-xl-3 mb-5 mb-xl-0">
											<div class="card card-flush h-md-100 bg-primary">
												<div class="card-body py-9">
													<div class="d-flex flex-stack mb-5">
														<div class="fw-semibold text-white fs-5">Jumlah Anggota</div>
														<i class="ki-duotone ki-profile-user fs-2x text-white opacity-50">
															<span class="path1"></span>
															<span class="path2"></span>
															<span class="path3"></span>
															<span class="path4"></span>
														</i>
													</div>
													<div class="fw-bolder text-white fs-1 mb-2">{{ $jumlahAnggota }}</div>
												</div>
											</div>
										</div>
										<div class="col-sm-6 col-xl-3 mb-5 mb-xl-0">
											<div class="card card-flush h-md-100 bg-success">
												<div class="card-body py-9">
													<div class="d-flex flex-stack mb-5">
														<div class="fw-semibold text-white fs-5">Client dari HO</div>
														<i class="ki-duotone ki-home-2 fs-2x text-white opacity-50">
															<span class="path1"></span>
															<span class="path2"></span>
														</i>
													</div>
													<div class="fw-bolder text-white fs-1 mb-2">{{ $jumlahClientHO }}</div>
												</div>
											</div>
										</div>
										<div class="col-sm-6 col-xl-3 mb-5 mb-xl-0">
											<div class="card card-flush h-md-100 bg-info">
												<div class="card-body py-9">
													<div class="d-flex flex-stack mb-5">
														<div class="fw-semibold text-white fs-5">Client (Penginputan)</div>
														<i class="ki-duotone ki-pencil fs-2x text-white opacity-50">
															<span class="path1"></span>
															<span class="path2"></span>
														</i>
													</div>
													<div class="fw-bolder text-white fs-1 mb-2">{{ $jumlahClientPenginputan }}</div>
												</div>
											</div>
										</div>
										<div class="col-sm-6 col-xl-3 mb-5 mb-xl-0">
											<div class="card card-flush h-md-100 bg-warning">
												<div class="card-body py-9">
													<div class="d-flex flex-stack mb-5">
														<div class="fw-semibold text-white fs-5">Kegiatan Anggota</div>
														<i class="ki-duotone ki-calendar-8 fs-2x text-white opacity-50">
															<span class="path1"></span>
															<span class="path2"></span>
															<span class="path3"></span>
															<span class="path4"></span>
															<span class="path5"></span>
															<span class="path6"></span>
														</i>
													</div>
													<div class="fw-bolder text-white fs-1 mb-2">{{ $jumlahKegiatan }}</div>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const token = localStorage.getItem('jwt_token');
    const userDataStr = localStorage.getItem('user_data');
    if (!token || !userDataStr) return;

    const userData = JSON.parse(userDataStr);
    let rawRole = userData.role_name || (userData.role && userData.role.nama_role) || '';
    let roleName = rawRole.toLowerCase();

    if (roleName !== 'anggota') {
        const mainStats = document.getElementById('main_stats_container');
        if (mainStats) {
            mainStats.style.display = 'flex';
        }

        fetch('/api/clients/stats/status', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })
        .then(response => response.json())
        .then(data => {
        const container = document.getElementById('status_stats_container');
        if (data.length === 0) return;
        
        let html = '';
        // Colors array to make them look distinct
        const colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger', 'bg-dark'];
        const icons = [
            'ki-chart-simple',
            'ki-check-circle',
            'ki-abstract-26',
            'ki-briefcase',
            'ki-cube-2',
            'ki-shield-tick'
        ];
        
        data.forEach((stat, index) => {
            const colorClass = colors[index % colors.length];
            const iconClass = icons[index % icons.length];
            html += `
                <div class="col-sm-6 col-xl-3 mb-5 mb-xl-0">
                    <div class="card card-flush h-md-100 ${colorClass}">
                        <div class="card-body py-9">
                            <div class="d-flex flex-stack mb-5">
                                <div class="fw-semibold text-white fs-5">Client ${stat.nama_status}</div>
                                <i class="ki-duotone ${iconClass} fs-2x text-white opacity-50">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </div>
                            <div class="fw-bolder text-white fs-1 mb-2">${stat.total}</div>
                        </div>
                    </div>
                </div>
            `;
        });
        
            container.innerHTML = html;
        })
        .catch(error => console.error('Error fetching status stats:', error));
    }
});
</script>
@endpush
@endsection
