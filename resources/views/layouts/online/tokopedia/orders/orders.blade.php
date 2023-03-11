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
                        <option value="600" @if($data_filter->status == '660') selected @endif>Order delivered</option>
                        <option value="601" @if($data_filter->status == '601') selected @endif>Buyer open a case to finish an order</option>
                        <option value="690" @if($data_filter->status == '690') selected @endif>Fraud Review</option>
                        <option value="700" @if($data_filter->status == '700') selected @endif>Order finished</option>
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
                    <div id="navSemuaProses" class="nav-link text-active-primary ms-0 me-10 py-5 @if($data_filter->status != '220') active @endif"
                        style="cursor: pointer;">Semua Invoice</div>
                </li>
                <li class="nav-item mt-2">
                    <div id="navBelumProses" class="nav-link text-active-primary ms-0 me-10 py-5 @if($data_filter->status == '220') active @endif"
                        style="cursor: pointer;">Belum Diproses</div>
                </li>
            </ul>
        </div>
    </div>
</div>

<div id="postOrder">
    <!--Start List Order-->
    @foreach($data_order as $data)
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
                        <span class="fs-7 fw-boldest badge badge-success">{{ strtoupper($data->order_status->keterangan) }}</span>
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
                <div class="d-flex flex-stack w-250px mt-1">
                    <div class="fs-7 fw-bolder text-gray-800">Accept:</div>
                    <div class="fs-7 fw-bolder text-danger text-end">{{ $data->shipment_fulfillment->accept_deadline }}</div>
                </div>
                <div class="d-flex flex-stack w-250px mt-1">
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
                @php
                    $total_faktur = 0;
                @endphp

                @foreach ($data->faktur as $data_faktur)
                @php
                    $total_faktur = (double)$total_faktur + (double)$data_faktur->total
                @endphp
                @endforeach

                @forelse ($data->faktur as $data_faktur)
                <div class="border border-gray-300 border-dashed rounded py-3 px-4 me-6 mb-3">
                    <span class="fs-7 fw-bolder text-gray-500 d-block">No Faktur:</span>
                    <span class="fs-7 fw-boldest text-danger d-block mt-1">{{ empty($data_faktur->nomor_faktur) ? '(Not Found)' : $data_faktur->nomor_faktur }}</span>
                    <span class="fs-7 fw-bolder text-gray-800 d-block">{{ empty($data_faktur->tanggal) ? '-' : date('d F Y', strtotime($data_faktur->tanggal)) }}</span>
                    <div class="mt-5">
                        <span class="fs-8 fw-boldest badge badge-danger">{{ empty($data_faktur->kode_lokasi) ? '-' : strtoupper($data_faktur->kode_lokasi) }}</span>
                        <span class="fs-8 fw-boldest badge badge-primary">{{ empty($data_faktur->kode_sales) ? '-' : strtoupper($data_faktur->kode_sales) }}</span>
                        <span class="fs-8 fw-boldest badge badge-info">{{ empty($data_faktur->kode_dealer) ? '-' : strtoupper($data_faktur->kode_dealer) }}</span>
                    </div>
                    <span class="fs-7 fw-bolder text-gray-500 d-block mt-6">Total Faktur:</span>
                    <span class="fs-6 fw-boldest text-danger">Rp. {{ empty($data_faktur->total) ? '-' : number_format($data_faktur->total) }}</span>
                    @if((double)$total_faktur <> (double)$data->amount->product)
                    <span class="fs-7 fw-boldest text-danger animation-blink mt-10 d-block">TOTAL PRODUK DAN TOTAL FAKTUR TIDAK SAMA</span>
                    @endif
                </div>
                @empty
                <div class="col bg-light-danger min-h-200px px-6 py-8 rounded-2 me-7">
                    <span class="svg-icon svg-icon-3x svg-icon-danger d-block my-2 mt-10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path opacity="0.3" d="M21.25 18.525L13.05 21.825C12.35 22.125 11.65 22.125 10.95 21.825L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.125C22.25 12.725 22.25 11.325 21.25 10.925L13.05 7.62502C12.35 7.32502 11.65 7.32502 10.95 7.62502L2.75 10.925C1.75 11.325 1.75 12.725 2.75 13.125L10.95 16.425C11.65 16.725 12.45 16.725 13.05 16.425Z" fill="currentColor"></path>
                            <path d="M11.05 11.025L2.84998 7.725C1.84998 7.325 1.84998 5.925 2.84998 5.525L11.05 2.225C11.75 1.925 12.45 1.925 13.15 2.225L21.35 5.525C22.35 5.925 22.35 7.325 21.35 7.725L13.05 11.025C12.45 11.325 11.65 11.325 11.05 11.025Z" fill="currentColor"></path>
                        </svg>
                    </span>
                    <span class="text-danger fw-bold fs-6 mt-2">Faktur Not Found</span>
                </div>
                @endforelse
            </div>
        </div>
        <div class="separator"></div>
        <div class="row pt-4 pb-4 ps-6 pe-6">
            <div class="d-flex align-items-center">
                <div class="col-lg-6 text-start">
                    <a href="{{ route('online.orders.tokopedia.form.form', trim($data->nomor_invoice)) }}"
                        @if($data->order_status->kode == 0)
                        class="btn btn-danger w-250px"
                        @elseif($data->order_status->kode == 3)
                        class="btn btn-danger w-250px"
                        @elseif($data->order_status->kode == 5)
                        class="btn btn-danger w-250px"
                        @elseif($data->order_status->kode == 6)
                        class="btn btn-danger w-250px"
                        @elseif($data->order_status->kode == 10)
                        class="btn btn-danger w-250px"
                        @elseif($data->order_status->kode == 15)
                        class="btn btn-danger w-250px"
                        @elseif($data->order_status->kode == 100)
                        class="btn btn-warning w-250px"
                        @elseif($data->order_status->kode == 103)
                        class="btn btn-warning w-250px"
                        @elseif($data->order_status->kode == 220)
                        class="btn btn-success w-250px"
                        @elseif($data->order_status->kode == 221)
                        class="btn btn-primary w-250px"
                        @elseif($data->order_status->kode == 400)
                        class="btn btn-primary w-250px"
                        @elseif($data->order_status->kode == 450)
                        class="btn btn-primary w-250px"
                        @elseif($data->order_status->kode == 500)
                        class="btn btn-success w-250px"
                        @elseif($data->order_status->kode == 501)
                        class="btn btn-success w-250px"
                        @elseif($data->order_status->kode == 520)
                        class="btn btn-success w-250px"
                        @elseif($data->order_status->kode == 530)
                        class="btn btn-success w-250px"
                        @elseif($data->order_status->kode == 540)
                        class="btn btn-success w-250px"
                        @elseif($data->order_status->kode == 550)
                        class="btn btn-info w-250px"
                        @elseif($data->order_status->kode == 600)
                        class="btn btn-success w-250px"
                        @elseif($data->order_status->kode == 601)
                        class="btn btn-success w-250px"
                        @elseif($data->order_status->kode == 690)
                        class="btn btn-success w-250px"
                        @elseif($data->order_status->kode == 700)
                        class="btn btn-success w-250px"
                        @endif>
                        <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i> Lihat Detail Transaksi
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    <!--End List Order-->
</div>

@push('scripts')
<script>
    const url = {
        'daftar_order': "{{ route('online.orders.tokopedia.daftar') }}",
    }
</script>
<script src="{{ asset('assets/js/suma/online/tokopedia/orders/daftar.js') }}?v={{ time() }}"></script>
@endpush
@endsection

