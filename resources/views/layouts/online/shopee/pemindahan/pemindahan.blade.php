@extends('layouts.main.index')
@section('title', 'Online')
@section('subtitle', 'Shopee')
@push('styles')
@endpush
@section('container')
    <div id="tab-content">
        {{-- @if (\Agent::isDesktop()) --}}
            <!--begin::Card-->
            <div class="tab-pane fade active show">
                <div class="card card-flush">
                    <div class="card-header align-items-center border-0 mt-4 mb-4">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="fw-bolder mb-2 text-dark">Pemindahan Antar Lokasi</span>
                            <span class="text-muted fw-bold fs-7">Daftar pemindahan antar lokasi Shopee</span>
                            <span class="text-muted fw-bold fs-7 mt-2">Periode
                                <span class="text-dark fw-bolder fs-7" id="text_start_date">{{ date("j F Y", strtotime($filter->start_date))}}</span> s/d
                                <span class="text-dark fw-bolder fs-7" id="text_end_date">{{date("j F Y", strtotime($filter->end_date))}}</span>
                            </span>
                        </h3>
                        <div class="card-toolbar">
                            <img src="{{ asset('assets/images/logo/shopee_lg.png') }}" class="h-75px" />
                        </div>
                    </div>
                    <div class="card-header align-items-center border-0">
                        <div class="align-items-start flex-column">
                            <div class="input-group">
                                <input type="text" id="get_start_date" class="form-control" placeholder="Start Date"
                                    aria-label="Start Date" value="{{ $filter->start_date }}">
                                <span class="input-group-text">SD</span>
                                <input type="text" id="get_end_date" class="form-control" placeholder="End Date"
                                    aria-label="End Date" value="{{ $filter->end_date }}">
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
                                <input id="filterSearch" name="search" type="text" class="form-control ps-10" value="{{ $filter->search }}"
                                    oninput="this.value = this.value.toUpperCase()" placeholder="Search">
                            </div>
                        </div>
                    </div>
                    <!--begin::Card body-->
                    <div class="card-body">
                        <!--start::container-->
                        <div class="table-responsive mt-3" id="tabel">
                            <div class="dataTables_wrapper dt-bootstrap4 no-footer">
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
                                            @if ($data_all->total > 0)
                                                @php
                                                    $no = $data_all->from;
                                                @endphp
                                                @foreach ($data_all->data as $data)
                                                    @php
                                                        $filter->marketplace = $data->status_marketplace;
                                                        $page_detail_data = json_encode([
                                                            'nomor_dokumen' => $data->nomor_dokumen,
                                                            'filter' => $filter,
                                                        ]);
                                                    @endphp
                                                    <tr class="fs-6 fw-bold text-gray-700"
                                                        data-no="{{ $data->nomor_dokumen }}">
                                                        <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                                            <span
                                                                class="fs-7 fw-bolder text-gray-800">{{ $no }}</span>
                                                        </td>
                                                        <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                                            <span
                                                                class="fs-7 fw-bolder text-gray-800 d-block">{{ $data->nomor_dokumen }}</span>
                                                            <span
                                                                class="fs-8 fw-bolder text-gray-600">{{ date('d F Y', strtotime($data->tanggal)) }}</span>
                                                        </td>
                                                        <td class="ps-3 pe-3"
                                                            style="text-align:center;vertical-align:top;">
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
                                                        <td class="ps-3 pe-3"
                                                            style="text-align:center;vertical-align:top;">
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
                                                        <td class="ps-3 pe-3"
                                                            style="text-align:{{ !empty($data->keterangan) ? 'left' : 'center' }};vertical-align:top;">
                                                            <span
                                                                class="fs-7 fw-bolder text-muted">{{ !empty($data->keterangan) ? $data->keterangan : '-' }}</span>
                                                        </td>
                                                        <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                                            <span
                                                                class="fs-7 fw-bolder text-dark">{{ !empty($data->usertime) ? explode('=', $data->usertime)[2] : '-' }}</span>
                                                        </td>
                                                        <td class="ps-3 pe-3"
                                                            style="text-align:center;vertical-align:top;">
                                                            @if ($data->status_cetak == 1)
                                                                <i class="bi bi-check text-success fs-1"></i>
                                                            @else
                                                                <i class="fa fa-minus-circle text-gray-400"></i>
                                                            @endif
                                                        </td>
                                                        <td class="ps-3 pe-3"
                                                            style="text-align:center;vertical-align:top;">
                                                            @if ($data->status_sj == 1)
                                                                <i class="bi bi-check text-success fs-1"></i>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="ps-3 pe-3"
                                                            style="text-align:center;vertical-align:top;">
                                                            @if ($data->validasi == 1)
                                                                <i class="bi bi-check text-success fs-1"></i>
                                                            @else
                                                                <i class="fa fa-minus-circle text-gray-400"></i>
                                                            @endif
                                                        </td>
                                                        <td class="ps-3 pe-3"
                                                            style="text-align:center;vertical-align:top;">
                                                            @if ($data->status_marketplace == 1)
                                                                <i class="bi bi-check text-success fs-1"></i>
                                                            @else
                                                                <i class="fa fa-minus-circle text-gray-400"></i>
                                                            @endif
                                                        </td>
                                                        <td class="ps-3 pe-3"
                                                            style="text-align:center;vertical-align:top;">
                                                            @if ($data->status_marketplace == 1)
                                                                <span
                                                                    class="fs-7 fw-bolder badge badge-light-success">success</span>
                                                            @else
                                                                @if ($data->validasi == 1)
                                                                    <button
                                                                    class="btn btn-sm btn-light-dark btn-hover-rise btn_detail"
                                                                    data-focus="0"><img alt="Logo"
                                                                        src="{{ asset('assets/images/logo/shopee.png') }}"
                                                                        class="h-20px" /></button>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td class="ps-3 pe-3"
                                                            style="text-align:center;vertical-align:top;">
                                                            <a href="{{ route('online.pemindahan.shopee.daftar-detail', base64_encode($page_detail_data)) }}"
                                                                class="btn btn-sm btn-primary" data-focus="0">
                                                                Detail
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @php
                                                        $no++;
                                                    @endphp
                                                @endforeach
                                            @else
                                                <tr class="odd">
                                                    <td class="fw-bold text-center" colspan="10"> Data Tidak Ditemukan
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div
                                        class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                        <div class="dataTables_length">
                                            <select class="form-select form-select-sm form-select-solid" id="per_page">
                                                <option value="10" {{ $data_all->per_page == 10 ? 'selected' : '' }}>10</option>
                                                <option value="25" {{ $data_all->per_page == 25 ? 'selected' : '' }}>25</option>
                                                <option value="50" {{ $data_all->per_page == 50 ? 'selected' : '' }}>50</option>
                                                <option value="100" {{ $data_all->per_page == 100 ? 'selected' : '' }}>100</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div
                                        class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                        <div class="dataTables_paginate paging_simple_numbers" id="view_daftar_paginat">
                                            @if ($data_all->total > 0)
                                                <ul class="pagination" data-current_page="{{ $data_all->current_page }}">
                                                    @foreach ($data_all->links as $data)
                                                        @if (strpos($data->label, 'Next') !== false)
                                                            <li
                                                                class="page-item next @if ($data->url == null) disabled @endif">
                                                                <a role="button"
                                                                    data-page="{{ (string)((int) $data_all->current_page + 1) }}"
                                                                    class="page-link">
                                                                    <i class="next"></i>
                                                                </a>
                                                            </li>
                                                        @elseif (strpos($data->label, 'Previous') !== false)
                                                            <li
                                                                class="page-item previous @if ($data->url == null) disabled @endif">
                                                                <a role="button"
                                                                    data-page="{{ (string) ((int) $data_all->current_page - 1) }}"
                                                                    class="page-link">
                                                                    <i class="previous"></i>
                                                                </a>
                                                            </li>
                                                        @elseif ($data->active == true)
                                                            <li
                                                                class="page-item active @if ($data->url == null) disabled @endif">
                                                                <a role="button" data-page="{{ $data->label }}"
                                                                    class="page-link">{{ $data->label }}</a>
                                                            </li>
                                                        @elseif ($data->active == false)
                                                            <li
                                                                class="page-item @if ($data->url == null) disabled @endif">
                                                                <a role="button" data-page="{{ $data->label }}"
                                                                    class="page-link">{{ $data->label }}</a>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <!--end::container-->
                            </div>
                            <!--end::Table-->
                        </div>
                        <!--end::Table container-->
                    </div>
                    <!--end::Card body-->
                </div>
            </div>
            <!--end::Card-->
        {{-- @else

        @endif --}}
    </div>
    <div id="respon_container">
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script language="JavaScript"
        src="{{ asset('assets/js/suma/online/shopee/pemindahan/Pemindahan.js') }}?v={{ time() }}"></script>
@endpush
