@extends('layouts.main.index')
@section('title','Parts')
@section('subtitle','Back Order')
@section('container')
    <div class="row g-0">
        <div class="card card-flush">
            <div class="card-header align-items-center border-0 mt-4 mb-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Back Order</span>
                    <span class="text-muted fw-bold fs-7">Daftar back order suma honda</span>
                </h3>
                <div class="card-toolbar">
                    <button id="btnFilterBackOrder" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFilter">
                        <i class="bi bi-funnel-fill fs-4 me-2"></i>Filter
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5" id="dataBackOrder">
        @if(strtoupper(trim($device)) == "DESKTOP")
            @include('layouts.parts.backorder.desktop.backorderlist')
        @else
            @include('layouts.parts.backorder.mobile.backorderlist')
        @endif
    </div>
    <div id="dataLoadBackOrder"></div>

    <div class="modal fade" tabindex="-1" id="modalFilter">
        <div class="modal-dialog">
            <div class="modal-content" id="modalFilterContent">
                <form id="formFilter" name="formFilter" autofill="off" autocomplete="off" method="get" action="{{ route('parts.back-order') }}">
                    <div class="modal-header">
                        <h5 id="modalTitle" name="modalTitle" class="modal-title">Filter Back Order</h5>
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
                            <label class="form-label">Salesman:</label>
                            <div class="input-group">
                                <input id="inputFilterSalesman" name="salesman" type="search" class="form-control" placeholder="Semua Salesman" readonly
                                    @if(isset($kode_sales)) value="{{ $kode_sales }}" @else value="{{ old('kode_sales') }}"@endif>
                                @if($role_id != 'MD_H3_SM')
                                    @if($role_id != 'D_H3')
                                    <button id="btnFilterPilihSalesman" name="btnFilterPilihSalesman" class="btn btn-icon btn-primary" type="button"
                                        data-toggle="modal" data-target="#salesmanSearchModal">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label">Dealer:</label>
                            <div class="input-group">
                                <input id="inputFilterDealer" name="dealer" type="search" class="form-control" placeholder="Semua Dealer" readonly
                                    @if(isset($kode_dealer)) value="{{ $kode_dealer }}" @else value="{{ old('kode_dealer') }}"@endif>
                                @if($role_id != 'D_H3')
                                <button id="btnFilterPilihDealer" name="btnFilterPilihDealer" class="btn btn-icon btn-primary" type="button"
                                    data-toggle="modal" data-target="#dealerSearchModal">
                                    <i class="fa fa-search"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label">Part Number:</label>
                            <div class="input-group has-validation mb-2">
                                <input id="inputFilterPartNumber" name="part_number" type="search" class="form-control" placeholder="Semua Part Number"
                                    @if(isset($part_number)) value="{{ $part_number }}" @else value="{{ old('part_number') }}"@endif>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button id="btnFilterReset" class="btn btn-danger" role="button">Reset Filter</button>
                        <div class="text-end">
                            <button id="btnFilterProses" type="submit" class="btn btn-primary">Terapkan</button>
                            <button id="btnFilterClose" name="btnClose" type="button" class="btn btn-light text-end" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('layouts.option.optionsalesman')
    @include('layouts.option.optiondealer')

    @push('scripts')
        <script src="{{ asset('assets/js/suma/option/option.js') }}"></script>
        <script type="text/javascript">
            var btnFilterProses = document.querySelector("#btnFilterProses");
            btnFilterProses.addEventListener("click", function(e) {
                e.preventDefault();
                loading.block();
                document.getElementById("formFilter").submit();
            });

            var pages = 1;

            $(window).scroll(function() {
                if(loading.isBlocked() === false) {
                    if($(window).scrollTop() >= $(document).height() - $(window).height() - 10) {
                        const params = new URLSearchParams(window.location.search)
                        for (const param of params) {
                            var salesman = params.get('salesman');
                            var dealer = params.get('dealer');
                            var part_number = params.get('part_number');
                        }
                        pages++;
                        loadMoreData(salesman, dealer, part_number, pages);
                    }
                }
            });

            window.onbeforeunload = function () {
                window.scrollTo(0, 0);
            }

            async function loadMoreData(salesman, dealer, part_number, pages) {
                loading.block();

                $.ajax({
                    url: "{{ route('parts.back-order') }}",
                    type: "get",
                    data: { salesman: salesman, dealer: dealer, part_number: part_number, page: pages },

                    success:function(response) {
                        if(response.html == '') {
                            $('#dataLoadBackOrder').html('<center><div class="fw-bolder fs-3 text-gray-600 text-hover-primary mt-10 mb-10">- No more record found -</div><center>');
                            loading.release();
                            return;
                        }
                        $("#dataBackOrder").append(response.html);
                        loading.release();
                    },
                    error:function() {
                        loading.release();
                        pages = pages - 1;

                        Swal.fire({
                            text: "Gagal mengambil data ke dalam server, Coba lagi",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-danger"
                            }
                        });
                    }
                });
            }

            $(document).ready(function() {
                $('#btnFilterBackOrder').on('click', function(e) {
                    e.preventDefault();

                    $('#inputFilterSalesman').val('{{ $kode_sales }}');
                    $('#inputFilterDealer').val('{{ $kode_dealer }}');
                    $('#inputFilterPartNumber').val('{{ $part_number }}');

                    $('#modalFilter').modal('show');
                });

                $('#btnFilterPilihSalesman').on('click', function(e) {
                    e.preventDefault();
                    loadDataSalesman();
                    $('#searchSalesmanForm').trigger('reset');
                    $('#salesmanSearchModal').modal('show');
                });

                $('body').on('click', '#salesmanContentModal #selectSalesman', function(e) {
                    e.preventDefault();
                    $('#inputFilterSalesman').val($(this).data('kode_sales'));
                    $('#salesmanSearchModal').modal('hide');
                });

                $('#btnFilterPilihDealer').on('click', function(e) {
                    e.preventDefault();
                    loadDataDealer(1, 10, '');
                    $('#searchDealerForm').trigger('reset');
                    $('#dealerSearchModal').modal('show');
                });


                $('body').on('click', '#dealerContentModal #selectDealer', function(e) {
                    e.preventDefault();
                    $('#inputFilterDealer').val($(this).data('kode_dealer'));
                    $('#dealerSearchModal').modal('hide');
                });


                $('#btnFilterReset').on('click', function(e) {
                    e.preventDefault();

                    @if($role_id == 'MD_H3_SM')
                    $('#inputFilterDealer').val('');
                    $('#inputFilterPartNumber').val('');
                    @elseif($role_id == 'D_H3')
                    $('#inputFilterPartNumber').val('');
                    @else
                    $('#inputFilterSalesman').val('');
                    $('#inputFilterDealer').val('');
                    $('#inputFilterPartNumber').val('');
                    @endif;
                });
            });
        </script>
    @endpush
@endsection
