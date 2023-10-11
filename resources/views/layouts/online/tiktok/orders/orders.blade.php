@extends('layouts.main.index')
@section('title','Tiktok')
@section('subtitle','Orders')
@section('container')
<div class="row g-0">
    <div class="card card-flush shadow">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Orders</span>
                <span class="text-muted fw-bold fs-7">Daftar order marketplace tiktok</span>
                <span class="text-muted fw-bold fs-7 mt-2">Periode
                    <span class="text-dark fw-bolder fs-7">{{ date('d F Y', strtotime($data_filter->start_date)) }}</span> s/d
                    <span class="text-dark fw-bolder fs-7">{{ date('d F Y', strtotime($data_filter->end_date)) }}</span>
                </span>
            </h3>
            <div class="card-toolbar">
                <img src="{{ asset('assets/images/logo/tiktok_lg.png') }}" class="h-75px" />
            </div>
        </div>
        <div class="card-header align-items-center border-0">
            <div class="align-items-start flex-column">
                <div class="input-group">
                    <input id="inputStartDate" name="start_date" class="form-control w-md-150px" placeholder="Dari Tanggal"
                        value="{{ $data_filter->start_date }}">
                    <span class="input-group-text">s/d</span>
                    <input id="inputEndDate" name="end_date" class="form-control w-md-150px" placeholder="Sampai Dengan"
                        value="{{ $data_filter->end_date }}">
                    <button id="btnFilterMasterData" class="btn btn-icon btn-primary" type="button">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="position-relative w-md-400px me-md-2">
                    <select id="selectStatus" name="status" class="form-select" aria-label="Status">
                        <option value="" @if($data_filter->status == '') selected @endif>ALL</option>
                        <option value="100" @if($data_filter->status == '100') selected @endif>UNPAID</option>
                        <option value="105" @if($data_filter->status == '105') selected @endif>ON HOLD</option>
                        <option value="111" @if($data_filter->status == '111') selected @endif>AWAITING SHIPMENT</option>
                        <option value="112" @if($data_filter->status == '112') selected @endif>AWAITING COLLECTION</option>
                        <option value="114" @if($data_filter->status == '114') selected @endif>PARTIALLY SHIPPING</option>
                        <option value="121" @if($data_filter->status == '121') selected @endif>IN TRANSIT</option>
                        <option value="122" @if($data_filter->status == '122') selected @endif>DELIVERED</option>
                        <option value="130" @if($data_filter->status == '130') selected @endif>COMPLETED</option>
                        <option value="140" @if($data_filter->status == '140') selected @endif>CANCELLED</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row g-0 mt-4">
            <div class="ms-10">
                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder">
                    <li class="nav-item mt-2">
                        <div id="navSemuaProses" class="nav-link text-active-primary ms-0 me-10 py-5 @if($data_filter->status != '111' && $data_filter->status != '112' && $data_filter->status != '114') active @endif"
                            style="cursor: pointer;">Semua Invoice</div>
                    </li>
                    <li class="nav-item mt-2">
                        <div id="navBelumProses" class="nav-link text-active-primary ms-0 me-10 py-5 @if($data_filter->status == '111' || $data_filter->status == '112' || $data_filter->status == '114') active @endif"
                            style="cursor: pointer;">Belum Diproses</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div id="postOrder">
    <!--Start List Order-->
    @include('layouts.online.tiktok.orders.orderlist')
    <!--End List Order-->
</div>

@push('scripts')
<script>
    const url = {
        'daftar_order': "{{ route('online.orders.tiktok.daftar') }}",
        // 'proses_cetak_label': "{{ route('online.serahterima.form.cetak-label-tiktok') }}",
        // 'proses_request_pickup_tiktok': "{{ route('online.serahterima.form.tiktok-request-pickup') }}",
    }
    const paging = {
        'status_next_page': "{{ $data_filter->status_next_page }}",
        'cursor_next_page': "{{ $data_filter->cursor_next_page }}"
    }
</script>
<script src="{{ asset('assets/js/suma/online/tiktok/orders/daftar.js') }}?v={{ time() }}"></script>
@endpush
@endsection

