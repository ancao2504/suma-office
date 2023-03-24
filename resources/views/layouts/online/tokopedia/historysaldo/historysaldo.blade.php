@extends('layouts.main.index')
@section('title','Tokopedia')
@section('subtitle','History Saldo')
@section('container')
<div class="row g-0">
    <div class="card card-flush mb-6">
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

        <div class="ms-10">
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder">
                <li class="nav-item mt-2">
                    <div id="navListDetail" class="nav-link text-active-primary ms-0 me-10 py-5 @if(strtoupper(trim($data_filter->list_view)) == 'DETAIL') active @endif" style="cursor: pointer;">Versi Detail</div>
                </li>
                <li class="nav-item mt-2">
                    <div id="navListGroupTotal" class="nav-link text-active-primary ms-0 me-10 py-5 @if(strtoupper(trim($data_filter->list_view)) == 'GROUP_TOTAL') active @endif" style="cursor: pointer;">Versi Group</div>
                </li>
            </ul>
        </div>
    </div>
</div>

@if(strtoupper(trim($data_filter->list_view)) == 'DETAIL')
@include('layouts.online.tokopedia.historysaldo.historysaldolistdetail')
@else
@include('layouts.online.tokopedia.historysaldo.historysaldolistgrouptotal')
@endif

@push('scripts')
<script>
    const url = {
        'daftar_history_saldo': "{{ route('online.historysaldo.tokopedia.daftar') }}",
    }
    const data = {
        'list_view': "{{ strtoupper(trim($data_filter->list_view)) }}",
    }
</script>
<script src="{{ asset('assets/js/suma/online/tokopedia/historysaldo/daftar.js') }}?v={{ time() }}"></script>
@endpush
@endsection

