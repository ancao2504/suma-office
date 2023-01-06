@extends('layouts.main.index')
@section('title','Parts')
@section('subtitle','Back Order')
@section('container')
    <div class="row g-0">
        <div class="card card-flush">
            <div class="card-header align-items-center border-0 mt-4 mb-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Back Order</span>
                    <span class="text-muted fw-bold fs-7">Daftar back order suma honda</span>
                    @if(trim($data_filter->salesman) != '' || trim($data_filter->dealer) != '' || trim($data_filter->part_number) != '')
                    <div class="d-flex flex-wrap mt-4">
                        @if(isset($data_filter->salesman) && trim($data_filter->salesman) != '')
                        <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">SALESMAN : {{ trim($data_filter->salesman) }}</span>
                        @endif
                        @if(isset($data_filter->dealer) && trim($data_filter->dealer) != '')
                        <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">DEALER : {{ trim($data_filter->dealer) }}</span>
                        @endif
                        @if(isset($data_filter->part_number) && trim($data_filter->part_number) != '')
                        <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">PART NUMBER : {{ trim($data_filter->part_number) }}</span>
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
    <div class="row mt-5">
        @if(strtoupper(trim($data_device->device)) == "DESKTOP")
            @include('layouts.parts.backorder.list.backorderdesktop')
        @else
            @include('layouts.parts.backorder.list.backordermobile')
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

    <div class="modal fade" tabindex="-1" id="modalFilter">
        <div class="modal-dialog">
            <div class="modal-content" id="modalFilterContent">
                <form id="formFilter" name="formFilter" autofill="off" autocomplete="off" method="get" action="{{ route('parts.backorder.daftar') }}">
                    <div class="modal-header">
                        <h5 id="modalTitle" name="modalTitle" class="modal-title">Filter Back Order</h5>
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
                            <label class="form-label">Salesman:</label>
                            <div class="input-group">
                                <input id="inputFilterSalesman" name="salesman" type="text" placeholder="Semua Salesman" readonly
                                    class="form-control @if(trim($data_user->role_id) == 'D_H3' || trim($data_user->role_id) == 'MD_H3_SM') form-control-solid @endif"
                                    @if(trim($data_user->role_id) != 'D_H3' && trim($data_user->role_id) != 'MD_H3_SM') style="cursor: pointer;" @endif
                                    @if(isset($data_filter->salesman)) value="{{ $data_filter->salesman }}" @else value="{{ old('salesman') }}"@endif>
                                @if(trim($data_user->role_id) != 'MD_H3_SM')
                                    @if(trim($data_user->role_id) != 'D_H3')
                                    <button id="btnFilterPilihSalesman" name="btnFilterPilihSalesman" class="btn btn-icon btn-primary" type="button" role="button">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label">Dealer:</label>
                            <div class="input-group">
                                <input id="inputFilterDealer" name="dealer" type="text" placeholder="Semua Dealer" readonly
                                    class="form-control @if(trim($data_user->role_id) == 'D_H3') form-control-solid @endif"
                                    @if(trim($data_user->role_id) != 'D_H3') style="cursor: pointer;" @endif
                                    @if(isset($data_filter->dealer)) value="{{ $data_filter->dealer }}" @else value="{{ old('dealer') }}"@endif>
                                @if(trim($data_user->role_id) != 'D_H3')
                                <button id="btnFilterPilihDealer" name="btnFilterPilihDealer" class="btn btn-icon btn-primary" type="button" role="button">
                                    <i class="fa fa-search"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label">Part Number:</label>
                            <div class="input-group has-validation mb-2">
                                <input id="inputFilterPartNumber" name="part_number" type="text" class="form-control" placeholder="Semua Part Number"
                                    @if(isset($data_filter->part_number)) value="{{ $data_filter->part_number }}" @else value="{{ old('part_number') }}"@endif>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button id="btnFilterReset" class="btn btn-danger" role="button">Reset Filter</button>
                        <div class="text-end">
                            <button id="btnFilterProses" type="button" role="button" class="btn btn-primary">Terapkan</button>
                            <button id="btnFilterClose" name="btnClose" type="button" role="button" class="btn btn-light text-end" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('layouts.option.optionsalesman')
    @include('layouts.option.optiondealer')
    @include('layouts.option.optiondealersalesman')

    @push('scripts')
        <script type="text/javascript">
            const data_filter = {
                'salesman': '{{ trim($data_filter->salesman) }}',
                'dealer': '{{ trim($data_filter->dealer) }}',
                'part_number': '{{ trim($data_filter->part_number) }}'
            }
            const data_user = {
                'user_id': '{{ trim($data_user->user_id) }}',
                'role_id': '{{ trim($data_user->role_id) }}',
            }
            const data_page = {
                'start_record': '{{ $data_page->from }}'
            }
        </script>
        <script src="{{ asset('assets/js/suma/parts/backorder.js') }}?v={{ time() }}"></script>
    @endpush
@endsection
