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
                        <div class="fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap">
                            <span class="pe-2">{{ trim($data->rit) }}</span>
                            <span class="fs-7 text-info d-flex align-items-center">
                                <span class="bullet bullet-dot bg-info me-2"></span>RIT
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="fs-7 fw-bold text-gray-600 mb-1">Keterangan:</div>
                        <div class="fs-6 fw-bolder text-dark">@if(strtoupper(trim($data->keterangan)) == '')-@else {{ strtoupper(trim($data->keterangan)) }} @endif</div>
                    </div>
                </div>
                <div class="row g-5 mb-8">
                    <div class="col-sm-6">
                        <div class="fs-7 fw-bold text-gray-600 mb-1">Mulai:</div>
                        <div class="fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap">
                            <span class="pe-2">{{ date('d F Y', strtotime($data->mulai->tanggal)) }}</span>
                            <span class="fs-7 text-danger d-flex align-items-center">
                                <span class="bullet bullet-dot bg-danger me-2"></span>{{ trim($data->mulai->jam) }}
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="fs-7 fw-bold text-gray-600 mb-1">Selesai:</div>
                        <div class="fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap">
                            <span class="pe-2">{{ date('d F Y', strtotime($data->selesai->tanggal)) }}</span>
                            <span class="fs-7 text-danger d-flex align-items-center">
                                <span class="bullet bullet-dot bg-danger me-2"></span>{{ trim($data->selesai->jam) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row g-5 mb-8">
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-300 align-middle">
                            <thead class="border">
                                <tr class="fs-8 fw-bolder text-muted">
                                    <th rowspan="2" class="w-50px ps-3 pe-3 text-center">No</th>
                                    <th rowspan="2" class="w-100px ps-3 pe-3 text-center">Nomor SJ</th>
                                    <th rowspan="2" class="w-100px ps-3 pe-3 text-center">Nomor Faktur</th>
                                    <th rowspan="2" class="w-200px ps-3 pe-3 text-center">Nomor Invoice</th>
                                    <th rowspan="2" class="w-100px ps-3 pe-3 text-center">Jml Koli</th>
                                    <th colspan="2" class="w-100px ps-3 pe-3 text-center">Action</th>
                                </tr>
                                <tr class="fs-8 fw-bolder text-muted">
                                    <th class="w-50px ps-3 pe-3 text-center">Marketplace</th>
                                    <th class="w-50px ps-3 pe-3 text-center">Internal</th>
                                </tr>
                            </thead>
                            <tbody class="border">
                                @foreach($data->detail as $data_detail)
                                <tr>
                                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                        <span class="fs-7 fw-bolder text-gray-800">{{ $loop->iteration }}</span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                        <span class="fs-7 fw-bolder text-gray-800">{{ $data_detail->nomor_sj }}</span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                        <span class="fs-7 fw-bolder text-gray-800">{{ $data_detail->nomor_faktur }}</span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                        <span class="fs-7 fw-bolder text-gray-800">{{ $data_detail->nomor_invoice }}</span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                        <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data_detail->jumlah_koli) }}</span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                        <button id="btnUpdateStatusPartNumber" class="btn btn-icon btn-sm btn-danger" type="button" data-nomor_dokumen="PL12527/III/23" data-part_number="06141KWB505">
                                            <i class="fa fa-database" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                        <button id="btnUpdateStatusPartNumber" class="btn btn-icon btn-sm btn-danger" type="button"
                                            data-nomor_dokumen="{{ strtoupper(trim($data->nomor_dokumen)) }}" data-no_faktur="{{ strtoupper(trim($data->nomor_dokumen)) }}">
                                            <i class="fa fa-database" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
