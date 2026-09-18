<!DOCTYPE html>
<!--
Author: Keenthemes
Product Name: MetronicProduct Version: 8.2.7
Purchase: https://1.envato.market/EA4JP
Website: http://www.keenthemes.com
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
License: For each use you must have a valid license purchased only from above link in order to legally use the theme for your project.
-->
<html lang="en">
	<!--begin::Head-->
	<head>
<base href="{{ url('/') }}/" />
		<title>Metronic - The World's #1 Selling Tailwind CSS & Bootstrap Admin Template by KeenThemes</title>
		<meta charset="utf-8" />
		<meta name="description" content="The most advanced Tailwind CSS & Bootstrap 5 Admin Theme with 40 unique prebuilt layouts on Themeforest trusted by 100,000 beginners and professionals. Multi-demo, Dark Mode, RTL support and complete React, Angular, Vue, Asp.Net Core, Rails, Spring, Blazor, Django, Express.js, Node.js, Flask, Symfony & Laravel versions. Grab your copy now and get life-time updates for free." />
		<meta name="keywords" content="tailwind, tailwindcss, metronic, bootstrap, bootstrap 5, angular, VueJs, React, Asp.Net Core, Rails, Spring, Blazor, Django, Express.js, Node.js, Flask, Symfony & Laravel starter kits, admin themes, web design, figma, web development, free templates, free admin themes, bootstrap theme, bootstrap template, bootstrap dashboard, bootstrap dak mode, bootstrap button, bootstrap datepicker, bootstrap timepicker, fullcalendar, datatables, flaticon" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta property="og:locale" content="en_US" />
		<meta property="og:type" content="article" />
		<meta property="og:title" content="Metronic - The World's #1 Selling Tailwind CSS & Bootstrap Admin Template by KeenThemes" />
		<meta property="og:url" content="https://keenthemes.com/metronic" />
		<meta property="og:site_name" content="Metronic by Keenthemes" />
		<link rel="canonical" href="http://preview.keenthemes.comauthentication/layouts/fancy/sign-in.html" />
		<link rel="shortcut icon" href="{{ asset('logo.png') }}" />
		<!--begin::Fonts(mandatory for all pages)-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
		<link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
		<link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
		<!--end::Global Stylesheets Bundle-->
		<script>// Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }</script>
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="app-blank">
		<!--begin::Theme mode setup on page load-->
		<script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>
		<!--end::Theme mode setup on page load-->
		<!--begin::Root-->
		<div class="d-flex flex-column flex-root" id="kt_app_root">
			<!--begin::Authentication - Sign-in -->
			<div class="d-flex flex-column flex-lg-row flex-column-fluid">
				<!--begin::Aside-->
				<div class="d-flex flex-column flex-column-fluid flex-center w-lg-50 p-6 p-lg-10">
					<!--begin::Mobile Logo-->
					<a href="#" class="d-block d-lg-none mb-10 text-center">
						<img alt="Logo" src="{{ asset('logo.png') }}" class="h-60px theme-light-show" />
					</a>
					<!--end::Mobile Logo-->

					<!--begin::Wrapper-->
					<div class="w-100 mw-450px">
						<!--begin::Form-->
						<form class="form w-100" novalidate="novalidate" id="loginForm" action="{{ url('/api/login') }}">
							<div class="card shadow-sm p-8 p-lg-12 rounded-4 border-0">
								<!--begin::Body-->
								<div class="card-body p-0">
									<!--begin::Heading-->
									<div class="text-center mb-12">
										<!--begin::Title-->
										<h1 class="text-gray-900 mb-3 fs-2x fw-bold">Login ke Sistem</h1>
										<!--end::Title-->
										<!--begin::Text-->
										<div class="text-gray-500 fw-semibold fs-6">Aplikasi Monitoring Karyawan & Client</div>
										<!--end::Link-->
									</div>
									<!--begin::Heading-->
									
									<!--begin::Input group-->
									<div class="fv-row mb-8">
										<!--begin::Label-->
										<label class="form-label fs-6 fw-bold text-gray-900">Username</label>
										<!--end::Label-->
										<!--begin::Username-->
										<input type="text" placeholder="Masukkan Username Anda" name="username" autocomplete="off" class="form-control form-control-solid bg-light-secondary rounded-3" />
										<!--end::Username-->
									</div>
									<!--end::Input group-->
									
									<!--begin::Input group-->
									<div class="fv-row mb-10" data-kt-password-meter="true">
										<!--begin::Wrapper-->
										<div class="d-flex flex-stack mb-2">
											<!--begin::Label-->
											<label class="form-label fs-6 fw-bold text-gray-900 mb-0">Password</label>
											<!--end::Label-->
										</div>
										<!--end::Wrapper-->
										<!--begin::Password-->
										<div class="position-relative mb-3">
											<input class="form-control form-control-solid bg-light-secondary rounded-3" type="password" placeholder="Masukkan Password Anda" name="password" autocomplete="off" />
											<span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
												<i class="ki-duotone ki-eye-slash fs-2">
													<span class="path1"></span>
													<span class="path2"></span>
													<span class="path3"></span>
													<span class="path4"></span>
												</i>
												<i class="ki-duotone ki-eye fs-2 d-none">
													<span class="path1"></span>
													<span class="path2"></span>
													<span class="path3"></span>
												</i>
											</span>
										</div>
										<!--end::Password-->
									</div>
									<!--end::Input group-->

									<!--begin::Actions-->
									<div class="d-flex flex-stack pt-4">
										<!--begin::Submit-->
										<button type="submit" id="kt_sign_in_submit" class="btn btn-primary me-2 flex-shrink-0 w-100 py-3 rounded-3 fw-bold">
											<!--begin::Indicator label-->
											<span class="indicator-label fs-5">Masuk Sekarang</span>
											<!--end::Indicator label-->
											<!--begin::Indicator progress-->
											<span class="indicator-progress fs-5">
												Mohon tunggu...
												<span class="spinner-border spinner-border-sm align-middle ms-2"></span>
											</span>
											<!--end::Indicator progress-->
										</button>
										<!--end::Submit-->
									</div>
									<!--end::Actions-->
								</div>
								<!--end::Body-->
							</div>
						</form>
						<!--end::Form-->
					</div>
					<!--end::Wrapper-->
				</div>
				<!--end::Aside-->
				
				<!--begin::Body-->
				<div class="d-none d-lg-flex flex-lg-row-fluid w-50 flex-center position-relative overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #1e1e2d 0%, #009ef7 100%);">
					<div style="position: absolute; top: -10%; left: -10%; width: 50%; padding-bottom: 50%; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
					<div style="position: absolute; bottom: -10%; right: -10%; width: 50%; padding-bottom: 50%; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
					
					<div class="d-flex flex-column align-items-center text-center p-10" style="z-index: 1;">
						<div class="bg-white p-8 rounded-4 shadow-sm mb-12 d-flex flex-center" style="width: 200px; height: 200px;">
							<img alt="Logo" src="{{ asset('logo.png') }}" style="max-width: 100%; max-height: 100%; object-fit: contain;" />
						</div>
						<h2 class="text-white fw-bold mb-4 fs-1" style="letter-spacing: 1px;">Sistem Monitoring Terpadu</h2>
						<p class="text-white opacity-75 fw-semibold fs-5 max-w-450px lh-lg">
							Pantau aktivitas anggota dan kelola data klien dengan lebih mudah, cepat, dan akurat melalui satu platform cerdas.
						</p>
					</div>
				</div>
				<!--end::Body-->
			</div>
			<!--end::Authentication - Sign-in-->
		</div>
		<!--end::Root-->
		<!--begin::Javascript-->
		<script>var hostUrl = "assets/";</script>
		<!--begin::Global Javascript Bundle(mandatory for all pages)-->
		<script src="assets/plugins/global/plugins.bundle.js"></script>
		<script src="assets/js/scripts.bundle.js"></script>
		<!--end::Global Javascript Bundle-->
		<script>
			document.getElementById('loginForm').addEventListener('submit', function(e) {
				e.preventDefault();
				const form = e.target;
				const btn = document.getElementById('kt_sign_in_submit');
				const indicatorLabel = btn.querySelector('.indicator-label');
				const indicatorProgress = btn.querySelector('.indicator-progress');
				
				indicatorLabel.style.display = 'none';
				indicatorProgress.style.display = 'inline-block';
				btn.disabled = true;

				const formData = new FormData(form);
				const data = Object.fromEntries(formData.entries());

				fetch(form.action, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'Accept': 'application/json'
					},
					body: JSON.stringify(data)
				})
				.then(response => response.json().then(data => ({status: response.status, body: data})))
				.then(result => {
					indicatorLabel.style.display = 'inline-block';
					indicatorProgress.style.display = 'none';
					btn.disabled = false;

					if (result.status === 200 && result.body.authorization) {
						// Save token
						localStorage.setItem('jwt_token', result.body.authorization.token);
						localStorage.setItem('user_data', JSON.stringify(result.body.user));
						
						Swal.fire({
							text: "You have successfully logged in!",
							icon: "success",
							buttonsStyling: false,
							confirmButtonText: "Ok, got it!",
							customClass: {
								confirmButton: "btn btn-primary"
							}
						}).then(() => {
							// Redirect to dashboard
							window.location.href = '{{ route('dashboard') }}';
						});
					} else {
						Swal.fire({
							text: result.body.message || "Sorry, the username or password is incorrect, please try again.",
							icon: "error",
							buttonsStyling: false,
							confirmButtonText: "Ok, got it!",
							customClass: {
								confirmButton: "btn btn-primary"
							}
						});
					}
				})
				.catch(error => {
					indicatorLabel.style.display = 'inline-block';
					indicatorProgress.style.display = 'none';
					btn.disabled = false;
					
					Swal.fire({
						text: "Sorry, looks like there are some errors detected, please try again.",
						icon: "error",
						buttonsStyling: false,
						confirmButtonText: "Ok, got it!",
						customClass: {
							confirmButton: "btn btn-primary"
						}
					});
				});
			});
		</script>
		<!--end::Custom Javascript-->
		<!--end::Javascript-->
	</body>
	<!--end::Body-->
</html>