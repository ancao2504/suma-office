@extends('layouts.main.index')
@section('title', 'Online')
@section('subtitle', 'Update stok shopee detail')
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
@endpush
@section('container')

    <div class="tab-content">
        @if (\Agent::isDesktop())
            <!--begin::Card-->
            <div id="view_table" class="tab-pane fade active show">
                <div class="card card-flush">
                    <div class="card-header align-items-center border-0 mt-4">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="fw-bolder mb-2 text-dark">Pemindahan Antar Lokasi</span>
                            <span class="text-muted fw-bold fs-7">Form pemindahan antar lokasi Shopee</span>
                        </h3>
                        <div class="card-toolbar">
                            <img src="{{ asset('assets/images/logo/shopee_lg.png') }}" class="h-75px" />
                        </div>
                    </div>
                    <!--begin::Card body-->
                    <div class="card-body">
                        <!--begin::Table container-->
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <div id="daftar_table" data-no="{{ trim($filter_header->nomor_dokumen) }}">
                                {{-- table --}}
                            </div>
                            <!--end::Table-->
                        </div>
                        <!--end::Table container-->
                    </div>
                    <!--end::Card body-->
                    {{-- card footer --}}
                    <div class="card-footer">
                        {{-- bat tombol start dan satunya end 2 tombol saja--}}
                        <div class="row">
                            <div class="col-6">
                                @if ($filter_header->filter->marketplace == 0)
                                <a class="btn btn-light-dark btn-hover-rise btn_detail" data-focus="0" onclick="updateSemuaDetail()">
                                Update Semua <img alt="Logo" src="{{ asset('assets/images/logo/shopee.png') }}" class="h-20px me-3"/>
                                </a>
                                @endif
                            </div>
                            <div class="col-6 text-end">
                                <a class="btn btn-secondary btn-hover-rise" data-focus="0" href="{{ route('online.pemindahan.shopee.index', [
                                    'param'        => base64_encode(json_encode([
                                        'search'        => $filter_header->filter->search,
                                        'start_date'    => $filter_header->filter->start_date,
                                        'end_date'      => $filter_header->filter->end_date,
                                        'page'          => $filter_header->filter->page,
                                        'per_page'      => $filter_header->filter->per_page
                                    ])),
                                ]) }}">
                                    Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Card-->
        @else
            <div id="daftar_table" class="tab-pane fade active show" data-no="{{ trim($filter_header->nomor_dokumen) }}">
                <div class="card mt-10 p-5">
                    {{-- bat tombol start dan satunya end 2 tombol saja--}}
                    <div class="row">
                        <div class="col-8">
                            @if ($filter_header->filter->marketplace == 0)
                            <a class="btn btn-light-dark btn-hover-rise btn_detail" data-focus="0" onclick="updateSemuaDetail()">
                            Update Semua <img alt="Logo" src="http://localhost:2022/suma-pmo/public/assets/images/logo/shopee.png" class="h-20px me-3"/>
                            </a>
                            @endif
                        </div>
                        <div class="col-4 text-end">
                            <a class="btn btn-secondary btn-hover-rise" data-focus="0" href="{{ route('online.pemindahan.shopee.index', [
                                'param'        => base64_encode(json_encode([
                                    'search'        => $filter_header->filter->search,
                                    'start_date'    => $filter_header->filter->start_date,
                                    'end_date'      => $filter_header->filter->end_date,
                                    'page'          => $filter_header->filter->page,
                                    'per_page'      => $filter_header->filter->per_page
                                ])),
                            ]) }}">
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        <script language="JavaScript"
            src="{{ asset('assets/js/suma/online/shopee/pemindahan/PemindahanDetail.js') }}?v={{ time() }}"></script>
    @endpush
@endsection