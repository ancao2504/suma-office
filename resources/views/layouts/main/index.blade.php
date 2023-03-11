<!DOCTYPE html>
<html lang="en">
	<head>
		<title>{{ env('APP_NAME') }}</title>
		<meta charset="utf-8" />
		<meta name="description" content="Program aplikasi member PT. Kharisma Suma Jaya Sakti divisi Honda" />
		<meta name="keywords" content="Sparepart Motor Honda, PT. Kharisma Suma Jaya Sakti, Suma Honda, Suma, Honda" />
        <meta name="viewport" content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' />
		<meta property="og:locale" content="en_US" />
		<meta property="og:type" content="article" />
		<meta property="og:title" content="{{ env('APP_NAME') }}" />
		<meta property="og:url" content="{{ env('APP_URL') }}" />
		<meta property="og:site_name" content="Suma | Honda" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
		<link rel="shortcut icon" href="{{ asset('assets/images/logo/ic_suma.png') }}" />

		<!--begin::Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
        <link rel="stylesheet" href="{{ asset('assets/plugins/font-awesome/css/font-awesome.min.css') }}">

		<!--begin::Page Vendor Stylesheets(used by this page)-->
		<link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />

		<!--begin::Global Stylesheets Bundle(used by all pages)-->
		<link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/css/stylesuma.css') }}" rel="stylesheet" type="text/css" />

        <!-- PWA  -->
        <meta name="theme-color" content="#6777ef"/>
        <link rel="apple-touch-icon" href="{{ asset('logo.PNG') }}">
        <link rel="manifest" href="{{ asset('/manifest.json') }}">

        @stack('styles')
    </head>

    <!--begin::Body-->
	<body id="kt_body" class="print-content-only">
        <div id="loading" style="position: fixed; width: 100%; height: 100%; overflow: hidden; background: rgba(0, 0, 0, 0.5); z-index: 9999; display: none;">
            <div class="d-flex flex-column flex-center w-100 h-100">
                <div class="text-center" id="loading-message">
                </div>
            </div>
        </div>
		<div class="d-flex flex-column flex-root">
			<div class="page d-flex flex-row flex-column-fluid">
				@include('layouts.main.sidebarleft')

				<div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
					@include('layouts.main.header')
					<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                        @csrf
                        <div id="infoCartTotal"></div>
						<div class="post d-flex flex-column-fluid" id="kt_post">
							<div id="kt_content_container" class="container-xxl">
								@yield('container')
							</div>
						</div>
					</div>
					<!--end::Content-->

					<!--begin::Footer-->
					<div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
						<div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
							<div class="text-dark order-2 order-md-1">
								<span class="text-muted fw-bold me-1">2022©</span>
								<span class="text-muted fw-bold me-1">PT. Kharisma Suma Jaya Sakti</span>
							</div>
						</div>
					</div>
					<!--end::Footer-->
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Page-->
		</div>
		<!--end::Root-->

		<!--begin::Scrolltop-->
		<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
			<span class="svg-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
					<rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor" />
					<path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor" />
				</svg>
			</span>
		</div>
		<!--end::Scrolltop-->

        <div class="modal fade" tabindex="-1" id="modalSalesmanDealerIndex">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="formSalesmanDealerIndex" name="formSalesmanDealerIndex" autofill="off" autocomplete="off" method="POST" action="{{ route('orders.cart.simpan-draft') }}">
                        @csrf
                        <div class="modal-header">
                            <h5 id="modalTitle" name="modalTitle" class="modal-title">Entry Salesman & Dealer</h5>
                            <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                                <span class="svg-icon svg-icon-muted svg-icon-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M6 19.7C5.7 19.7 5.5 19.6 5.3 19.4C4.9 19 4.9 18.4 5.3 18L18 5.3C18.4 4.9 19 4.9 19.4 5.3C19.8 5.7 19.8 6.29999 19.4 6.69999L6.7 19.4C6.5 19.6 6.3 19.7 6 19.7Z" fill="currentColor"/>
                                        <path d="M18.8 19.7C18.5 19.7 18.3 19.6 18.1 19.4L5.40001 6.69999C5.00001 6.29999 5.00001 5.7 5.40001 5.3C5.80001 4.9 6.40001 4.9 6.80001 5.3L19.5 18C19.9 18.4 19.9 19 19.5 19.4C19.3 19.6 19 19.7 18.8 19.7Z" fill="currentColor"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="fv-row">
                                <label class="required form-label">Salesman:</label>
                                <div class="input-group">
                                    <input id="inputKodeSalesIndex" name="salesmanIndex" type="text" style="cursor: pointer;" placeholder="Pilih Data Salesman"
                                        class="form-control @if(session()->get('app_user_role_id') == 'MD_H3_SM') form-control-solid @endif"
                                        @if(session()->get('app_user_role_id') == 'MD_H3_SM') value="{{ session()->get('app_user_id') }}" @endif readonly required>
                                        @if(strtoupper(trim(session()->get('app_user_role_id'))) != 'D_H3')
                                            @if(strtoupper(trim(session()->get('app_user_role_id'))) != 'MD_H3_SM')
                                            <button id="btnPilihSalesmanIndex" name="btnPilihSalesmanIndex" class="btn btn-icon btn-primary" type="button" role="button"
                                                @if(strtoupper(trim(session()->get('app_user_role_id'))) == 'D_H3') disabled
                                                @elseif(strtoupper(trim(session()->get('app_user_role_id'))) == 'MD_H3_SM') disabled @endif>
                                                <i class="fa fa-search"></i>
                                            </button>
                                            @endif
                                        @endif
                                </div>
                            </div>
                            <div class="fv-row mt-8">
                                <label class="required form-label">Dealer:</label>
                                <div class="input-group">
                                    <input id="inputKodeDealerIndex" name="dealerIndex" type="text" style="cursor: pointer;" class="form-control"
                                        placeholder="Pilih Data Dealer" readonly required>
                                    <button id="btnPilihDealerIndex" name="btnPilihDealerIndex" class="btn btn-icon btn-primary" type="button" role="button">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" role="button" name="form" value="index" class="btn btn-primary">Simpan</button>
                            <button id="btnClose" name="btnClose" type="button" role="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @yield('after-container')
        <script src="{{ asset('/sw.js') }}"></script>
        <script>
            if (!navigator.serviceWorker.controller) {
                navigator.serviceWorker.register("/sw.js").then(function (reg) {
                    console.log("Service worker has been registered for scope: " + reg.scope);
                });
            }
        </script>
		<!--begin::Global Javascript Bundle(used by all pages)-->
		<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
		<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
		<!--end::Global Javascript Bundle-->

		<!--begin::Page Vendors Javascript(used by this page)-->
		<script src="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>
		<!--end::Page Vendors Javascript-->

		<!--begin::Page Custom Javascript(used by this page)-->
		<script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
		<script src="{{ asset('assets/js/custom/widgets.js') }}"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.4.1/jquery.jscroll.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>


		<!--begin::Javascript-->
		<script>
            const base_url = "{{ url('/') }}";
            const url_dsb = {
                estimasi_cart: "{{ route('orders.cart.index.estimasi-cart') }}",
                cart_index: "{{ route('orders.cart.index.index') }}"
            };

            window.onbeforeunload = function(event) {
                loading.block();
            };

            // dokumen ready
            $(document).ready(function() {
                @if(session()->get('app_user_role_id') == 'MD_H3_MGMT' || session()->get('app_user_role_id') == 'MD_H3_KORSM' ||
                    session()->get('app_user_role_id') == 'MD_H3_SM')
                    @if ($title_menu != 'Cart')
                    estimasiTotalCart();
                    @else
                        $('#kt_body').addClass('header-fixed header-tablet-and-mobile-fixed aside-enabled aside-fixed');
                        document.getElementById('kt_body').removeAttribute("style");
                    @endif
                @else
                    $('#kt_body').addClass('header-fixed header-tablet-and-mobile-fixed aside-enabled aside-fixed');
                    document.getElementById('kt_body').removeAttribute("style");
                @endif
            });
        </script>
        <script src="{{ asset('assets/js/suma/main/index.js') }}?v={{ time() }}"></script>
		<script src="{{ asset('assets/js/custom/module/loading.js') }}?v={{ time() }}"></script>
        <!-- App scripts -->
        @stack('scripts')

        @extends('components.swalfailed')
        @extends('components.swalsuccess')

        @include('layouts.option.optionsalesmanindex')
        @include('layouts.option.optiondealerindex')

        <script src="{{ asset('assets/js/suma/option/salesmanindex.js') }}?v={{ time() }}"></script>
        <script src="{{ asset('assets/js/suma/option/dealerindex.js') }}?v={{ time() }}"></script>

	</body>
</html>
