@extends('layouts.main.index')
@section('title','Tokopedia')
@section('subtitle','Serah Terima')
@section('container')
<div class="row g-0">
    <form action="#">
        @csrf
        <div class="card card-flush">
            <div class="card-header align-items-center border-0 mt-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Serah Terima Ekspedisi</span>
                    <span class="text-muted fw-bold fs-7">Form serah terima ekspedisi online</span>
                </h3>
                <div class="card-toolbar">
                    <img src="{{ asset('assets/images/logo/tokopedia_lg.png') }}" class="h-75px" />
                </div>
            </div>
            <div class="card-body">
                <div class="row g-5 mb-8">
                    <div class="col-sm-6">
                        <div class="fs-7 fw-bold text-gray-600 mb-1">Nomor Dokumen:</div>
                        <div class="fs-6 fw-bolder text-dark">{{ strtoupper(trim($data->nomor_dokumen)) }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="fs-7 fw-bold text-gray-600 mb-1">Tanggal:</div>
                        <div class="fs-6 fw-bolder text-dark">{{ date('d F Y', strtotime($data->tanggal)) }}</div>
                    </div>
                </div>
                <div class="row g-5 mb-8">
                    <div class="col-sm-6">
                        <div class="fs-7 fw-bold text-gray-600 mb-1">Ekspedisi:</div>
                        <div class="fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap">
                            <span class="pe-2">{{ trim($data->ekspedisi->nama) }}</span>
                            <span class="fs-7 text-danger d-flex align-items-center">
                                <span class="bullet bullet-dot bg-danger me-2"></span>{{ strtoupper(trim($data->ekspedisi->kode)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
