@extends('layouts.main.index')
@section('title','Parts')
@section('subtitle','Part Number')
@section('container')
    <div class="row g-0">
        <div class="card card-flush">
            <div class="card-header align-items-center border-0 mt-4 mb-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Part Number</span>
                    <span class="text-muted fw-bold fs-7">Daftar parts suma honda</span>
                </h3>
                <div class="card-toolbar">
                    <button id="btnFilterPartNumber" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFilter">
                        <i class="bi bi-funnel-fill fs-4 me-2"></i>Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5" id="dataPartNumber">
        @if (strtoupper(trim($device)) == 'DESKTOP')
            @include('layouts.parts.partnumber.desktop.partnumberlist')
        @else
            @include('layouts.parts.partnumber.mobile.partnumberlist')
        @endif
    </div>
    <div id="dataLoadPartNumber"></div>

    <div class="modal fade bs-example-modal-xl" tabindex="-1" id="modalPartNumberCart">
        <div class="modal-dialog">
            <div id="modalContentPartNumber" name="modalContentPartNumber" class="modal-content">
                <form id="partForm" name="partForm" autofill="off" autocomplete="off" method="POST" action="#">
                    @csrf
                    <div class="modal-header">
                        <h5 id="modalTitle" name="modalTitle" class="modal-title"></h5>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <span class="svg-icon svg-icon-muted svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M6 19.7C5.7 19.7 5.5 19.6 5.3 19.4C4.9 19 4.9 18.4 5.3 18L18 5.3C18.4 4.9 19 4.9 19.4 5.3C19.8 5.7 19.8 6.29999 19.4 6.69999L6.7 19.4C6.5 19.6 6.3 19.7 6 19.7Z" fill="currentColor"/>
                                    <path d="M18.8 19.7C18.5 19.7 18.3 19.6 18.1 19.4L5.40001 6.69999C5.00001 6.29999 5.00001 5.7 5.40001 5.3C5.80001 4.9 6.40001 4.9 6.80001 5.3L19.5 18C19.9 18.4 19.9 19 19.5 19.4C19.3 19.6 19 19.7 18.8 19.7Z" fill="currentColor"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div id="modalBodyPartNumber" name="modalBodyPartNumber" class="modal-body">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <span id="messageErrorPartNumber"></span>
                            <div class="form-group">
                                <span id="modalImgParts"></span>
                                <div class="fv-row mt-5 mb-2">
                                    <div class="d-flex align-items-center">
                                        <span id="modalTextPartNumber" name="part_number" class="fw-bolder fs-4 text-dark"></span>
                                    </div>
                                    <span id="modalTextDescription" name="description" class="form-label"></span>
                                </div>
                                <div class="fv-row mt-2">
                                    <span id="modalTextProduk" name="produk" class="badge badge-danger fs-8"></span>
                                </div>
                                <div class="fv-row mt-4">
                                    <span id="modalTextHargaNetto" class="fw-bolder text-dark fs-3"></span>
                                    <div class="d-flex align-items-center">
                                        <div id="modalTextDiscount"></div>
                                        <div id="modalTextHet"></div>
                                    </div>
                                </div>
                                <div class="fv-row mt-6 mb-4">
                                    <label class="col-lg-4 form-label">Type Motor:</label>
                                    <div class="row">
                                        <span id="modalListTypeMotor"></span>
                                    </div>
                                </div>
                                <div id="modalTextKeteranganBo"></div>
                                <div class="fv-row mt-8 mb-4">
                                    <label class="form-label">Jumlah Order:</label>
                                    <div class="col-md-6">
                                        <div class="input-group w-md-200px"
                                            data-kt-dialer="true"
                                            data-kt-dialer-min="1"
                                            data-kt-dialer-step="1">
                                            <button class="btn btn-icon btn-outline btn-outline-secondary" type="button" data-kt-dialer-control="decrease">
                                                <i class="bi bi-dash fs-1"></i>
                                            </button>
                                            <input id="inputJumlahOrder" type="number" min="1" class="form-control" placeholder="Amount" value="1" data-kt-dialer-control="input" />
                                            <button class="btn btn-icon btn-outline btn-outline-secondary" type="button" data-kt-dialer-control="increase">
                                                <i class="bi bi-plus fs-1"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="fv-row mt-8">
                                    <button type="button" class="btn btn-success waves-effect text-left" id="btnOrder" name="btnOrder">
                                        <div>
                                            <span class="svg-icon svg-icon-muted svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M18.041 22.041C18.5932 22.041 19.041 21.5932 19.041 21.041C19.041 20.4887 18.5932 20.041 18.041 20.041C17.4887 20.041 17.041 20.4887 17.041 21.041C17.041 21.5932 17.4887 22.041 18.041 22.041Z" fill="currentColor"/>
                                                    <path opacity="0.3" d="M6.04095 22.041C6.59324 22.041 7.04095 21.5932 7.04095 21.041C7.04095 20.4887 6.59324 20.041 6.04095 20.041C5.48867 20.041 5.04095 20.4887 5.04095 21.041C5.04095 21.5932 5.48867 22.041 6.04095 22.041Z" fill="currentColor"/>
                                                    <path opacity="0.3" d="M7.04095 16.041L19.1409 15.1409C19.7409 15.1409 20.141 14.7409 20.341 14.1409L21.7409 8.34094C21.9409 7.64094 21.4409 7.04095 20.7409 7.04095H5.44095L7.04095 16.041Z" fill="currentColor"/>
                                                    <path d="M19.041 20.041H5.04096C4.74096 20.041 4.34095 19.841 4.14095 19.541C3.94095 19.241 3.94095 18.841 4.14095 18.541L6.04096 14.841L4.14095 4.64095L2.54096 3.84096C2.04096 3.64096 1.84095 3.04097 2.14095 2.54097C2.34095 2.04097 2.94096 1.84095 3.44096 2.14095L5.44096 3.14095C5.74096 3.24095 5.94096 3.54096 5.94096 3.84096L7.94096 14.841C7.94096 15.041 7.94095 15.241 7.84095 15.441L6.54096 18.041H19.041C19.641 18.041 20.041 18.441 20.041 19.041C20.041 19.641 19.641 20.041 19.041 20.041Z" fill="currentColor"/>
                                                </svg>
                                            </span>Add To Cart
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalFilter" tabindex="-1">
        <div class="modal-dialog">
            <div id="modalFilterContent" class="modal-content">
                <form id="formFilter" name="formFilter" autofill="off" autocomplete="off" method="get" action="{{ route('parts.part-number') }}">
                    <div class="modal-header">
                        <h5 id="modalTitle" name="modalTitle" class="modal-title">Filter Part Number</h5>
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
                            <label class="form-label">Level:</label>
                            <select id="selectFilterGroupLevel" name="group_level" class="form-select" data-dropdown-parent="#modalFilter"
                                data-placeholder="Semua Level Produk" data-allow-clear="true">
                                <option value="" @if($kode_level != 'HANDLE' && $kode_level != 'HON_HANDLE' && $kode_level != 'TUBE' && $kode_level != 'OLI') selected @endif>Semua Level Produk</option>
                                <option value="HANDLE" @if($kode_level == 'HANDLE') selected @endif>Handle</option>
                                <option value="NON_HANDLE" @if($kode_level == 'NON_HANDLE') selected @endif>Non-Handle</option>
                                <option value="TUBE" @if($kode_level == 'TUBE') selected @endif>Tube</option>
                                <option value="OLI" @if($kode_level == 'OLI') selected @endif>Oli</option>
                            </select>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label">Produk:</label>
                            <div class="input-group">
                                <input id="inputFilterKodeProduk" name="group_produk" type="search" class="form-control" placeholder="Semua Produk" readonly
                                    @if(isset($kode_produk)) value="{{ $kode_produk }}" @else value="{{ old('produk') }}"@endif>
                                <button id="btnFilterProduk" name="btnFilterProduk" class="btn btn-icon btn-primary" type="button"
                                    data-toggle="modal" data-target="#produkSearchModalForm">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label">Tipe Motor:</label>
                            <div class="input-group">
                                <input id="inputFilterTipeMotor" name="tipe_motor" type="search" class="form-control" placeholder="Semua Tipe Motor" readonly
                                    @if(isset($tipe_motor)) value="{{ $tipe_motor }}" @else value="{{ old('tipe_motor') }}"@endif>
                                <button id="btnFilterPilihTipeMotor" name="btnFilterPilihTipeMotor" class="btn btn-icon btn-primary" type="button"
                                    data-toggle="modal" data-target="#tipeMotorSearchModal">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label">Part Number:</label>
                            <input type="text" id="inputFilterPartNumber" name="part_number" class="form-control" placeholder="Semua Part Number" autocomplete="off"
                                @if(isset($part_number)) value="{{ $part_number }}" @else value="{{ old('part_number') }}"@endif>
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

    @include('layouts.option.optiontipemotor')
    @include('layouts.option.optiongroupproduk')

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
                        var tipe_motor = params.get('tipe_motor');
                        var group_level = params.get('group_level');
                        var group_produk = params.get('group_produk');
                        var part_number = params.get('part_number');
                    }
                    pages++;
                    loadMoreData(tipe_motor, group_level, group_produk, part_number, pages);
                }
            }
        });

        // window.addEventListener("scroll", myFunction);
        // // saat kt_aside_footer terlihat di layar maka
        // function myFunction() {
        //     var scrollpercent = (document.body.scrollTop + document.documentElement.scrollTop) / (document.documentElement.scrollHeight - document.documentElement.clientHeight);
        //     console.log(scrollpercent);
        //     if (scrollpercent > 0.975 && scrollpercent <= 1 && loading.isBlocked() === false) {
        //         const params = new URLSearchParams(window.location.search)
        //         for (const param of params) {
        //             var tipe_motor = params.get('tipe_motor');
        //             var group_level = params.get('group_level');
        //             var group_produk = params.get('group_produk');
        //             var part_number = params.get('part_number');
        //         }
        //         pages++;
        //         loadMoreData(tipe_motor, group_level, group_produk, part_number, pages);
        //         // console.log(pages);
        //     }
        // }

        window.onbeforeunload = function () {
            window.scrollTo(0, 0);
        }

        async function loadMoreData(tipe_motor, group_level, group_produk, part_number, pages) {
            loading.block();

            $.ajax({
                url: "{{ route('parts.part-number') }}",
                type: "get",
                data: {
                    page: pages, tipe_motor: tipe_motor, group_level: group_level,
                    group_produk: group_produk, part_number: part_number,
                },
                success:function(response) {
                    if(response.html == '') {
                        $('#dataLoadPartNumber').html('<center><div class="fw-bolder fs-3 text-gray-600 text-hover-primary mt-10 mb-10">- No more record found -</div><center>');
                        loading.release();
                        return;
                    }
                    $("#dataPartNumber").append(response.html);
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

        var modalContentPartNumber = document.querySelector("#modalContentPartNumber");
        var blockmodalContentPartNumber = new KTBlockUI(modalContentPartNumber, {
            message: '<div class="blockui-message">'+
                        '<span class="spinner-border text-primary"></span> Loading...'+
                    '</div>'
        });

        $(document).ready(function() {
            $('#btnFilterPartNumber').on('click', function(e) {
                e.preventDefault();

                $('#inputFilterTipeMotor').val('{{ $tipe_motor }}');
                $("#selectFilterGroupLevel").val('{{ $kode_level }}').trigger("change");
                $('#inputFilterKodeProduk').val('{{ $kode_produk }}');
                $('#inputFilterPartNumber').val('{{ $part_number }}');

                $('#modalFilter').modal('show');
            });

            $('#btnFilterProduk').on('click', function(e) {
                e.preventDefault();

                var selectFilterGroupLevel = $('#selectFilterGroupLevel').val();
                loadDataProduk(1, 10, '', selectFilterGroupLevel);
                $('#searchProdukForm').trigger('reset');
                $('#produkSearchModal').modal('show');
            });

            $('body').on('click', '#produkContentModal #selectProduk', function(e) {
                e.preventDefault();
                $('#inputFilterKodeProduk').val($(this).data('kode_produk'));
                $('#produkSearchModal').modal('hide');
            });


            $('#btnFilterPilihTipeMotor').on('click', function(e) {
                e.preventDefault();

                loadDataTipeMotor();
                $('#searchTipeMotorForm').trigger('reset');
                $('#tipeMotorSearchModal').modal('show');
            });

            $('body').on('click', '#tipeMotorContentModal #selectTipeMotor', function(e) {
                e.preventDefault();
                $('#inputFilterTipeMotor').val($(this).data('kode'));
                $('#tipeMotorSearchModal').modal('hide');
            });

            $('#btnFilterReset').on('click', function(e) {
                e.preventDefault();
                $('#inputFilterTipeMotor').val('');
                $('#selectFilterGroupProduk').prop('selectedIndex', 0).change();
                $('#selectFilterGroupLevel').prop('selectedIndex', 0).change();
                $('#inputFilterPartNumber').val('');
            });

            $('body').on('click', '#addToCart', function () {
                var part_number = $(this).data('kode');
                var _token = $('input[name="_token"]').val();

                blockmodalContentPartNumber.block();
                $.ajax({
                    url: "{{ route('parts.view-cart-part-number') }}",
                    method: "POST",
                    data: { part_number: part_number, _token: _token },

                    success:function(response) {
                        blockmodalContentPartNumber.release();

                        if (response.status == true) {
                            $('#modalTitle').html(part_number);

                            $('#btnOrder').attr('disabled',false);
                            $('#btnOrder').show();
                            $('#modalImgParts').html(response.view_images_part);
                            $('#modalTextPartNumber').html(response.view_part_number);
                            $('#modalTextDescription').html(response.view_nama_part);
                            $('#modalTextProduk').html(response.view_produk);
                            $('#modalTextHargaNetto').html(response.view_harga_netto);
                            $('#modalTextDiscount').html(response.view_discount);
                            $('#modalTextHet').html(response.view_het);
                            $('#modalListTypeMotor').html(response.view_type_motor);
                            $('#modalTextKeteranganBo').html(response.view_keterangan_bo);

                            $('#modalPartNumberCart').modal('show');

                            $('#modalPartNumberCart').on('shown.bs.modal', function () {
                                $("#inputJumlahOrder").focus();
                            });
                        } else {
                            Swal.fire({
                                text: response.message,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-danger"
                                }
                            });
                        }
                    },
                    error:function() {
                        blockmodalContentPartNumber.release();
                    }
                })
            });

            $('#btnOrder').click(function(e) {
                e.preventDefault();

                var messageErrorPartNumber = '';
                var part_number = $('#modalTextPartNumber').html();
                var jml_order   = $('#inputJumlahOrder').val();
                var _token      = $('input[name="_token"]').val();

                if(jml_order == '' || jml_order <= 0) {
                    Swal.fire({
                        text: "Jumlah order harus lebih besar dari 0 (nol)",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-danger"
                        }
                    });
                } else {
                    blockmodalContentPartNumber.block();
                    $.ajax({
                        url: "{{ route('parts.add-cart-part-number') }}",
                        method: "POST",
                        data: {
                            part_number: part_number, jumlah_order: jml_order, _token: _token
                        },
                        success:function(response) {
                            blockmodalContentPartNumber.release();

                            if (response.status == false) {
                                if(response.message == 'PILIH_DEALER') {
                                    $('#modalPartNumberCart').modal('hide');
                                    openModalSalesDealer();
                                } else {
                                    Swal.fire({
                                        text: response.message,
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn btn-danger"
                                        }
                                    });
                                }
                            } else {
                                Swal.fire({
                                    text: response.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-success"
                                    }
                                });
                                $('#modalPartNumberCart').modal('hide');
                                getInfoDataCart();
                            }
                        },
                        error:function() {
                            blockmodalContentPartNumber.release();
                        }
                    })
                }
            });
        });
    </script>
    @endpush
@endsection
