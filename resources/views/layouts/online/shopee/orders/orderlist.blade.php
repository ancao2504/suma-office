@forelse($data_order as $data)
<div class="card mb-5 mb-xl-8 mt-6">
    <div class="row pt-4 pb-4 ps-6 pe-6">
        <div class="col-lg-6 text-start">
            <span class="fs-7 fw-bolder text-gray-600">Users:
                <span class="ms-2">
                    <span class="fs-7 fw-bolder text-info">{{ trim($data->buyer_username) }}</span>
                </span>
            </span>
        </div>
        <div class="col-lg-6 text-end">
            <span class="fs-7 fw-bolder text-gray-600">Status:
                <span class="ms-2">
                    @if(strtoupper(trim($data->order_status)) == 'UNPAID')
                    <span class="fs-7 fw-boldest badge badge-danger">UNPAID</span>
                    @elseif(strtoupper(trim($data->order_status)) == 'READY_TO_SHIP')
                    <span class="fs-7 fw-boldest badge badge-success">READY TO SHIP</span>
                    @elseif(strtoupper(trim($data->order_status)) == 'PROCESSED')
                    <span class="fs-7 fw-boldest badge badge-info">PROCESSED</span>
                    @elseif(strtoupper(trim($data->order_status)) == 'SHIPPED')
                    <span class="fs-7 fw-boldest badge badge-info">SHIPPED</span>
                    @elseif(strtoupper(trim($data->order_status)) == 'COMPLETED')
                    <span class="fs-7 fw-boldest badge badge-primary">COMPLETED</span>
                    @elseif(strtoupper(trim($data->order_status)) == 'IN_CANCEL')
                    <span class="fs-7 fw-boldest badge badge-warning">IN CANCEL</span>
                    @elseif(strtoupper(trim($data->order_status)) == 'CANCELLED')
                    <span class="fs-7 fw-boldest badge badge-warning">CANCELLED</span>
                    @elseif(strtoupper(trim($data->order_status)) == 'INVOICE_PENDING')
                    <span class="fs-7 fw-boldest badge badge-warning">INVOICE PENDING</span>
                    @endif
                </span>
            </span>
        </div>
    </div>
    <div class="separator"></div>
    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-6 pt-6 pb-6 ps-10">
            <span class="fs-7 fw-bolder text-gray-500 d-block">No Invoice:</span>
            <span class="fs-7 fw-boldest text-primary d-block mt-1">{{ trim($data->order_sn) }}</span>
            <span class="fs-7 fw-bolder text-gray-800 d-block">{{ date('d F Y', $data->create_time) }}</span>

            <span class="fs-7 fw-bolder text-gray-500 d-block mt-6">Logistics:</span>
            <span class="fs-7 fw-boldest text-danger d-block mt-1">{{ trim($data->shipping_carrier) }}</span>

            <span class="fs-7 fw-bolder text-gray-500 d-block mt-6">Amounts:</span>
            <div class="d-flex flex-stack w-200px mt-1">
                <div class="fs-7 fw-bolder text-gray-800">Products:</div>
                <div class="fs-7 fw-boldest text-danger text-end">Rp. {{ number_format($data->actual_products) }}</div>
            </div>
            <div class="d-flex flex-stack w-200px mt-1">
                <div class="fs-7 fw-bolder text-gray-800">Total:</div>
                <div class="fs-7 fw-bolder text-dark text-end">Rp. {{ number_format($data->total_amount) }}</div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 pt-6 pb-6 ps-6">
            <span class="fs-7 fw-bolder text-gray-500 d-block">Recipient:</span>
            <span class="fs-7 fw-bolder text-dark d-block mt-1">{{ $data->recipient->full_address }}</span>
            <span class="fs-7 fw-bolder text-gray-800 d-block mt-8">{{ $data->recipient->city }}</span>
            <span class="fs-7 fw-bolder text-gray-800 d-block">{{ $data->recipient->state }}, {{ $data->recipient->region }}</span>
            <span class="fs-7 fw-bolder text-gray-800 d-block">{{ $data->recipient->zipcode }}</span>

            <span class="fs-7 fw-bolder text-gray-500 d-block mt-8">Payments:</span>
            <span class="fs-7 fw-boldest text-info d-block mt-1">{{ trim($data->payment_method) }}</span>

            @if(strtoupper(trim($data->cod)) === true)
            <span class="fs-7 fw-boldest badge badge-danger mt-6">COD</span>
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

                @if((double)$total_faktur == (double)$data->actual_products)
                @if(strtoupper(trim($data->order_status)) != 'READY_TO_SHIP' || strtoupper(trim($data->order_status)) != 'UNPAID')
                <button id="btnCetakLabel" class="btn btn-sm btn-info" data-nomor_invoice="{{ trim($data->order_sn) }}">
                    <i class="fa fa-file-text text-white" data-toggle="tooltip" data-placement="top" title="Select"></i> Cetak Label
                </button>
                @endif
                @endif

                @if(trim($data_faktur->nomor_surat_jalan) != '' || trim($data_faktur->nomor_serah_terima) == '')
                <div class="row mt-4">
                    @if(trim($data_faktur->nomor_surat_jalan) != '')
                    <div class="d-flex flex-stack w-250px mt-1">
                        <div class="fs-7 fw-bolder text-gray-500">Surat Jalan:</div>
                        <div class="fs-7 fw-bolder text-gray-800 text-end">{{ trim($data_faktur->nomor_surat_jalan) }}</div>
                    </div>
                    @endif
                    @if(trim($data_faktur->nomor_serah_terima) != '')
                    <div class="d-flex flex-stack w-250px mt-1">
                        <div class="fs-7 fw-bolder text-gray-500">Serah Terima:</div>
                        <div class="fs-7 fw-bolder text-gray-800 text-end">{{ trim($data_faktur->nomor_serah_terima) }}</div>
                    </div>
                    @endif
                </div>
                @endif

                @if((double)$total_faktur != (double)$data->actual_products)
                <span class="fs-7 fw-boldest text-danger animation-blink mt-6 d-block">TOTAL PRODUK DAN TOTAL FAKTUR TIDAK SAMA</span>
                @else
                    @if(!empty($data_faktur->nomor_serah_terima) && trim($data_faktur->nomor_serah_terima) != '')
                        @if(strtoupper(trim($data->order_status)) == 'READY_TO_SHIP')
                        <div class="d-flex mt-4">
                            <button id="btnRequestPickup" class="btn btn-sm btn-danger" data-nomor_invoice="{{ trim($data->order_sn) }}">
                                <i class="fa fa-truck text-white" data-toggle="tooltip" data-placement="top" title="Select"></i> Request Pickup
                            </button>
                        </div>
                        @endif
                    @endif
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
                <a id="btnDetailInvoice" href="{{ route('online.orders.shopee.form.form', trim($data->order_sn)) }}"
                    @if(strtoupper(trim($data->order_status)) == 'UNPAID')
                    class="btn btn-danger"
                    @elseif(strtoupper(trim($data->order_status)) == 'READY_TO_SHIP')
                    class="btn btn-success"
                    @elseif(strtoupper(trim($data->order_status)) == 'PROCESSED')
                    class="btn btn-info"
                    @elseif(strtoupper(trim($data->order_status)) == 'SHIPPED')
                    class="btn btn-info"
                    @elseif(strtoupper(trim($data->order_status)) == 'COMPLETED')
                    class="btn btn-primary"
                    @elseif(strtoupper(trim($data->order_status)) == 'IN_CANCEL')
                    class="btn btn-warning"
                    @elseif(strtoupper(trim($data->order_status)) == 'CANCELLED')
                    class="btn btn-warning"
                    @elseif(strtoupper(trim($data->order_status)) == 'INVOICE_PENDING')
                    class="btn btn-warning"
                    @endif>
                    <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i> Detail Invoice
                </a>
            </div>
        </div>
    </div>
</div>
@empty
<div class="card mb-5 mb-xl-8 mt-6">
    <div class="fs-6 fw-boldest text-muted p-20 text-center">- TIDAK ADA DATA YANG DITAMPILKAN -</div>
</div>
@endforelse
