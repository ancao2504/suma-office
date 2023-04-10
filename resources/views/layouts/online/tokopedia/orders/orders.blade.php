@extends('layouts.main.index')
@section('title','Tokopedia')
@section('subtitle','Orders')
@section('container')
<div class="row g-0">
    <div class="card card-flush">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Orders</span>
                <span class="text-muted fw-bold fs-7">Daftar order marketplace tokopedia</span>
                <span class="text-muted fw-bold fs-7 mt-2">Periode
                    <span class="text-dark fw-bolder fs-7">{{ date('d F Y', strtotime($data_filter->start_date)) }}</span> s/d
                    <span class="text-dark fw-bolder fs-7">{{ date('d F Y', strtotime($data_filter->end_date)) }}</span>
                </span>
            </h3>
            <div class="card-toolbar">
                <img src="{{ asset('assets/images/logo/tokopedia_lg.png') }}" class="h-75px" />
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
                        <option value="0" @if($data_filter->status == '0') selected @endif>Seller cancel order</option>
                        <option value="3" @if($data_filter->status == '3') selected @endif>Order Reject Due Empty Stock</option>
                        <option value="5" @if($data_filter->status == '5') selected @endif>Order Canceled by Fraud</option>
                        <option value="6" @if($data_filter->status == '6') selected @endif>Order Rejected (Auto Cancel Out of Stock)</option>
                        <option value="10" @if($data_filter->status == '10') selected @endif>Order rejected by seller</option>
                        <option value="15" @if($data_filter->status == '15') selected @endif>Instant Cancel by Buyer</option>
                        <option value="100" @if($data_filter->status == '100') selected @endif>Order Created</option>
                        <option value="103" @if($data_filter->status == '103') selected @endif>Wait for payment confirmation from third party</option>
                        <option value="220" @if($data_filter->status == '220') selected @endif>Payment verified, order ready to process</option>
                        <option value="221" @if($data_filter->status == '221') selected @endif>Waiting for partner approval</option>
                        <option value="400" @if($data_filter->status == '400') selected @endif>Seller accept order</option>
                        <option value="450" @if($data_filter->status == '450') selected @endif>Waiting for pickup</option>
                        <option value="500" @if($data_filter->status == '500') selected @endif>Order shipment</option>
                        <option value="501" @if($data_filter->status == '501') selected @endif>Status changed to waiting resi have no input</option>
                        <option value="520" @if($data_filter->status == '520') selected @endif>Invalid shipment reference number (AWB)</option>
                        <option value="530" @if($data_filter->status == '530') selected @endif>Requested by user to correct invalid entry of shipment reference number</option>
                        <option value="540" @if($data_filter->status == '540') selected @endif>Delivered to Pickup Point</option>
                        <option value="550" @if($data_filter->status == '550') selected @endif>Return to Seller</option>
                        <option value="600" @if($data_filter->status == '600') selected @endif>Order delivered</option>
                        <option value="601" @if($data_filter->status == '601') selected @endif>Buyer open a case to finish an order</option>
                        <option value="690" @if($data_filter->status == '690') selected @endif>Fraud Review</option>
                        <option value="700" @if($data_filter->status == '700') selected @endif>Order finished</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row g-0 mt-4">
            <div class="ms-10">
                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder">
                    <li class="nav-item mt-2">
                        <div id="navSemuaProses" class="nav-link text-active-primary ms-0 me-10 py-5 @if($data_filter->status != '220' && $data_filter->status != '400') active @endif"
                            style="cursor: pointer;">Semua Invoice</div>
                    </li>
                    <li class="nav-item mt-2">
                        <div id="navBelumProses" class="nav-link text-active-primary ms-0 me-10 py-5 @if($data_filter->status == '220') active @endif"
                            style="cursor: pointer;">Belum Diproses</div>
                    </li>
                    <li class="nav-item mt-2">
                        <div id="navRequestPickup" class="nav-link text-active-primary ms-0 me-10 py-5 @if($data_filter->status == '400') active @endif"
                            style="cursor: pointer;">Request Pickup</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div id="postOrder">
    <!--Start List Order-->
    @include('layouts.online.tokopedia.orders.orderlist')
    <!--End List Order-->
</div>

@push('scripts')
<script>
    const url = {
        'daftar_order': "{{ route('online.orders.tokopedia.daftar') }}",
        'proses_cetak_label': "{{ route('online.serahterima.form.cetak-label-tokopedia') }}",
        'proses_request_pickup_tokopedia': "{{ route('online.serahterima.form.tokopedia-request-pickup') }}",
    }
</script>
<script src="{{ asset('assets/js/suma/online/tokopedia/orders/daftar.js') }}?v={{ time() }}"></script>
@endpush
@endsection

