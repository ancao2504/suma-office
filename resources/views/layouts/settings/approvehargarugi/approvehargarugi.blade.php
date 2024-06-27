@extends('layouts.main.index')
@section('title','settings')
@section('subtitle','Approve Harga Rugi')
@section('container')
<div class="row g-0">
    <div class="card card-flush shadow">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Approve Harga Rugi</span>
                <span class="text-muted fw-bold fs-7">Cari data Faktur yang akan di approve</span>
            </h3>
            <div class="card-toolbar">
                <img src="{{ asset('assets/images/logo/shopee_lg.png') }}" class="h-75px" />
                <img src="{{ asset('assets/images/logo/tokopedia_lg.png') }}" class="h-70px" />
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
                <input id="inputNoFaktur" name="no_faktur" type="text" class="form-control ps-14 me-2" placeholder="Cari Data Faktur"
                    value="{{ $input }}">
                <button id="btnFilterMasterData" class="btn btn-icon btn-primary" type="button" onclick="search()">
                    <i class="fa fa-search"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@include('layouts.settings.approvehargarugi.approveharagarugilist')
@push('scripts')
<script>
    function search() {
        let inputNoFaktur = $('#inputNoFaktur').val();
        window.location.href = "{{ route('setting.approvehargarugi.approve-harga-rugi') }}?nomor_faktur=" + inputNoFaktur
    }
</script>
@endpush
@endsection
