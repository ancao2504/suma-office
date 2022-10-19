<!DOCTYPE html>
<html lang="en">
	<head>
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
		<link rel="shortcut icon" href="{{ asset('assets/images/logo/ic_suma.png') }}" />

		<!--begin::Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />

		<!--begin::Page Vendor Stylesheets(used by this page)-->
		<link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />

		<!--begin::Global Stylesheets Bundle(used by all pages)-->
		<link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/css/stylesuma.css') }}" rel="stylesheet" type="text/css" />
        @stack('styles')
    </head>

    <!--begin::Body-->
	<body id="kt_body" class="print-content-only">
        <div id="loading" style="position: fixed; width: 100%; height: 100%; overflow: hidden; background: rgba(0, 0, 0, 0.5); z-index: 9999; display: none;">
            <div class="d-flex flex-column flex-center w-100 h-100">
                <div class="text-center" id="loading-massage">
                </div>
            </div>
        </div>
		<div class="d-flex flex-column flex-root">
			<div class="page d-flex flex-row flex-column-fluid">
				@extends('layouts.main.sidebarleft')

				<div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
					@extends('layouts.main.header')
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
                    <form id="formSalesmanDealerIndex" name="formSalesmanDealerIndex" autofill="off" autocomplete="off" method="POST" action="{{ route('orders.cart-simpan-draft') }}">
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
                                <label class="required form-label">Salesman</label>
                                <div class="input-group">
                                    <input id="inputKodeSalesIndex" name="salesmanIndex" type="text" class="form-control @if(session()->get('app_user_role_id') == 'MD_H3_SM') form-control-solid @endif"
                                        @if(session()->get('app_user_role_id') == 'MD_H3_SM') value="{{ session()->get('app_user_id') }}" @endif readonly required>

                                        @if(strtoupper(trim(session()->get('app_user_role_id'))) != 'D_H3')
                                            @if(strtoupper(trim(session()->get('app_user_role_id'))) != 'MD_H3_SM')
                                            <button id="btnPilihSalesmanIndex" name="btnPilihSalesmanIndex" class="btn btn-icon btn-primary" type="button"
                                                data-toggle="modal" data-target="#salesmanSearchModalIndex"
                                                @if(strtoupper(trim(session()->get('app_user_role_id'))) == 'D_H3') disabled
                                                @elseif(strtoupper(trim(session()->get('app_user_role_id'))) == 'MD_H3_SM') disabled @endif>
                                                <i class="fa fa-search"></i>
                                            </button>
                                            @endif
                                        @endif
                                </div>
                            </div>
                            <div class="fv-row mt-8">
                                <label class="required form-label">Dealer</label>
                                <div class="input-group">
                                    <input id="inputKodeDealerIndex" name="dealerIndex" style="text-transform: uppercase" type="text" class="form-control" readonly required>
                                    <button id="btnPilihDealerIndex" name="btnPilihDealerIndex" class="btn btn-icon btn-primary" type="button"
                                        data-toggle="modal" data-target="#dealerSearchModalIndex">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="form" value="index" class="btn btn-primary">Simpan</button>
                            <button id="btnClose" name="btnClose" type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


		<!--begin::Javascript-->
		<script>const base_url = "{{ url('/') }}";</script>

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


		<script src="{{ asset('assets/js/custom/module/loading.js') }}?v={{ time() }}"></script>
        <!-- App scripts -->
        @stack('scripts')

        @extends('components.swalfailed')
        @extends('components.swalsuccess')

        @include('layouts.option.optionsalesmanindex')
        @include('layouts.option.optiondealerindex')

        <script type="text/javascript">
            $('body').on('click', '.menu-item a', function(e) {
                loading.block();
            });

            $('body').on('keypress', '#searchHeaderParts', function(e) {
                if (e.which == 13) {
                    loading.block();
                }
            });

            @if ($title_menu != 'Cart')
                getInfoDataCart();
            @else
                $('#kt_body').addClass('header-fixed header-tablet-and-mobile-fixed aside-enabled aside-fixed');
                document.getElementById('kt_body').removeAttribute("style");
            @endif

            function getInfoDataCart() {
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('header.cart-total') }}",
                    method: "POST",
                    data: { _token: _token },

                    success:function(response) {
                        if (response.status == true) {
                            $('#infoCartTotal').html(response.view_total_estimate_cart);
                            if(response.view_total_item_cart > 0) {
                                $('#infoItemCart').html(response.view_total_item_cart);
                            } else {
                                $('#infoItemCart').html('');
                            }
                            $('#kt_body').addClass('header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed');
                            document.getElementById('kt_body').style.cssText = '--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px';
                        } else {
                            $('#infoItemCart').html('');
                            $('#infoCartTotal').html('');
                            $('#kt_body').addClass('header-fixed header-tablet-and-mobile-fixed aside-enabled aside-fixed');
                            document.getElementById('kt_body').removeAttribute("style");
                        }
                    }
                });
            }

            function openModalSalesDealer() {
                var _token = $('input[name="_token"]').val();

                $.ajax({
                    url: "{{ route('orders.edit-header-cart') }}",
                    method: "POST",
                    data: { _token: _token },
                    success:function(response) {
                        if (response.status == false) {
                            Swal.fire({
                                text: response.message,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-danger"
                                }
                            });
                        } else {
                            if(response.data != null) {
                                $('#inputKodeSalesIndex').val(response.data.salesman);
                                $('#inputKodeDealerIndex').val(response.data.dealer);
                            }

                            $('#modalSalesmanDealerIndex').modal({backdrop: 'static', keyboard: false});
                            $('#modalSalesmanDealerIndex').modal('show');
                        }
                    }
                });
            }

            $(document).ready(function() {
                $('body').on('click', '#btnSalesmanDealerIndex', function(e) {
                    openModalSalesDealer();
                });

                function loadDataSalesmanIndex(page = 1, per_page = 10, search = '') {
                    $.ajax({
                        url: "{{ route('option.option-salesman') }}?search="+search+"&per_page="+per_page+"&page="+page,
                        method: "get",
                        success:function(response) {
                            if (response.status == false) {
                                Swal.fire({
                                    text: response.message,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-danger"
                                    }
                                });
                            } else {
                                $('#salesmanContentModalIndex').html(response.data);
                            }
                        }
                    });
                }

                $(document).on('click', '#searchSalesmanFormIndex  #pagesalesman .pagination .page-item a', function() {
                    pagesIndex = $(this)[0].getAttribute("data-page");
                    pageIndex = pagesIndex.split('?page=')[1];

                    var search_sales_index = $('#searchSalesmanFormIndex #inputSearchSalesmanIndex').val();
                    var per_page_sales_index = $('#searchSalesmanFormIndex #salesmanContentModalIndex #pagesalesman #selectPerPageSalesman').val();

                    loadDataSalesmanIndex(pageIndex, per_page_sales_index, search_sales_index);
                });

                $('body').on('change', '#searchSalesmanFormIndex #salesmanContentModalIndex #pagesalesman #selectPerPageSalesman', function(e) {
                    e.preventDefault();
                    pagesIndex = $('#searchSalesmanFormIndex #pagesalesman .pagination .page-item a')[0].getAttribute("data-page");
                    pageIndex = pagesIndex.split('?page=')[1];

                    var start_record_sales_index = $('#searchSalesmanFormIndex #salesmanContentModalIndex #pagesalesman #selectPerPageSalesmanInfo #startRecordSalesman').html();
                    var search_sales_index = $('#searchSalesmanFormIndex #inputSearchSalesmanIndex').val();
                    var per_page_sales_index = $('#searchSalesmanFormIndex #salesmanContentModalIndex #pagesalesman #selectPerPageSalesman').val();

                    var page = Math.ceil(start_record_sales_index / per_page_sales_index);

                    loadDataSalesmanIndex(pageIndex, per_page_sales_index, search_sales_index);
                });

                $('body').on('click', '#searchSalesmanFormIndex #btnSearchSalesmanIndex', function(e) {
                    e.preventDefault();
                    var search_sales_index = $('#searchSalesmanFormIndex #inputSearchSalesmanIndex').val();
                    var per_page_sales_index = $('#searchSalesmanFormIndex #salesmanContentModalIndex #pagesalesman #selectPerPageSalesman').val();

                    loadDataSalesmanIndex(1, per_page_sales_index, search_sales_index);
                });

                $('body').on('click', '#btnPilihSalesmanIndex', function(e) {
                    loadDataSalesmanIndex();
                    $('#searchSalesmanFormIndex').trigger('reset');
                    $('#salesmanSearchModalIndex').modal('show');
                });

                $('body').on('click', '#searchSalesmanFormIndex #salesmanContentModalIndex #selectSalesman', function(e) {
                    e.preventDefault();
                    var salesman_index = $(this).data('kode_sales');

                    $('#inputKodeSalesIndex').val(salesman_index);
                    $('#inputKodeDealerIndex').val('');
                    $('#salesmanSearchModalIndex').modal('hide');
                    $('#inputKodeSalesIndex').focus();
                });

                function loadDataDealerIndex(salesman = '', page = 1, per_page = 10, search = '') {
                    $.ajax({
                        url: "{{ route('option.option-dealer-index') }}?salesman="+salesman+"&search="+search+"&per_page="+per_page+"&page="+page,
                        method: "get",
                        success:function(response) {
                            if (response.status == false) {
                                Swal.fire({
                                    text: response.message,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-danger"
                                    }
                                });
                            } else {
                                $('#dealerContentModalIndex').html(response.data);
                            }
                        }
                    });
                }

                $(document).on('click', '#searchDealerFormIndex #pageDealerIndex .pagination .page-item a', function() {
                    pagesIndex = $(this)[0].getAttribute("data-page");
                    pageIndex = pagesIndex.split('?page=')[1];

                    var salesmanIndex = $('#inputKodeSalesIndex').val();
                    var search_dealer_index = $('#searchDealerFormIndex #inputSearchDealerIndex').val();
                    var per_page_dealer_index = $('#searchDealerFormIndex #dealerContentModalIndex #pageDealerIndex #selectPerPageDealerIndex').val();

                    loadDataDealerIndex(salesmanIndex, pageIndex, per_page_dealer_index, search_dealer_index);
                });

                $('body').on('change', '#searchDealerFormIndex #dealerContentModalIndex #pageDealerIndex #selectPerPageDealerIndex', function(e) {
                    e.preventDefault();
                    var salesmanIndex = $('#inputKodeSalesIndex').val();
                    var start_record_dealer_index = $('#searchDealerFormIndex #dealerContentModalIndex #pageDealerIndex #selectPerPageDealerInfo #startRecordDealer').html();
                    var search_dealer_index = $('#searchDealerFormIndex #inputSearchDealerIndex').val();
                    var per_page_dealer_index = $('#searchDealerFormIndex #dealerContentModalIndex #pageDealerIndex #selectPerPageDealerIndex').val();

                    var pageIndex = Math.ceil(start_record_dealer_index / per_page_dealer_index);

                    loadDataDealerIndex(salesmanIndex, pageIndex, per_page_dealer_index, search_dealer_index);
                });

                $('body').on('click', '#searchDealerFormIndex #btnSearchDealerIndex', function(e) {
                    e.preventDefault();
                    var salesmanIndex = $('#inputKodeSalesIndex').val();
                    var search_dealer_index = $('#searchDealerFormIndex #inputSearchDealerIndex').val();
                    var per_page_dealer_index = $('#searchDealerFormIndex #dealerContentModalIndex #pageDealerIndex #selectPerPageDealerIndex').val();

                    loadDataDealerIndex(salesmanIndex, 1, per_page_dealer_index, search_dealer_index);
                });

                $('body').on('click', '#modalSalesmanDealerIndex #btnPilihDealerIndex', function(e) {
                    var salesmanIndex = $('#inputKodeSalesIndex').val();

                    if(salesmanIndex == '') {
                        Swal.fire({
                            text: "Pilih kode sales terlebih dahulu",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-danger"
                            }
                        });
                    } else {
                        loadDataDealerIndex(salesmanIndex, 1, 10, '');
                        $('#searchDealerFormIndex').trigger('reset');
                        $('#dealerSearchModalIndex').modal('show');
                    }
                });

                $('body').on('click', '#searchDealerFormIndex #dealerContentModalIndex #selectDealerIndex', function(e) {
                    e.preventDefault();
                    var dealerIndex = $(this).data('kode_dealer');

                    $('#inputKodeDealerIndex').val(dealerIndex);
                    $('#dealerSearchModalIndex').modal('hide');
                    $('#inputKodeDealerIndex').focus();
                });
            });
        </script>
	</body>
</html>
