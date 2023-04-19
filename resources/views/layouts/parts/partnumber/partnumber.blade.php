@extends('layouts.main.index')
@section('title','Parts')
@section('subtitle','Part Number')
@section('container')
<div class="row g-0">
    <div class="card card-flush shadow">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Part Number</span>
                <span class="text-muted fw-bold fs-7">Daftar parts suma honda</span>
                @if(trim($data_filter->kode_level) != '' || trim($data_filter->kode_produk) != '' || trim($data_filter->type_motor) != '' || trim($data_filter->part_number) != '')
                <div class="d-flex flex-wrap mt-2">
                    @if(isset($data_filter->kode_level) && trim($data_filter->kode_level) != '')
                    <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">LEVEL : {{ trim($data_filter->kode_level) }}</span>
                    @endif
                    @if(isset($data_filter->kode_produk) && trim($data_filter->kode_produk) != '')
                    <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">PRODUK : {{ trim($data_filter->kode_produk) }}</span>
                    @endif
                    @if(isset($data_filter->type_motor) && trim($data_filter->type_motor) != '')
                    <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">TIPE MOTOR : {{ trim($data_filter->type_motor) }}</span>
                    @endif
                    @if(isset($data_filter->part_number) && trim($data_filter->part_number) != '')
                    <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-22">PART NUMBER : {{ trim($data_filter->part_number) }}</span>
                    @endif
                </div>
                @endif
            </h3>
            <div class="card-toolbar">
                <button id="btnFilterMasterData" type="button" role="button" class="btn btn-primary">
                    <i class="bi bi-funnel-fill fs-4 me-2"></i>Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row mt-6">
    @if (strtoupper(trim($data_device->device)) == 'DESKTOP')
    @include('layouts.parts.partnumber.list.partnumberdesktop')
    @else
    @include('layouts.parts.partnumber.list.partnumbermobile')
    @endif
</div>
<div class="row">
    <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start mt-8">
        <div class="dataTables_length">
            <label>
                <select id="selectPerPageMasterData" name="per_page" class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                    <option value="12" @if($data_page->per_page == '12') {{'selected'}} @endif>12</option>
                    <option value="28" @if($data_page->per_page == '28') {{'selected'}} @endif>28</option>
                    <option value="56" @if($data_page->per_page == '56') {{'selected'}} @endif>56</option>
                    <option value="112" @if($data_page->per_page == '112') {{'selected'}} @endif>112</option>
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

<div class="modal fade bs-example-modal-xl" tabindex="-1" id="modalPartNumberCart">
    <div class="modal-dialog">
        <div id="modalCartContentPartNumber" name="modalCartContentPartNumber" class="modal-content">
            <form id="formCartPartNumber" name="formCartPartNumber" autofill="off" autocomplete="off" method="POST" action="#">
                @csrf
                <div class="modal-header">
                    <h5 id="modalPartNumberCartTitle" name="modalPartNumberCartTitle" class="modal-title"></h5>
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
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <span id="messageErrorPartNumber"></span>
                        <div class="form-group">
                            <span id="modalPartNumberCartImages"></span>
                            <div class="fv-row mt-5 mb-2">
                                <div class="d-flex align-items-center">
                                    <span id="modalPartNumberCartTextPartNumber" name="part_number" class="fw-bolder fs-4 text-dark"></span>
                                </div>
                                <span id="modalPartNumberCartTextDescription" name="description" class="form-label"></span>
                            </div>
                            <div class="fv-row mt-2">
                                <span id="modalPartNumberCartTextProduk" name="produk" class="badge badge-danger fs-8"></span>
                            </div>
                            <div class="fv-row mt-4">
                                <span id="modalPartNumberCartTextHargaNetto" class="fw-bolder text-dark fs-3"></span>
                                <div class="d-flex align-items-center">
                                    <div id="modalPartNumberCartTextDiscount"></div>
                                    <div id="modalPartNumberCartTextHet"></div>
                                </div>
                            </div>
                            <div class="fv-row mt-6 mb-4">
                                <label class="col-lg-4 form-label">Type Motor:</label>
                                <div class="row d-flex flex-wrap">
                                    <span id="modalPartNumberCartListTypeMotor"></span>
                                </div>
                            </div>
                            <div id="modalPartNumberCartTextKeteranganBo"></div>
                            @if(session()->get('app_user_role_id') == 'MD_H3_MGMT' || session()->get('app_user_role_id') == 'MD_H3_KORSM' ||
                                session()->get('app_user_role_id') == 'MD_H3_SM')
                                <div class="fv-row mt-8 mb-4">
                                    <label class="form-label">Jumlah Order:</label>
                                    <div class="col-md-6">
                                        <div class="input-group w-md-200px"
                                            data-kt-dialer="true"
                                            data-kt-dialer-min="1"
                                            data-kt-dialer-step="1">
                                            <button class="btn btn-icon btn-outline btn-outline-secondary" type="button" role="button" data-kt-dialer-control="decrease">
                                                <i class="bi bi-dash fs-1"></i>
                                            </button>
                                            <input id="modalPartNumberCartInputJumlahOrder" type="number" min="1" class="form-control" placeholder="Amount" value="1" data-kt-dialer-control="input" />
                                            <button class="btn btn-icon btn-outline btn-outline-secondary" type="button" role="button" data-kt-dialer-control="increase">
                                                <i class="bi bi-plus fs-1"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="fv-row mt-8">
                                    <button id="btnOrder" name="btnOrder" type="button" role="button" class="btn btn-success waves-effect text-left">
                                        <div>
                                            <span class="svg-icon svg-icon-muted svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M18.041 22.041C18.5932 22.041 19.041 21.5932 19.041 21.041C19.041 20.4887 18.5932 20.041 18.041 20.041C17.4887 20.041 17.041 20.4887 17.041 21.041C17.041 21.5932 17.4887 22.041 18.041 22.041Z" fill="currentColor"/>
                                                    <path opacity="0.3" d="M6.04095 22.041C6.59324 22.041 7.04095 21.5932 7.04095 21.041C7.04095 20.4887 6.59324 20.041 6.04095 20.041C5.48867 20.041 5.04095 20.4887 5.04095 21.041C5.04095 21.5932 5.48867 22.041 6.04095 22.041Z" fill="currentColor"/>
                                                    <path opacity="0.3" d="M7.04095 16.041L19.1409 15.1409C19.7409 15.1409 20.141 14.7409 20.341 14.1409L21.7409 8.34094C21.9409 7.64094 21.4409 7.04095 20.7409 7.04095H5.44095L7.04095 16.041Z" fill="currentColor"/>
                                                    <path d="M19.041 20.041H5.04096C4.74096 20.041 4.34095 19.841 4.14095 19.541C3.94095 19.241 3.94095 18.841 4.14095 18.541L6.04096 14.841L4.14095 4.64095L2.54096 3.84096C2.04096 3.64096 1.84095 3.04097 2.14095 2.54097C2.34095 2.04097 2.94096 1.84095 3.44096 2.14095L5.44096 3.14095C5.74096 3.24095 5.94096 3.54096 5.94096 3.84096L7.94096 14.841C7.94096 15.041 7.94095 15.241 7.84095 15.441L6.54096 18.041H19.041C19.641 18.041 20.041 18.441 20.041 19.041C20.041 19.641 19.641 20.041 19.041 20.041Z" fill="currentColor"/>
                                                </svg>
                                            </span>Add To Cart
                                        </div>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" role="button" class="btn btn-light text-end" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFilter" tabindex="-1">
    <div class="modal-dialog">
        <div id="modalFilterContent" class="modal-content">
            <form id="formFilter" name="formFilter" autofill="off" autocomplete="off" method="get" action="{{ route('parts.partnumber.daftar') }}">
                <div class="modal-header">
                    <h5 id="modalTitle" name="modalTitle" class="modal-title">Filter Part Number</h5>
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
                        <label class="form-label">Level:</label>
                        <select id="selectFilterGroupLevel" name="group_level" class="form-select" data-dropdown-parent="#modalFilter"
                            data-placeholder="Semua Level Produk" data-allow-clear="true">
                            <option value="" @if($data_filter->kode_level != 'HANDLE' && $data_filter->kode_level != 'HON_HANDLE' && $data_filter->kode_level != 'TUBE' && $data_filter->kode_level != 'OLI') selected @endif>Semua Level Produk</option>
                            <option value="HANDLE" @if($data_filter->kode_level == 'HANDLE') selected @endif>Handle</option>
                            <option value="NON_HANDLE" @if($data_filter->kode_level == 'NON_HANDLE') selected @endif>Non-Handle</option>
                            <option value="TUBE" @if($data_filter->kode_level == 'TUBE') selected @endif>Tube</option>
                            <option value="OLI" @if($data_filter->kode_level == 'OLI') selected @endif>Oli</option>
                        </select>
                    </div>
                    <div class="fv-row mt-8">
                        <label class="form-label">Produk:</label>
                        <div class="input-group">
                            <input id="inputFilterKodeProduk" name="group_produk" type="search" class="form-control" style="cursor: pointer;" placeholder="Semua Produk" readonly
                                @if(isset($data_filter->kode_produk)) value="{{ $data_filter->kode_produk }}" @else value="{{ old('produk') }}"@endif>
                            <button id="btnFilterProduk" name="btnFilterProduk" class="btn btn-icon btn-primary" type="button" role="button">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="fv-row mt-8">
                        <label class="form-label">Tipe Motor:</label>
                        <div class="input-group">
                            <input id="inputFilterTipeMotor" name="type_motor" type="search" class="form-control" style="cursor: pointer;" placeholder="Semua Tipe Motor" readonly
                                @if(isset($data_filter->type_motor)) value="{{ $data_filter->type_motor }}" @else value="{{ old('type_motor') }}"@endif>
                            <button id="btnFilterPilihTipeMotor" name="btnFilterPilihTipeMotor" class="btn btn-icon btn-primary" type="button" role="button">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="fv-row mt-8">
                        <label class="form-label">Part Number:</label>
                        <input id="inputFilterPartNumber" type="text" name="part_number" class="form-control" placeholder="Semua Part Number" autocomplete="off"
                            @if(isset($data_filter->part_number)) value="{{ $data_filter->part_number }}" @else value="{{ old('part_number') }}"@endif>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button id="btnFilterReset" class="btn btn-danger" role="button">Reset Filter</button>
                    <div class="text-end">
                        <button id="btnFilterProses" type="button" role="button" class="btn btn-primary">Terapkan</button>
                        <button id="btnFilterClose" type="button" role="button" class="btn btn-light text-end" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@include('layouts.option.optiontipemotor')
@include('layouts.option.optiongroupproduk')

@push('scripts')
<script type="text/javascript">
    const url = {
        'tambah_cart' : "{{ route('parts.partnumber.tambah') }}",
        'proses_cart':"{{ route('parts.partnumber.proses') }}"
    }

    const data_filter = {
        'type_motor' : "{{ trim($data_filter->type_motor) }}",
        'kode_level' : "{{ trim($data_filter->kode_level) }}",
        'kode_produk' : "{{ trim($data_filter->kode_produk) }}",
        'part_number' : "{{ trim($data_filter->part_number) }}"
    }
    const data_page = {
        'start_record': '{{ $data_page->from }}'
    }
</script>
<script src="{{ asset('assets/js/suma/parts/partnumber.js') }}?v={{ time() }}"></script>
@endpush
@endsection
