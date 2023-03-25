@extends('layouts.main.index')
@section('title','Tokopedia')
@section('subtitle','History Saldo')
@section('container')
<div class="row g-0">
    <div class="card card-flush mb-6">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">History Saldo</span>
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
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableHistorySaldo" class="table table-row-dashed table-row-gray-300 align-middle">
                    <thead class="border">
                        <tr class="fs-8 fw-bolder text-muted bg-light">
                            <th class="w-50px ps-3 pe-3 text-center">Tanggal</th>
                            <th class="w-100px ps-3 pe-3 text-center">No Invoice</th>
                            <th class="w-50px ps-3 pe-3 text-center">Status</th>
                            <th class="min-w-100px ps-3 pe-3 text-center">Description</th>
                            <th class="w-100px ps-3 pe-3 text-center">Amount</th>
                            <th class="w-100px ps-3 pe-3 text-center">Saldo</th>
                            <th class="w-50px ps-3 pe-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="border">
                        <!--Start List History Saldo-->
                        @foreach($data_saldo as $data)
                        <tr id="postOrder">
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;
                                @if($data->transaction_type == 'WITHDRAWAL_COMPLETED' || $data->transaction_type == 'WITHDRAWAL_CREATED') background-color: #7239ea;@endif">
                                <span class="fs-7 fw-bolder d-block
                                    @if($data->transaction_type == 'WITHDRAWAL_COMPLETED' || $data->transaction_type == 'WITHDRAWAL_CREATED')
                                        text-white
                                    @else
                                        text-gray-800
                                    @endif">{{ date('d/m/Y', $data->create_time) }}</span>
                                <span class="fs-7 fw-bolder d-block
                                    @if($data->transaction_type == 'WITHDRAWAL_COMPLETED' || $data->transaction_type == 'WITHDRAWAL_CREATED')
                                        text-white
                                    @else
                                        text-gray-600
                                    @endif">{{ date('h:i:s', $data->create_time) }}</span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;
                                @if($data->transaction_type == 'WITHDRAWAL_COMPLETED' || $data->transaction_type == 'WITHDRAWAL_CREATED') background-color: #7239ea;@endif">
                                <span class="fs-7 fw-bolder d-block
                                    @if($data->transaction_type == 'WITHDRAWAL_COMPLETED' || $data->transaction_type == 'WITHDRAWAL_CREATED')
                                        text-white
                                    @else
                                        text-gray-800
                                    @endif">{{ trim($data->order_sn) }}</span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;
                                @if($data->transaction_type == 'WITHDRAWAL_COMPLETED' || $data->transaction_type == 'WITHDRAWAL_CREATED') background-color: #7239ea;@endif">
                                <div class="d-flex">
                                    @if($data->transaction_type == 'WITHDRAWAL_COMPLETED' || $data->transaction_type == 'WITHDRAWAL_CREATED')
                                    <span class="badge badge-white fs-8 fw-boldest">{{ strtoupper(trim($data->status)) }}</span>
                                    @else
                                        @if($data->status == 'COMPLETED')
                                        <span class="badge badge-light-success fs-8 fw-boldest">{{ strtoupper(trim($data->status)) }}</span>
                                        @else
                                        <span class="badge badge-light-danger fs-8 fw-boldest">{{ strtoupper(trim($data->status)) }}</span>
                                        @endif
                                    @endif

                                </div>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;
                                @if($data->transaction_type == 'WITHDRAWAL_COMPLETED' || $data->transaction_type == 'WITHDRAWAL_CREATED') background-color: #7239ea;@endif">
                                @if($data->transaction_type == 'WITHDRAWAL_COMPLETED' || $data->transaction_type == 'WITHDRAWAL_CREATED')
                                <span class="fs-7 fw-bolder d-block
                                    @if($data->transaction_type == 'WITHDRAWAL_COMPLETED' || $data->transaction_type == 'WITHDRAWAL_CREATED')
                                        text-white
                                    @else
                                        text-gray-800
                                    @endif">Penarikan Dana Bank</span>
                                @elseif($data->transaction_type == 'ADJUSTMENT_CENTER_ADD')
                                <span class="fs-7 fw-bolder d-block
                                    @if($data->transaction_type == 'WITHDRAWAL_COMPLETED' || $data->transaction_type == 'WITHDRAWAL_CREATED')
                                        text-white
                                    @else
                                        text-gray-800
                                    @endif">{{ trim($data->reason) }}</span>
                                @elseif($data->transaction_type == 'ESCROW_VERIFIED_ADD')
                                <span class="fs-7 fw-bolder d-block
                                    @if($data->transaction_type == 'WITHDRAWAL_COMPLETED' || $data->transaction_type == 'WITHDRAWAL_CREATED')
                                        text-white
                                    @else
                                        text-gray-800
                                    @endif">Penghasilan dari Pesanan</span>
                                @endif

                                @if($data->transaction_type == 'WITHDRAWAL_COMPLETED')
                                <span class="fs-8 fw-boldest text-white d-block">{{ strtoupper(trim($data->transaction_type)) }}</span>
                                @elseif($data->transaction_type == 'WITHDRAWAL_CREATED')
                                <span class="fs-8 fw-boldest text-white d-block">{{ strtoupper(trim($data->transaction_type)) }}</span>
                                @else
                                <span class="fs-8 fw-boldest d-block
                                    @if((double)$data->amount > 0)
                                        text-success
                                    @else
                                        text-danger
                                    @endif">{{ strtoupper(trim($data->transaction_type)) }}</span>
                                @endif
                            </td>
                            <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;
                                @if($data->transaction_type == 'WITHDRAWAL_COMPLETED' || $data->transaction_type == 'WITHDRAWAL_CREATED') background-color: #7239ea;@endif">
                                @if($data->transaction_type == 'WITHDRAWAL_COMPLETED' || $data->transaction_type == 'WITHDRAWAL_CREATED')
                                <span class="fs-7 fw-bolder text-white d-block">{{ number_format($data->amount) }}</span>
                                @else
                                <span class="fs-7 fw-boldest d-block
                                    @if((double)$data->amount > 0)
                                        text-success
                                    @else
                                        text-danger
                                    @endif">{{ number_format($data->amount) }}</span>
                                @endif
                            </td>
                            <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;
                                @if($data->transaction_type == 'WITHDRAWAL_COMPLETED' || $data->transaction_type == 'WITHDRAWAL_CREATED') background-color: #7239ea;@endif">
                                <span class="fs-7 fw-bolder d-block
                                    @if($data->transaction_type == 'WITHDRAWAL_COMPLETED' || $data->transaction_type == 'WITHDRAWAL_CREATED')
                                        text-white
                                    @else
                                        text-dark
                                    @endif">{{ number_format($data->current_balance) }}</span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;
                                @if($data->transaction_type == 'WITHDRAWAL_COMPLETED' || $data->transaction_type == 'WITHDRAWAL_CREATED') background-color: #7239ea;@endif">
                                @if($data->transaction_type == 'ESCROW_VERIFIED_ADD')
                                <button id="btnDetail" class="btn btn-icon btn-primary btn-sm" data-nomor_invoice="{{ strtoupper(trim($data->order_sn)) }}">
                                    <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        <!--End List History Saldo-->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-2" id="modalDetailSaldo">
    <div class="modal-dialog">
        <div class="modal-content" id="modalResultDetailSaldoContent">
            <form action="#">
                <div class="modal-header">
                    <h5 id="modalTitle" name="modalTitle" class="modal-title">Data Detail Saldo</h5>
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
                    <div id="textInfoInvoice"></div>
                    <div class="fv-row">
                        <label class="form-label">Nomor Invoice:</label>
                        <span id="textNomorInvoice" class="fs-7 fw-boldest text-dark d-block"></span>
                    </div>
                    <div class="fv-row mt-8">
                        <label class="form-label">Data Shopee:</label>
                        <div class="table-responsive">
                            <table class="table table-row-dashed table-row-gray-300 align-middle">
                                <thead>
                                    <tr>
                                        <tr class="fs-8 fw-bolder text-muted bg-light">
                                            <th class="w-200px ps-3 pe-3">Keterangan</th>
                                            <th class="w-100px ps-3 pe-3">Nominal</th>
                                        </tr>
                                    </tr>
                                </thead>
                                <tbody class="border">
                                    <tr>
                                        <td class="ps-3 pe-3 fs-7 fw-bolder" style="text-align:left;vertical-align:center;">Harga Produk</td>
                                        <td id="textHargaProduk" class="ps-3 pe-3 fs-7 fw-bolder" style="text-align:right;vertical-align:center;"></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-3 pe-3 fs-7 fw-bolder" style="text-align:left;vertical-align:center;">Biaya Administrasi</td>
                                        <td id="textBiayaAdministrasi" class="ps-3 pe-3 fs-7 fw-bolder text-danger" style="text-align:right;vertical-align:center;"></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-3 pe-3 fs-7 fw-bolder" style="text-align:left;vertical-align:center;">Biaya Layanan</td>
                                        <td id="textBiayaLayanan" class="ps-3 pe-3 fs-7 fw-bolder text-danger" style="text-align:right;vertical-align:center;"></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-3 pe-3 fs-7 fw-bolder" style="text-align:left;vertical-align:center;">Premi</td>
                                        <td id="textBiayaPremi" class="ps-3 pe-3 fs-7 fw-bolder text-danger" style="text-align:right;vertical-align:center;"></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-3 pe-3 fs-7 fw-bolder" style="text-align:left;vertical-align:center;">Total Penghasilan</td>
                                        <td id="textTotalPenghasilan" class="ps-3 pe-3 fs-7 fw-bolder text-gray-800" style="text-align:right;vertical-align:center;"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="fv-row mt-6">
                        <label class="form-label">Data Internal:</label>
                        <div class="table-responsive">
                            <table id="tableFakturInternal" class="table table-row-dashed table-row-gray-300 align-middle">
                                <thead>
                                    <tr class="fs-8 fw-bolder text-muted bg-light">
                                        <th class="w-200px ps-3 pe-3">Nomor Faktur</th>
                                        <th class="w-100px ps-3 pe-3">Nilai Faktur</th>
                                    </tr>
                                </thead>
                                <tbody class="border"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="text-end">
                        <button id="btnFilterClose" name="btnClose" type="button" class="btn btn-light text-end" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


@push('scripts')
<script>
    const url = {
        'daftar_history_saldo': "{{ route('online.historysaldo.shopee.daftar') }}",
        'detail_history_saldo': "{{ route('online.historysaldo.shopee.detail') }}",
    }
    const data = {
        'start_date': "{{ $data_filter->start_date }}",
        'end_date': "{{ $data_filter->end_date }}",
    }
</script>
<script src="{{ asset('assets/js/suma/online/shopee/historysaldo/daftar.js') }}?v={{ time() }}"></script>
@endpush
@endsection

