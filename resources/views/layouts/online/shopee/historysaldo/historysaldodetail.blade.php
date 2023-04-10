@extends('layouts.online.shopee.historysaldo.historysaldo')
@section('containerhistorysaldo')
<div class="row g-0">
    <div class="card card-flush mb-6">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Detail Saldo</span>
                <span class="text-muted fw-bold fs-7">Daftar list saldo detail marketplace shopee</span>
                <span class="text-muted fw-bold fs-7 mt-2">Periode
                    <span class="text-dark fw-bolder fs-7">{{ date('d F Y', strtotime($data_filter->start_date)) }}</span> s/d
                    <span class="text-dark fw-bolder fs-7">{{ date('d F Y', strtotime($data_filter->end_date)) }}</span>
                </span>
            </h3>
        </div>
        <div class="card-header align-items-center border-0">
            <div class="align-items-start flex-column">
                <div class="input-group">
                    <input id="inputStartDate" name="start_date" class="form-control w-md-150px" placeholder="Dari Tanggal" value="{{ $data_filter->start_date }}">
                    <span class="input-group-text">s/d</span>
                    <input id="inputEndDate" name="end_date" class="form-control w-md-150px" placeholder="Sampai Dengan" value="{{ $data_filter->end_date }}">
                    <button id="btnFilterMasterData" class="btn btn-icon btn-primary" type="button">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="postData">
<!--Start List History Saldo-->
@foreach($data_saldo as $data)
@php
    $total_faktur = 0;
@endphp

@foreach ($data->faktur as $data_faktur)
@php
    $total_faktur = (double)$total_faktur + (double)$data_faktur->total
@endphp
@endforeach

@if(strtoupper(trim(trim($data->transaction_type))) == 'ESCROW_VERIFIED_ADD')
<div class="card mb-5 mb-xl-8 mt-6">
    <div class="row pt-4 pb-4 ps-6 pe-6">
        <div class="col-lg-6 text-start">
            <span class="fs-8 fw-bold text-gray-600">Transaksi:
                <span class="ms-2">
                    <span class="fs-8 fw-boldest text-info">{{ strtoupper(trim(trim($data->transaction_type))) }}</span>
                </span>
            </span>
        </div>
        <div class="col-lg-6 text-end">
            <span class="fs-8 fw-bold text-gray-600">Status:
                <span class="ms-2">
                    @if(strtoupper(trim($data->status)) == 'COMPLETED')
                    <span class="fs-8 fw-boldest badge badge-success">{{ trim($data->status) }}</span>
                    @else
                    <span class="fs-8 fw-boldest badge badge-danger">{{ trim($data->status) }}</span>
                    @endif
                </span>
            </span>
        </div>
    </div>
    <div class="separator"></div>
    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-6 pt-6 pb-6 ps-10">
            <span class="fs-7 fw-bolder text-gray-500 d-block">Nomor Invoice:</span>
            <span class="fs-7 fw-bolder text-primary d-block mt-1">{{ strtoupper(trim($data->order_sn)) }}</span>
            <span class="fs-7 fw-bolder text-gray-800 d-block">{{ date('d F Y', $data->create_time) }}</span>
            <span class="fs-7 fw-bolder text-gray-600 d-block">{{ date('H:i:s', $data->create_time) }}</span>

            <span class="fs-7 fw-bolder text-gray-500 d-block mt-6">Detail Biaya Admin:</span>
            <div class="d-flex flex-stack w-200px mt-1">
                <div class="fs-7 fw-bolder text-gray-800">Administrasi:</div>
                <div class="fs-7 fw-boldest text-danger text-end">Rp. {{ number_format($data->saldo_detail->commission_fee) }}</div>
            </div>
            <div class="d-flex flex-stack w-200px mt-1">
                <div class="fs-7 fw-bolder text-gray-800">Layanan:</div>
                <div class="fs-7 fw-boldest text-danger text-end">Rp. {{ number_format($data->saldo_detail->service_fee) }}</div>
            </div>
            <div class="d-flex flex-stack w-200px mt-1">
                <div class="fs-7 fw-bolder text-gray-800">Premi:</div>
                <div class="fs-7 fw-boldest text-danger text-end">Rp. {{ number_format($data->saldo_detail->delivery_seller_protection_fee_premium_amount) }}</div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 pt-6 pb-6 ps-10">
            <span class="fs-7 fw-bolder text-gray-500 d-block">Description:</span>
            @if($data->transaction_type == 'WITHDRAWAL_COMPLETED' || $data->transaction_type == 'WITHDRAWAL_CREATED')
            <span class="fs-7 fw-bolder text-gray-800 d-block mt-1">Penarikan Dana Bank</span>
            @elseif($data->transaction_type == 'ADJUSTMENT_CENTER_ADD')
            <span class="fs-7 fw-bolder text-gray-800 d-block mt-1">{{ trim($data->reason) }}</span>
            @elseif($data->transaction_type == 'ESCROW_VERIFIED_ADD')
            <span class="fs-7 fw-bolder text-gray-800 d-block mt-1">Penghasilan dari Pesanan #{{ strtoupper(trim($data->order_sn)) }}</span>
            @else
            <span class="fs-7 fw-bolder text-gray-800 d-block mt-1">{{ trim($data->reason) }}</span>
            @endif

            <span class="fs-7 fw-bolder text-gray-500 d-block mt-6">Total:</span>
            <div class="d-flex flex-stack w-200px mt-1">
                <div class="fs-7 fw-bolder text-gray-800">Produk:</div>
                <div class="fs-7 fw-boldest text-gray-800 text-end">Rp. {{ number_format($data->saldo_detail->original_cost_of_goods_sold) }}</div>
            </div>
            <div class="d-flex flex-stack w-200px mt-1">
                <div class="fs-7 fw-bolder text-gray-800">Biaya Admin:</div>
                <div class="fs-7 fw-boldest text-danger text-end">- Rp. {{ number_format($data->saldo_detail->admin_amount) }}</div>
            </div>
            <div class="d-flex flex-stack w-200px mt-1">
                <div class="fs-7 fw-bolder text-gray-800">Penghasilan:</div>
                <div class="fs-7 fw-boldest text-success text-end">+ Rp. {{ number_format($data->amount) }}</div>
            </div>
            <div class="separator w-200px my-5"></div>
            <div class="d-flex flex-stack w-200px mt-1">
                <div class="fs-7 fw-bolder text-gray-800">Saldo Akhir:</div>
                <div class="fs-7 fw-boldest text-info text-end">Rp. {{ number_format($data->current_balance) }}</div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 pt-6 pb-6 ps-10">
            @forelse($data->faktur as $data_faktur)
            <div class="border border-gray-300 border-dashed rounded py-3 px-4 me-6 mb-3">
                <span class="fs-8 fw-bolder text-gray-500 d-block">No Faktur:</span>
                @if((double)$total_faktur != (double)$data->saldo_detail->original_cost_of_goods_sold)
                <span class="fs-7 fw-bolder text-danger d-block mt-1">{{ $data_faktur->nomor_faktur }}</span>
                @else
                <span class="fs-7 fw-bolder text-primary d-block mt-1">{{ $data_faktur->nomor_faktur }}</span>
                @endif

                <span class="fs-7 fw-bolder text-gray-800 d-block">{{ date('d F Y', strtotime($data_faktur->tanggal_faktur)) }}</span>
                <span class="fs-8 fw-bolder text-gray-500 d-block mt-4">Total Faktur:</span>

                @if((double)$total_faktur != (double)$data->saldo_detail->original_cost_of_goods_sold)
                <span class="fs-7 fw-boldest text-danger d-block">Rp. {{ number_format($data_faktur->total) }}</span>
                @else
                <span class="fs-7 fw-boldest text-primary d-block">Rp. {{ number_format($data_faktur->total) }}</span>
                @endif

                @if((double)$total_faktur != (double)$data->saldo_detail->original_cost_of_goods_sold)
                <div class="mt-6">
                    <span class="fs-8 fw-boldest text-danger animation-blink">TOTAL PRODUK DAN FAKTUR TIDAK SAMA</span>
                </div>
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
</div>
@elseif(str_contains(strtoupper(trim(trim($data->transaction_type))), 'WITHDRAWAL'))
<div class="card mb-5 mb-xl-8 mt-6" style="background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);">
    <div class="card-body">
        <div class="row align-items-center h-75">
            <div class="col-7">
                <div class="text-white">
                    <span class="fs-5 fw-bold me-2 d-block pb-2">{{ date('d F Y', $data->create_time) }} {{ date('H:i:s', $data->create_time) }}</span>
                    <span class="fs-2 fw-bolder">Penarikan Dana Bank</span>
                </div>
                <span class="fw-bold text-white fs-6 mb-8 d-block opacity-75">{{ strtoupper(trim(trim($data->transaction_type))) }}</span>
            </div>
        </div>
        <div class="row align-items-center h-75">
            <div class="d-flex flex-stack">
                <span class="fs-6 fw-bold text-white">Total</span>
                <span class="fs-6 fw-bolder text-white">Rp. {{ number_format($data->amount) }}</span>
            </div>
            <div class="d-flex flex-stack">
                <span class="fs-6 fw-bold text-white">Saldo Akhir</span>
                <span class="fs-6 fw-bolder text-white">Rp. {{ number_format($data->current_balance) }}</span>
            </div>
        </div>
    </div>
</div>
@else
<div class="card mb-5 mb-xl-8 mt-6"
    @if((double)$data->amount > 0) style="background: linear-gradient(90deg, #9ebd13 0%, #008552 100%);"
    @else style="background: linear-gradient(90deg, #FC466B 0%, #3F5EFB 100%);" @endif>
    <div class="card-body">
        <div class="row align-items-center h-75">
            <div class="col-7 ps-xl-13">
                <div class="text-white">
                    <span class="fs-5 fw-bold me-2 d-block pb-2">{{ date('d F Y', $data->create_time) }} {{ date('H:i:s', $data->create_time) }}</span>
                    <span class="fs-2 fw-bolder">{{ $data->reason }}</span>
                </div>
                <span class="fw-bold text-white fs-6 mb-8 d-block opacity-75">{{ strtoupper(trim(trim($data->transaction_type))) }}</span>
            </div>
        </div>
        <div class="row align-items-center h-75">
            <div class="d-flex flex-stack">
                <span class="fs-6 fw-bold text-white">Total</span>
                <span class="fs-6 fw-bolder text-white">Rp. {{ number_format($data->amount) }}</span>
            </div>
            <div class="d-flex flex-stack">
                <span class="fs-6 fw-bold text-white">Saldo Akhir</span>
                <span class="fs-6 fw-bolder text-white">Rp. {{ number_format($data->current_balance) }}</span>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach
<!--End List History Saldo-->
</div>

@push('scripts')
<script>
    const url = {
        'daftar_history_saldo_detail': "{{ route('online.historysaldo.shopee.daftar-detail') }}",
    }
    const data = {
        'start_date': "{{ $data_filter->start_date }}",
        'end_date': "{{ $data_filter->end_date }}",
    }
</script>
<script src="{{ asset('assets/js/suma/online/shopee/historysaldo/daftar-detail.js') }}?v={{ time() }}"></script>
@endpush
@endsection

