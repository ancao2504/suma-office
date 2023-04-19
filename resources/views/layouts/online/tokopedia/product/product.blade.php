@extends('layouts.main.index')
@section('title','Tokopedia')
@section('subtitle','Products')
@section('container')
<div class="row g-0">
    <div class="card card-flush shadow">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Products Tokopedia</span>
                <span class="text-muted fw-bold fs-7">Form update productID marketplace tokopedia</span>
            </h3>
            <div class="card-toolbar">
                <img src="{{ asset('assets/images/logo/tokopedia_lg.png') }}" class="h-75px" />
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
                <div id="tableResultPartNumber"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-2" id="modalEditProduct">
    <div class="modal-dialog">
        <div class="modal-content" id="modalEditProductContent">
            <div class="modal-header">
                <h5 id="modalTitle" name="modalTitle" class="modal-title">Edit Product ID</h5>
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
                    <span id="modalEditProductPartNumber" class="fs-5 text-dark fw-bolder d-block"></span>
                    <span id="modalEditProductDescription" class="fs-6 text-gray-700 fw-boldest"></span>
                </div>
                <div class="fv-row mt-8">
                    <label class="form-label">Product Id:</label>
                    <div class="input-group has-validation mb-2">
                        <input id="modalEditProductInputProductId" name="product_id" type="text" class="form-control"
                            style="text-transform:uppercase" placeholder="Inputkan product id" autocomplete="off">
                    </div>
                </div>
                <span id="messageProductId"></span>
            </div>
            <div class="modal-footer">
                <button id="modalEditProductBtnSimpan" name="modalEditProductBtnSimpan" type="button" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const url = {
        'daftar_product_id': "{{ route('online.product.tokopedia.daftar') }}",
        'cek_product_id': "{{ route('online.product.tokopedia.cek') }}",
        'update_product_id': "{{ route('online.product.tokopedia.update') }}",
    }
</script>
<script src="{{ asset('assets/js/suma/online/tokopedia/products/form.js') }}?v={{ time() }}"></script>
@endpush
@endsection
