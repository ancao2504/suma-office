@extends('layouts.main.index')
@section('title','Shopee')
@section('subtitle','Orders')
@section('container')
@if($data->faktur->status == 1)
@if((double)$data->faktur->total_amount != (double)$data->shopee->item_price)
<div class="alert alert-dismissible bg-danger d-flex flex-column flex-sm-row w-100 p-5">
    <span class="svg-icon svg-icon-2hx svg-icon-light me-4 mb-5 mb-sm-0">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path opacity="0.3" d="M12 22C13.6569 22 15 20.6569 15 19C15 17.3431 13.6569 16 12 16C10.3431 16 9 17.3431 9 19C9 20.6569 10.3431 22 12 22Z" fill="currentColor"></path>
            <path d="M19 15V18C19 18.6 18.6 19 18 19H6C5.4 19 5 18.6 5 18V15C6.1 15 7 14.1 7 13V10C7 7.6 8.7 5.6 11 5.1V3C11 2.4 11.4 2 12 2C12.6 2 13 2.4 13 3V5.1C15.3 5.6 17 7.6 17 10V13C17 14.1 17.9 15 19 15ZM11 10C11 9.4 11.4 9 12 9C12.6 9 13 8.6 13 8C13 7.4 12.6 7 12 7C10.3 7 9 8.3 9 10C9 10.6 9.4 11 10 11C10.6 11 11 10.6 11 10Z" fill="currentColor"></path>
        </svg>
    </span>
    <div class="d-flex flex-column text-light pe-0 pe-sm-10">
        <h4 class="mb-2 text-light">Informasi</h4>
        <span>Jumlah nominal invoice dengan faktur internal tidak sama</span>
    </div>
</div>
@endif
@endif
<div class="row g-0">
    <div class="card card-flush shadow">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Invoice</span>
                <span class="text-muted fw-bold fs-7">Data invoice marketplace shopee</span>
            </h3>
            <div class="card-toolbar">
                <img src="{{ asset('assets/images/logo/shopee_lg.png') }}" class="h-75px" />
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 pt-6 pb-6">
                    <span class="fs-7 fw-bolder text-gray-500 d-block">No Invoice:</span>
                    <span class="fs-7 fw-bolder text-dark d-block mt-1">{{ $data->shopee->nomor_invoice }}</span>
                    <span class="fs-7 fw-bolder text-primary d-block">{{ $data->shopee->order_id }}</span>

                    <span class="fs-7 fw-bolder text-gray-500 d-block mt-6">Logistics:</span>
                    <div class="fw-bolder fs-7 text-gray-800 d-flex align-items-center flex-wrap mt-1">
                        <span class="pe-2">{{ $data->shopee->logistics->name }}</span>
                        <span class="fs-7 text-danger d-flex align-items-center">
                        <span class="bullet bullet-dot bg-danger me-2"></span>{{ $data->shopee->logistics->id }}</span>
                    </div>

                    <span class="fs-7 fw-bolder text-gray-500 d-block mt-6">Amounts:</span>
                    <div class="d-flex flex-stack w-200px mt-1">
                        <div class="fs-7 fw-bolder text-gray-800">Product:</div>
                        <div class="fs-7 fw-boldest text-danger text-end">Rp. {{ number_format($data->shopee->item_price) }}</div>
                    </div>
                    <div class="d-flex flex-stack w-200px mt-1">
                        <div class="fs-7 fw-bolder text-gray-800">Shipping:</div>
                        <div class="fs-7 fw-bolder text-dark text-end">Rp. {{ number_format($data->shopee->shipping_price) }}</div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 pt-6 pb-6 ps-6">
                    <span class="fs-7 fw-bolder text-gray-500 d-block">Payment:</span>
                    <span class="fs-7 fw-bolder text-gray-800 d-block mt-1">{{ $data->shopee->payment }}</span>

                    <span class="fs-7 fw-bolder text-gray-500 d-block mt-6">Recipient:</span>
                    <span class="fs-7 fw-bolder text-dark d-block mt-1">{{ $data->shopee->address->full_address }}</span>
                    <span class="fs-7 fw-bolder text-dark d-block mt-4">{{ $data->shopee->address->district }}</span>
                    <span class="fs-7 fw-bolder text-gray-800 d-block">{{ $data->shopee->address->city }}, {{ $data->shopee->address->province }}</span>
                    <span class="fs-7 fw-bolder text-gray-800 d-block">{{ $data->shopee->address->postal }}</span>

                    <span class="fs-7 fw-bolder text-gray-500 d-block mt-6">Status:</span>
                    @if($data->shopee->status == 'READY_TO_SHIP')
                    <span class="fs-7 fw-boldest badge badge-success">READY TO SHIP</span>
                    @else
                        @if(strtoupper(trim($data->shopee->status)) == 'UNPAID')
                        <span class="fs-7 fw-boldest badge badge-danger">UNPAID</span>
                        @elseif(strtoupper(trim($data->shopee->status)) == 'READY_TO_SHIP')
                        <span class="fs-7 fw-boldest badge badge-success">READY TO SHIP</span>
                        @elseif(strtoupper(trim($data->shopee->status)) == 'PROCESSED')
                        <span class="fs-7 fw-boldest badge badge-danger">PROCESSED</span>
                        @elseif(strtoupper(trim($data->shopee->status)) == 'SHIPPED')
                        <span class="fs-7 fw-boldest badge badge-danger">SHIPPED</span>
                        @elseif(strtoupper(trim($data->shopee->status)) == 'COMPLETED')
                        <span class="fs-7 fw-boldest badge badge-danger">COMPLETED</span>
                        @elseif(strtoupper(trim($data->shopee->status)) == 'IN_CANCEL')
                        <span class="fs-7 fw-boldest badge badge-danger">IN CANCEL</span>
                        @elseif(strtoupper(trim($data->shopee->status)) == 'CANCELLED')
                        <span class="fs-7 fw-boldest badge badge-danger">CANCELLED</span>
                        @elseif(strtoupper(trim($data->shopee->status)) == 'INVOICE_PENDING')
                        <span class="fs-7 fw-boldest badge badge-danger">INVOICE PENDING</span>
                        @endif
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle">
                        <thead class="border">
                            <tr class="fs-8 fw-bolder text-muted">
                                <th class="w-50px ps-3 pe-3 text-center">No</th>
                                <th class="w-300px ps-3 pe-3 text-center">Products</th>
                                <th class="w-100px ps-3 pe-3 text-center">Quantity</th>
                                <th class="w-100px ps-3 pe-3 text-center">Price</th>
                                <th class="w-100px ps-3 pe-3 text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody class="border">
                            @forelse($data->shopee->detail as $detail_shopee)
                            <tr>
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                    <span class="fs-7 fw-bold text-gray-800">{{ $loop->iteration }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                    <div class="d-flex">
                                        <div class="symbol symbol-45px me-5">
                                            <img src="{{ $detail_shopee->pictures }}" alt="{{ $detail_shopee->item_sku }}">
                                        </div>
                                        <div class="d-flex justify-content-start flex-column">
                                            <span class="fs-7 fw-bolder text-dark">{{ $detail_shopee->item_name }}</span>
                                            <span class="fs-8 fw-bolder text-gray-600 d-block">(SKU : {{ $detail_shopee->item_sku }})</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($detail_shopee->model_quantity_purchased) }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($detail_shopee->model_discounted_price) }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($detail_shopee->subtotal_price) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="fs-6 fw-boldest text-muted p-20 text-center">- TIDAK ADA DATA YANG DITAMPILKAN -</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-0">
    @forelse($data->faktur->list as $data_internal)
    <div class="card card-flush shadow mt-6">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Data Faktur</span>
                <span class="text-muted fw-bold fs-7">Data faktur internal</span>
            </h3>
            <div class="card-toolbar">
                <img src="{{ asset('assets/images/logo/bg_logo_suma.png') }}" class="h-50px" />
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 pt-6 pb-6">
                    <span class="fs-7 fw-bolder text-gray-500 d-block">No Faktur:</span>
                    <span class="fs-7 fw-bolder text-dark d-block mt-1">{{ $data_internal->nomor_faktur }}</span>
                    <span class="fs-7 fw-bolder text-danger d-block">{{ date('d F Y', strtotime($data_internal->tanggal)) }}</span>

                    <div class="row mt-6">
                        <div class="col-lg-4">
                            <span class="fs-7 fw-bolder text-gray-500">Jenis Beli:</span>
                        </div>
                        <div class="col-lg-8">
                            <div class="fw-bolder fs-7 text-gray-800 d-flex align-items-center flex-wrap mt-1">
                                <span class="pe-2">{{ $data_internal->jenis_beli->keterangan }}</span>
                                <span class="fs-7 text-danger d-flex align-items-center">
                                <span class="bullet bullet-dot bg-danger me-2"></span>{{ $data_internal->jenis_beli->kode }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <span class="fs-7 fw-bolder text-gray-500">Salesman:</span>
                        </div>
                        <div class="col-lg-8">
                            <div class="fw-bolder fs-7 text-gray-800 d-flex align-items-center flex-wrap mt-1">
                                <span class="pe-2">{{ $data_internal->salesman->nama }}</span>
                                <span class="fs-7 text-danger d-flex align-items-center">
                                <span class="bullet bullet-dot bg-danger me-2"></span>{{ $data_internal->salesman->kode }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <span class="fs-7 fw-bolder text-gray-500">Dealer:</span>
                        </div>
                        <div class="col-lg-8">
                            <div class="fw-bolder fs-7 text-gray-800 d-flex align-items-center flex-wrap mt-1">
                                <span class="pe-2">{{ $data_internal->dealer->nama }}</span>
                                <span class="fs-7 text-danger d-flex align-items-center">
                                <span class="bullet bullet-dot bg-danger me-2"></span>{{ $data_internal->dealer->kode }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <span class="fs-7 fw-bolder text-gray-500">Ekspedisi:</span>
                        </div>
                        <div class="col-lg-8">
                            <div class="fw-bolder fs-7 text-gray-800 d-flex align-items-center flex-wrap mt-1">
                                <span class="pe-2">{{ $data_internal->ekspedisi->nama }}</span>
                                <span class="fs-7 text-danger d-flex align-items-center">
                                <span class="bullet bullet-dot bg-danger me-2"></span>{{ $data_internal->ekspedisi->kode }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 pt-6 pb-6 ps-6">
                    <span class="fs-7 fw-bolder text-gray-500 d-block">Keterangan:</span>
                    <span class="fs-7 fw-bolder text-gray-800 d-block mt-1">{{ $data_internal->keterangan }}</span>


                    <span class="fs-7 fw-bolder text-gray-500 d-block mt-6">Umur Faktur:</span>
                    <div class="fw-bolder fs-7 text-gray-800 d-flex align-items-center flex-wrap mt-1">
                        <span class="pe-2">{{ $data_internal->jatuh_tempo->tanggal }}</span>
                        <span class="fs-7 text-danger d-flex align-items-center">
                        <span class="bullet bullet-dot bg-danger me-2"></span>{{ $data_internal->jatuh_tempo->umur_faktur }} Hari</span>
                    </div>

                    <span class="fs-7 fw-bolder text-gray-500 d-block mt-6">Status:</span>
                    @if($data_internal->kode_tpc == '14')
                    <span class="fs-8 fw-boldest badge badge-primary mt-2">TPC {{ $data_internal->kode_tpc }}</span>
                    @else
                    <span class="fs-8 fw-boldest badge badge-danger mt-2">TPC {{ $data_internal->kode_tpc }}</span>
                    @endif
                    @if($data_internal->status->rh == 'H')
                    <span class="fs-8 fw-boldest badge badge-danger mt-2">HOTLINE</span>
                    @else
                    <span class="fs-8 fw-boldest badge badge-success mt-2">REGULER</span>
                    @endif
                    @if($data_internal->status->bo == 'B')
                    <span class="fs-8 fw-boldest badge badge-danger mt-2">BACKORDER</span>
                    @else
                    <span class="fs-8 fw-boldest badge badge-success mt-2">TIDAK BO</span>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle">
                        <thead class="border">
                            <tr class="fs-8 fw-bolder text-muted">
                                <th class="w-50px ps-3 pe-3 text-center">No</th>
                                <th class="w-300px ps-3 pe-3 text-center">Part Number</th>
                                <th class="w-100px ps-3 pe-3 text-center">Jml Order</th>
                                <th class="w-100px ps-3 pe-3 text-center">Jml Jual</th>
                                <th class="w-100px ps-3 pe-3 text-center">Harga</th>
                                <th class="w-100px ps-3 pe-3 text-center">Disc (%)</th>
                                <th class="w-100px ps-3 pe-3 text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody class="border">
                            @forelse($data_internal->detail as $data_internal_detail)
                            <tr>
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                    <span class="fs-7 fw-bold text-gray-800">{{ $loop->iteration }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                    <div class="d-flex">
                                        <div class="symbol symbol-45px me-5">
                                            <img src="{{ $data_internal_detail->pictures }}" alt="{{ $data_internal_detail->part_number }}"
                                                onerror="this.onerror=null; this.src='{{ URL::asset('assets/images/background/part_image_not_found.png') }}'">
                                        </div>
                                        <div class="d-flex justify-content-start flex-column">
                                            <span class="fs-7 fw-bolder text-dark">{{ $data_internal_detail->nama_part }}</span>
                                            <span class="fs-8 fw-bolder text-gray-600 d-block">{{ $data_internal_detail->part_number }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data_internal_detail->jml_order) }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data_internal_detail->jml_jual) }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data_internal_detail->harga) }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data_internal_detail->disc_detail, 2) }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data_internal_detail->total_detail) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7">
                                    <div class="fs-6 fw-boldest text-muted p-20 text-center">- TIDAK ADA DATA YANG DITAMPILKAN -</div>
                                </td>
                            </tr>
                            @endforelse
                            <tr>
                                <td colspan="5" class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-muted">Sub Total</span>
                                </td>
                                <td colspan="2" class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data_internal->total->sub_total) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-muted">Discount (%)</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data_internal->total->disc_header, 2) }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data_internal->total->disc_header_rp) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-muted">Discount (Rp)</span>
                                </td>
                                <td colspan="2" class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data_internal->total->disc_rp1) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-muted">Total</span>
                                </td>
                                <td colspan="2" class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data_internal->total->total) }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="card card-flush shadow mt-6">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Data Faktur</span>
                <span class="text-muted fw-bold fs-7">Data faktur internal</span>
            </h3>
            <div class="card-toolbar">
                <img src="{{ asset('assets/images/logo/bg_logo_suma.png') }}" class="h-50px" />
            </div>
        </div>
        <div class="card-body">
            <h4 class="text-muted">- TIDAK ADA DATA YANG DITAMPILKAN -</h4>
        </div>
    </div>
    @endforelse
</div>

<div class="row g-0">
    @if($data->faktur->status == 1)
        @if((double)$data->faktur->total_amount == (double)$data->shopee->item_price)
        <div class="d-flex">
            <button id="btnApproveFakturMarketplace" name="approve_faktur_marketplace" data-nomor_invoice="{{ $data->shopee->nomor_invoice }}"
                type="button" class="btn btn-primary mt-6">
                <i class="fa fa-check" aria-hidden="true"></i> Approve Faktur
            </button>
        </div>
        @else
        <div class="alert alert-dismissible bg-danger d-flex flex-column flex-sm-row w-100 p-5 mt-6">
            <span class="svg-icon svg-icon-2hx svg-icon-light me-4 mb-5 mb-sm-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.3" d="M12 22C13.6569 22 15 20.6569 15 19C15 17.3431 13.6569 16 12 16C10.3431 16 9 17.3431 9 19C9 20.6569 10.3431 22 12 22Z" fill="currentColor"></path>
                    <path d="M19 15V18C19 18.6 18.6 19 18 19H6C5.4 19 5 18.6 5 18V15C6.1 15 7 14.1 7 13V10C7 7.6 8.7 5.6 11 5.1V3C11 2.4 11.4 2 12 2C12.6 2 13 2.4 13 3V5.1C15.3 5.6 17 7.6 17 10V13C17 14.1 17.9 15 19 15ZM11 10C11 9.4 11.4 9 12 9C12.6 9 13 8.6 13 8C13 7.4 12.6 7 12 7C10.3 7 9 8.3 9 10C9 10.6 9.4 11 10 11C10.6 11 11 10.6 11 10Z" fill="currentColor"></path>
                </svg>
            </span>
            <div class="d-flex flex-column text-light pe-0 pe-sm-10">
                <h4 class="mb-2 text-light">Informasi</h4>
                <span>Jumlah nominal invoice dengan faktur internal tidak sama</span>
            </div>
        </div>
        @endif
    @endif
</div>

@push('scripts')
<script>
    const url = {
        'daftar_approve_order': "{{ route('online.orders.approveorder.daftar') }}",
        'proses_approve_marketplace': "{{ route('online.orders.approveorder.form.proses.marketplace') }}",
    }
</script>
<script src="{{ asset('assets/js/suma/online/approveorder/formmarketplace.js') }}?v={{ time() }}"></script>
@endpush
@endsection
