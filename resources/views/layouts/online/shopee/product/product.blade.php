@extends('layouts.main.index')
@section('title', 'Shopee')
@section('subtitle', 'Products')
@section('container')
    <div class="row g-0">
        <div class="card card-flush">
            <div class="card-header align-items-center border-0 mt-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Products Shopee</span>
                    <span class="text-muted fw-bold fs-7">Form update productID marketplace Shopee</span>
                </h3>
                <div class="card-toolbar">
                    <img src="{{ asset('assets/images/logo/shopee_lg.png') }}" class="h-75px" />
                </div>
            </div>
            <div class="card-body">
                <div class="fv-row">
                    <div class="fw-bold fs-7 text-gray-600 mb-1">Part Number:</div>
                    <div class="input-group">
                        <input id="inputCariPartNumber" name="cari_part_number" type="text" class="form-control"
                            style="text-transform:uppercase" placeholder="Cari Data Part Number" autocomplete="off">
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
                                        <th class="min-w-300px ps-3 pe-3 text-center">Suma</th>
                                        <th class="min-w-300px ps-3 pe-3 text-center">Shopee</th>
                                        <th class="w-100px ps-3 pe-3 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="border">
                                    @if (!empty($data_all->data) || collect($data_all->data)->count() > 0)
                                    @foreach ($data_all->data as $key => $data)
                                        <tr>
                                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                                <span class="fs-6 fw-bolder text-gray-800">{{ $key + 1 }}</span>
                                            </td>
                                            <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                                <div class="d-flex mb-7">
                                                    <span class="symbol symbol-100px me-5">
                                                        <img src="{{ $data->images }}"
                                                            onerror="this.onerror=null; this.src='{{  asset('assets/images/background/part_image_not_found.png') }}'"
                                                            alt="{{  trim($data->part_number) }}">
                                                    </span>
                                                    <div class="flex-grow-1">
                                                        <div class="row">
                                                            <p class="fs-6 text-gray-800 fw-bolder descriptionpart">
                                                                {{ strtoupper($data->description) }}</p>
                                                            <span
                                                                class="fs-6 text-gray-700 fw-bolder">{{ strtoupper(trim($data->part_number)) }}</span>
                                                            @if (strtoupper(trim($data->product_id)) == 0)
                                                                <span class="fs-7 text-danger fw-boldest">(ProductID Masih Belum Terisi)</span>
                                                            @else
                                                                <span class="fs-7 text-danger fw-boldest">{{ strtoupper(trim($data->product_id)) }}</span>
                                                            @endif
                                                            <span class="fs-8 text-gray-400 fw-bolder mt-4">Harga:</span>
                                                            <span class="fs-5 text-dark fw-bolder">Rp.
                                                                {{ number_format($data->het) }}</span>
                                                            <span class="fs-8 text-gray-400 fw-bolder mt-4">Stock:</span>
                                                            <div class="align-items-center">
                                                                <span
                                                                    class="fs-6 text-dark fw-bolder">{{ number_format($data->stock) }}</span>
                                                                <span class="fs-7 text-gray-600 fw-bolder ms-2">PCS</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            @if (trim($data->marketplace->sku) == '')
                                                <td class="ps-3 pe-3"
                                                    style="text-align:center;vertical-align:center;">
                                                    <p class="fs-6 text-danger   fw-boldest descriptionpart">PRODUCT ID TIDAK
                                                        TERHUBUNG<br>DENGAN MARKETPLACE</p>
                                                </td>
                                            @else
                                                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                                    <div class="d-flex mb-7">
                                                        <span class="symbol symbol-100px me-5">
                                                            <img src="{{ trim($data->marketplace->pictures[0]) }}"
                                                                onerror="this.onerror=null; this.src={{ asset('assets/images/background/part_image_not_found.png') }}"
                                                                alt="{{ $data->marketplace->product_id }}">
                                                        </span>
                                                        <div class="flex-grow-1">
                                                            <div class="row">
                                                                <p class="fs-6 text-gray-800 fw-bolder descriptionpart">
                                                                    {{ $data->marketplace->name }}</p>
                                                                <span
                                                                    class="fs-6 text-gray-700 fw-bolder">{{ trim($data->marketplace->sku) }}</span>
                                                                <span
                                                                    class="fs-7 text-danger fw-boldest">{{ $data->marketplace->product_id }}</span>
                                                                <span
                                                                    class="fs-8 text-gray-400 fw-bolder mt-4">Harga:</span>
                                                                <span class="fs-5 text-dark fw-bolder">Rp.
                                                                    {{ number_format($data->marketplace->price) }}</span>
                                                                <span
                                                                    class="fs-8 text-gray-400 fw-bolder mt-4">Stock:</span>
                                                                <div class="align-items-center">
                                                                    <span
                                                                        class="fs-6 text-dark fw-bolder">{{ number_format($data->marketplace->stock) }}</span>
                                                                    <span
                                                                        class="fs-7 text-gray-600 fw-bolder ms-2">PCS</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            @endif
                                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                                <button id="btnUpdateProductId" class="btn btn-icon btn-sm btn-danger"
                                                    type="button"
                                                    data-part_number="{{ strtoupper(trim($data->part_number)) }}"
                                                    data-description="{{ strtoupper(trim($data->description)) }}"
                                                    data-product_id="{{ strtoupper(trim($data->product_id)) }}">
                                                    <i class="fa fa-database" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="pt-12 pb-12">
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
                        <!--end::container-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-2" id="modalEditProduct">
        <div class="modal-dialog">
            <div class="modal-content" id="modalEditProductContent">
                <div class="modal-header">
                    <h5 id="modalTitle" name="modalTitle" class="modal-title">Edit Product ID</h5>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <span class="svg-icon svg-icon-muted svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path opacity="0.3"
                                    d="M6 19.7C5.7 19.7 5.5 19.6 5.3 19.4C4.9 19 4.9 18.4 5.3 18L18 5.3C18.4 4.9 19 4.9 19.4 5.3C19.8 5.7 19.8 6.29999 19.4 6.69999L6.7 19.4C6.5 19.6 6.3 19.7 6 19.7Z"
                                    fill="currentColor" />
                                <path
                                    d="M18.8 19.7C18.5 19.7 18.3 19.6 18.1 19.4L5.40001 6.69999C5.00001 6.29999 5.00001 5.7 5.40001 5.3C5.80001 4.9 6.40001 4.9 6.80001 5.3L19.5 18C19.9 18.4 19.9 19 19.5 19.4C19.3 19.6 19 19.7 18.8 19.7Z"
                                    fill="currentColor" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="fv-row">
                        <span id="modalEditProductPartNumber" class="fs-5 text-dark fw-bolder d-block"></span>
                        <span id="modalEditProductDescription" class="fs-6 text-gray-700 fw-boldest"></span>
                    </div>
                    <div class="fv-row mt-8">
                        <label class="form-label">Product Id:</label>
                        <div class="input-group has-validation mb-2">
                            <input id="modalEditProductInputProductId" name="product_id" type="text"
                                class="form-control" style="text-transform:uppercase" placeholder="Inputkan product id"
                                autocomplete="off" onkeypress="return /^[0-9]*$/i.test(event.key)" />
                        </div>
                    </div>
                    <span id="messageProductId">
                    </span>
                </div>
                <div class="modal-footer">
                    <button id="modalEditProductBtnSimpan" name="modalEditProductBtnSimpan" type="button"
                        class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        const url = {
            'daftar_product_id': "{{ route('online.product.shopee.daftar') }}",
            'cek_product_id': "{{ route('online.product.shopee.cek') }}",
            'update_product_id': "{{ route('online.product.shopee.update') }}",
        };
    </script>
    <script src="{{ asset('assets/js/suma/online/shopee/products/form.js') }}?v={{ time() }}"></script>
@endpush
