@extends('layouts.main.index')
@section('title','Tokopedia')
@section('subtitle','Update Harga')
@section('container')
<div class="row g-0">
    <div class="card card-flush">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Daftar Update Harga</span>
                <span class="text-muted fw-bold fs-7">Form update harga tokopedia</span>
            </h3>
            <div class="card-toolbar">
                <button id="btnFilterMasterData" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFilter">
                    <i class="bi bi-funnel-fill fs-4 me-2"></i>Filter
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="fv-row">
                <div class="col-lg-6">

                </div>
            </div>
            <div class="fv-row mt-6">

            </div>
            <div class="fv-row mt-6">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle">
                        <thead class="border">
                            <tr class="fs-8 fw-bolder text-muted">
                                <th rowspan="2" class="w-50px ps-3 pe-3 text-center">No</th>
                                <th rowspan="2" class="w-100px ps-3 pe-3 text-center">No Dokumen</th>
                                <th rowspan="2" class="w-100px ps-3 pe-3 text-center">Tanggal</th>
                                <th rowspan="2" class="w-50px ps-3 pe-3 text-center">Status</th>
                                <th rowspan="2" class="w-100px ps-3 pe-3 text-center">Jml Item</th>
                                <th rowspan="2" class="w-100px ps-3 pe-3 text-center">Terupdate</th>
                                <th rowspan="2" class="w-100px ps-3 pe-3 text-center">Jml Selisih</th>
                                <th colspan="2" class="w-100px ps-3 pe-3 text-center">Action</th>
                            </tr>
                            <tr class="fs-8 fw-bolder text-muted">
                                <th class="w-50px ps-3 pe-3 text-center">Update</th>
                                <th class="w-50px ps-3 pe-3 text-center">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="border">
                            <tr>
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-gray-800">1</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-gray-800">202302070001</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-gray-800">07/02/2023</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:center;">
                                    <i class="fa fa-check text-success"></i>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-gray-800">25 Item</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-gray-800">25 Item</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-gray-800">600,000</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:center;">
                                    <a href="#" class="btn btn-icon btn-primary btn-sm">
                                        <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                                    </a>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:center;">
                                    <a href="#" class="btn btn-icon btn-danger btn-sm">
                                        <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-2" id="modalBuatDokumen">
    <div class="modal-dialog">
        <div class="modal-content" id="modalBuatDokumenContent">
            <div class="modal-header">
                <h5 id="modalTitle" name="modalTitle" class="modal-title">Buat Dokumen</h5>
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
                <div class="fv-row">
                    <label class="form-label">Kode:</label>
                    <div class="input-group">
                        <input id="inputKodeUpdateHarga" name="kode" type="text" placeholder="Pilih Kode Update Harga" readonly
                            class="form-control" style="cursor: pointer;">
                        <button id="btnFilterKodeUpdateHarga" name="btnFilterKodeUpdateHarga" class="btn btn-icon btn-primary" type="button" role="button">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button id="btnFilterReset" class="btn btn-danger" role="button">Reset Filter</button>
                <div class="text-end">
                    <button id="btnBuatDokumen" name="btnBuatDokumen" class="btn btn-primary" type="button" role="button">Buat Dokumen</button>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-2" id="modalFilter">
    <div class="modal-dialog">
        <div class="modal-content" id="modalFilterContent">
            <div class="modal-header">
                <h5 id="modalTitle" name="modalTitle" class="modal-title">Filter Faktur</h5>
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
                <div class="fv-row">
                    <label class="form-label required">Bulan:</label>
                    <select id="selectFilterMonth" name="month" class="form-select">
                        <option value="1" @if($data_filter->month == 1) {{"selected"}} @endif>Januari</option>
                        <option value="2" @if($data_filter->month == 2) {{"selected"}} @endif>Februari</option>
                        <option value="3" @if($data_filter->month == 3) {{"selected"}} @endif>Maret</option>
                        <option value="4" @if($data_filter->month == 4) {{"selected"}} @endif>April</option>
                        <option value="5" @if($data_filter->month == 5) {{"selected"}} @endif>Mei</option>
                        <option value="6" @if($data_filter->month == 6) {{"selected"}} @endif>Juni</option>
                        <option value="7" @if($data_filter->month == 7) {{"selected"}} @endif>Juli</option>
                        <option value="8" @if($data_filter->month == 8) {{"selected"}} @endif>Agustus</option>
                        <option value="9" @if($data_filter->month == 9) {{"selected"}} @endif>September</option>
                        <option value="10" @if($data_filter->month == 10) {{"selected"}} @endif>Oktober</option>
                        <option value="11" @if($data_filter->month == 11) {{"selected"}} @endif>November</option>
                        <option value="12" @if($data_filter->month == 12) {{"selected"}} @endif>Desember</option>
                    </select>
                </div>
                <div class="fv-row mt-8">
                    <label class="form-label required">Tahun:</label>
                    <input type="number" id="inputFilterYear" name="year" class="form-control" placeholder="Tahun"
                        @if(isset($data_filter->year)) value="{{ $data_filter->year }}" @else value="{{ old('year') }}"@endif>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button id="btnFilterReset" class="btn btn-danger" role="button">Reset Filter</button>
                <div class="text-end">
                    <button id="btnFilterProses" type="submit" class="btn btn-primary">Terapkan</button>
                    <button id="btnFilterClose" name="btnClose" type="button" class="btn btn-light text-end" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>


@include('layouts.option.optionupdateharga')

@push('scripts')
<script>
    const url = {
            'clossing_marketing': "{{ route('setting.default.clossing-marketing') }}",
    }
    const data_user = {
        'role_id': '{{ trim($data_user->role_id) }}'
    }
    const data_page = {
        'start_record': '{{ $data_page->from }}'
    }
</script>
<script src="{{ asset('assets/js/suma/online/tokopedia/updateharga/daftar.js') }}?v={{ time() }}"></script>
@endpush
@endsection
