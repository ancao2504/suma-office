@extends('layouts.main.index')
@section('title','Shopee')
@section('subtitle','Orders')
@section('container')
<div class="row g-0">
    <div class="card card-flush">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Orders</span>
                <span class="text-muted fw-bold fs-7">Daftar order marketplace shopee</span>
                <span class="text-muted fw-bold fs-7 mt-2">Periode
                    <span class="text-dark fw-bolder fs-7">{{ date('d F Y', strtotime($data_filter->start_date)) }}</span> s/d
                    <span class="text-dark fw-bolder fs-7">{{ date('d F Y', strtotime($data_filter->end_date)) }}</span>
                </span>
            </h3>
            <div class="card-toolbar">
                <img src="{{ asset('assets/images/logo/shopee_lg.png') }}" class="h-75px" />
            </div>
        </div>
        <div class="card-header align-items-center border-0">
            <div class="align-items-start flex-column">
                <div class="input-group">
                    <select id="selectFields" name="fields" class="form-select w-md-200px" aria-label="Status">
                        <option value="create_time" @if($data_filter->fields == 'create_time') selected @endif>Pesanan Dibuat</option>
                        <option value="update_time" @if($data_filter->fields == 'update_time') selected @endif>Pesanan Diupdate</option>
                    </select>
                    <input id="inputStartDate" name="start_date" class="form-control w-md-150px" placeholder="Dari Tanggal"
                        value="{{ $data_filter->start_date }}">
                    <span class="input-group-text">s/d</span>
                    <input id="inputEndDate" name="end_date" class="form-control w-md-150px" placeholder="Sampai Dengan"
                        value="{{ $data_filter->end_date }}">
                    <button id="btnFilterMasterData" class="btn btn-icon btn-primary" type="button">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="position-relative w-md-200px me-md-2">
                    <select id="selectStatus" name="status" class="form-select" aria-label="Status">
                        <option value="" @if($data_filter->status == '') selected @endif>ALL</option>
                        <option value="UNPAID" @if($data_filter->status == 'UNPAID') selected @endif>Unpaid</option>
                        <option value="READY_TO_SHIP" @if($data_filter->status == 'READY_TO_SHIP') selected @endif>Ready To Ship</option>
                        <option value="PROCESSED" @if($data_filter->status == 'PROCESSED') selected @endif>Processed</option>
                        <option value="SHIPPED" @if($data_filter->status == 'SHIPPED') selected @endif>Shipped</option>
                        <option value="COMPLETED" @if($data_filter->status == 'COMPLETED') selected @endif>Completed</option>
                        <option value="IN_CANCEL" @if($data_filter->status == 'IN_CANCEL') selected @endif>In Cancel</option>
                        <option value="CANCELLED" @if($data_filter->status == 'CANCELLED') selected @endif>Canceled</option>
                        <option value="INVOICE_PENDING" @if($data_filter->status == 'INVOICE_PENDING') selected @endif>Invoice Pending</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row g-0 mt-4">
            <div class="ms-10">
                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder">
                    <li class="nav-item mt-2">
                        <div id="navSemuaProses" class="nav-link text-active-primary ms-0 me-10 py-5 @if($data_filter->status != 'READY_TO_SHIP') active @endif"
                            style="cursor: pointer;">Semua Invoice</div>
                    </li>
                    <li class="nav-item mt-2">
                        <div id="navBelumProses" class="nav-link text-active-primary ms-0 me-10 py-5 @if($data_filter->status == 'READY_TO_SHIP') active @endif"
                            style="cursor: pointer;">Belum Diproses</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div id="postOrder">
    <!--Start List Order-->
    @include('layouts.online.shopee.orders.orderlist')
    <!--End List Order-->
</div>

<div class="modal fade" tabindex="-2" id="modalRequestPickupShopee">
    <div class="modal-dialog">
        <div class="modal-content" id="modalRequestPickupShopeeContent">
            <form action="#">
                <div class="modal-header">
                    <h5 id="modalTitle" name="modalTitle" class="modal-title">Request Pickup Shopee</h5>
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
                        <label for="inputNomorInvoice" class="form-label">Nomor Invoice</label>
                        <input id="inputNomorInvoice" type="text" class="form-control" placeholder="" readonly/>
                    </div>
                    <div class="fv-row mt-8">
                        <div class="w-lg-100" data-kt-buttons="true">
                            <label class="d-flex flex-stack mb-5 cursor-pointer">
                                <span class="d-flex align-items-center me-2">
                                    <span class="symbol symbol-50px me-6">
                                        <span class="symbol-label bg-light-danger">
                                            <span class="svg-icon svg-icon-1 svg-icon-danger">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M18.041 22.041C18.5932 22.041 19.041 21.5932 19.041 21.041C19.041 20.4887 18.5932 20.041 18.041 20.041C17.4887 20.041 17.041 20.4887 17.041 21.041C17.041 21.5932 17.4887 22.041 18.041 22.041Z" fill="currentColor"/>
                                                    <path opacity="0.3" d="M6.04095 22.041C6.59324 22.041 7.04095 21.5932 7.04095 21.041C7.04095 20.4887 6.59324 20.041 6.04095 20.041C5.48867 20.041 5.04095 20.4887 5.04095 21.041C5.04095 21.5932 5.48867 22.041 6.04095 22.041Z" fill="currentColor"/>
                                                    <path opacity="0.3" d="M7.04095 16.041L19.1409 15.1409C19.7409 15.1409 20.141 14.7409 20.341 14.1409L21.7409 8.34094C21.9409 7.64094 21.4409 7.04095 20.7409 7.04095H5.44095L7.04095 16.041Z" fill="currentColor"/>
                                                    <path d="M19.041 20.041H5.04096C4.74096 20.041 4.34095 19.841 4.14095 19.541C3.94095 19.241 3.94095 18.841 4.14095 18.541L6.04096 14.841L4.14095 4.64095L2.54096 3.84096C2.04096 3.64096 1.84095 3.04097 2.14095 2.54097C2.34095 2.04097 2.94096 1.84095 3.44096 2.14095L5.44096 3.14095C5.74096 3.24095 5.94096 3.54096 5.94096 3.84096L7.94096 14.841C7.94096 15.041 7.94095 15.241 7.84095 15.441L6.54096 18.041H19.041C19.641 18.041 20.041 18.441 20.041 19.041C20.041 19.641 19.641 20.041 19.041 20.041Z" fill="currentColor"/>
                                                </svg>
                                            </span>
                                        </span>
                                    </span>
                                    <span class="d-flex flex-column">
                                        <span class="fw-bolder fs-6">Drop Off</span>
                                        <span class="fs-7 text-muted">Penjual mengantar paket ke pihak logistic</span>
                                    </span>
                                </span>
                                <span class="form-check form-check-custom form-check-solid">
                                    <input id="inputJenisMetodePengiriman" class="form-check-input" type="radio" name="category" value="0" disabled>
                                </span>
                            </label>
                            <label class="d-flex flex-stack mb-5 cursor-pointer">
                                <span class="d-flex align-items-center me-2">
                                    <span class="symbol symbol-50px me-6">
                                        <span class="symbol-label bg-light-primary">
                                            <span class="svg-icon svg-icon-1 svg-icon-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M20 8H16C15.4 8 15 8.4 15 9V16H10V17C10 17.6 10.4 18 11 18H16C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18H21C21.6 18 22 17.6 22 17V13L20 8Z" fill="currentColor"/>
                                                    <path opacity="0.3" d="M20 18C20 19.1 19.1 20 18 20C16.9 20 16 19.1 16 18C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18ZM15 4C15 3.4 14.6 3 14 3H3C2.4 3 2 3.4 2 4V13C2 13.6 2.4 14 3 14H15V4ZM6 16C4.9 16 4 16.9 4 18C4 19.1 4.9 20 6 20C7.1 20 8 19.1 8 18C8 16.9 7.1 16 6 16Z" fill="currentColor"/>
                                                </svg>
                                            </span>
                                        </span>
                                    </span>
                                    <span class="d-flex flex-column">
                                        <span class="fw-bolder fs-6">Pickup</span>
                                        <span class="fs-7 text-muted">Pihak logistic mengambil paket ke penjual</span>
                                    </span>
                                </span>
                                <span class="form-check form-check-custom form-check-solid">
                                    <input id="inputJenisMetodePengiriman" class="form-check-input" type="radio" name="category" value="1" checked>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="fv-row mt-8">
                        <label for="selectTanggalJamPickup" class="form-label">Tanggal & Jam Pickup</label>
                        <select id="selectTanggalJamPickup" class="form-select" aria-label="Select example">
                            <option value="">Pilih tanggal & jam pickup</option>
                        </select>
                    </div>
                    <div class="fv-row mt-8">
                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                            <span class="svg-icon svg-icon-2tx svg-icon-warning me-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"></rect>
                                    <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="currentColor"></rect>
                                    <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="currentColor"></rect>
                                </svg>
                            </span>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-bold">
                                    <h4 class="text-gray-900 fw-bolder">Alamat Toko:</h4>
                                    <div id="inputIdAlamatSeller" class="fs-6 text-gray-700"></div>
                                    <div id="inputAlamatSeller" class="fs-6 text-gray-700"></div>
                                    <div id="inputKotaSeller" class="fs-6 text-gray-700"></div>
                                    <div id="inputProvinsiSeller" class="fs-6 text-gray-700"></div>
                                    <div id="inputKodePosSeller" class="fs-6 text-gray-700"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="text-end">
                        <button id="btnSimpanRequestPickupShopee" name="btnSimpanRequestPickupShopee" type="button" class="btn btn-primary text-end">Simpan</button>
                        <button id="btnFilterClose" name="btnClose" type="button" class="btn btn-light text-end" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-2" id="modalRequestPickupShopee">
    <div class="modal-dialog">
        <div class="modal-content" id="modalRequestPickupShopeeContent">
            <form action="#">
                <div class="modal-header">
                    <h5 id="modalTitle" name="modalTitle" class="modal-title">Request Pickup Shopee</h5>
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
                        <label for="inputNomorInvoice" class="form-label">Nomor Invoice</label>
                        <input id="inputNomorInvoice" type="text" class="form-control" placeholder="" readonly/>
                    </div>
                    <div class="fv-row mt-8">
                        <div class="w-lg-100" data-kt-buttons="true">
                            <label class="d-flex flex-stack mb-5 cursor-pointer">
                                <span class="d-flex align-items-center me-2">
                                    <span class="symbol symbol-50px me-6">
                                        <span class="symbol-label bg-light-danger">
                                            <span class="svg-icon svg-icon-1 svg-icon-danger">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M18.041 22.041C18.5932 22.041 19.041 21.5932 19.041 21.041C19.041 20.4887 18.5932 20.041 18.041 20.041C17.4887 20.041 17.041 20.4887 17.041 21.041C17.041 21.5932 17.4887 22.041 18.041 22.041Z" fill="currentColor"/>
                                                    <path opacity="0.3" d="M6.04095 22.041C6.59324 22.041 7.04095 21.5932 7.04095 21.041C7.04095 20.4887 6.59324 20.041 6.04095 20.041C5.48867 20.041 5.04095 20.4887 5.04095 21.041C5.04095 21.5932 5.48867 22.041 6.04095 22.041Z" fill="currentColor"/>
                                                    <path opacity="0.3" d="M7.04095 16.041L19.1409 15.1409C19.7409 15.1409 20.141 14.7409 20.341 14.1409L21.7409 8.34094C21.9409 7.64094 21.4409 7.04095 20.7409 7.04095H5.44095L7.04095 16.041Z" fill="currentColor"/>
                                                    <path d="M19.041 20.041H5.04096C4.74096 20.041 4.34095 19.841 4.14095 19.541C3.94095 19.241 3.94095 18.841 4.14095 18.541L6.04096 14.841L4.14095 4.64095L2.54096 3.84096C2.04096 3.64096 1.84095 3.04097 2.14095 2.54097C2.34095 2.04097 2.94096 1.84095 3.44096 2.14095L5.44096 3.14095C5.74096 3.24095 5.94096 3.54096 5.94096 3.84096L7.94096 14.841C7.94096 15.041 7.94095 15.241 7.84095 15.441L6.54096 18.041H19.041C19.641 18.041 20.041 18.441 20.041 19.041C20.041 19.641 19.641 20.041 19.041 20.041Z" fill="currentColor"/>
                                                </svg>
                                            </span>
                                        </span>
                                    </span>
                                    <span class="d-flex flex-column">
                                        <span class="fw-bolder fs-6">Drop Off</span>
                                        <span class="fs-7 text-muted">Penjual mengantar paket ke pihak logistic</span>
                                    </span>
                                </span>
                                <span class="form-check form-check-custom form-check-solid">
                                    <input id="inputJenisMetodePengiriman" class="form-check-input" type="radio" name="category" value="0" disabled>
                                </span>
                            </label>
                            <label class="d-flex flex-stack mb-5 cursor-pointer">
                                <span class="d-flex align-items-center me-2">
                                    <span class="symbol symbol-50px me-6">
                                        <span class="symbol-label bg-light-primary">
                                            <span class="svg-icon svg-icon-1 svg-icon-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M20 8H16C15.4 8 15 8.4 15 9V16H10V17C10 17.6 10.4 18 11 18H16C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18H21C21.6 18 22 17.6 22 17V13L20 8Z" fill="currentColor"/>
                                                    <path opacity="0.3" d="M20 18C20 19.1 19.1 20 18 20C16.9 20 16 19.1 16 18C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18ZM15 4C15 3.4 14.6 3 14 3H3C2.4 3 2 3.4 2 4V13C2 13.6 2.4 14 3 14H15V4ZM6 16C4.9 16 4 16.9 4 18C4 19.1 4.9 20 6 20C7.1 20 8 19.1 8 18C8 16.9 7.1 16 6 16Z" fill="currentColor"/>
                                                </svg>
                                            </span>
                                        </span>
                                    </span>
                                    <span class="d-flex flex-column">
                                        <span class="fw-bolder fs-6">Pickup</span>
                                        <span class="fs-7 text-muted">Pihak logistic mengambil paket ke penjual</span>
                                    </span>
                                </span>
                                <span class="form-check form-check-custom form-check-solid">
                                    <input id="inputJenisMetodePengiriman" class="form-check-input" type="radio" name="category" value="1" checked>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="fv-row mt-8">
                        <label for="selectTanggalJamPickup" class="form-label">Tanggal & Jam Pickup</label>
                        <select id="selectTanggalJamPickup" class="form-select" aria-label="Select example">
                            <option value="">Pilih tanggal & jam pickup</option>
                        </select>
                    </div>
                    <div class="fv-row mt-8">
                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                            <span class="svg-icon svg-icon-2tx svg-icon-warning me-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"></rect>
                                    <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="currentColor"></rect>
                                    <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="currentColor"></rect>
                                </svg>
                            </span>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-bold">
                                    <h4 class="text-gray-900 fw-bolder">Alamat Toko:</h4>
                                    <div id="inputIdAlamatSeller" class="fs-6 text-gray-700"></div>
                                    <div id="inputAlamatSeller" class="fs-6 text-gray-700"></div>
                                    <div id="inputKotaSeller" class="fs-6 text-gray-700"></div>
                                    <div id="inputProvinsiSeller" class="fs-6 text-gray-700"></div>
                                    <div id="inputKodePosSeller" class="fs-6 text-gray-700"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="text-end">
                        <button id="btnSimpanRequestPickupShopee" name="btnSimpanRequestPickupShopee" type="button" class="btn btn-primary text-end">Simpan</button>
                        <button id="btnFilterClose" name="btnClose" type="button" class="btn btn-light text-end" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const url = {
        'daftar_order': "{{ route('online.orders.shopee.daftar') }}",
        'proses_cetak_label': "{{ route('online.serahterima.form.cetak-label-shopee') }}",
        'proses_request_pickup_shopee': "{{ route('online.serahterima.form.shopee-request-pickup') }}",
        'data_request_pickup_shopee': "{{ route('online.serahterima.form.data-shopee-request-pickup') }}"
    }
</script>
<script src="{{ asset('assets/js/suma/online/shopee/orders/daftar.js') }}?v={{ time() }}"></script>
@endpush
@endsection

