@extends('layouts.main.index')
@section('title','Tokopedia')
@section('subtitle','Update Harga')
@section('container')
<div class="row g-0">
    <form action="#">
        @csrf
        <div class="card card-flush">
            <div class="card-header align-items-center border-0 mt-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Update Harga</span>
                    <span class="text-muted fw-bold fs-7">Form update harga tokopedia</span>
                </h3>
            </div>
            <div class="card-body">
                <div class="row g-5 mb-8">
                    <div class="col-sm-6">
                        <div class="fw-bold fs-7 text-gray-600 mb-1">Nomor Dokumen:</div>
                        <div class="fw-bolder text-dark">{{ strtoupper(trim($data->nomor_dokumen)) }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="fw-bold fs-7 text-gray-600 mb-1">Tanggal:</div>
                        <div class="fw-bolder text-dark">{{ date('d F Y', strtotime($data->tanggal)) }}</div>
                    </div>
                </div>
                <div class="row">
                    <div id="tableDetailUpdateHarga"></div>
                </div>
                <div class="row g-5 mb-8">
                    <div class="d-flex">
                        <button id="btnUpdateStockAll" class="btn btn-primary" data-nomor_dokumen="{{ strtoupper(trim($data->nomor_dokumen)) }}">UPDATE HARGA MARKETPLACE</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@push('scripts')
<script>
    const url = {
        'daftar_update_harga': "{{ route('online.updateharga.tokopedia.form-detail') }}",
    }
    const data = {
        'nomor_dokumen': "{{ strtoupper(trim($data->nomor_dokumen)) }}",
    }
</script>
<script src="{{ asset('assets/js/suma/online/tokopedia/updateharga/form.js') }}?v={{ time() }}"></script>
@endpush
@endsection
