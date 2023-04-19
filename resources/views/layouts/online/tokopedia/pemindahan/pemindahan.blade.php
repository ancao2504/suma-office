@extends('layouts.main.index')
@section('title','Tokopedia')
@section('subtitle','Pemindahan')
@section('container')
<div class="row g-0">
    <div class="card card-flush shadow">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Pemindahan Antar Lokasi</span>
                <span class="text-muted fw-bold fs-7">Daftar pemindahan antar lokasi tokopedia</span>
                <span class="text-muted fw-bold fs-7 mt-2">Periode
                    <span class="text-dark fw-bolder fs-7">{{ date('d F Y', strtotime($data_filter->start_date)) }}</span> s/d
                    <span class="text-dark fw-bolder fs-7">{{ date('d F Y', strtotime($data_filter->end_date))  }}</span>
                </span>
            </h3>
            <div class="card-toolbar">
                <img src="{{ asset('assets/images/logo/tokopedia_lg.png') }}" class="h-75px" />
            </div>
        </div>
        <div class="card-header align-items-center border-0">
            <div class="align-items-start flex-column">
                <div class="input-group">
                    <input id="inputStartDate" name="start_date" class="form-control" placeholder="Dari Tanggal" value="{{ $data_filter->start_date }}">
                    <span class="input-group-text">s/d</span>
                    <input id="inputEndDate" name="end_date" class="form-control" placeholder="Sampai Dengan" value="{{ $data_filter->end_date }}">
                    <button id="btnFilterMasterData" class="btn btn-icon btn-primary" type="button">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="position-relative w-md-200px me-md-2">
                    <span class="svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                        </svg>
                    </span>
                    <input id="inputSearch" name="search" type="text" class="form-control ps-10" value="{{ $data_filter->search }}"
                        oninput="this.value = this.value.toUpperCase()" placeholder="Search">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle">
                    <thead class="border">
                        <tr class="fs-8 fw-bolder text-muted">
                            <th rowspan="2" class="w-50px ps-3 pe-3 text-center">No</th>
                            <th rowspan="2" class="w-150px ps-3 pe-3 text-center">No Dokumen</th>
                            <th rowspan="2" class="w-50px ps-3 pe-3 text-center">Awal</th>
                            <th rowspan="2" class="w-50px ps-3 pe-3 text-center">Tujuan</th>
                            <th rowspan="2" class="min-w-100px ps-3 pe-3 text-center">Keterangan</th>
                            <th rowspan="2" class="w-100px ps-3 pe-3 text-center">User</th>
                            <th rowspan="2" class="w-50px ps-3 pe-3 text-center">Cetak</th>
                            <th rowspan="2" class="w-50px ps-3 pe-3 text-center">SJ</th>
                            <th rowspan="2" class="w-50px ps-3 pe-3 text-center">Validasi</th>
                            <th rowspan="2" class="w-50px ps-3 pe-3 text-center">Marketplace</th>
                            <th colspan="2" class="w-100px ps-3 pe-3 text-center">Action</th>
                        </tr>
                        <tr class="fs-8 fw-bolder text-muted">
                            <th class="w-50px ps-3 pe-3 text-center">Update</th>
                            <th class="w-50px ps-3 pe-3 text-center">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="border">
                        @forelse($data_pemindahan as $data)
                        <tr>
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                <span class="fs-7 fw-bold text-gray-800">{{ ((($data_page->current_page * $data_page->per_page) - $data_page->per_page) + $loop->iteration) }}</span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                <span class="fs-7 fw-bolder text-gray-800 d-block">{{ strtoupper(trim($data->nomor_dokumen)) }}</span>
                                <span class="fs-8 fw-bolder text-gray-600">{{ date('d F Y', strtotime($data->tanggal)) }}</span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                @if(strtoupper(trim($data->lokasi_awal)) == 'RK')
                                <span class="badge badge-primary fs-8 fw-boldest">{{ trim($data->lokasi_awal) }}</span>
                                @elseif(strtoupper(trim($data->lokasi_awal)) == 'OL')
                                <span class="badge badge-success fs-8 fw-boldest">{{ trim($data->lokasi_awal) }}</span>
                                @elseif(strtoupper(trim($data->lokasi_awal)) == 'OB')
                                <span class="badge badge-danger fs-8 fw-boldest">{{ trim($data->lokasi_awal) }}</span>
                                @elseif(strtoupper(trim($data->lokasi_awal)) == 'OS')
                                <span class="badge badge-warning fs-8 fw-boldest">{{ trim($data->lokasi_awal) }}</span>
                                @elseif(strtoupper(trim($data->lokasi_awal)) == 'OP')
                                <span class="badge badge-info fs-8 fw-boldest">{{ trim($data->lokasi_awal) }}</span>
                                @else
                                <span class="badge badge-secondary fs-8 fw-boldest">{{ trim($data->lokasi_awal) }}</span>
                                @endif
                            </td>
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                @if(strtoupper(trim($data->lokasi_tujuan)) == 'RK')
                                <span class="badge badge-primary fs-8 fw-boldest">{{ trim($data->lokasi_tujuan) }}</span>
                                @elseif(strtoupper(trim($data->lokasi_tujuan)) == 'OL')
                                <span class="badge badge-success fs-8 fw-boldest">{{ trim($data->lokasi_tujuan) }}</span>
                                @elseif(strtoupper(trim($data->lokasi_tujuan)) == 'OB')
                                <span class="badge badge-danger fs-8 fw-boldest">{{ trim($data->lokasi_tujuan) }}</span>
                                @elseif(strtoupper(trim($data->lokasi_tujuan)) == 'OS')
                                <span class="badge badge-warning fs-8 fw-boldest">{{ trim($data->lokasi_tujuan) }}</span>
                                @elseif(strtoupper(trim($data->lokasi_tujuan)) == 'OP')
                                <span class="badge badge-info fs-8 fw-boldest">{{ trim($data->lokasi_tujuan) }}</span>
                                @else
                                <span class="badge badge-secondary fs-8 fw-boldest">{{ trim($data->lokasi_tujuan) }}</span>
                                @endif
                            </td>
                            <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                <span class="fs-7 fw-bold text-gray-800">{{ trim($data->keterangan) }}</span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                <span class="fs-7 fw-bold text-gray-800">{{ trim($data->users) }}</span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                @if((double)$data->status_cetak == 1)
                                <i class="fa fa-check text-success"></i>
                                @else
                                <i class="fa fa-minus-circle text-gray-400"></i>
                                @endif
                            </td>
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                @if((double)$data->status_sj == 1)
                                <i class="fa fa-check text-success"></i>
                                @else
                                <i class="fa fa-minus-circle text-gray-400"></i>
                                @endif
                            </td>
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                @if((double)$data->status_validasi == 1)
                                <i class="fa fa-check text-success"></i>
                                @else
                                <i class="fa fa-minus-circle text-gray-400"></i>
                                @endif
                            </td>
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                @if((double)$data->status_marketplace == 1)
                                <i class="fa fa-check text-success"></i>
                                @else
                                <i class="fa fa-minus-circle text-gray-400"></i>
                                @endif
                            </td>
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                @if((double)$data->status_cetak == 1 && (double)$data->status_sj == 1 && (double)$data->status_validasi == 1)
                                @if((double)$data->status_marketplace == 0)
                                <button id="btnUpdateStockAll" class="btn btn-icon btn-sm btn-danger" type="button" data-nomor_dokumen="{{ strtoupper(trim($data->nomor_dokumen)) }}">
                                    <i class="fa fa-refresh text-white"></i>
                                </button>
                                @endif
                                @endif
                            </td>
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                <a href="{{ route('online.pemindahan.tokopedia.form.form', strtoupper(trim($data->nomor_dokumen))) }}" class="btn btn-icon btn-sm btn-primary" type="button">
                                    <i class="fa fa-check text-white"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="pt-12 pb-12">
                                <div class="row text-center pe-10">
                                    <span class="svg-icon svg-icon-muted">
                                        <svg class="h-100px w-100px" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M21.7 18.9L18.6 15.8C17.9 16.9 16.9 17.9 15.8 18.6L18.9 21.7C19.3 22.1 19.9 22.1 20.3 21.7L21.7 20.3C22.1 19.9 22.1 19.3 21.7 18.9Z" fill="currentColor"/>
                                            <path opacity="0.3" d="M11 20C6 20 2 16 2 11C2 6 6 2 11 2C16 2 20 6 20 11C20 16 16 20 11 20ZM11 4C7.1 4 4 7.1 4 11C4 14.9 7.1 18 11 18C14.9 18 18 14.9 18 11C18 7.1 14.9 4 11 4ZM8 11C8 9.3 9.3 8 11 8C11.6 8 12 7.6 12 7C12 6.4 11.6 6 11 6C8.2 6 6 8.2 6 11C6 11.6 6.4 12 7 12C7.6 12 8 11.6 8 11Z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                </div>
                                <div class="row text-center pt-8">
                                    <span class="fs-6 fw-bolder text-gray-500">-  Tidak ada data yang ditampilkan -</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start mt-8">
        <div class="dataTables_length">
            <label>
                <select id="selectPerPageMasterData" name="per_page" class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                    <option value="10" @if($data_page->per_page == '10') {{'selected'}} @endif>10</option>
                    <option value="25" @if($data_page->per_page == '25') {{'selected'}} @endif>25</option>
                    <option value="50" @if($data_page->per_page == '50') {{'selected'}} @endif>50</option>
                    <option value="100" @if($data_page->per_page == '100') {{'selected'}} @endif>100</option>
                </select>
            </label>
        </div>
        <div class="dataTables_info" id="selectPerPageMasterDataInfo" role="status" aria-live="polite">Showing <span id="startRecordMasterData">{{ $data_page->from }}</span> to {{ $data_page->to }} of {{ $data_page->total }} records</div>
    </div>
    <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end mt-8">
        <div class="dataTables_paginate paging_simple_numbers" id="paginationMasterData">
            <ul class="pagination">
                @foreach ($data_page->links as $link)
                <li class="page-item @if($link->active == true) active @endif
                    @if($link->url == '') disabled @endif
                    @if($data_page->current_page == $link->label) active @endif">
                    @if($link->active == true)
                    <span class="page-link">{{ $link->label }}</span>
                    @else
                    <a href="#" class="page-link" data-page="@if(trim($link->url) != ''){{ explode("?page=" , $link->url)[1] }}@endif"
                        @if(trim($link->url) == '') disabled @endif>
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
<div class="modal fade" tabindex="-2" id="modalResultPindahLokasi">
    <div class="modal-dialog">
        <div class="modal-content" id="modalResultPindahLokasiContent">
            <div class="modal-header">
                <h5 id="modalTitle" name="modalTitle" class="modal-title">Result Marketplace</h5>
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
                    <div class="fw-bolder fs-7 text-gray-600 mb-4">Update Stock Marketplace:</div>
                    <div id="resultUpdateStock"></div>
                </div>
                <div class="fv-row mt-8">
                    <div class="fw-bolder fs-7 text-gray-600 mb-4">Update Status Product Marketplace:</div>
                    <div id="resultUpdateStatus"></div>
                </div>
                <div class="fv-row">
                    <div class="fw-bold fs-7 text-danger mb-4">* Data status product hanya mengupdate
                        <span class="fw-bolder fs-7 text-danger">"data update stock"</span> yang ber-status success
                    </div>
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
        'update_per_dokumen': "{{ route('online.pemindahan.tokopedia.form.update.dokumen') }}",
    }
    const data_filter = {
        'start_date': '{{ trim($data_filter->start_date) }}',
        'end_date': '{{ trim($data_filter->end_date) }}',
        'search': '{{ trim($data_filter->search) }}',
    }
    const data_user = {
        'role_id': '{{ trim($data_user->role_id) }}'
    }
    const data_page = {
        'start_record': '{{ $data_page->from }}'
    }
</script>
<script src="{{ asset('assets/js/suma/online/tokopedia/pemindahan/daftar.js') }}?v={{ time() }}"></script>
@endpush
@endsection
