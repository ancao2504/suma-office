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
                    <div class="mx-auto mb-2 d-flex w-150px h-150px bgi-no-repeat bgi-size-contain bgi-position-center"
                        style="background-image:url('{{ $data_aktif->ShipmentImage }}')">
                    </div>
                    <div class="mb-0">
                        <span class="text-dark fw-boldest text-hover-primary fs-3">{{ $data_aktif->ShipmentName }}</span>
                        <div class="text-muted fs-7 fw-bolder">Tokopedia Code : {{ $data_aktif->ShipmentCode }}</div>
                        <div class="text-primary fs-7 fw-boldest">Tokopedia ID : {{ $data_aktif->ShipmentID }}</div>
                        @if(empty($data_aktif->internal->marketplace_id))
                        <button id="btnDetailEkspedisi" name="btnDetailEkspedisi" class="btn btn-sm btn-danger mt-6"
                            data-tokopedia_id="{{ $data_aktif->ShipmentID }}" data-keterangan="{{ $data_aktif->ShipmentName }}">Hubungkan
                        </button>
                        @else
                        <button id="btnDetailEkspedisi" name="btnDetailEkspedisi" class="btn btn-sm btn-secondary mt-6"
                            data-tokopedia_id="{{ $data_aktif->ShipmentID }}" data-keterangan="{{ $data_aktif->ShipmentName }}"
                            data-id="{{ $data_aktif->internal->id }}" data-kode="{{ $data_aktif->internal->kode_ekspedisi }}">Terhubung
                        </button>
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
                    <div class="mx-auto mb-2 d-flex w-150px h-150px bgi-no-repeat bgi-size-contain bgi-position-center"
                        style="background-image:url('{{ $data_list->logo }}')"></div>
                    <div class="mb-0">
                        <span class="text-dark fw-boldest text-hover-primary fs-3">{{ $data_list->shipper_name }}</span>
                        <div class="text-muted fs-7 fw-bolder">Tokopedia Code : {{ $data_list->shipper_code }}</div>
                        <div class="text-primary fs-7 fw-boldest">Tokopedia ID : {{ $data_list->shipper_id }}</div>
                        @if(empty($data_list->internal->marketplace_id))
                        <button id="btnDetailEkspedisi" name="btnDetailEkspedisi" class="btn btn-sm btn-danger mt-6"
                            data-tokopedia_id="{{ $data_list->shipper_id }}" data-keterangan="{{ $data_list->shipper_name }}">Hubungkan
                        </button>
                        @else
                        <button id="btnDetailEkspedisi" name="btnDetailEkspedisi" class="btn btn-sm btn-secondary mt-6"
                            data-tokopedia_id="{{ $data_list->shipper_id }}" data-keterangan="{{ $data_list->shipper_name }}"
                            data-id="{{ $data_list->internal->id }}" data-kode="{{ $data_list->internal->kode_ekspedisi }}">Terhubung
                        </button>
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
            <form action="#">
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
                    @csrf
                    <div class="fv-row">
                        <label for="selectKodeEkspedisi" class="form-label required">Kode Ekspedisi</label>
                        <select id="selectKodeEkspedisi" name="selectKodeEkspedisi" class="form-select" aria-label="Select example">
                            <option value=""></option>
                            @foreach ($option_ekspedisi as $item)
                            <option value="{{ strtoupper(trim($item->kode_ekspedisi)) }}">{{ strtoupper(trim($item->nama_ekspedisi)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fv-row mt-8">
                        <label for="inputKeterangan" class="form-label required">Kode Ekspedisi</label>
                        <input id="inputKeterangan" type="text" class="form-control form-control-solid" readonly/>
                    </div>
                    <div class="fv-row mt-8">
                        <label for="inputTokopediaID" class="form-label required">Kode Ekspedisi</label>
                        <input id="inputTokopediaID" type="text" class="form-control form-control-solid" readonly/>
                    </div>
                    <div class="fv-row mt-8">
                        <label for="inputIDInternal" class="form-label required">ID Internal</label>
                        <input id="inputIDInternal" type="text" class="form-control form-control-solid" readonly/>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="text-end">
                        <button id="btnSimpanEkspedisi" name="btnSimpanEkspedisi" type="button" class="btn btn-primary text-end">Simpan</button>
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
        'simpan_ekspedisi': "{{ route('online.ekspedisi.tokopedia.simpan') }}"
    }
</script>
<script src="{{ asset('assets/js/suma/online/tokopedia/ekspedisi/daftar.js') }}?v={{ time() }}"></script>
@endpush
@endsection
