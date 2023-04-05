@extends('layouts.main.index')
@section('title', 'Shopee')
@section('subtitle', 'Products')
@section('container')
    <div class="row g-0">
        <div class="card card-flush">
            <div class="card-header align-items-center border-0 mt-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Products</span>
                    <span class="text-muted fw-bold fs-7">Form Product marketplace</span>
                </h3>
                <div class="card-toolbar">
                    {{-- <img src="{{ asset('assets/images/logo/shopee_lg.png') }}" class="h-80px" />
                    <img src="{{ asset('assets/images/logo/tokopedia_lg.png') }}" class="h-70px" /> --}}
                </div>
            </div>
            <div class="card-body">
                <div class="fv-row">
                    <div class="fw-bold fs-7 text-gray-600 mb-1">Part Number:</div>
                    <div class="input-group">
                        <input id="inputCariPartNumber" name="cari_part_number" type="text" class="form-control"
                            style="text-transform:uppercase" placeholder="Cari Data Part Number" autocomplete="off" value="{{ $filter->part_number }}">
                        <button id="btnCariPartNumber" type="button" class="btn btn-primary">Cari</button>
                    </div>
                </div>
                <div class="fv-row mt-6">
                    <div id="tableResultPartNumber">
                        <!--start::container-->
                        <div class="table-responsive">
                            <table class="table align-middle gs-0 gy-3">
                                <thead class="border">
                                    <tr class="fs-8 fw-bolder text-muted">
                                        <th class="w-50px ps-3 pe-3 text-center">No</th>
                                        <th class="w-50px ps-3 pe-3 text-center">Part Number</th>
                                        <th class="min-w-300px ps-3 pe-3 text-center">Tokopedia</th>
                                        <th class="min-w-300px ps-3 pe-3 text-center">Shopee</th>
                                        <th class="w-100px ps-3 pe-3 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="border">
                                    @if (!empty($data_all->data) || collect($data_all->data)->count() > 0)
                                    @php
                                        $no = $data_all->from;
                                    @endphp
                                    
                                    @foreach ($data_all->data as $key => $data)
                                    <tr>
                                        <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                            <span class="fs-6 fw-bolder text-gray-800">{{ $no++ }}</span>
                                        </td>
                                        <td class="ps-3 pe-3" style="text-align:start;vertical-align:top;">
                                            <span class="fs-6 fw-bolder text-gray-800">{{ $data->part_number }}</span>
                                            
                                            <span class="fs-8 fw-bold text-gray-600 mt-20">
                                                Tokopedia:
                                                @if ($data->product_id_tokopedia == 0)
                                                    <span class="badge badge-danger fs-8 fw-boldest animation-blink">tidak ditemukan</span>
                                                @else
                                                    <span class="fs-7 fw-bolder text-success ms-2">{{ $data->product_id_tokopedia }}</span>
                                                @endif
                                                <br>
                                                Shopee:
                                                @if ($data->product_id_shopee == 0)
                                                    <span class="badge badge-danger fs-8 fw-boldest animation-blink">tidak ditemukan</span>
                                                @else
                                                    <span class="fs-7 fw-bolder text-success ms-2">{{ $data->product_id_shopee }}</span>
                                                @endif
                                            </span>
                                        </td>
                                    @if ($data->tokopedia->sku == null)
                                        <td class="ps-3 pe-3"
                                            style="text-align:center;vertical-align:center;">
                                            <P class="fs-6 fw-boldest descriptionpart text-dark">
                                                {{-- PRODUK BELUM ADA PADA <span style="color: #008000;">TOKOPEDIA</span> --}}
                                                {{ strtoupper($data->tokopedia->messages) }}
                                            </p>
                                        </td>
                                    @else
                                        <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                            <div class="d-flex mb-7">
                                                <span class="symbol symbol-100px me-5">
                                                    <img src="{{ trim($data->tokopedia->pictures) }}"
                                                            onerror="this.onerror=null; this.src={{ asset('assets/images/background/part_image_not_found.png') }}"
                                                            alt="{{ $data->tokopedia->product_id }}">
                                                    </span>
                                                    <div class="flex-grow-1">
                                                        <div class="row">
                                                            <p class="fs-6 text-gray-800 fw-bolder descriptionpart">
                                                                {{ $data->tokopedia->name }}</p>
                                                                <span
                                                                    class="fs-6 text-gray-700 fw-bolder">{{ trim($data->tokopedia->sku) }}</span>
                                                                <span class="fs-7 text-danger fw-boldest">{{ $data->tokopedia->product_id }}</span>
                                                                <span
                                                                    class="fs-8 text-gray-400 fw-bolder mt-4">Harga:</span>
                                                                <span class="fs-5 text-dark fw-bolder">Rp. {{ number_format($data->tokopedia->price) }}</span>
                                                                <span
                                                                    class="fs-8 text-gray-400 fw-bolder mt-4">Stock:</span>
                                                                <div class="align-items-center">
                                                                    <span class="fs-6 text-dark fw-bolder">{{ number_format($data->tokopedia->stock) }}</span>
                                                                    <span
                                                                        class="fs-7 text-gray-600 fw-bolder ms-2">PCS</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                @endif

                                        @if ($data->shopee->sku == null)
                                            <td class="ps-3 pe-3"
                                                style="text-align:center;vertical-align:center;">
                                                <p class="fs-6 fw-boldest descriptionpart text-dark">
                                                    {{-- PRODUK BELUM ADA PADA <span style="color: #EE4D2D;">SHOPEE</span> --}}
                                                    {{ strtoupper($data->shopee->messages) }}
                                                </p>
                                            </td>
                                        @else
                                            <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                                <div class="d-flex mb-7">
                                                    <span class="symbol symbol-100px me-5">
                                                        <img src="{{ trim($data->shopee->pictures[0]) }}"
                                                            onerror="this.onerror=null; this.src={{ asset('assets/images/background/part_image_not_found.png') }}"
                                                            alt="{{ $data->shopee->product_id }}">
                                                    </span>
                                                    <div class="flex-grow-1">
                                                        <div class="row">
                                                            <p class="fs-6 text-gray-800 fw-bolder descriptionpart">
                                                                {{ $data->shopee->name }}</p>
                                                            <span
                                                                class="fs-6 text-gray-700 fw-bolder">{{ trim($data->shopee->sku) }}</span>
                                                            <span
                                                                class="fs-7 text-danger fw-boldest">{{ $data->shopee->product_id }}</span>
                                                            <span
                                                                class="fs-8 text-gray-400 fw-bolder mt-4">Harga:</span>
                                                            <span class="fs-5 text-dark fw-bolder">Rp.
                                                                {{ number_format($data->shopee->price) }}</span>
                                                            <span
                                                                class="fs-8 text-gray-400 fw-bolder mt-4">Stock:</span>
                                                            <div class="align-items-center">
                                                                <span  class="fs-6 text-dark fw-bolder">{{ number_format($data->shopee->stock) }}</span>
                                                                <span class="fs-7 text-gray-600 fw-bolder ms-2">PCS</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </div>
                                            </td>
                                        @endif
                                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                                @if ($data->tokopedia->sku !== null && $data->shopee->sku === null && $data->shopee->status == 1)
                                                    <a href="{{ route('online.product.form' , base64_encode(json_encode(
                                                        [
                                                            'part_number' => $data->part_number,
                                                            'filter' => $filter,
                                                        ]))) }}" class="btn btn-icon btn-sm btn-light-dark btn-hover-rise">
                                                        <img alt="Logo"
                                                            src="{{ asset('assets/images/logo/shopee.png') }}"
                                                            class="h-20px" />
                                                    </a>
                                                @elseif ($data->tokopedia->sku === null && $data->shopee->sku !== null && $data->tokopedia->status == 1)
                                                    <a href="{{ route('online.product.form' , base64_encode(json_encode(
                                                        [
                                                            'part_number' => $data->part_number,
                                                            'filter' => $filter,
                                                        ]))) }}" class="btn btn-icon btn-sm btn-light-dark btn-hover-rise">
                                                        <img alt="Logo"
                                                            src="{{ asset('assets/images/logo/tokopedia.png') }}"
                                                            class="h-20px" />
                                                    </a>
                                                @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" class="pt-12 pb-12">
                                                    <div class="row text-center pe-10">
                                                        <span class="svg-icon svg-icon-muted">
                                                            <svg class="h-100px w-100px" xmlns="http://www.w3.org/2000/svg"
                                                                width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none">
                                                                <path
                                                                    d="M21.7 18.9L18.6 15.8C17.9 16.9 16.9 17.9 15.8 18.6L18.9 21.7C19.3 22.1 19.9 22.1 20.3 21.7L21.7 20.3C22.1 19.9 22.1 19.3 21.7 18.9Z"
                                                                    fill="currentColor" />
                                                                <path opacity="0.3"
                                                                    d="M11 20C6 20 2 16 2 11C2 6 6 2 11 2C16 2 20 6 20 11C20 16 16 20 11 20ZM11 4C7.1 4 4 7.1 4 11C4 14.9 7.1 18 11 18C14.9 18 18 14.9 18 11C18 7.1 14.9 4 11 4ZM8 11C8 9.3 9.3 8 11 8C11.6 8 12 7.6 12 7C12 6.4 11.6 6 11 6C8.2 6 6 8.2 6 11C6 11.6 6.4 12 7 12C7.6 12 8 11.6 8 11Z"
                                                                    fill="currentColor" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="row text-center pt-8">
                                                        <span class="fs-6 fw-bolder text-gray-500">{{ $data_all->message??'Tidak ada data' }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                        </tbody>
                            </table>
                        </div>
                        @if (!empty($data_all->data) || collect($data_all->data)->count() > 0)
                        <div class="row">
                            <div
                                class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                <div class="dataTables_length">
                                    <select class="form-select form-select-sm form-select-solid" id="per_page">
                                        <option value="10" {{ $data_all->per_page == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ $data_all->per_page == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ $data_all->per_page == 50 ? 'selected' : '' }}>50</option>
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
                        @endif
                        <!--end::container-->
                    </div>
                    </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('assets/js/suma/online/product/product.js') }}?v={{ time() }}"></script>
@endpush