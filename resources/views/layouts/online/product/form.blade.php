@extends('layouts.main.index')
@section('title', 'Shopee')
@section('subtitle', 'Products')
@section('container')
    <div class="row g-0">
        <div class="card card-flush shadow">
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
                <div class="fv-row mb-7">
                    <label class="d-block fw-bold fs-6 mb-5">Foto Product :</label>
                    <span class="symbol symbol-100px me-3 mt-3 border">
                        <img src="{{ asset('assets/images/background/part_image_not_found.png') }}"
                            onerror="this.onerror=null; this.src={{ asset('assets/images/background/part_image_not_found.png') }}"
                            alt="{{ asset('assets/images/background/part_image_not_found.png') }}">
                    </span>
                </div>

                <div class="fv-row mb-7">
                    <label class="form-label"><span class="required"></span>Nama Produk :</label>
                    <input type="text" name="nama_produk" class="form-control mb-3 mb-lg-0"
                        placeholder="Mohon masukan" value="" required>
                </div>
                <div class="fv-row mb-7">
                    <label class="form-label"><span class="required"></span>Deskripsi Produk :</label>
                    <textarea name="deskripsi_produk" class="form-control mb-3 mb-lg-0" rows="3" placeholder="Mohon masukan" required data-kt-autosize="true"  maxlength="2000"></textarea>
                </div>
            </div>
        </div>

        <div class="card card-flush shadow mt-6">
            <div class="card-header align-items-center border-0 mt-4">
                <div class="card-body pt-4 px-0">
                    <ul class="nav nav-pills nav-pills-custom item position-relative mx-9 mb-9">
                        <li class="nav-item col-6 mx-0 p-0">
                            <a class="nav-link d-flex justify-content-center w-100 border-0 h-100 active" data-bs-toggle="pill" href="#shopee">
                                <span class="nav-text text-gray-800 fw-bolder fs-6 mb-3">Shopee</span>
                                <span class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded"></span>
                            </a>
                        </li>
                        <li class="nav-item col-6 mx-0 px-0">
                            <a class="nav-link d-flex justify-content-center w-100 border-0 h-100" data-bs-toggle="pill" href="#tokopedia">
                                <span class="nav-text text-gray-800 fw-bolder fs-6 mb-3">Tokopedia</span>
                                <span class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded"></span>
                            </a>
                        </li>
                        <span class="position-absolute z-index-1 bottom-0 w-100 h-4px bg-light rounded"></span>
                    </ul>
                    <div class="tab-content px-9 pe-7 me-3 mb-2">
                        <div class="tab-pane fade active show" id="shopee">
                            <div class="fv-row mb-7">
                                <label class="form-label"><span class="required"></span>Kategori :</label>
                                <input type="text" name="kategori" class="form-control mb-3 mb-lg-0"
                                    placeholder="Mohon masukan" value="" required>
                            </div>
                            <div class="fv-row mb-7">
                                <label class="form-label"><span class="required"></span>Stock :</label>
                                <input type="text" name="stock" class="form-control mb-3 mb-lg-0"
                                    placeholder="Mohon masukan" value="" required>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tokopedia">
                            {{-- tokopedia --}}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                
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
    <script src="{{ asset('assets/js/suma/online/product/form.js') }}?v={{ time() }}"></script>
@endpush
