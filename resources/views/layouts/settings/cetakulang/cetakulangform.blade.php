@extends('layouts.main.index')
@section('title','Setting')
@section('subtitle','Cetak Ulang')
@section('container')
    <div class="row g-0">
        <div class="card card-flush">
            <div class="card-header align-items-center border-0 mt-4 mb-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Cetak Ulang Faktur</span>
                    <span class="text-muted fw-boldest fs-7">Form cetak ulang faktur</span>
                </h3>
            </div>
            <div class="card-body">

            </div>
            <div class="card-footer">
                <div class="text-end">
                    <button id="btnBukaAkses" type="submit" class="btn btn-primary">Buka Akses</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')

    @endpush
@endsection
