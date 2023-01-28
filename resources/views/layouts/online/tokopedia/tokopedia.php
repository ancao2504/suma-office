@extends('layouts.main.index')
@section('title','Tokopedia')
@section('subtitle','Dashboard')
@push('styles')
@endpush
@section('container')
<div class="card card-flush">
    <div class="card-header align-items-center border-0 mt-4 mb-4">
        <div class="align-items-start flex-column">
            <!--begin::Input group-->
            <div class="position-relative w-md-400px me-md-2">
                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                <span class="svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                    </svg>
                </span>
                <!--end::Svg Icon-->
                <input type="text" class="form-control form-control-solid ps-10" name="search" id="filterSearch" value="" oninput="this.value = this.value.toUpperCase()" placeholder="Search">
            </div>
            <!--end::Input group-->
        </div>
        {{-- <div class="d-flex align-items-center ms-3">
            <button type="reset" class="btn btn-primary" id="btn-adddiskonproduk" data-bs-toggle="modal" data-bs-target="#tambah_diskon"><i class="bi bi-plus-circle fs-1"></i> Tambah</button>
        </div> --}}
        <div class="card-toolbar">
            <div class="input-group input-group-solid">
                <input type="text" class="form-control" id="get_tgl_dokumen" name="get_tgl_dokumen" placeholder="Tanggal Dolumen">
                <span class="input-group-text">
                    <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="currentColor"></path>
                            <path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="currentColor"></path>
                            <path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="currentColor"></path>
                        </svg>
                    <!--end::Svg Icon-->
                </span>
            </div>
        </div>
    </div>
</div>

<div class="d-flex flex-wrap flex-stack my-4" data-select2-id="select2-data-131-enac">
    <!--begin::Title-->
    <div class="d-flex flex-wrap align-items-center my-1">
                            {{-- $data_disc_dealer->total --}}
        <h3 class="fw-bolder me-5 my-1"> 1000  Daftar Pemindahan Stock Antar lokasi
        <span class="text-gray-400 fs-6">↓</span></h3>
    </div>
    <!--end::Title-->
</div>

{{-- modal edit-edit --}}
<div class="modal fade" id="update_stok" data-backdrop="static" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="update_stokLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="update_stokLabel">Update Stok</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-boady px-3">
            </div>
        </div>
    </div>
</div>

<div class="tab-content">
    @if (\Agent::isDesktop())
    <!--begin::Card-->
    <div id="kt_project_users_table_pane" class="tab-pane fade active show">
        <div class="card card-flush">
            <!--begin::Card body-->
            <div class="card-body">
                <!--begin::Table container-->
                <div class="table-responsive mt-10">
                    <!--begin::Table-->
                    <div id="kt_project_users_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        @if (!empty($table))
                            {!! $table !!}
                        @else
                            <div class="table-responsive">
                                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                    <thead>
                                        <tr class="fw-bolder text-muted">
                                            <th class="min-w-30px">#</th>
                                            <th class="min-w-100px">Nomor Dokumen</th>
                                            <th class="min-w-100px">Tanggal</th>
                                            <th class="min-w-100px">Lokasi Awal</th>
                                            <th class="min-w-100px">Lokasi Tujuan</th>
                                            <th class="min-w-100px">Keterangan</th>
                                            <th style="width: 0px;">Sts Cetak</th>
                                            <th style="width: 0px;">Sts In</th>
                                            <th style="width: 0px;">Sts Marketplace</th>
                                            <th class="min-w-60px" style="width: 0px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fs-6 border">
                                        <tr class="odd">
                                            <td class="fw-bold text-center" colspan="10">Tidak ada data</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                <div class="dataTables_length">
                                    <label>
                                        <select class="form-select form-select-sm form-select-solid" id="per_page">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {{-- paginatesen --}}
                                    {!! $pagination !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Table-->
                </div>
            <!--end::Table container-->
            </div>
        <!--end::Card body-->
        </div>
    </div>
    <!--end::Card-->
    @else
    <!--begin::Tab pane-->
    <div id="kt_project_users_card_pane" class="tab-pane fade active show">
        <!--begin::Row-->
        <div class="row g-3" id="dataDiskon">
        @if ( $data_disc_dealer->total > 0)
        @foreach ( $data_disc_dealer->data as $data)
            <div class="col-sm-4 col-12">
                <!--begin::Card-->
                <div class="card h-100 flex-row flex-stack flex-wrap p-6 ribbon ribbon-top">
                    <!--begin::Info-->
                    <div class="d-flex flex-column py-2 w-100">
                        <!--begin::Owner-->
                        <div class="d-flex align-items-center fs-4 fw-bolder mb-5">
                            <div class="ribbon-label bg-success">{{ $data->kode_dealer }}</div>
                            {{-- <span class="text-gray-800">{{ $data->kode_dealer }}</span> --}}
                            {{-- <span class="text-muted fs-6 fw-bold ms-2">{{ $data->nama_produk }}</span> --}}
                        </div>
                        <!--end::Owner-->
                        <div class="d-flex align-items-center w-100 rounded border border-gray-300 p-3">
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <thead>
                                    <tr class="fs-7 fw-bolder text-gray-400 border-bottom-1">
                                        <th>Umur Faktur</th>
                                        <th>Diskon</th>
                                        <th>Diskon +</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bolder">{{ $data->umur_faktur == '.00' ? '0' : $data->umur_faktur }}</td>
                                        <td><span class="fw-bolder">{{ $data->disc_default == '.00' ? '0' : $data->disc_default  }}</span></td>
                                        <td><span class="fw-bolder">{{  $data->disc_plus == '.00' ? '0' : $data->disc_plus  }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--end::Info-->
                    <!--begin::Actions-->
                    <div class="d-flex align-items-center justify-content-cente py-2">
                        <button type="reset" class="btn btn-sm btn-danger me-3 btn-delete" data-bs-toggle="modal" data-bs-target="#delet_model" data-array="{{json_encode(['kode_dealer' => $data->kode_dealer])}}">Delete</button>
                        <button class="btn btn-sm btn-primary btn-edit"
                        data-array="{{json_encode($data)}}">
                        Edit</button>
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Card-->
            </div>
        @endforeach
        @else
        <div class="col-12">
            <!--begin::Card-->
            <div class="card h-xl-100 flex-row flex-stack flex-wrap p-6">
                <!--begin::Owner-->
                <div class="text-center w-100">
                    <span class="fw-bold text-gray-800">Tidak ada data</span>
                </div>
                <!--end::Owner-->
            </div>
            <!--end::Card-->
        </div>
        @endif
        </div>
        <!--end::Row-->
        <!--begin::Pagination-->
        <div class="d-flex flex-stack flex-wrap pt-10">
            <div class="fs-6 fw-bold text-gray-700">Showing {{ $data_disc_dealer->from }} to {{ $data_disc_dealer->to }} of {{ $data_disc_dealer->total }} entries</div>
            <!--begin::Pages-->
            <ul class="pagination">
                @foreach ($data_disc_dealer->links as $data)
                    @if (strpos($data->label, 'Next') !== false)
                        <li class="page-item next {{ ($data->url == null)?'disabled':'' }}">
                            <a role="button" data-page="{{ (string)((int)($data_disc_dealer->current_page) + 1) }}" class="page-link">
                                <i class="next"></i>
                            </a>
                        </li>
                    @elseif (strpos($data->label, 'Previous') !== false)
                        <li class="page-item previous {{ ($data->url == null)?'disabled':'' }}">
                            <a role="button" data-page="{{ (string)((int)($data_disc_dealer->current_page) - 1) }}" class="page-link">
                                <i class="previous"></i>
                            </a>
                        </li>
                    @elseif ($data->active == true)
                        <li class="page-item active {{ ($data->url == null)?'disabled':'' }}">
                            <a role="button" data-page="{{ $data->label }}" class="page-link">{{ $data->label }}</a>
                        </li>
                    @elseif ($data->active == false)
                        <li class="page-item {{ ($data->url == null)?'disabled':'' }}">
                            <a role="button" data-page="{{ $data->label }}" class="page-link">{{ $data->label }}</a>
                        </li>
                    @endif
                @endforeach
            </ul>
            <!--end::Pages-->
        </div>
        <!--end::Pagination-->
    </div>
    <!--end::Tab pane-->
    @endif
</div>

    @push('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script language="JavaScript" src="{{ asset('assets/js/suma/online/tokopedia/daftarPemindahan.js') }}?v={{ time() }}"></script>
    @endpush
@endsection
