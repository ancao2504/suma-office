@extends('layouts.orders.pembayaranfaktur.pembayaranfaktur')
@section('containerpembayaranfaktur')
<div class="row g-0">
    @if(request()->get('salesman') || request()->get('dealer'))
    <div class="card card-flush shadow">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Belum Terbayar</span>
                <span class="text-muted fw-bold fs-7">Daftar faktur yang belum terbayar</span>
                @if(trim($data_filter->kode_sales) != '' || trim($data_filter->kode_dealer) != '' || trim($data_filter->nomor_faktur) != '')
                <div class="d-flex flex-grow mt-2">
                    @if(trim($data_filter->kode_sales) != '')
                    <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">SALESMAN : {{ strtoupper(trim($data_filter->kode_sales)) }}</span>
                    @endif
                    @if(trim($data_filter->kode_dealer) != '')
                    <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">DEALER : {{ strtoupper(trim($data_filter->kode_dealer)) }}</span>
                    @endif
                    @if(trim($data_filter->nomor_faktur) != '')
                    <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">FAKTUR : {{ trim($data_filter->nomor_faktur) }}</span>
                    @endif
                </div>
                @endif
            </h3>
            <div class="card-toolbar">
                <button id="btnFilterMasterData" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFilter">
                    <i class="bi bi-funnel-fill fs-4 me-2"></i>Filter
                </button>
            </div>
        </div>
    </div>
    @else
    <div class="card card-flush shadow">
        <div class="p-4">
            <div class="alert alert-danger d-flex align-items-center p-5">
                <span class="svg-icon svg-icon-2hx svg-icon-danger me-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path opacity="0.3" d="M12 22C13.6569 22 15 20.6569 15 19C15 17.3431 13.6569 16 12 16C10.3431 16 9 17.3431 9 19C9 20.6569 10.3431 22 12 22Z" fill="currentColor"></path>
                        <path d="M19 15V18C19 18.6 18.6 19 18 19H6C5.4 19 5 18.6 5 18V15C6.1 15 7 14.1 7 13V10C7 7.6 8.7 5.6 11 5.1V3C11 2.4 11.4 2 12 2C12.6 2 13 2.4 13 3V5.1C15.3 5.6 17 7.6 17 10V13C17 14.1 17.9 15 19 15ZM11 10C11 9.4 11.4 9 12 9C12.6 9 13 8.6 13 8C13 7.4 12.6 7 12 7C10.3 7 9 8.3 9 10C9 10.6 9.4 11 10 11C10.6 11 11 10.6 11 10Z" fill="currentColor"></path>
                    </svg>
                </span>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-danger">Informasi</h4>
                    <span>Kolom kode sales atau kode dealer harus terisi</span>
                </div>
            </div>
        </div>
        <div class="card-header align-items-center border-0">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Filter Belum Terbayar</span>
                <span class="text-muted fw-bold fs-7">Daftar faktur yang belum terbayar</span>
            </h3>
        </div>
        <form id="formFilter" name="formFilter" autofill="off" autocomplete="off" method="get" action="{{ route('orders.pembayaranfaktur.daftar-belum-terbayar') }}">
            <div class="card-body">
                <div class="fv-row">
                    <label class="form-label">Salesman:</label>
                    <div class="input-group">
                        <input id="inputFilterSalesman" name="salesman" type="search" placeholder="Semua Salesman" readonly
                            class="form-control @if(trim($data_user->role_id) == 'D_H3' || trim($data_user->role_id) == 'MD_H3_SM') form-control-solid @endif"
                            @if(trim($data_user->role_id) != 'D_H3' && trim($data_user->role_id) != 'MD_H3_SM') style="cursor: pointer;" @endif
                        @if(isset($data_filter->kode_sales)) value="{{ strtoupper(trim($data_filter->kode_sales)) }}" @else value="{{ old('kode_sales') }}" @endif>
                        @if(strtoupper(trim($data_user->role_id)) != 'MD_H3_SM')
                            @if (strtoupper(trim($data_user->role_id)) !='D_H3')
                            <button id="btnFilterPilihSalesman" name="btnFilterPilihSalesman" class="btn btn-icon btn-primary" type="button"
                                data-toggle="modal" data-target="#salesmanSearchModal">
                                <i class="fa fa-search"></i>
                            </button>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="fv-row mt-6">
                    <label class="form-label">Dealer:</label>
                    <div class="input-group">
                        <input id="inputFilterDealer" name="dealer" type="search" placeholder="Semua Dealer" readonly
                            class="form-control @if(trim($data_user->role_id) == 'D_H3') form-control-solid @endif"
                            @if(trim($data_user->role_id) != 'D_H3') style="cursor: pointer;" @endif
                            @if(isset($data_filter->kode_dealer)) value="{{ strtoupper(trim($data_filter->kode_dealer)) }}" @else value="{{ old('kode_dealer') }}"@endif>
                        @if(strtoupper(trim($data_user->role_id)) != 'D_H3')
                        <button id="btnFilterPilihDealer" name="btnFilterPilihDealer" class="btn btn-icon btn-primary" type="button"
                            data-toggle="modal" data-target="#dealerSearchModal">
                            <i class="fa fa-search"></i>
                        </button>
                        @endif
                    </div>
                </div>
                <div class="fv-row mt-6">
                    <label class="form-label">Nomor Faktur:</label>
                    <div class="input-group has-validation mb-2">
                        <input id="inputFilterNomorFaktur" name="nomor_faktur" type="search" class="form-control" placeholder="Semua Nomor Faktur"
                            @if(isset($data_filter->nomor_faktur)) value="{{ $data_filter->nomor_faktur }}" @else value="{{ old('nomor_faktur') }}"@endif>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button id="btnFilterProses" type="submit" class="btn btn-primary me-2">Terapkan</button>
                <button id="btnFilterReset" class="btn btn-danger" role="button">Reset Filter</button>
            </div>
        </form>
    </div>
    @endif

    @include('layouts.orders.pembayaranfaktur.pembayaranfakturlist')
</div>

@if(request()->get('salesman') || request()->get('dealer'))
<div class="modal fade" tabindex="-1" id="modalFilter" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="modalContentFilter">
            <form id="formFilter" name="formFilter" autofill="off" autocomplete="off" method="get" action="{{ route('orders.pembayaranfaktur.daftar-belum-terbayar') }}">
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
                            <input id="inputFilterSalesman" name="salesman" type="search" placeholder="Semua Salesman" readonly
                                class="form-control @if(trim($data_user->role_id) == 'D_H3' || trim($data_user->role_id) == 'MD_H3_SM') form-control-solid @endif"
                                @if(trim($data_user->role_id) != 'D_H3' && trim($data_user->role_id) != 'MD_H3_SM') style="cursor: pointer;" @endif
                                @if(isset($data_filter->kode_sales)) value="{{ strtoupper(trim($data_filter->kode_sales)) }}" @else value="{{ old('kode_sales') }}" @endif>
                            @if(strtoupper(trim($data_user->role_id)) != 'MD_H3_SM')
                                @if (strtoupper(trim($data_user->role_id)) !='D_H3')
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
                            <input id="inputFilterDealer" name="dealer" type="search" placeholder="Semua Dealer" readonly
                                class="form-control @if(trim($data_user->role_id) == 'D_H3') form-control-solid @endif"
                                @if(trim($data_user->role_id) != 'D_H3') style="cursor: pointer;" @endif
                                @if(isset($data_filter->kode_dealer)) value="{{ strtoupper(trim($data_filter->kode_dealer)) }}" @else value="{{ old('kode_dealer') }}"@endif>
                            @if(strtoupper(trim($data_user->role_id)) != 'D_H3')
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
                                @if(isset($data_filter->nomor_faktur)) value="{{ $data_filter->nomor_faktur }}" @else value="{{ old('nomor_faktur') }}"@endif>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button id="btnFilterReset" class="btn btn-danger" role="button">Reset Filter</button>
                    <div class="text-end">
                        <button id="btnFilterProses" type="button" class="btn btn-primary">Terapkan</button>
                        <button id="btnFilterClose" name="btnClose" type="button" class="btn btn-light text-end" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@include('layouts.option.optionsalesman')
@include('layouts.option.optiondealer')
@include('layouts.option.optiondealersalesman')

@push('scripts')
<script type="text/javascript">
    const data_filter = {
        'salesman': '{{ trim($data_filter->kode_sales) }}',
        'dealer': '{{ trim($data_filter->kode_dealer) }}',
        'nomor_faktur': '{{ trim($data_filter->nomor_faktur) }}',
    }
    const data_user = {
        'role_id': '{{ trim($data_user->role_id) }}'
    }
    const data_page = {
        'start_record': '{{ $data_page->from }}'
    }
</script>
<script src="{{ asset('assets/js/suma/orders/pembayaranfaktur/pembayaranfakturbelumterbayar.js') }}?v={{ time() }}"></script>
@endpush
@endsection
