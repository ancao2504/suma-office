@extends('layouts.main.index')
@section('title','Tokopedia')
@section('subtitle','History Saldo')
@section('container')
<div class="row g-0">
    <div class="card card-flush">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">History Saldo</span>
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
        </div>
    </div>
</div>

<div class="row g-0">
    <div class="card card-flush">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableHistorySaldo" class="table table-row-dashed table-row-gray-300 align-middle">
                    <thead class="border">
                        <tr class="fs-8 fw-bolder text-muted">
                            <th class="w-100px ps-3 pe-3 text-center">Tanggal</th>
                            <th class="w-200px ps-3 pe-3 text-center">Description</th>
                            <th class="min-w-100px ps-3 pe-3 text-center">Keterangan</th>
                            <th class="w-100px ps-3 pe-3 text-center">Amount</th>
                            <th class="w-100px ps-3 pe-3 text-center">Saldo</th>
                            <th class="w-100px ps-3 pe-3 text-center">Faktur</th>
                            <th class="w-100px ps-3 pe-3 text-center">No Faktur</th>
                        </tr>
                    </thead>
                    <tbody class="border">
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

                            <tr id="postOrder">
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-gray-800 d-block">{{ date('d/m/Y', strtotime($data->create_time)) }}</span>
                                    <span class="fs-8 fw-bolder text-gray-600">{{ date('h:i:s', strtotime($data->create_time)) }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-gray-800 d-block">{{ trim($data->type_description) }}</span>
                                    @if((int)$data->type == 1001)
                                    <div class="fs-8 fw-boldest text-success d-flex align-items-center flex-wrap">
                                        <span class="pe-2">{{ trim($data->class) }}</span>
                                        <span class="fs-7 text-success d-flex align-items-center">
                                            <span class="bullet bullet-dot bg-success me-2"></span>{{ trim($data->type) }}
                                        </span>
                                    </div>
                                    @elseif((int)$data->type == 7001)
                                    <div class="fs-8 fw-boldest text-info d-flex align-items-center flex-wrap">
                                        <span class="pe-2">{{ trim($data->class) }}</span>
                                        <span class="fs-7 text-info d-flex align-items-center">
                                            <span class="bullet bullet-dot bg-info me-2"></span>{{ trim($data->type) }}
                                        </span>
                                    </div>
                                    @else
                                    <div class="fs-8 fw-boldest text-danger d-flex align-items-center flex-wrap">
                                        <span class="pe-2">{{ trim($data->class) }}</span>
                                        <span class="fs-7 text-danger d-flex align-items-center">
                                            <span class="bullet bullet-dot bg-danger me-2"></span>{{ trim($data->type) }}
                                        </span>
                                    </div>
                                    @endif
                                </td>
                                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-gray-800 d-block">{{ trim($data->note) }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                    @if((int)$data->type == 1001)
                                    <span class="fs-7 fw-boldest text-success d-block">{{ number_format($data->amount) }}</span>
                                    @elseif((int)$data->type == 7001)
                                    <span class="fs-7 fw-boldest text-info d-block">{{ number_format($data->amount) }}</span>
                                    @else
                                    <span class="fs-7 fw-boldest text-danger d-block">{{ number_format($data->amount) }}</span>
                                    @endif
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-dark d-block">{{ number_format($data->saldo) }}</span>
                                </td>
                                @if((int)$data->type == 1001)
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                    @if((double)$total_faktur != (double)$data->amount)
                                    <span class="fs-7 fw-boldest text-danger d-block">{{ number_format($total_faktur) }}</span>
                                    @else
                                    <span class="fs-7 fw-bolder text-dark d-block">{{ number_format($total_faktur) }}</span>
                                    @endif
                                </td>
                                @elseif((int)$data->type == 7001)
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;background-color: #d9214e ;"></td>
                                @else
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;background-color: #009ef7;"></td>
                                @endif

                                @if((int)$data->type == 1001)
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                    @if((double)$total_faktur != (double)$data->amount)
                                        @foreach($data->faktur as $data_faktur)
                                        <span class="fs-7 fw-boldest text-danger d-block">{{ strtoupper(trim($data_faktur->nomor_faktur)) }}</span>
                                        @endforeach
                                    @else
                                        @foreach($data->faktur as $data_faktur)
                                        <span class="fs-7 fw-bolder text-gray-800 d-block">{{ strtoupper(trim($data_faktur->nomor_faktur)) }}</span>
                                        @endforeach
                                    @endif
                                </td>
                                @elseif((int)$data->type == 7001)
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;background-color: #d9214e ;"></td>
                                @else
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;background-color: #009ef7;"></td>
                                @endif
                            </tr>
                        @endforeach
                        <!--End List History Saldo-->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
    const url = {
        'daftar_history_saldo': "{{ route('online.historysaldo.tokopedia.daftar') }}",
    }
</script>
<script src="{{ asset('assets/js/suma/online/tokopedia/historysaldo/daftar.js') }}?v={{ time() }}"></script>
@endpush
@endsection

