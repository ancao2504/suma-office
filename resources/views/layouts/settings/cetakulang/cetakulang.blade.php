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
                <div class="card-toolbar">
                    <button id="btnTambah" class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#modalEntryCetakUlang">
                        <span class="svg-icon svg-icon-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M11 13H7C6.4 13 6 12.6 6 12C6 11.4 6.4 11 7 11H11V13ZM17 11H13V13H17C17.6 13 18 12.6 18 12C18 11.4 17.6 11 17 11Z" fill="currentColor"/>
                                <path d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM17 11H13V7C13 6.4 12.6 6 12 6C11.4 6 11 6.4 11 7V11H7C6.4 11 6 11.4 6 12C6 12.6 6.4 13 7 13H11V17C11 17.6 11.4 18 12 18C12.6 18 13 17.6 13 17V13H17C17.6 13 18 12.6 18 12C18 11.4 17.6 11 17 11Z" fill="currentColor"/>
                            </svg>
                        </span>Tambah
                    </button>
                </div>
            </div>
            @if(strtoupper(trim($device)) == 'DESKTOP')
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle">
                        <thead class="border">
                            <tr class="fw-bolder text-muted">
                                <th class="w-75px ps-3 pe-3 text-center">Divisi</th>
                                <th class="w-75px ps-3 pe-3 text-center">Tanggal</th>
                                <th class="w-100px ps-3 pe-3 text-center">No Faktur</th>
                                <th class="w-100px ps-3 pe-3 text-center">Cabang</th>
                                <th class="w-150px ps-3 pe-3 text-center">Jenis</th>
                                <th class="w-50px ps-3 pe-3 text-center">Jumlah</th>
                                <th class="min-w-150px ps-3 pe-3 text-center">Alasan</th>
                                <th class="min-w-100px ps-3 pe-3 text-center">Usertime</th>
                            </tr>
                        </thead>
                        <tbody class="border">
                            @forelse($data_cetak_ulang->data as $data)
                            <tr class="fs-6 fw-bold text-gray-700">
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                    @if(strtoupper(trim($data->divisi)) == 'HONDA')
                                    <span class="badge badge-light-danger fs-8 fw-boldest text-uppercase">Honda</span>
                                    @else
                                    <span class="badge badge-light-primary fs-8 fw-boldest text-uppercase">FDR</span>
                                    @endif
                                </td>
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                    <div class="row">
                                        <span class="fs-7 fw-bolder text-gray-800">{{ date('d/m/Y', strtotime($data->tanggal)) }}</span>
                                    </div>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                    <div class="row">
                                        <span class="fs-7 fw-bolder text-gray-800">{{ trim($data->no_faktur) }}</span>
                                    </div>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                    <span class="badge badge-light-info fs-8 fw-boldest text-uppercase">{{ trim($data->cabang) }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                    @if(strtoupper(trim($data->jenis)) == 'FAKTUR')
                                    <span class="text-primary fs-8 fw-boldest text-uppercase">{{ trim($data->jenis) }}</span>
                                    @elseif(strtoupper(trim($data->jenis)) == 'PEMINDAHAN KELUAR')
                                    <span class="text-danger fs-8 fw-boldest text-uppercase">{{ trim($data->jenis) }}</span>
                                    @elseif(strtoupper(trim($data->jenis)) == 'PURCHASE ORDER HOTLINE')
                                    <span class="text-success fs-8 fw-boldest text-uppercase">{{ trim($data->jenis) }}</span>
                                    @endif
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data->jml_edit) }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                    <p class="fs-7 fw-bolder text-gray-800">{{ $data->alasan }}</p>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                    <p class="fs-7 fw-bolder text-gray-800">{{ $data->usertime }}</p>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="fs-7 fw-bolder text-gray-500 text-center pt-10 pb-10">- TIDAK ADA DATA YANG DITAMPILKAN -</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="row mt-4">
                    <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                        <div class="dataTables_length">
                            <label>
                                <select id="selectPerPage" name="per_page" class="form-select form-select-sm">
                                    <option value="10" @if($data_page->per_page == 10) selected @endif>10</option>
                                    <option value="25" @if($data_page->per_page == 25) selected @endif>25</option>
                                    <option value="50" @if($data_page->per_page == 50) selected @endif>50</option>
                                    <option value="100" @if($data_page->per_page == 100) selected @endif>100</option>
                                </select>
                            </label>
                        </div>
                        <div class="dataTables_info" role="status" aria-live="polite">Showing {{ $data_page->from }} to {{ $data_page->to }} of {{ $data_page->total }} records</div>
                    </div>
                    <div class="col-sm-12 col-md-7 d-flex align-rooms-center justify-content-center justify-content-md-end">
                        <div class="dataTables_paginate paging_simple_numbers">
                            <ul class="pagination">
                                @foreach ($data_cetak_ulang->links as $link)
                                <li class="page-item @if($link->active == true) active @endif
                                    @if($link->url == '') disabled @endif
                                    @if($data_page->current_page == $link->label) active @endif">
                                    @if($link->active == true)
                                    <span class="page-link">{{ $link->label }}</span>
                                    @else
                                    <a href="#" class="page-link" data-page="{{ $link->url }}">
                                        @if(Str::contains(strtolower($link->label), 'previous'))
                                        <i class="previous"></i>
                                        @elseif(Str::contains(strtolower($link->label), 'next'))
                                        <i class="next"></i>
                                        @else
                                        {{ $link->label }}
                                        @endif
                                    </a>
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @if(strtoupper(trim($device)) == 'MOBILE')
    @forelse($data_cetak_ulang->data as $data)
    <div class="card card-flush mt-4">
        <div class="card-body">
            <div class="row">
                <div class="align-items-center">
                    <span class="fs-6 fw-boldest text-gray-800">{{ trim($data->no_faktur) }}</span>
                    @if(strtoupper(trim($data->divisi)) == 'HONDA')
                    <span class="badge badge-light-danger fs-8 fw-boldest text-uppercase ms-2">Honda</span>
                    @else
                    <span class="badge badge-light-primary fs-8 fw-boldest text-uppercase ms-2">FDR</span>
                    @endif
                </div>
                <span class="fs-7 fw-bolder text-gray-800 mt-2">{{ date('d/m/Y', strtotime($data->tanggal)) }}</span>
                <div class="d-flex align-items-center mt-2">
                    <span class="badge badge-light-info fs-8 fw-boldest text-uppercase">{{ trim($data->cabang) }}</span>
                    @if(strtoupper(trim($data->jenis)) == 'FAKTUR')
                    <span class="badge badge-light-primary fs-8 fw-boldest text-uppercase ms-2">{{ trim($data->jenis) }}</span>
                    @elseif(strtoupper(trim($data->jenis)) == 'PEMINDAHAN KELUAR')
                    <span class="badge badge-light-danger fs-8 fw-boldest text-uppercase ms-2">{{ trim($data->jenis) }}</span>
                    @elseif(strtoupper(trim($data->jenis)) == 'PURCHASE ORDER HOTLINE')
                    <span class="badge badge-light-success fs-8 fw-boldest text-uppercase ms-2">{{ trim($data->jenis) }}</span>
                    @endif
                </div>
                <span class="fs-7 fw-bolder text-gray-600 mt-4">Jumlah Edit : </span>
                <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data->jml_edit) }}</span>
                <span class="fs-7 fw-bolder text-gray-600 mt-4">Alasan : </span>
                <p class="fs-7 fw-bolder text-gray-800">{{ $data->alasan }}</p>
                <span class="fs-7 fw-bolder text-gray-600 mt-4">Usertime : </span>
                <p class="fs-7 fw-bolder text-gray-800">{{ $data->usertime }}</p>
            </div>
        </div>
    </div>
    @endforeach
    <div class="row mt-4">
        <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
            <div class="dataTables_length">
                <label>
                    <select id="selectPerPage" name="per_page" class="form-select form-select-sm">
                        <option value="10" @if($data_page->per_page == 10) selected @endif>10</option>
                        <option value="25" @if($data_page->per_page == 25) selected @endif>25</option>
                        <option value="50" @if($data_page->per_page == 50) selected @endif>50</option>
                        <option value="100" @if($data_page->per_page == 100) selected @endif>100</option>
                    </select>
                </label>
            </div>
            <div class="dataTables_info" role="status" aria-live="polite">Showing {{ $data_page->from }} to {{ $data_page->to }} of {{ $data_page->total }} records</div>
        </div>
    </div>
    <div class="row mt-8">
        <div class="col-sm-12 col-md-7 d-flex align-rooms-center justify-content-center justify-content-md-end">
            <div class="dataTables_paginate paging_simple_numbers">
                <ul class="pagination">
                    @foreach ($data_cetak_ulang->links as $link)
                    <li class="page-item @if($link->active == true) active @endif
                        @if($link->url == '') disabled @endif
                        @if($data_page->current_page == $link->label) active @endif">
                        @if($link->active == true)
                        <span class="page-link">{{ $link->label }}</span>
                        @else
                        <a href="#" class="page-link" data-page="{{ $link->url }}">
                            @if(Str::contains(strtolower($link->label), 'previous'))
                            <i class="previous"></i>
                            @elseif(Str::contains(strtolower($link->label), 'next'))
                            <i class="next"></i>
                            @else
                            {{ $link->label }}
                            @endif
                        </a>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <div id="modalEntryCetakUlang" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div id="modalContentCetakUlang" class="modal-content">
                <form id="formEntryCetakUlang" name="formEntryCetakUlang" autofill="off" autocomplete="off" method="post" action="{{ route('setting.cetakulang.simpan') }}">
                    @csrf
                    <div class="modal-header">
                        <h3 id="modalTitleCetakUlang" class="modal-title">Entry Data Cetak Ulang</h3>
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
                        @include('components.alertfailed')

                        <div class="fv-row">
                            <label class="form-label required">Divisi:</label>
                            <select id="selectDivisi" name="divisi" class="form-select">
                                <option value="HONDA">HONDA</option>
                                <option value="FDR">FDR</option>
                            </select>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label required">Transaksi:</label>
                            <select id="selectTransaksi" name="transaksi" class="form-select">
                                <option value="FAKTUR">FAKTUR</option>
                                <option value="PEMINDAHAN">PEMINDAHAN BARANG KELUAR</option>
                                <option value="MEMOKOREKSI">MEMO KOREKSI</option>
                                <option value="HOTLINE">HOTLINE PC</option>
                            </select>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label required">Nomor Dokumen:</label>
                            <input id="inputNomorDokumen" name="nomor_dokumen" type="text" class="form-control" placeholder="Input Nomor Dokumen"
                                oninput="this.value = this.value.toUpperCase()" required
                                value="@if(isset($nomor_faktur)){{ trim($nomor_faktur) }}@else{{ old('nomor_faktur') }}@endif">
                        </div>
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-lg-6 mt-8">
                                    <label class="form-label required">Kode Cabang:</label>
                                    <input id="inputKodeCabang" name="kode_cabang" type="text" class="form-control form-control-solid" placeholder="Data Kode Cabang"
                                        oninput="this.value = this.value.toUpperCase()" required readonly
                                        value="@if(isset($kode_cabang)){{ trim($kode_cabang) }}@else{{ old('kode_cabang') }}@endif">
                                </div>
                                <div class="col-lg-6 mt-8">
                                    <label class="form-label required">Company Cabang:</label>
                                    <input id="inputCompanyCabang" name="company_cabang" type="text" class="form-control form-control-solid" placeholder="Data Company Cabang"
                                        oninput="this.value = this.value.toUpperCase()" required readonly
                                        value="@if(isset($company_cabang)){{ trim($company_cabang) }}@else{{ old('company_cabang') }}@endif">
                                </div>
                            </div>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label required">Keterangan:</label>
                            <input id="inputKeterangan" name="keterangan" type="text" class="form-control form-control-solid" placeholder="Keterangan" required
                                value="@if(isset($keterangan)){{ trim($keterangan) }}@else{{ old('keterangan') }}@endif" readonly>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label required">Alasan:</label>
                            <input id="inputAlasan" name="alasan" type="text" class="form-control" placeholder="Alasan" required
                                value="@if(isset($alasan)){{ trim($alasan) }}@else{{ old('alasan') }}@endif">
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label required">Status Market Place:</label>
                            <select id="selectStatusApprove" name="status_approve" class="form-select">
                                <option value="1">APPROVE</option>
                                <option value="0">BATAL APPROVE</option>
                            </select>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label required">Status Edit PC:</label>
                            <select id="selectStatusEdit" name="status_edit" class="form-select">
                                <option value="1">EDIT FAKTUR</option>
                                <option value="0">CETAK ULANG</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="btnSimpan" name="btnSimpan" type="submit" class="btn btn-primary">
                            <span class="svg-icon svg-icon-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M10.3 14.3L11 13.6L7.70002 10.3C7.30002 9.9 6.7 9.9 6.3 10.3C5.9 10.7 5.9 11.3 6.3 11.7L10.3 15.7C9.9 15.3 9.9 14.7 10.3 14.3Z" fill="currentColor"/>
                                    <path d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM11.7 15.7L17.7 9.70001C18.1 9.30001 18.1 8.69999 17.7 8.29999C17.3 7.89999 16.7 7.89999 16.3 8.29999L11 13.6L7.70001 10.3C7.30001 9.89999 6.69999 9.89999 6.29999 10.3C5.89999 10.7 5.89999 11.3 6.29999 11.7L10.3 15.7C10.5 15.9 10.8 16 11 16C11.2 16 11.5 15.9 11.7 15.7Z" fill="currentColor"/>
                                </svg>
                            </span>Simpan
                        </button>
                        <button id="btnClose" name="btnClose" type="button" class="btn btn-light btn-active-light-primary" data-bs-dismiss="modal">
                            <span class="svg-icon svg-icon-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="4" y="11" width="12" height="2" rx="1" fill="currentColor"/>
                                    <path d="M5.86875 11.6927L7.62435 10.2297C8.09457 9.83785 8.12683 9.12683 7.69401 8.69401C7.3043 8.3043 6.67836 8.28591 6.26643 8.65206L3.34084 11.2526C2.89332 11.6504 2.89332 12.3496 3.34084 12.7474L6.26643 15.3479C6.67836 15.7141 7.3043 15.6957 7.69401 15.306C8.12683 14.8732 8.09458 14.1621 7.62435 13.7703L5.86875 12.3073C5.67684 12.1474 5.67684 11.8526 5.86875 11.6927Z" fill="currentColor"/>
                                    <path d="M8 5V6C8 6.55228 8.44772 7 9 7C9.55228 7 10 6.55228 10 6C10 5.44772 10.4477 5 11 5H18C18.5523 5 19 5.44772 19 6V18C19 18.5523 18.5523 19 18 19H11C10.4477 19 10 18.5523 10 18C10 17.4477 9.55228 17 9 17C8.44772 17 8 17.4477 8 18V19C8 20.1046 8.89543 21 10 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H10C8.89543 3 8 3.89543 8 5Z" fill="#C4C4C4"/>
                                </svg>
                            </span>Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script type="text/javascript">
        const data = {
            'start_record': "{{ $data_page->from }}",
            'year': "{{ $data_filter->year }}",
            'month': "{{ $data_filter->month }}",
        }
        const url = {
            'data_cetak_ulang': "{{ route('setting.cetakulang.daftar') }}",
            'cek_dokumen': "{{ route('setting.cetakulang.cek-dokumen') }}",
        }
    </script>
    <script src="{{ asset('assets/js/suma/settings/cetakulang/cetakulang.js') }}?v={{ time() }}"></script>
    @endpush
@endsection
