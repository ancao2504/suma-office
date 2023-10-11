@extends('layouts.main.index')
@section('title','Tiktok')
@section('subtitle','Ekspedisi (Logistic)')
@section('container')
<div class="row g-0">
    <div class="card card-flush shadow">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Daftar Ekspedisi (Logistic)</span>
                <span class="text-muted fw-bold fs-7">Daftar ekspedisi logistic marketplace Tiktok</span>
            </h3>
            <div class="card-toolbar">
                <img src="{{ asset('assets/images/logo/tiktok_lg.png') }}" class="h-75px" />
            </div>
        </div>
        <div class="card-body">
            @foreach ($data->aktif as $data_aktif)
            <div class="text-center mb-17">
                <span class="fs-4 fw-boldest text-dark mb-5">{{ $data_aktif->delivery_option_name }}</span>
                <br>
                <span class="fs-6 fw-boldest text-gray-600 mb-5">({{ $data_aktif->delivery_option_id }})</span>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 gy-10">
                @foreach($data_aktif->shipping_provider_list as $shipping)
                <div class="col text-center mb-9">
                    <span class="svg-icon text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" class="h-100px w-100px">
                            <path d="M20 8H16C15.4 8 15 8.4 15 9V16H10V17C10 17.6 10.4 18 11 18H16C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18H21C21.6 18 22 17.6 22 17V13L20 8Z" fill="currentColor"/>
                            <path opacity="0.3" d="M20 18C20 19.1 19.1 20 18 20C16.9 20 16 19.1 16 18C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18ZM15 4C15 3.4 14.6 3 14 3H3C2.4 3 2 3.4 2 4V13C2 13.6 2.4 14 3 14H15V4ZM6 16C4.9 16 4 16.9 4 18C4 19.1 4.9 20 6 20C7.1 20 8 19.1 8 18C8 16.9 7.1 16 6 16Z" fill="currentColor"/>
                        </svg>
                    </span>
                    <div class="mb-0">
                        <span class="text-dark fw-boldest text-hover-primary fs-3">{{ $shipping->shipping_provider_name }}</span>
                        <div class="text-primary fs-7 fw-boldest">{{ $shipping->shipping_provider_id }}</div>
                        @if(empty($shipping->internal->marketplace_id))
                        <div class="text-danger fs-7 fw-boldest">Code : TIDAK TERHUBUNG</div>
                        @else
                        <div class="text-info fs-7 fw-boldest">Code: {{ $shipping->internal->kode_ekspedisi }}</div>
                        @endif
                        @if(empty($shipping->internal->marketplace_id))
                        <button id="btnDetailEkspedisi" name="btnDetailEkspedisi" class="btn btn-sm btn-danger mt-6"
                            data-tiktok_id="{{ $shipping->shipping_provider_id }}" data-keterangan="{{ $shipping->shipping_provider_name }}">Hubungkan
                        </button>
                        @else
                        <button id="btnDetailEkspedisi" name="btnDetailEkspedisi" class="btn btn-sm btn-secondary mt-6"
                            data-tiktok_id="{{ $shipping->shipping_provider_id }}" data-keterangan="{{ $shipping->shipping_provider_name }}"
                            data-id="{{ $shipping->internal->id }}" data-kode="{{ $shipping->internal->kode_ekspedisi }}">Terhubung
                        </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <div class="separator my-10"></div>
            @endforeach
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
                        <label for="inputTiktokID" class="form-label required">Kode Ekspedisi</label>
                        <input id="inputTiktokID" type="text" class="form-control form-control-solid" readonly/>
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
        'simpan_ekspedisi': "{{ route('online.ekspedisi.tiktok.simpan') }}"
    }
</script>
<script src="{{ asset('assets/js/suma/online/tiktok/ekspedisi/daftar.js') }}?v={{ time() }}"></script>
@endpush
@endsection
