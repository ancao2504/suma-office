@extends('layouts.orders.pembayaranfaktur.pembayaranfaktur')
@section('containerpembayaranfaktur')
    <div class="row g-0">
        <div class="card card-flush">
            <div class="card-header align-items-center border-0 mt-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Belum Terbayar</span>
                    <span class="text-muted fw-bold fs-7">Daftar faktur yang belum terbayar</span>
                </h3>
                <div class="card-toolbar">
                    <button id="btnFilterPembayaran" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFilter">
                        <i class="bi bi-funnel-fill fs-4 me-2"></i>Filter
                    </button>
                </div>
            </div>
        </div>

        <div id="dataPembayaran">
            @include('layouts.orders.pembayaranfaktur.pembayaranfakturlist')
        </div>
        <div id="dataLoadPembayaran"></div>
    </div>

    <div class="modal fade" tabindex="-1" id="modalFilter">
        <div class="modal-dialog">
            <div class="modal-content" id="modalContentFilter">
                <form id="formFilter" name="formFilter" autofill="off" autocomplete="off" method="get" action="{{ route('orders.pembayaran-faktur-belum-terbayar') }}">
                    <div class="modal-header">
                        <h5 id="modalTitle" name="modalTitle" class="modal-title">Filter Faktur (Belum Terbayar)</h5>
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
                            <label class="form-label">Nomor Faktur:</label>
                            <div class="input-group has-validation mb-2">
                                <input id="inputFilterNomorFaktur" name="nomor_faktur" type="search" class="form-control" placeholder="Semua Nomor Faktur"
                                    @if(isset($nomor_faktur)) value="{{ $nomor_faktur }}" @else value="{{ old('nomor_faktur') }}"@endif>
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
                blockIndex.block();
                document.getElementById("formFilter").submit();
            });

            var targetDataPembayaran = document.querySelector("#dataPembayaran");
            var blockDataPembayaran = new KTBlockUI(targetDataPembayaran, {
                message: '<div class="blockui-message" style="position: fixed;top: 50%;left: 50%;transform: translate(-50%, -50%);">'+
                            '<span class="spinner-border text-primary"></span> Loading...'+
                        '</div>'
            });

            var pages = 1;

            $(window).scroll(function() {
                if(blockDataPembayaran.isBlocked() === false) {
                    if($(window).scrollTop() >= $(document).height() - $(window).height() - 10) {
                        const params = new URLSearchParams(window.location.search)
                        for (const param of params) {
                            var start_date = params.get('start_date');
                            var end_date = params.get('month');
                            var salesman = params.get('salesman');
                            var dealer = params.get('dealer');
                            var nomor_faktur = params.get('nomor_faktur');
                        }
                        pages++;
                        loadMoreData(start_date, end_date, salesman, dealer, nomor_faktur, pages);
                    }
                }
            });

            window.onbeforeunload = function () {
                window.scrollTo(0, 0);
            }

            async function loadMoreData(start_date, end_date, salesman, dealer, nomor_faktur, pages) {
                blockDataPembayaran.block();

                $.ajax({
                    url: "{{ route('orders.pembayaran-faktur-belum-terbayar') }}",
                    type: "get",
                    data: { start_date: start_date, end_date: end_date, page: pages,
                            salesman: salesman, dealer: dealer, nomor_faktur: nomor_faktur },

                    success:function(response) {
                        if(response.html == '') {
                            $('#dataLoadPembayaran').html('<center><div class="fw-bolder fs-3 text-gray-600 text-hover-primary mt-10 mb-10">- No more record found -</div><center>');
                            blockDataPembayaran.release();
                            return;
                        }
                        $("#dataPembayaran").append(response.html);
                        blockDataPembayaran.release();
                    },
                    error:function() {
                        blockDataPembayaran.release();
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
                $('#btnFilterPembayaran').on('click', function(e) {
                    e.preventDefault();

                    $('#inputFilterSalesman').val('{{ $kode_sales }}');
                    $('#inputFilterDealer').val('{{ $kode_dealer }}');
                    $('#inputFilterNomorFaktur').val('{{ $nomor_faktur }}');

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
                    $('#inputFilterNomorFaktur').val('');
                    @elseif($role_id == 'D_H3')
                    $('#inputFilterNomorFaktur').val('');
                    @else
                    $('#inputFilterSalesman').val('');
                    $('#inputFilterDealer').val('');
                    $('#inputFilterNomorFaktur').val('');
                    @endif;
                });
            });
        </script>
    @endpush
@endsection
