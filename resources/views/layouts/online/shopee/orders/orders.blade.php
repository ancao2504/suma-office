@extends('layouts.main.index')
@section('title','Shopee')
@section('subtitle','Orders')
@section('container')
<div class="row g-0">
    <div class="card card-flush">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Orders</span>
                <span class="text-muted fw-bold fs-7">Daftar order marketplace shopee</span>
                <span class="text-muted fw-bold fs-7 mt-2">Periode
                    <span class="text-dark fw-bolder fs-7">{{ date('d F Y', strtotime($data_filter->start_date)) }}</span> s/d
                    <span class="text-dark fw-bolder fs-7">{{ date('d F Y', strtotime($data_filter->end_date)) }}</span>
                </span>
            </h3>
            <div class="card-toolbar">
                <img src="{{ asset('assets/images/logo/shopee_lg.png') }}" class="h-75px" />
            </div>
        </div>
        <div class="card-header align-items-center border-0">
            <div class="align-items-start flex-column">
                <div class="input-group">
                    <select id="selectFields" name="fields" class="form-select w-md-200px" aria-label="Status">
                        <option value="create_time" @if($data_filter->fields == 'create_time') selected @endif>Pesanan Dibuat</option>
                        <option value="update_time" @if($data_filter->fields == 'update_time') selected @endif>Pesanan Diupdate</option>
                    </select>
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
                <div class="position-relative w-md-200px me-md-2">
                    <select id="selectStatus" name="status" class="form-select" aria-label="Status">
                        <option value="" @if($data_filter->status == '') selected @endif>ALL</option>
                        <option value="UNPAID" @if($data_filter->status == 'UNPAID') selected @endif>Unpaid</option>
                        <option value="READY_TO_SHIP" @if($data_filter->status == 'READY_TO_SHIP') selected @endif>Ready To Ship</option>
                        <option value="PROCESSED" @if($data_filter->status == 'PROCESSED') selected @endif>Processed</option>
                        <option value="SHIPPED" @if($data_filter->status == 'SHIPPED') selected @endif>Shipped</option>
                        <option value="COMPLETED" @if($data_filter->status == 'COMPLETED') selected @endif>Completed</option>
                        <option value="IN_CANCEL" @if($data_filter->status == 'IN_CANCEL') selected @endif>In Cancel</option>
                        <option value="CANCELLED" @if($data_filter->status == 'CANCELLED') selected @endif>Canceled</option>
                        <option value="INVOICE_PENDING" @if($data_filter->status == 'INVOICE_PENDING') selected @endif>Invoice Pending</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-0 mt-6">
    <div class="card card-flush">
        <div class="ms-8">
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder">
                <li class="nav-item mt-2">
                    <div id="navSemuaProses" class="nav-link text-active-primary ms-0 me-10 py-5 @if($data_filter->status != 'READY_TO_SHIP') active @endif"
                        style="cursor: pointer;">Semua Invoice</div>
                </li>
                <li class="nav-item mt-2">
                    <div id="navBelumProses" class="nav-link text-active-primary ms-0 me-10 py-5 @if($data_filter->status == 'READY_TO_SHIP') active @endif"
                        style="cursor: pointer;">Belum Diproses</div>
                </li>
            </ul>
        </div>
    </div>
</div>

<div id="postOrder">
    <!--Start List Order-->
    @include('layouts.online.shopee.orders.orderlist')
    <!--End List Order-->
</div>

@push('scripts')
<script>
    const url = {
        'daftar_order': "{{ route('online.orders.shopee.daftar') }}",
        'proses_pickup': "{{ route('online.orders.shopee.form.pickup') }}",
        'proses_cetak_label': "{{ route('online.orders.shopee.form.cetak-label') }}",
    }
</script>
<script src="{{ asset('assets/js/suma/online/shopee/orders/daftar.js') }}?v={{ time() }}"></script>
@endpush
@endsection

