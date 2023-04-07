@extends('layouts.main.index')
@section('title','Tokopedia')
@section('subtitle','Orders')
@section('container')
@if($data->faktur->status == 1)
    @if((double)$data->faktur->total_amount != (double)$data->tokopedia->item_price)
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
    <div class="card card-flush">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Invoice</span>
                <span class="text-muted fw-bold fs-7">Data invoice marketplace tokopedia</span>
            </h3>
            <div class="card-toolbar">
                <img src="{{ asset('assets/images/logo/tokopedia_lg.png') }}" class="h-75px" />
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 pt-6 pb-6">
                    <span class="fs-7 fw-bolder text-gray-500 d-block">No Invoice:</span>
                    <span class="fs-7 fw-bolder text-dark d-block mt-1">{{ $data->tokopedia->nomor_invoice }}</span>
                    <span class="fs-7 fw-bolder text-primary d-block">{{ $data->tokopedia->order_id }}</span>

                    <span class="fs-7 fw-bolder text-gray-500 d-block mt-6">Logistics:</span>
                    <div class="fw-bolder fs-7 text-gray-800 d-flex align-items-center flex-wrap mt-1">
                        <span class="pe-2">{{ $data->tokopedia->logistic->name }}</span>
                        <span class="fs-7 text-danger d-flex align-items-center">
                        <span class="bullet bullet-dot bg-danger me-2"></span>{{ $data->tokopedia->logistic->service }}</span>
                    </div>

                    <span class="fs-7 fw-bolder text-gray-500 d-block mt-6">Amounts:</span>
                    <div class="d-flex flex-stack w-200px mt-1">
                        <div class="fs-7 fw-bolder text-gray-800">Product:</div>
                        <div class="fs-7 fw-boldest text-danger text-end">Rp. {{ number_format($data->tokopedia->item_price) }}</div>
                    </div>
                    <div class="d-flex flex-stack w-200px mt-1">
                        <div class="fs-7 fw-bolder text-gray-800">Shipping:</div>
                        <div class="fs-7 fw-bolder text-dark text-end">Rp. {{ number_format($data->tokopedia->shipping_price) }}</div>
                    </div>

                    <span class="fs-7 fw-bolder text-gray-500 d-block mt-6">Deadline Confirmation:</span>
                    <div class="d-flex flex-stack w-250px mt-1">
                        <div class="fs-7 fw-bolder text-gray-800">Accept:</div>
                        <div class="fs-7 fw-bolder text-danger text-end">{{ $data->tokopedia->shipment_fulfillment->accept_deadline }}</div>
                    </div>
                    <div class="d-flex flex-stack w-250px mt-1">
                        <div class="fs-7 fw-bolder text-gray-800">Shipping:</div>
                        <div class="fs-7 fw-bolder text-danger text-end">{{ $data->tokopedia->shipment_fulfillment->confirm_shipping_deadline }}</div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 pt-6 pb-6 ps-6">
                    <span class="fs-7 fw-bolder text-gray-500 d-block">Payment:</span>
                    <span class="fs-7 fw-bolder text-gray-800 d-block mt-1">{{ $data->tokopedia->payment->ref_number }}</span>
                    <span class="fs-7 fw-bolder text-gray-800 d-block">{{ date('d F Y', strtotime($data->tokopedia->payment->date)) }}</span>
                    @if($data->tokopedia->payment->status == 'verified')
                    <span class="fs-8 fw-boldest badge badge-light-success mt-2">
                        <i class="fa fa-check me-2 text-success" aria-hidden="true"></i>Verified
                    </span>
                    @else
                    <span class="fs-8 fw-boldest badge badge-light-danger mt-2">
                        <i class="fa fa-times me-2 text-danger" aria-hidden="true"></i>{{ strtoupper($data->tokopedia->payment->status) }}
                    </span>
                    @endif

                    <span class="fs-7 fw-bolder text-gray-500 d-block mt-6">Recipient:</span>
                    <span class="fs-7 fw-bolder text-dark d-block mt-1">{{ $data->tokopedia->address->district }}</span>
                    <span class="fs-7 fw-bolder text-gray-800 d-block">{{ $data->tokopedia->address->city }}, {{ $data->tokopedia->address->province }}</span>
                    <span class="fs-7 fw-bolder text-gray-800 d-block">{{ $data->tokopedia->address->postal }}</span>

                    <span class="fs-7 fw-bolder text-gray-500 d-block mt-6">Status:</span>
                    <span class="fs-8 fw-boldest badge @if($data->tokopedia->status->is_accepted == true) badge-light-success @else badge-light-danger @endif me-2 mt-2">
                        @if($data->tokopedia->status->is_accepted == true)
                        <i class="fa fa-check me-2 text-success" aria-hidden="true"></i>Accepted
                        @else
                        <i class="fa fa-times me-2 text-danger" aria-hidden="true"></i>Accepted
                        @endif
                    </span>
                    <span class="fs-8 fw-boldest badge @if($data->tokopedia->status->is_confirm_shipping == true) badge-light-success @else badge-light-danger @endif me-2 mt-2">
                        @if($data->tokopedia->status->is_confirm_shipping == true)
                        <i class="fa fa-check me-2 text-success" aria-hidden="true"></i>Confirm Shipping
                        @else
                        <i class="fa fa-times me-2 text-danger" aria-hidden="true"></i>Confirm Shipping
                        @endif
                    </span>
                    <span class="fs-8 fw-boldest badge @if($data->tokopedia->status->is_item_delivered == true) badge-light-success @else badge-light-danger @endif me-2 mt-2">
                        @if($data->tokopedia->status->is_item_delivered == true)
                        <i class="fa fa-check me-2 text-success" aria-hidden="true"></i>Item Delivered
                        @else
                        <i class="fa fa-times me-2 text-danger" aria-hidden="true"></i>Item Delivered
                        @endif
                    </span>
                </div>
            </div>
            <div class="row mt-4 mb-4">
                <div class="d-flex">
                @if($data->tokopedia->status->is_accepted == true)
                    <button id="btnCetakLabel" name="cetak_label" type="button" class="btn btn-sm btn-primary me-2"
                        data-nomor_invoice="{{ trim($data->tokopedia->nomor_invoice) }}">
                        <i class="fa fa-file-text text-white" data-toggle="tooltip" data-placement="top" title="Select"></i> Cetak Label
                    </button>

                    @if($data->tokopedia->status->is_confirm_shipping == false)
                        @if($data->faktur->status == 1)
                            @php
                                $total_faktur = 0;
                            @endphp

                            @foreach ($data->faktur->list as $data_total)
                            @php
                                $total_faktur = (double)$total_faktur + (double)$data_total->total->total
                            @endphp
                            @endforeach

                            @if((double)$data->faktur->total_amount == (double)$data->tokopedia->item_price)
                                @foreach($data->faktur->list as $data_serah_terima)
                                    @if(trim($data_serah_terima->nomor_serah_terima) != '')
                                    <button id="btnRequestPickup" name="request_pickup" type="button" class="btn btn-sm btn-danger"
                                        data-nomor_invoice="{{ trim($data->tokopedia->nomor_invoice) }}">
                                        <i class="fa fa-truck text-white" data-toggle="tooltip" data-placement="top" title="Select"></i> Request Pickup
                                    </button>
                                    @endif
                                @endforeach
                            @endif
                        @endif
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
                            @forelse($data->tokopedia->detail as $detail_tokopedia)
                            <tr>
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                    <span class="fs-7 fw-bold text-gray-800">{{ $loop->iteration }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                    <div class="d-flex">
                                        <div class="symbol symbol-45px me-5">
                                            <img src="{{ $detail_tokopedia->pictures }}" alt="{{ $detail_tokopedia->sku }}">
                                        </div>
                                        <div class="d-flex justify-content-start flex-column">
                                            <span class="fs-7 fw-bolder text-dark">{{ $detail_tokopedia->product_name }}</span>
                                            <span class="fs-8 fw-bolder text-gray-600 d-block">(SKU : {{ $detail_tokopedia->sku }})</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($detail_tokopedia->quantity) }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($detail_tokopedia->product_price) }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($detail_tokopedia->subtotal_price) }}</span>
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
    @if($data->faktur->status == 0)
        @if($data->tokopedia->payment->status == 'verified')
        <div class="d-block">
            <button id="btnSimulasiFaktur" name="simulasi_faktur" type="button" class="btn btn-primary mt-6">
                <i class="fa fa-calculator" aria-hidden="true"></i> Simulasi Faktur
            </button>
        </div>
        @endif
    @else
        @forelse($data->faktur->list as $data_internal)
        <div class="card card-flush mt-6">
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
        <div class="card card-flush mt-6">
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
    @endif
</div>

<div class="modal fade bd-example-modal-lg" tabindex="-2" id="modalSimulasiOrder">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="modalSeimulasiOrderContent">
            <div class="modal-header">
                <h5 id="modalTitle" name="modalTitle" class="modal-title">Simulasi Order</h5>
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
                <div class="row">
                    <div class="col-lg-4 pt-6 pb-6">
                        <span class="fs-7 fw-bold text-gray-400 d-block">No Faktur:</span>
                        <span class="fs-6 fw-bolder text-dark d-block mt-1">{{ $data->faktur->list[0]->nomor_faktur }}</span>
                        <div class="fw-bolder fs-7 text-gray-800 d-flex align-items-center flex-wrap mt-1">
                            <span class="pe-2">{{ $data->faktur->list[0]->jenis_beli->keterangan }}</span>
                            <span class="fs-7 text-danger d-flex align-items-center">
                            <span class="bullet bullet-dot bg-danger me-2"></span>{{ $data->faktur->list[0]->jenis_beli->kode }}</span>
                        </div>
                        <span class="fs-7 fw-bold text-gray-500 d-block mt-6">Ekspedisi:</span>
                        <div class="fw-bolder fs-7 text-gray-800 d-flex align-items-center flex-wrap mt-1">
                            <span class="pe-2">{{ $data->faktur->list[0]->ekspedisi->nama }}</span>
                            <span class="fs-7 text-danger d-flex align-items-center">
                            <span class="bullet bullet-dot bg-danger me-2"></span>{{ $data->faktur->list[0]->ekspedisi->kode }}</span>
                        </div>
                    </div>
                    <div class="col-lg-4 pt-6 pb-6">
                        <span class="fs-7 fw-bold text-gray-500 d-block">Nomor POF:</span>
                        <span class="fs-7 fw-bolder text-dark d-block mt-1">{{ $data->faktur->list[0]->nomor_pof }}</span>

                        <span class="fs-7 fw-bold text-gray-400 d-block mt-6">Salesman / Dealer:</span>
                        <div class="fw-bolder fs-7 text-gray-800 d-flex align-items-center flex-wrap mt-1">
                            <span class="pe-2">
                                <span class="fs-8 fw-boldest badge badge-primary">{{ $data->faktur->list[0]->salesman->kode }}</span>
                            </span>
                            <span class="fs-7 text-danger d-flex align-items-center">
                                <span class="bullet bullet-dot bg-info me-2"></span>
                                <span class="fs-8 fw-boldest badge badge-info">{{ $data->faktur->list[0]->dealer->kode }}</span>
                            </span>
                        </div>
                        <span class="fs-7 fw-bold text-gray-500 d-block mt-6">Keterangan:</span>
                        <span class="fs-7 fw-bolder text-danger d-block mt-1">{{ $data->faktur->list[0]->keterangan }}</span>
                    </div>
                    <div class="col-lg-4 pt-6 pb-6">
                        <span class="fs-7 fw-bold text-gray-500 d-block">Umur Faktur:</span>
                        <div class="fw-bolder fs-7 text-gray-800 d-flex align-items-center flex-wrap mt-1">
                            <span class="pe-2">{{ date('d F Y', strtotime($data->faktur->list[0]->jatuh_tempo->tanggal)) }}</span>
                            <span class="fs-7 text-danger d-flex align-items-center">
                            <span class="bullet bullet-dot bg-danger me-2"></span>{{ $data->faktur->list[0]->jatuh_tempo->umur_faktur }} Hari</span>
                        </div>

                        <span class="fs-7 fw-bold text-gray-500 d-block mt-6">Status:</span>
                        <span class="fs-8 fw-boldest badge badge-primary mt-1 me-2">TPC {{ $data->faktur->list[0]->kode_tpc }}</span>
                        <span class="fs-8 fw-boldest badge badge-success mt-1 me-2">
                            @if(strtoupper($data->faktur->list[0]->status->rh) == 'H') HOTLINE @else REGULER @endif
                        </span>
                        <span class="fs-8 fw-boldest badge badge-danger mt-1 me-2">
                            @if(strtoupper($data->faktur->list[0]->status->bo) == 'B') BACKORDER @else TIDAK BO @endif
                        </span>
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
                                    <th class="w-100px ps-3 pe-3 text-center">Stock</th>
                                    <th class="w-100px ps-3 pe-3 text-center">Harga</th>
                                    <th class="w-100px ps-3 pe-3 text-center">Disc(%)</th>
                                    <th class="w-100px ps-3 pe-3 text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody class="border">
                                @forelse($data->faktur->list[0]->detail as $data_faktur)
                                <tr>
                                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                        <span class="fs-7 fw-bold text-gray-800">{{ $loop->iteration }}</span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                        <div class="d-flex">
                                            <div class="symbol symbol-45px me-5">
                                                <img src="{{ trim($data_faktur->pictures) }}" alt="{{ trim($data_faktur->part_number) }}">
                                            </div>
                                            <div class="d-flex justify-content-start flex-column">
                                                <span class="fs-7 fw-bolder text-dark">{{ trim($data_faktur->nama_part) }}</span>
                                                <span class="fs-8 fw-bolder text-gray-600 d-block">(SKU : {{ trim($data_faktur->part_number) }})</span>
                                                @if(trim($data_faktur->keterangan) != '')
                                                <div class="d-flex">
                                                    <div class="badge badge-danger mt-4 animation-blink">{{ trim($data_faktur->keterangan) }}</div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                        <span class="fs-7 fw-bold text-gray-800">{{ number_format($data_faktur->jml_order) }}</span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                        <span class="fs-7 fw-bold text-gray-800">{{ number_format($data_faktur->jml_jual) }}</span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                        <span class="fs-7 fw-bold text-gray-800">{{ number_format($data_faktur->stock) }}</span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                        <span class="fs-7 fw-bold text-gray-800">{{ number_format($data_faktur->harga) }}</span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                        <span class="fs-7 fw-bold text-gray-800">{{ number_format($data_faktur->disc_detail, 2) }}</span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                        <span class="fs-7 fw-bold text-gray-800">{{ number_format($data_faktur->total_detail) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="ps-3 pe-3" style="text-align:center;vertical-align:center;">
                                        <span class="fs-7 fw-boldest text-muted">- TIDAK ADA DATA YANG DITAMPILKAN -</span>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="border">
                                <tr>
                                    <td colspan="6" class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                        <span class="fs-8 fw-bolder text-muted">Subtotal</span>
                                    </td>
                                    <td colspan="2" class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                        <span class="fs-7 fw-bold text-gray-800">{{ number_format($data->faktur->list[0]->total->sub_total) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                        <span class="fs-8 fw-bolder text-muted">Disc(%)</span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                        <span class="fs-7 fw-bold text-gray-800">{{ number_format($data->faktur->list[0]->total->disc_header, 2) }}</span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                        <span class="fs-7 fw-bold text-gray-800">{{ number_format($data->faktur->list[0]->total->disc_header_rp) }}</span>
                                    </td>

                                </tr>
                                <tr>
                                    <td colspan="6" class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                        <span class="fs-8 fw-bolder text-muted">DiscRp</span>
                                    </td>
                                    <td colspan="2" class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                        <span class="fs-7 fw-bold text-gray-800">{{ number_format($data->faktur->list[0]->total->disc_rp1) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                        <span class="fs-8 fw-bolder text-muted">Total</span>
                                    </td>
                                    <td colspan="2" class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                        <span class="fs-7 fw-bold text-gray-800">{{ number_format($data->faktur->list[0]->total->total) }}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <div class="text-start">
                    <div class="input-group">
                        <span class="input-group-text">Tanggal Faktur</span>
                        <input id="inputTanggalProses" name="tanggal" class="form-control w-150px" type="date" placeholder="Tanggal Proses"
                            @if(isset($tanggal)) value="{{ $tanggal }}" @else value="{{ old('tanggal') }}" @endif>
                        <button id="btnProsesOrder" name="btnProsesOrder" type="button" class="btn btn-primary">Proses Order</button>
                    </div>
                </div>
                <div class="text-end">
                    <button id="btnCloseModal" name="btnClose" type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    const url = {
        'clossing_marketing': "{{ route('setting.default.clossing-marketing') }}",
        'proses_order': "{{ route('online.orders.tokopedia.form.proses') }}",
        'proses_cetak_label': "{{ route('online.serahterima.form.cetak-label-tokopedia') }}",
        'proses_request_pickup_tokopedia': "{{ route('online.serahterima.form.tokopedia-request-pickup') }}",
    }
    const data = {
        'nomor_invoice': "{{ $data->tokopedia->nomor_invoice }}",
        'tanggal': "{{ $tanggal }}",
    }
</script>
<script src="{{ asset('assets/js/suma/online/tokopedia/orders/form.js') }}?v={{ time() }}"></script>
@endpush
@endsection
