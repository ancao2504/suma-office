@extends('layouts.main.index')
@section('title','Tokopedia')
@section('subtitle','Orders')
@section('container')
<div class="row g-0">
    <div class="card card-flush">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Orders</span>
                <span class="text-muted fw-bold fs-7">Cari data invoice marketplace tokopedia</span>
            </h3>
            <div class="card-toolbar">
                <img src="{{ asset('assets/images/logo/tokopedia_lg.png') }}" class="h-75px" />
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center position-relative my-1">
                <span class="svg-icon svg-icon-1 position-absolute ms-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                    </svg>
                </span>
                <input id="inputNomorInvoice" name="nomor_invoice" type="text" class="form-control ps-14" placeholder="Cari Data Invoice"
                    value="{{ $data_filter->nomor_invoice }}">
                <button id="btnFilterMasterData" class="btn btn-icon btn-primary" type="button">
                    <i class="fa fa-search"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@forelse($data_order as $data)
<div class="card mb-5 mb-xl-8 mt-6">
    <div class="row pt-4 pb-4 ps-6 pe-6">
        <div class="col-lg-6 text-start">
            <span class="fs-7 fw-bolder text-gray-600">Order ID:
                <span class="ms-2">
                    <span class="fs-7 fw-bolder text-primary">{{ trim($data->order_id) }}</span>
                </span>
            </span>
        </div>
        <div class="col-lg-6 text-end">
            <span class="fs-7 fw-bolder text-gray-600">Status:
                <span class="ms-2">
                    @if($data->order_status->kode == 0)
                    <span class="fs-7 fw-boldest badge badge-danger">{{ strtoupper($data->order_status->keterangan) }}</span>
                    @elseif($data->order_status->kode == 3)
                    <span class="fs-7 fw-boldest badge badge-danger">{{ strtoupper($data->order_status->keterangan) }}</span>
                    @elseif($data->order_status->kode == 5)
                    <span class="fs-7 fw-boldest badge badge-danger">{{ strtoupper($data->order_status->keterangan) }}</span>
                    @elseif($data->order_status->kode == 6)
                    <span class="fs-7 fw-boldest badge badge-danger">{{ strtoupper($data->order_status->keterangan) }}</span>
                    @elseif($data->order_status->kode == 10)
                    <span class="fs-7 fw-boldest badge badge-danger">{{ strtoupper($data->order_status->keterangan) }}</span>
                    @elseif($data->order_status->kode == 15)
                    <span class="fs-7 fw-boldest badge badge-danger">{{ strtoupper($data->order_status->keterangan) }}</span>
                    @elseif($data->order_status->kode == 100)
                    <span class="fs-7 fw-boldest badge badge-warning">{{ strtoupper($data->order_status->keterangan) }}</span>
                    @elseif($data->order_status->kode == 103)
                    <span class="fs-7 fw-boldest badge badge-warning">{{ strtoupper($data->order_status->keterangan) }}</span>
                    @elseif($data->order_status->kode == 220)
                    <span class="fs-7 fw-boldest badge badge-primary">{{ strtoupper($data->order_status->keterangan) }}</span>
                    @elseif($data->order_status->kode == 221)
                    <span class="fs-7 fw-boldest badge badge-primary">{{ strtoupper($data->order_status->keterangan) }}</span>
                    @elseif($data->order_status->kode == 400)
                    <span class="fs-7 fw-boldest badge badge-primary">{{ strtoupper($data->order_status->keterangan) }}</span>
                    @elseif($data->order_status->kode == 450)
                    <span class="fs-7 fw-boldest badge badge-primary">{{ strtoupper($data->order_status->keterangan) }}</span>
                    @elseif($data->order_status->kode == 500)
                    <span class="fs-7 fw-boldest badge badge-success">{{ strtoupper($data->order_status->keterangan) }}</span>
                    @elseif($data->order_status->kode == 501)
                    <span class="fs-7 fw-boldest badge badge-success">{{ strtoupper($data->order_status->keterangan) }}</span>
                    @elseif($data->order_status->kode == 520)
                    <span class="fs-7 fw-boldest badge badge-success">{{ strtoupper($data->order_status->keterangan) }}</span>
                    @elseif($data->order_status->kode == 530)
                    <span class="fs-7 fw-boldest badge badge-success">{{ strtoupper($data->order_status->keterangan) }}</span>
                    @elseif($data->order_status->kode == 540)
                    <span class="fs-7 fw-boldest badge badge-success">{{ strtoupper($data->order_status->keterangan) }}</span>
                    @elseif($data->order_status->kode == 550)
                    <span class="fs-7 fw-boldest badge badge-info">{{ strtoupper($data->order_status->keterangan) }}</span>
                    @elseif($data->order_status->kode == 600)
                    <span class="fs-7 fw-boldest badge badge-success">{{ strtoupper($data->order_status->keterangan) }}</span>
                    @elseif($data->order_status->kode == 601)
                    <span class="fs-7 fw-boldest badge badge-success">{{ strtoupper($data->order_status->keterangan) }}</span>
                    @elseif($data->order_status->kode == 690)
                    <span class="fs-7 fw-boldest badge badge-success">{{ strtoupper($data->order_status->keterangan) }}</span>
                    @elseif($data->order_status->kode == 700)
                    <span class="fs-7 fw-boldest badge badge-success">{{ strtoupper($data->order_status->keterangan) }}</span>
                    @endif
                </span>
            </span>
        </div>
    </div>
    <div class="separator"></div>
    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-6 pt-6 pb-6 ps-10">
            <span class="fs-7 fw-bolder text-gray-500 d-block">No Invoice:</span>
            <span class="fs-7 fw-bolder text-dark d-block mt-1">{{ $data->nomor_invoice }}</span>
            <span class="fs-7 fw-bolder text-gray-800 d-block">{{ date('d F Y', strtotime($data->tanggal)) }}</span>

            <span class="fs-7 fw-bolder text-gray-500 d-block mt-6">Logistics:</span>
            <div class="fw-bolder fs-7 text-gray-800 d-flex align-items-center flex-wrap mt-1">
                <span class="pe-2">{{ $data->logistics->shipping_agency }}</span>
                <span class="fs-7 text-danger d-flex align-items-center">
                <span class="bullet bullet-dot bg-danger me-2"></span>{{ $data->logistics->service_type }}</span>
            </div>

            <span class="fs-7 fw-bolder text-gray-500 d-block mt-6">Amounts:</span>
            <div class="d-flex flex-stack w-200px mt-1">
                <div class="fs-7 fw-bolder text-gray-800">Product:</div>
                <div class="fs-7 fw-boldest text-danger text-end">Rp. {{ number_format($data->amount->product) }}</div>
            </div>
            <div class="d-flex flex-stack w-200px mt-1">
                <div class="fs-7 fw-bolder text-gray-800">Shipping:</div>
                <div class="fs-7 fw-bolder text-dark text-end">Rp. {{ number_format($data->amount->shipping) }}</div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 pt-6 pb-6 ps-6">
            <span class="fs-7 fw-bolder text-gray-500 d-block">Recipient:</span>
            <span class="fs-7 fw-bolder text-dark d-block mt-1">{{ $data->recipient->address->city }}</span>
            <span class="fs-7 fw-bolder text-gray-800 d-block">{{ $data->recipient->address->province }}, {{ $data->recipient->address->country }}</span>
            <span class="fs-7 fw-bolder text-gray-800 d-block">{{ $data->recipient->address->postal_code }}</span>

            <span class="fs-7 fw-bolder text-gray-500 d-block mt-6">Deadline Confirmation:</span>
            <div class="d-flex flex-stack w-200px mt-1">
                <div class="fs-7 fw-bolder text-gray-800">Accept:</div>
                <div class="fs-7 fw-bolder text-danger text-end">{{ $data->shipment_fulfillment->accept_deadline }}</div>
            </div>
            <div class="d-flex flex-stack w-200px mt-1">
                <div class="fs-7 fw-bolder text-gray-800">Shipping:</div>
                <div class="fs-7 fw-bolder text-danger text-end">{{ $data->shipment_fulfillment->confirm_shipping_deadline }}</div>
            </div>

            @if($data->is_plus == true)
            <span class="badge badge-success mt-6" style="background-color: #006a0b;">
                <img src="{{ asset('assets/images/logo/tokopedia_plus.png') }}" class="h-20px" />
            </span>
            @endif
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 pt-6 pb-6 ps-6 pe-10">
            <span class="fs-7 fw-bolder text-gray-500 d-block">No Faktur:</span>
            <span class="fs-7 fw-boldest text-danger d-block mt-1">{{ empty($data->faktur->nomor_faktur) ? '(Not Found)' : $data->faktur->nomor_faktur }}</span>
            <span class="fs-7 fw-bolder text-gray-800 d-block">{{ empty($data->faktur->tanggal) ? '-' : date('d F Y', strtotime($data->faktur->tanggal)) }}</span>
            <div class="mt-5">
                <span class="fs-8 fw-boldest badge badge-danger">{{ empty($data->faktur->kode_lokasi) ? '-' : strtoupper($data->faktur->kode_lokasi) }}</span>
                <span class="fs-8 fw-boldest badge badge-primary">{{ empty($data->faktur->kode_sales) ? '-' : strtoupper($data->faktur->kode_sales) }}</span>
                <span class="fs-8 fw-boldest badge badge-info">{{ empty($data->faktur->kode_dealer) ? '-' : strtoupper($data->faktur->kode_dealer) }}</span>
            </div>
            <span class="fs-7 fw-bolder text-gray-500 d-block mt-6">Total Faktur:</span>
            <span class="fs-6 fw-boldest text-danger">Rp. {{ empty($data->faktur->total) ? '-' : number_format($data->faktur->total) }}</span>
        </div>
    </div>
    <div class="separator"></div>
    <div class="row pt-4 pb-4 ps-6 pe-6">
        <div class="d-flex align-items-center">
            <div class="col-lg-6 text-start">
                <a href="{{ route('online.orders.tokopedia.form.form', trim($data->nomor_invoice)) }}" class="btn btn-primary w-250px">
                    <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i> Lihat Detail Transaksi
                </a>
            </div>
            <div class="col-lg-6 text-end">
                <div class="fs-5 fw-bolder text-dark text-end">{{ empty($data->faktur->nomor_faktur) ? '(Not Found)' : $data->faktur->nomor_faktur }}</div>
            </div>
        </div>
    </div>
</div>
@empty
<div class="card mb-5 mb-xl-8 mt-6">
    <div class="fs-6 fw-boldest text-muted p-20 text-center">- TIDAK ADA DATA YANG DITAMPILKAN -</div>
</div>
@endforelse
@push('scripts')
<script src="{{ asset('assets/js/suma/online/tokopedia/orders/single.js') }}?v={{ time() }}"></script>
@endpush
@endsection
