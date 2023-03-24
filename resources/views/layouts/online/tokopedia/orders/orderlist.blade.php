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
            <span class="fs-7 fw-bolder text-gray-800 d-block">{{ $data->tanggal }}</span>

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
            <div class="border border-gray-300 border-dashed rounded py-2 px-4 me-6">
                <span class="fs-8 fw-bolder text-gray-500 d-block">No Faktur:</span>
                <span class="fs-7 fw-boldest text-danger d-block mt-1">{{ empty($data_faktur->nomor_faktur) ? '(Not Found)' : $data_faktur->nomor_faktur }}</span>
                <span class="fs-7 fw-bolder text-gray-800 d-block">{{ empty($data_faktur->tanggal) ? '-' : date('d F Y', strtotime($data_faktur->tanggal)) }}</span>
                <div class="mt-4">
                    <span class="fs-8 fw-boldest badge badge-light-danger">{{ empty($data_faktur->kode_lokasi) ? '-' : strtoupper($data_faktur->kode_lokasi) }}</span>
                    <span class="fs-8 fw-boldest badge badge-light-primary">{{ empty($data_faktur->kode_sales) ? '-' : strtoupper($data_faktur->kode_sales) }}</span>
                    <span class="fs-8 fw-boldest badge badge-light-info">{{ empty($data_faktur->kode_dealer) ? '-' : strtoupper($data_faktur->kode_dealer) }}</span>
                </div>
                <span class="fs-8 fw-bolder text-gray-500 d-block mt-4">Total Faktur:</span>
                <span class="fs-7 fw-boldest text-danger">Rp. {{ empty($data_faktur->total) ? '-' : number_format($data_faktur->total) }}</span>

                <div class="separator border-2 my-4"></div>

                @if((double)$total_faktur == (double)$data->amount->product)
                    @if((int)$data->order_status->kode > 220)
                    <button id="btnCetakLabel" class="btn btn-sm btn-primary" data-nomor_invoice="{{ trim($data->nomor_invoice) }}">
                        <i class="fa fa-file-text text-white" data-toggle="tooltip" data-placement="top" title="Select"></i> Cetak Label
                    </button>
                    @endif
                @endif

                @if(trim($data_faktur->nomor_surat_jalan) != '' || trim($data_faktur->nomor_serah_terima) == '')
                <div class="row mt-4">
                    @if(trim($data_faktur->nomor_surat_jalan) != '')
                    <div class="d-flex flex-stack w-250px mt-1">
                        <div class="fs-8 fw-bolder text-gray-500">Surat Jalan:</div>
                        <div class="fs-7 fw-bolder text-gray-800 text-end">{{ trim($data_faktur->nomor_surat_jalan) }}</div>
                    </div>
                    @endif

                    @if(trim($data_faktur->nomor_serah_terima) != '')
                    <div class="d-flex flex-stack w-250px mt-1">
                        <div class="fs-8 fw-bolder text-gray-500">Serah Terima:</div>
                        <div class="fs-7 fw-bolder text-gray-800 text-end">{{ trim($data_faktur->nomor_serah_terima) }}</div>
                    </div>
                    @endif
                </div>
                @endif

                @if((int)$data->order_status->kode == 400)
                    @if((double)$total_faktur == (double)$data->amount->product)
                        @if(trim($data_faktur->nomor_serah_terima) != '')
                        <div class="separator border-2 my-4"></div>
                        <div class="row">
                            <div class="d-flex">
                                <button id="btnRequestPickup" class="btn btn-sm btn-danger"
                                    data-nomor_invoice="{{ trim($data->nomor_invoice) }}"
                                    data-nomor_surat_jalan="{{ trim($data_faktur->nomor_surat_jalan) }}"
                                    data-nomor_serah_terima="{{ trim($data_faktur->nomor_serah_terima) }}">
                                    <i class="fa fa-truck text-white" data-toggle="tooltip" data-placement="top" title="Select"></i> Request Pickup
                                </button>
                            </div>
                        </div>
                        @endif
                    @endif
                @endif

                @if((double)$total_faktur != (double)$data->amount->product)
                <span class="fs-7 fw-boldest text-danger animation-blink mt-6 d-block">TOTAL PRODUK DAN TOTAL FAKTUR TIDAK SAMA</span>
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
                <a id="btnDetailInvoice" href="{{ route('online.orders.tokopedia.form.form', trim($data->nomor_invoice)) }}"
                    @if($data->order_status->kode == 0)
                    class="btn btn-danger"
                    @elseif($data->order_status->kode == 3)
                    class="btn btn-danger"
                    @elseif($data->order_status->kode == 5)
                    class="btn btn-danger"
                    @elseif($data->order_status->kode == 6)
                    class="btn btn-danger"
                    @elseif($data->order_status->kode == 10)
                    class="btn btn-danger"
                    @elseif($data->order_status->kode == 15)
                    class="btn btn-danger"
                    @elseif($data->order_status->kode == 100)
                    class="btn btn-warning"
                    @elseif($data->order_status->kode == 103)
                    class="btn btn-warning"
                    @elseif($data->order_status->kode == 220)
                    class="btn btn-success"
                    @elseif($data->order_status->kode == 221)
                    class="btn btn-primary"
                    @elseif($data->order_status->kode == 400)
                    class="btn btn-primary"
                    @elseif($data->order_status->kode == 450)
                    class="btn btn-primary"
                    @elseif($data->order_status->kode == 500)
                    class="btn btn-success"
                    @elseif($data->order_status->kode == 501)
                    class="btn btn-success"
                    @elseif($data->order_status->kode == 520)
                    class="btn btn-success"
                    @elseif($data->order_status->kode == 530)
                    class="btn btn-success"
                    @elseif($data->order_status->kode == 540)
                    class="btn btn-success"
                    @elseif($data->order_status->kode == 550)
                    class="btn btn-info"
                    @elseif($data->order_status->kode == 600)
                    class="btn btn-success"
                    @elseif($data->order_status->kode == 601)
                    class="btn btn-success"
                    @elseif($data->order_status->kode == 690)
                    class="btn btn-success"
                    @elseif($data->order_status->kode == 700)
                    class="btn btn-success"
                    @endif>
                    <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i> Detail Invoice
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach
