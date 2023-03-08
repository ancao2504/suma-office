@extends('layouts.main.index')
@section('title','Tokopedia')
@section('subtitle','Ekspedisi (Logistic)')
@section('container')
<div class="row g-0">
    <div class="card card-flush">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Daftar Ekspedisi (Logistic)</span>
                <span class="text-muted fw-bold fs-7">Daftar ekspedisi logistic marketplace Tokopedia</span>
            </h3>
            <div class="card-toolbar">
                <img src="{{ asset('assets/images/logo/tokopedia_lg.png') }}" class="h-75px" />
            </div>
        </div>
        <div class="card-body">
            <div class="text-center mb-17">
                <span class="fs-4 fw-boldest text-dark mb-5">- LOGISTIC AKTIF -</span>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 gy-10">
                @foreach ($data->aktif as $data_aktif)
                <div class="col text-center mb-9">
                    <div class="octagon mx-auto mb-2 d-flex w-150px h-150px bgi-no-repeat bgi-size-contain bgi-position-center"
                        style="background-image:url('{{ $data_aktif->ShipmentImage }}')">
                    </div>
                    <div class="mb-0">
                        <span class="text-dark fw-boldest text-hover-primary fs-3">{{ $data_aktif->ShipmentName }}</span>
                        <div class="text-muted fs-6 fw-bolder">Code : {{ $data_aktif->ShipmentCode }}</div>
                        <div class="text-primary fs-6 fw-boldest">ID : {{ $data_aktif->ShipmentID }}</div>
                        @if(empty($data_aktif->internal->tokopedia_id))
                        <button id="btnSimpanEkspedisi" name="btnSimpanEkspedisi" class="btn btn-sm btn-danger mt-6"
                            data-kode="{{ $data_aktif->ShipmentCode }}" data-nama="{{ $data_aktif->ShipmentName }}"
                            data-id="{{ $data_aktif->ShipmentID }}">Hubungkan</button>
                        @else
                        <button id="btnDetailEkspedisi" name="btnDetailEkspedisi" class="btn btn-sm btn-secondary mt-6"
                            data-kode="{{ $data_aktif->internal->kode }}" data-nama="{{ $data_aktif->internal->nama }}"
                            data-id="{{ $data_aktif->internal->tokopedia_id }}" data-images="{{ $data_aktif->ShipmentImage }}">Sudah Terhubung</button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <div class="separator my-10"></div>
            <div class="text-center mt-17 mb-17">
                <span class="fs-4 fw-boldest text-dark mb-5">- LIST LOGISTIC TOKOPEDIA -</span>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 gy-10">
                @foreach ($data->list as $data_list)
                <div class="col text-center mb-9">
                    <div class="octagon mx-auto mb-2 d-flex w-150px h-150px bgi-no-repeat bgi-size-contain bgi-position-center"
                        style="background-image:url('{{ $data_list->logo }}')"></div>
                    <div class="mb-0">
                        <span class="text-dark fw-boldest text-hover-primary fs-3">{{ $data_list->shipper_name }}</span>
                        <div class="text-muted fs-6 fw-bolder">Code : {{ $data_list->shipper_code }}</div>
                        <div class="text-primary fs-6 fw-boldest">ID : {{ $data_list->shipper_id }}</div>
                        @if(empty($data_list->internal->tokopedia_id))
                        <button id="btnSimpanEkspedisi" name="btnSimpanEkspedisi" class="btn btn-sm btn-danger mt-6"
                            data-kode="{{ $data_list->shipper_code }}" data-nama="{{ $data_list->shipper_name }}"
                            data-id="{{ $data_list->shipper_id }}">Hubungkan</button>
                        @else
                        <button id="btnDetailEkspedisi" name="btnDetailEkspedisi" class="btn btn-sm btn-secondary mt-6"
                            data-kode="{{ $data_list->internal->kode }}" data-nama="{{ $data_list->internal->nama }}"
                            data-id="{{ $data_list->internal->tokopedia_id }}" data-images="{{ $data_list->logo }}">Sudah Terhubung</button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-2" id="modalDetailEkspedisi">
    <div class="modal-dialog">
        <div class="modal-content" id="modalResultDetailEkspedisiContent">
            <div class="modal-header">
                <h5 id="modalTitle" name="modalTitle" class="modal-title">Data Internal</h5>
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
                <div id="modalImages"></div>
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle">
                        <thead class="border">
                            <tr class="fs-7 fw-bolder text-muted">
                                <th class="w-150px ps-3 pe-3 text-center">ID</th>
                                <th class="w-150px ps-3 pe-3 text-center">Kode</th>
                                <th class="w-150px ps-3 pe-3 text-center">Nama</th>
                            </tr>
                        </thead>
                        <tbody class="border">
                            <tr class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                <td id="modalID" class="fs-6 fw-bolder text-gray-800"></td>
                                <td id="modalKode" class="fs-6 fw-bolder text-gray-800"></td>
                                <td id="modalNama" class="fs-6 fw-bolder text-gray-800"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-end">
                    <button id="btnFilterClose" name="btnClose" type="button" class="btn btn-light text-end" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    const url = {
        'simpan_ekspedisi': "{{ route('online.ekspedisi.tokopedia.simpan') }}"
    }
</script>
<script src="{{ asset('assets/js/suma/online/tokopedia/ekspedisi/daftar.js') }}?v={{ time() }}"></script>
@endpush
@endsection
