@extends('layouts.main.index')
@section('title','Visit')
@section('subtitle','Planning Visit')
@section('container')
<div class="row g-0">
    <form id="purchase_order" name="purchase_order" action="{{ route('visit.planning-visit') }}" method="get" autocomplete="off">
        <div class="card card-flush">
            <div class="card-header align-items-center border-0 mt-4 mb-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Planning Visit</span>
                    <span class="text-muted fw-bold fs-7">Daftar planning visit salesman</span>
                </h3>
                <div class="card-toolbar">
                    <button id="btnTambah" name="btnTambah" class="btn btn-success m-2" type="button">
                        <i class="bi bi-plus-circle-fill fs-4 me-2"></i>Tambah
                    </button>
                    <button class="btn btn-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="bi bi-funnel-fill fs-4 me-2"></i>Filter
                    </button>
                    <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_6244763d95a3a" style="">
                        <div class="px-7 py-5">
                            <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                        </div>
                        <div class="separator border-gray-200"></div>
                        <div class="px-7 py-5">
                            <div class="mb-5">
                                <label class="form-label required">Tanggal:</label>
                                <input type="date" id="inputDate" name="date" class="form-control"
                                    @if(isset($date)) value="{{ $date }}" @else value="{{ old('date') }}"@endif />
                            </div>
                            <div class="mb-5">
                                <label class="form-label">Kode Sales:</label>
                                <div class="d-flex align-items-center position-relative my-1">
                                    <span class="svg-icon svg-icon-1 position-absolute ms-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                                            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                                        </svg>
                                    </span>
                                    <input id="inputKodeSales" name="kode_sales" type="text" placeholder="Search Kode Sales" class="ps-14 form-control
                                        @if($role_id == 'MD_H3_SM') form-control-solid @elseif ($role_id == 'D_H3') form-control-solid @endif"
                                        @if($role_id == 'MD_H3_SM') readonly @elseif ($role_id == 'D_H3') readonly @endif
                                        @if(isset($kode_sales)) value="{{ $kode_sales }}" @else value="{{ old('kode_sales') }}"@endif />
                                </div>
                                <span id="messageFilterKodeSales"></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <button id="btnFilter" class="btn btn-sm btn-primary m-2" type="submit">Terapkan</button>
                                <a href="{{ route('visit.planning-visit') }}" class="btn btn-sm btn-danger" role="button">Reset Filter</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="datapost">
            @include('layouts.visit.planningvisit.planningvisitlist')
        </div>
        <div class="dataload text-center" style="display:none">
            <p><img src="{{ asset('assets/images/logo/loading.gif') }}" class="h-200px"></p>
        </div>
    </form>
</div>

<div class="modal fade bs-example-modal-xl" tabindex="-1" id="entryModalPlanningVisit">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="planningVisitForm" name="planningVisitForm" autofill="off" autocomplete="off" method="POST" action="{{ route('visit.save-planning-visit') }}">
                @csrf
                <div class="modal-header">
                    <h5 id="modalTitlePlanningVisit" name="modalTitlePlanningVisit" class="modal-title"></h5>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-muted svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M6 19.7C5.7 19.7 5.5 19.6 5.3 19.4C4.9 19 4.9 18.4 5.3 18L18 5.3C18.4 4.9 19 4.9 19.4 5.3C19.8 5.7 19.8 6.29999 19.4 6.69999L6.7 19.4C6.5 19.6 6.3 19.7 6 19.7Z" fill="currentColor"/>
                                <path d="M18.8 19.7C18.5 19.7 18.3 19.6 18.1 19.4L5.40001 6.69999C5.00001 6.29999 5.00001 5.7 5.40001 5.3C5.80001 4.9 6.40001 4.9 6.80001 5.3L19.5 18C19.9 18.4 19.9 19 19.5 19.4C19.3 19.6 19 19.7 18.8 19.7Z" fill="currentColor"/>
                            </svg>
                        </span>
                    </div>
                </div>
                <div id="modalBodyPlanningVisit" name="modalBodyPlanningVisit" class="modal-body">
                    <span id="messageErrorPlanningVisit"></span>
                    <div class="fv-row">
                        <label class="form-label required">Tanggal</label>
                        <input id="inputTanggal" name="tanggal" type="date" class="form-control" required>
                    </div>
                    <div class="fv-row mt-8">
                        <label class="form-label required">Salesman</label>
                        <div class="input-group">
                            <input id="inputSalesman" name="salesman" type="search" class="form-control" placeholder="Pilih Kode Salesman" readonly>
                            <button id="btnPilihSalesman" name="btnPilihSalesman" class="btn btn-icon btn-primary" type="button"
                                data-toggle="modal" data-target="#salesmanSearchModal">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="fv-row mt-8">
                        <label class="form-label required">Salesman</label>
                        <div class="input-group">
                            <input id="inputDealer" name="dealer" type="search" class="form-control" placeholder="Pilih Kode Dealer" readonly>
                            <button id="btnPilihDealerSalesman" name="btnPilihDealerSalesman" class="btn btn-icon btn-primary" type="button"
                                data-toggle="modal" data-target="#dealerSalesmanSearchModal">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="fv-row mt-8">
                        <label class="form-label required">Keterangan</label>
                        <input id="inputKeterangan" name="keterangan" type="text" class="form-control" placeholder="Input Keterangan Visit" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('layouts.option.optionsalesman')
@include('layouts.option.optiondealersalesman')

@push('scripts')
<script src="{{ asset('assets/js/suma/option/option.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#btnPilihSalesman').on('click', function(e) {
            e.preventDefault();
            loadDataSalesman();
            $('#searchSalesmanForm').trigger('reset');
            $('#salesmanSearchModal').modal('show');
        });

        $('body').on('click', '#salesmanContentModal #selectSalesman', function(e) {
            e.preventDefault();
            $('#inputSalesman').val($(this).data('kode_sales'));
            $('#salesmanSearchModal').modal('hide');
        });

        $('#btnPilihDealerSalesman').on('click', function(e) {
            e.preventDefault();

            var salesman = $('#inputSalesman').val();

            if(salesman == null || salesman == '') {
                Swal.fire({
                    text: 'Pilih data salesman terlebih dahulu',
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-danger"
                    }
                });
            } else {
                loadDataDealerSalesman(salesman, 1, 10, '');
                $('#searchDealerSalesmanForm').trigger('reset');
                $('#dealerSalesmanSearchModal').modal('show');
            }
        });


        $('body').on('click', '#dealerSalesmanContentModal #selectDealerSalesman', function(e) {
            e.preventDefault();
            $('#inputDealer').val($(this).data('kode_dealer'));
            $('#dealerSalesmanSearchModal').modal('hide');
        });

        $('#btnTambah').click(function () {
            $('#modalTitlePlanningVisit').html("Tambah Data Planning Visit");
            $('#btnSimpan').attr('disabled',false);
            $('#btnSimpan').show();
            $('#planningVisitForm').trigger('reset');
            $('#entryModalPlanningVisit').modal('show');

            $('#entryModalPlanningVisit').on('shown.bs.modal', function () {
                $("#inputTanggal").focus();
            });
        });

        $('body').on('click', '#deletePlanVisit', function (event) {
            var element = $(this).parent().parent();
            var kode_visit = $(this).data('kode');
            var _token = $('input[name="_token"]').val();

            Swal.fire({
                html: `Apa anda yakin akan menghapus data <strong>Planning Visit Ini</strong> ?`,
                icon: "info",
                buttonsStyling: false,
                showCancelButton: true,
                confirmButtonText: "Yes",
                cancelButtonText: 'No',
                customClass: {
                    confirmButton: "btn btn-danger",
                    cancelButton: 'btn btn-primary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('visit.delete-planning-visit') }}",
                        method: "POST",
                        data: {
                            kode_visit: kode_visit, _token: _token
                        },

                        success:function(response) {
                            if (response.status == true) {
                                Swal.fire({
                                    text: response.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-danger"
                                    }
                                });
                                element.fadeOut().remove();
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
                        }
                    })
                } else {

                }
            });
        });
    });
</script>
@endpush
@endsection
