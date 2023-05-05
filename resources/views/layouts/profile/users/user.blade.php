@extends('layouts.main.index')
@section('title','Profile')
@section('subtitle','Users')
@section('container')
<div class="row g-0">
    <form id="formUsers" action="{{ route('profile.users.daftar') }}" method="get" autocomplete="off">
        <div class="card card-flush shadow">
            <div class="card-header align-items-center border-0 mt-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Users</span>
                    <span class="text-muted fw-bold fs-7">Daftar user PMO Suma Honda</span>
                    @if(trim($data_filter->role_id) != '' || trim($data_filter->user_id) != '')
                    <div class="d-flex flex-wrap mb-4">
                        @if(isset($data_filter->role_id) && trim($data_filter->role_id) != '')
                        <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">Role Id : {{ trim($data_filter->role_id) }}</span>
                        @endif
                        @if(isset($data_filter->user_id) && trim($data_filter->user_id) != '')
                        <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">User Id : {{ trim($data_filter->user_id) }}</span>
                        @endif
                    </div>
                    @endif
                </h3>
                <div class="card-toolbar">
                    <button class="btn btn-primary" type="button" role="button" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="bi bi-funnel-fill fs-4 me-2"></i>Filter
                    </button>
                    <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_6244763d95a3a" style="">
                        <div class="px-7 py-5">
                            <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                        </div>
                        <div class="separator border-gray-200"></div>
                        <div class="px-7 py-5">
                            <div class="mb-8">
                                <label class="form-label required">Role:</label>
                                <select id="selectFilterRoleId" name="role_id" class="form-select" data-control="select2" data-hide-search="true">
                                    <option value="" @if($data_filter->role_id == '' || empty($data_filter->role_id)) {{"selected"}} @endif>SEMUA</option>
                                    @foreach ($data_role as $list)
                                        <option value="{{ trim($list->role_id) }}"
                                            @if(trim($data_filter->role_id) == trim($list->role_id)) {{"selected"}} @endif>{{ trim($list->role_id) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-8">
                                <label class="form-label">User Id:</label>
                                <div class="d-flex align-items-center position-relative my-1">
                                    <span class="svg-icon svg-icon-1 position-absolute ms-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                                            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                                        </svg>
                                    </span>
                                    <input type="text" id="inputFilterUserId" name="user_id" class="form-control ps-14" placeholder="Search User Id"
                                        @if(isset($user_id)) value="{{ $user_id }}" @else value="{{ old('user_id') }}"@endif>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <button id="btnFilterProses" class="btn btn-sm btn-primary m-2" type="submit">Terapkan</button>
                                <a id="btnFilterReset" href="{{ route('profile.users.daftar') }}" class="btn btn-sm btn-danger" role="button">Reset Filter</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <a href="{{ route('profile.users.tambah') }}" role="button" class="btn btn-sm btn-primary">Tambah</a>
                <div class="row mt-12">
                    @forelse($data_user as $data)
                    <div class="d-flex">
                        <div class="symbol symbol-circle symbol-50px me-6">
                            <div class="symbol-label">
                                <img src="{{ trim($data->photo) }}" alt="{{ trim($data->user_id) }}" class="w-100">
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center">
                                <a href="{{ route('profile.users.form', trim($data->user_id)) }}" class="text-dark fw-bolder text-hover-primary fs-6">{{ trim($data->user_id) }}</a>
                                <span class="text-muted d-block fw-bold ms-2 me-2 fs-6">-</span>
                                <span class="text-muted d-block fw-bolder fs-7">{{ trim($data->name) }}</span>
                            </div>
                            <span class="text-muted d-block fw-bold fs-7">{{ trim($data->email) }}</span>
                            <div class="d-flex align-items-center mt-2">
                                @if ($data->role_id == 'D_H3')
                                    <span class="badge badge-light-danger fw-boldest fs-8">{{ trim($data->role_id) }}</span>
                                    <span class="text-danger d-block fw-bold ms-2 me-2 fs-6">-</span>
                                    <span class="text-danger d-block fw-boldest fs-8">{{ trim($data->jabatan) }}</span>
                                @elseif($data->role_id == 'MD_H3_SM')
                                    <span class="badge badge-light-success fw-boldest fs-8">{{ trim($data->role_id) }}</span>
                                    <span class="text-success d-block fw-bold ms-2 me-2 fs-6">-</span>
                                    <span class="text-success d-block fw-boldest fs-8">{{ trim($data->jabatan) }}</span>
                                @elseif($data->role_id == 'MD_H3_KORSM')
                                    <span class="badge badge-light-info fw-boldest fs-8">{{ trim($data->role_id) }}</span>
                                    <span class="text-info d-block fw-bold ms-2 me-2 fs-6">-</span>
                                    <span class="text-info d-block fw-boldest fs-8">{{ trim($data->jabatan) }}</span>
                                @else
                                    <span class="badge badge-light-primary fw-boldest fs-8">{{ trim($data->role_id) }}</span>
                                    <span class="text-primary d-block fw-bold ms-2 me-2 fs-6">-</span>
                                    <span class="text-primary d-block fw-boldest fs-8">{{ trim($data->jabatan) }}</span>
                                @endif
                            </div>
                            <div class="d-flex align-items-center mt-4">
                                @if ($data->status == 1)
                                    <span class="badge badge-light-success fw-boldest fs-8">Active</span>
                                @else
                                    <span class="badge badge-light-danger fw-boldest fs-8">Non-Active</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('profile.users.form', trim($data->user_id)) }}" class="btn btn-icon btn-primary me-2 mb-2" role="button">
                            <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                        </a>
                    </div>
                    <div class="separator my-5"></div>
                    @empty
                    <div class="row text-center pe-10 pt-12">
                        <span class="svg-icon svg-icon-muted">
                            <svg class="h-100px w-100px" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M21.7 18.9L18.6 15.8C17.9 16.9 16.9 17.9 15.8 18.6L18.9 21.7C19.3 22.1 19.9 22.1 20.3 21.7L21.7 20.3C22.1 19.9 22.1 19.3 21.7 18.9Z" fill="currentColor"/>
                                <path opacity="0.3" d="M11 20C6 20 2 16 2 11C2 6 6 2 11 2C16 2 20 6 20 11C20 16 16 20 11 20ZM11 4C7.1 4 4 7.1 4 11C4 14.9 7.1 18 11 18C14.9 18 18 14.9 18 11C18 7.1 14.9 4 11 4ZM8 11C8 9.3 9.3 8 11 8C11.6 8 12 7.6 12 7C12 6.4 11.6 6 11 6C8.2 6 6 8.2 6 11C6 11.6 6.4 12 7 12C7.6 12 8 11.6 8 11Z" fill="currentColor"/>
                            </svg>
                        </span>
                    </div>
                    <div class="row text-center pt-8 pb-12">
                        <span class="fs-6 fw-bolder text-gray-500">-  Tidak ada data yang ditampilkan -</span>
                    </div>
                    @endforelse
                </div>

                <div class="row">
                    <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start mt-8">
                        <div class="dataTables_length">
                            <label>
                                <select id="selectPerPageUser" name="per_page" class="form-select form-select-sm" data-control="select2" data-hide-search="true"
                                    onchange="this.form.submit()">
                                    <option value="10" @if($data_page->per_page == '10') {{'selected'}} @endif>10</option>
                                    <option value="25" @if($data_page->per_page == '25') {{'selected'}} @endif>25</option>
                                    <option value="50" @if($data_page->per_page == '50') {{'selected'}} @endif>50</option>
                                    <option value="100" @if($data_page->per_page == '100') {{'selected'}} @endif>100</option>
                                </select>
                            </label>
                        </div>
                        <div class="dataTables_info" id="selectPerPageUserInfo" role="status" aria-live="polite">Showing <span id="startRecordUser">{{ $data_page->from }}</span> to {{ $data_page->to }} of {{ $data_page->total }} records</div>
                    </div>
                    <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end mt-8">
                        <div class="dataTables_paginate paging_simple_numbers" id="paginationUser">
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
            </div>
        </div>
    </form>
</div>
@push('scripts')
    <script type="text/javascript">
        const data_page = {
            'start_record': '{{ $data_page->from }}'
        }
    </script>
    <script src="{{ asset('assets/js/suma/profile/user.js') }}?v={{ time() }}"></script>
@endpush
@endsection
