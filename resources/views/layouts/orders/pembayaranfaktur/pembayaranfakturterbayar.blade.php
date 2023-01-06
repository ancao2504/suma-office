@extends('layouts.orders.pembayaranfaktur.pembayaranfaktur')
@section('containerpembayaranfaktur')
<div class="row g-0">
    @if (request()->get('month') ||request()->get('year') ||request()->get('salesman') || request()->get('dealer'))
    <div class="card card-flush">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Terbayar</span>
                <span class="text-muted fw-bold fs-7">Daftar faktur terbayar. Data faktur bulan
                    <span class="text-dark fw-bolder fs-7">
                        @if($data_filter->month == 1) Januari
                        @elseif($data_filter->month == 2) Februari
                        @elseif($data_filter->month == 3) Maret
                        @elseif($data_filter->month == 4) April
                        @elseif($data_filter->month == 5) Mei
                        @elseif($data_filter->month == 6) Juni
                        @elseif($data_filter->month == 7) Juli
                        @elseif($data_filter->month == 8) Agustus
                        @elseif($data_filter->month == 9) September
                        @elseif($data_filter->month == 10) Oktober
                        @elseif($data_filter->month == 11) November
                        @elseif($data_filter->month == 12) Desember
                        @endif {{ $data_filter->year }}
                    </span>
                </span>
                @if(trim($data_filter->kode_sales) != '' || trim($data_filter->kode_dealer) != '' || trim($data_filter->nomor_faktur) != '')
                <div class="d-flex flex-grow mt-2">
                    @if(trim($data_filter->kode_sales) != '')
                    <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">SALESMAN : {{ strtoupper(trim($data_filter->kode_sales)) }}</span>
                    @endif
                    @if(trim($data_filter->kode_dealer) != '')
                    <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">DEALER : {{ strtoupper(trim($data_filter->kode_dealer)) }}</span>
                    @endif
                    @if(trim($data_filter->nomor_faktur) != '')
                    <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">FAKTUR : {{ strtoupper(trim($data_filter->nomor_faktur)) }}</span>
                    @endif
                </div>
                @endif
            </h3>
            <div class="card-toolbar">
                <button id="btnFilterPembayaran" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFilter">
                    <i class="bi bi-funnel-fill fs-4 me-2"></i>Filter
                </button>
            </div>
        </div>
    </div>
    @else
    <div class="card card-flush">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Filter Terbayar</span>
                <span class="text-muted fw-bold fs-7">Daftar faktur yang sudah terbayar (data yang ditampilkan berdasarkan bulan dan tahun faktur)</span>
            </h3>
        </div>
        <div class="card-boady">
            <form id="formFilter" name="formFilter" autofill="off" autocomplete="off" method="get" action="{{ route('orders.pembayaranfaktur.daftar-terbayar') }}">
                <div class="modal-body row">
                    <div class="fv-row">
                        <label class="form-label required">Bulan:</label>
                        <select id="selectFilterMonth" name="month" class="form-select">
                            <option value="1" @if($data_filter->month == 1) {{"selected"}} @endif>Januari</option>
                            <option value="2" @if($data_filter->month == 2) {{"selected"}} @endif>Februari</option>
                            <option value="3" @if($data_filter->month == 3) {{"selected"}} @endif>Maret</option>
                            <option value="4" @if($data_filter->month == 4) {{"selected"}} @endif>April</option>
                            <option value="5" @if($data_filter->month == 5) {{"selected"}} @endif>Mei</option>
                            <option value="6" @if($data_filter->month == 6) {{"selected"}} @endif>Juni</option>
                            <option value="7" @if($data_filter->month == 7) {{"selected"}} @endif>Juli</option>
                            <option value="8" @if($data_filter->month == 8) {{"selected"}} @endif>Agustus</option>
                            <option value="9" @if($data_filter->month == 9) {{"selected"}} @endif>September</option>
                            <option value="10" @if($data_filter->month == 10) {{"selected"}} @endif>Oktober</option>
                            <option value="11" @if($data_filter->month == 11) {{"selected"}} @endif>November</option>
                            <option value="12" @if($data_filter->month == 12) {{"selected"}} @endif>Desember</option>
                        </select>
                    </div>
                    <div class="fv-row mt-6">
                        <label class="form-label required">Tahun:</label>
                        <input type="number" id="inputFilterYear" name="year" class="form-control" placeholder="Tahun"
                            @if(isset($data_filter->year)) value="{{ $data_filter->year }}" @else value="{{ old('year') }}"@endif>
                    </div>
                    <div class="fv-row mt-6">
                        <label class="form-label">Salesman:</label>
                        <div class="input-group">
                            <input id="inputFilterSalesman" name="salesman" type="search" placeholder="Semua Salesman" readonly
                                class="form-control @if(trim($data_user->role_id) == 'D_H3' || trim($data_user->role_id) == 'MD_H3_SM') form-control-solid @endif"
                                @if(trim($data_user->role_id) != 'D_H3' && trim($data_user->role_id) != 'MD_H3_SM') style="cursor: pointer;" @endif
                                @if(isset($data_filter->kode_sales)) value="{{ strtoupper(trim($data_filter->kode_sales)) }}" @else value="{{ old('kode_sales') }}"@endif>
                            @if(strtoupper(trim($data_user->role_id)) != 'MD_H3_SM')
                                @if(strtoupper(trim($data_user->role_id)) != 'D_H3')
                                <button id="btnFilterPilihSalesman" name="btnFilterPilihSalesman" class="btn btn-icon btn-primary" type="button">
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
                            <button id="btnFilterPilihDealer" name="btnFilterPilihDealer" class="btn btn-icon btn-primary" type="button">
                                <i class="fa fa-search"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                    <div class="fv-row mt-6">
                        <label class="form-label">Nomor Faktur:</label>
                        <div class="input-group has-validation mb-2">
                            <input id="inputFilterNomorFaktur" name="nomor_faktur" type="search" class="form-control" placeholder="Semua Nomor Faktur"
                                @if(isset($nomor_faktur)) value="{{ trim($data_filter->nomor_faktur) }}" @else value="{{ old('nomor_faktur') }}"@endif>
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
    @endif

    @include('layouts.orders.pembayaranfaktur.pembayaranfakturlist')
</div>


@if (request()->get('month') ||request()->get('year') ||request()->get('salesman') || request()->get('dealer') || request()->get('nomor_faktur'))
<div class="modal fade" tabindex="-1" id="modalFilter">
    <div class="modal-dialog">
        <div class="modal-content" id="modalContentFilter">
            <form id="formFilter" name="formFilter" autofill="off" autocomplete="off" method="get" action="{{ route('orders.pembayaranfaktur.daftar-terbayar') }}">
                <div class="modal-header">
                    <h5 id="modalTitle" name="modalTitle" class="modal-title">Filter Faktur (Terbayar)</h5>
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
                        <label class="form-label required">Bulan:</label>
                        <select id="selectFilterMonth" name="month" class="form-select">
                            <option value="1" @if($data_filter->month == 1) {{"selected"}} @endif>Januari</option>
                            <option value="2" @if($data_filter->month == 2) {{"selected"}} @endif>Februari</option>
                            <option value="3" @if($data_filter->month == 3) {{"selected"}} @endif>Maret</option>
                            <option value="4" @if($data_filter->month == 4) {{"selected"}} @endif>April</option>
                            <option value="5" @if($data_filter->month == 5) {{"selected"}} @endif>Mei</option>
                            <option value="6" @if($data_filter->month == 6) {{"selected"}} @endif>Juni</option>
                            <option value="7" @if($data_filter->month == 7) {{"selected"}} @endif>Juli</option>
                            <option value="8" @if($data_filter->month == 8) {{"selected"}} @endif>Agustus</option>
                            <option value="9" @if($data_filter->month == 9) {{"selected"}} @endif>September</option>
                            <option value="10" @if($data_filter->month == 10) {{"selected"}} @endif>Oktober</option>
                            <option value="11" @if($data_filter->month == 11) {{"selected"}} @endif>November</option>
                            <option value="12" @if($data_filter->month == 12) {{"selected"}} @endif>Desember</option>
                        </select>
                    </div>
                    <div class="fv-row mt-6">
                        <label class="form-label required">Tahun:</label>
                        <input type="number" id="inputFilterYear" name="year" class="form-control" placeholder="Tahun"
                            @if(isset($data_filter->year)) value="{{ $data_filter->year }}" @else value="{{ old('year') }}"@endif>
                    </div>
                    <div class="fv-row mt-6">
                        <label class="form-label">Salesman:</label>
                        <div class="input-group">
                            <input id="inputFilterSalesman" name="salesman" type="search" placeholder="Semua Salesman" readonly
                                class="form-control @if(trim($data_user->role_id) == 'D_H3' || trim($data_user->role_id) == 'MD_H3_SM') form-control-solid @endif"
                                @if(trim($data_user->role_id) != 'D_H3' && trim($data_user->role_id) != 'MD_H3_SM') style="cursor: pointer;" @endif
                                @if(isset($data_filter->kode_sales)) value="{{ strtoupper(trim($data_filter->kode_sales)) }}" @else value="{{ old('kode_sales') }}"@endif>
                            @if(strtoupper(trim($data_user->role_id)) != 'MD_H3_SM')
                                @if(strtoupper(trim($data_user->role_id)) != 'D_H3')
                                <button id="btnFilterPilihSalesman" name="btnFilterPilihSalesman" class="btn btn-icon btn-primary" type="button">
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
                            <button id="btnFilterPilihDealer" name="btnFilterPilihDealer" class="btn btn-icon btn-primary" type="button">
                                <i class="fa fa-search"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                    <div class="fv-row mt-6">
                        <label class="form-label">Nomor Faktur:</label>
                        <div class="input-group has-validation mb-2">
                            <input id="inputFilterNomorFaktur" name="nomor_faktur" type="search" class="form-control" placeholder="Semua Nomor Faktur"
                                @if(isset($nomor_faktur)) value="{{ trim($data_filter->nomor_faktur) }}" @else value="{{ old('nomor_faktur') }}"@endif>
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
@endif

@include('layouts.option.optionsalesman')
@include('layouts.option.optiondealer')
@include('layouts.option.optiondealersalesman')

@push('scripts')
<script type="text/javascript">
    const data_filter = {
        'year': '{{ trim($data_filter->year) }}',
        'month': '{{ trim($data_filter->month) }}',
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
<script src="{{ asset('assets/js/suma/orders/pembayaranfaktur/pembayaranfakturterbayar.js') }}?v={{ time() }}"></script>
@endpush
@endsection
