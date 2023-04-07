<!DOCTYPE html>
<html lang="en">
	<!--begin::Head-->
	<head>
        <base href="">
		<title>{{ env('APP_NAME') }}</title>
		<meta charset="utf-8" />
		<meta name="description" content="Program aplikasi member PT. Kharisma Suma Jaya Sakti divisi Honda" />
		<meta name="keywords" content="Sparepart Motor Honda, PT. Kharisma Suma Jaya Sakti, Suma Honda, Suma, Honda" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta property="og:locale" content="en_US" />
		<meta property="og:type" content="article" />
		<meta property="og:title" content="{{ env('APP_NAME') }}" />
		<meta property="og:url" content="{{ env('APP_URL') }}" />
		<meta property="og:site_name" content="Suma | Honda" />
		<link rel="canonical" href="https://preview.keenthemes.com/metronic8" />
		<link rel="shortcut icon" href="{{ asset('assets/images/logo/ic_suma.png') }}" />
		<!--begin::Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Page Vendor Stylesheets(used by this page)-->
		<link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
		<!--end::Page Vendor Stylesheets-->
		<!--begin::Global Stylesheets Bundle(used by all pages)-->
		<link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
		<!--end::Global Stylesheets Bundle-->
		@stack('styles')
	</head>

	<body id="kt_body">
		<div class="d-flex flex-column flex-root">
			<div class="page d-flex flex-row flex-column-fluid">
				<div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
					<div class="post d-flex flex-column-fluid" id="kt_post">
                        <div id="kt_content_container" class="container-xxl">
                            <div class="card card-flush">
                                <div class="card-body">
                                    <div class="d-flex flex-row">
                                        <div class="d-flex flex-column">
                                            <img alt="Logo" src="{{ asset('assets/images/logo/bg_logo_suma.png') }}" class="h-60px" />
                                        </div>

                                        <div class="d-flex flex-column px-5">
                                            <div class="row g-0">
                                                <span class="text-muted fw-bold fs-7">{{ $nama_company }}</span>
                                            </div>
                                            <div class="row g-0">
                                                <span class="text-muted fw-bold fs-7">{{ $alamat_company }}</span>
                                            </div>
                                            <div class="row g-0">
                                                <span class="text-muted fw-bold fs-7">{{ $kota_company }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <h3 class="fw-bold text-gray-800 text-center py-5">
                                        <span class="fw-bolder mb-2 text-dark">@yield('title')</span><br>
                                        <span class="text-muted fw-bold fs-7">@yield('subtitle')</span>
                                    </h3>
                                    @yield('container')
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		</div>

		<!--begin::Global Javascript Bundle(used by all pages)-->
		<script src="{{ URL::asset('assets/plugins/global/plugins.bundle.js') }}"></script>
		<script src="{{ URL::asset('assets/js/scripts.bundle.js') }}"></script>

		<!--begin::Page Vendors Javascript(used by this page)-->
		<script src="{{ URL::asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>
		<script src="{{ URL::asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

		<!--begin::Page Custom Javascript(used by this page)-->
		<script src="{{ URL::asset('assets/js/widgets.bundle.js') }}"></script>
		<script src="{{ URL::asset('assets/js/custom/widgets.js') }}"></script>
		<script src="{{ URL::asset('assets/js/custom/apps/chat/chat.js') }}"></script>
		<script src="{{ URL::asset('assets/js/custom/utilities/modals/upgrade-plan.js') }}"></script>
		<script src="{{ URL::asset('assets/js/custom/utilities/modals/create-app.js') }}"></script>
		<script src="{{ URL::asset('assets/js/custom/utilities/modals/users-search.js') }}"></script>

        @stack('scripts')
	</body>
</html>
