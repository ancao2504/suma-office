@extends('layouts.main.index')
@section('title','Orders')
@section('subtitle','Cart')
@section('container')
    <div class="row g-0">
        @if($message = Session::get('failed'))
        <div class="alert alert-dismissible bg-danger d-flex flex-column flex-sm-row p-5 mb-10">
            <span class="svg-icon svg-icon-2hx svg-icon-light me-4 mb-5 mb-sm-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.3" d="M12 22C13.6569 22 15 20.6569 15 19C15 17.3431 13.6569 16 12 16C10.3431 16 9 17.3431 9 19C9 20.6569 10.3431 22 12 22Z" fill="currentColor"></path>
                    <path d="M19 15V18C19 18.6 18.6 19 18 19H6C5.4 19 5 18.6 5 18V15C6.1 15 7 14.1 7 13V10C7 7.6 8.7 5.6 11 5.1V3C11 2.4 11.4 2 12 2C12.6 2 13 2.4 13 3V5.1C15.3 5.6 17 7.6 17 10V13C17 14.1 17.9 15 19 15ZM11 10C11 9.4 11.4 9 12 9C12.6 9 13 8.6 13 8C13 7.4 12.6 7 12 7C10.3 7 9 8.3 9 10C9 10.6 9.4 11 10 11C10.6 11 11 10.6 11 10Z" fill="currentColor"></path>
                </svg>
            </span>

            <div class="d-flex flex-column text-light pe-0 pe-sm-10">
                <h4 class="mb-2 text-light">Informasi</h4>
                <h6 class="mb-2 text-light">{{ $message }}</h6>
            </div>
            <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                <span class="svg-icon svg-icon-2x svg-icon-light">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor"></rect>
                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor"></rect>
                    </svg>
                </span>
            </button>
        </div>
        @endif

        <form id="cartForm" name="cartForm" class="form d-flex flex-column flex-lg-row fv-plugins-bootstrap5 fv-plugins-framework">
            @csrf
            <div class="w-100 flex-lg-row-auto w-lg-300px mb-7 me-7 me-lg-10">
                <div class="card card-flush py-4">
                    <div class="card-body pt-0">
                        <div class="d-flex flex-column">
                            <div class="fv-row mt-4">
                                <label class="form-label required">Order ID:</label>
                                <input id="inputViewKodeCart" name="kode_cart" type="text" class="form-control form-control-solid" readonly
                                    @if(isset($kode_cart)) value="{{ $kode_cart }}" @else value="{{ old('kode_cart') }}"@endif>
                            </div>
                            <div class="fv-row mt-8">
                                <label class="form-label required">Tanggal:</label>
                                <input id="inputViewTanggal" name="tanggal" type="text" class="form-control form-control-solid" readonly
                                    @if(isset($tanggal)) value="{{ $tanggal }}" @else value="{{ old('tanggal') }}"@endif>
                            </div>
                            <div class="fv-row mt-8">
                                <label class="required form-label">Salesman:</label>
                                <input id="inputViewSalesman" name="salesman" type="text" style="text-transform: uppercase" class="form-control form-control-solid"
                                    @if(isset($salesman)) value="{{ $salesman }}" @else value="{{ old('salesman') }}"@endif readonly required>
                            </div>
                            <div class="fv-row mt-8">
                                <label class="required form-label">Dealer:</label>
                                <input id="inputViewDealer" name="dealer" style="text-transform: uppercase" type="text" class="form-control form-control-solid"
                                    @if(isset($dealer)) value="{{ $dealer }}" @else value="{{ old('dealer') }}"@endif readonly required>
                            </div>
                            <div class="fv-row mt-8">
                                <label class="required form-label">BO / Tidak BO:</label>
                                <input id="inputViewBackOrder" name="back_order" type="text" class="form-control form-control-solid"
                                    @if(isset($back_order)) @if($back_order == 'T') value="Tidak BO" @else value="BO" @endif @else value="{{ old('back_order') }}"@endif readonly required>
                            </div>
                            <div class="fv-row mt-8">
                                <label class="form-label">Keterangan:</label>
                                <input id="inputViewKeterangan" name="keterangan" type="text" class="form-control form-control-solid"
                                    @if(isset($keterangan)) value="{{ $keterangan }}" @else value="{{ old('keterangan') }}"@endif readonly>
                            </div>
                            <button id="btnEditCart" name="btnEditCart" type="button" class="btn btn-primary fw-bold text-uppercase mt-8" data-bs-toggle="modal" data-bs-target="#modalCartHeader">Edit Header Cart</button>
                            <button id="btnCheckOut" name="btnCheckOut" type="button" class="btn btn-danger fw-bold text-uppercase mt-4">Check Out</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column flex-lg-row-fluid gap-7 gap-lg-10">
                <div class="card card-flush py-4">
                    <div class="card-body pt-0">
                        <div class="d-flex flex-column gap-10">
                            <div class="fv-row mt-2">
                                <label class="form-label">Tambah item atau kosongkan data cart:</label>
                                <div class="fv-row">
                                    <a class="btn btn-primary waves-effect text-left" role="button" href="{{ route('parts.part-number') }}" id="btnTambahCart" name="btnTambahCart">Tambah Item</a>
                                    <button type="button" class="btn btn-danger waves-effect text-left" id="btnResetCart" name="btnResetCart">Kosongkan Cart</button>
                                </div>
                            </div>
                            <div class="fv-row">
                                <label class="form-label">Isi data cart melalui import excel:</label>
                                <div class="fv-row">
                                    <input type="button" name="btnImportExcelCart" id="btnImportExcelCart" class="btn btn-success" value="Proses File Excel">
                                    <input type="button" name="btnSampleExcelCart" id="btnSampleExcelCart" class="btn btn-light btn-active-light-primary" value="Contoh File Excel">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(strtoupper(trim($device)) == 'DESKTOP')
                <div id="cardCartDetail" class="card card-flush py-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <div id='tableDetailCart'></div>
                        </div>
                    </div>
                </div>
                @else
                <div id="cardCartDetail" class="py-4">
                    <div id='tableDetailCart'></div>
                </div>
                @endif

            </div>
        </form>
    </div>

    <div class="modal fade" tabindex="-1" id="modalCartHeader">
        <div class="modal-dialog">
            <div class="modal-content" id="modalContentCartHeader">
                <form id="formModalCartHeader" name="formModalCartHeader" autofill="off" autocomplete="off" method="post" action="{{ route('orders.cart-simpan-draft') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Cart</h5>
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
                            <label class="form-label required">Order ID:</label>
                            <input id="inputKodeCart" name="kode_cart" type="text" class="form-control form-control-solid" readonly required
                                @if(isset($kode_cart)) value="{{ $kode_cart }}" @else value="{{ old('kode_cart') }}"@endif>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label required">Tanggal:</label>
                            <input id="inputTanggal" name="tanggal" type="text" class="form-control form-control-solid" readonly required
                                @if(isset($tanggal)) value="{{ $tanggal }}" @else value="{{ old('tanggal') }}"@endif>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label required">Salesman:</label>
                            <div class="input-group">
                                <input id="inputSalesman" name="salesman" type="search" class="form-control" placeholder="Pilih Data Salesman" readonly required
                                    @if(isset($salesman)) value="{{ $salesman }}" @else value="{{ old('kode_sales') }}"@endif>
                                @if($role_id != 'MD_H3_SM')
                                    @if($role_id != 'D_H3')
                                    <button id="btnPilihSalesman" name="btnPilihSalesman" class="btn btn-icon btn-primary" type="button"
                                        data-toggle="modal" data-target="#salesmanSearchModal">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label required">Dealer:</label>
                            <div class="input-group">
                                <input id="inputDealer" name="dealer" type="search" class="form-control" placeholder="Pilih Data Dealer" readonly required
                                    @if(isset($dealer)) value="{{ $dealer }}" @else value="{{ old('dealer') }}"@endif>
                                @if($role_id != 'D_H3')
                                <button id="btnPilihDealer" name="btnPilihDealer" class="btn btn-icon btn-primary" type="button"
                                    data-toggle="modal" data-target="#dealerSearchModal">
                                    <i class="fa fa-search"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="required form-label">BO / Tidak BO:</label>
                            <select id="selectBo" name="back_order" class="form-select">
                                <option value="B" @if($back_order == 'B') {{"selected"}} @endif>BO</option>
                                <option value="T" @if($back_order == 'T') {{"selected"}} @endif>Tidak BO</option>
                            </select>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label">Keterangan:</label>
                            <input id="inputKeterangan" name="keterangan" type="text" class="form-control"
                                @if(isset($keterangan)) value="{{ $keterangan }}" @else value="{{ old('keterangan') }}"@endif>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="btnSimpanHeaderCart">Simpan</button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="modalCartDetail" data-backdrop="false">
        <div class="modal-dialog">
            <div class="modal-content" id="modalContentCartDetail">
                <form id="modalCartDetailForm" name="modalCartDetailForm" autofill="off" autocomplete="off" method="POST" action="#">
                    @csrf
                    <div class="modal-header">
                        <h5 id="modalTitle" name="modalTitle" class="modal-title"></h5>
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
                        <span id="messageErrorPartNumber"></span>
                        <div class="fv-row">
                            <label class="fs-7 fw-bold form-label">Images:</label>
                            <span id="modalEditCartPicturePart"></span>
                        </div>
                        <div class="fv-row mt-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="fs-7 fw-bold form-label required">Part Number:</label>
                                    <input id="modalEditCartInputPartNumber" name="part_number" type="text" class="form-control form-control-solid" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="fs-7 fw-bold form-label required">Produk:</label>
                                    <input id="modalEditCartInputProduk" name="produk" type="text" class="form-control form-control-solid" required>
                                </div>
                            </div>
                        </div>
                        <div class="fv-row mt-6">
                            <label class="fs-7 fw-bold form-label required">Description:</label>
                            <input id="modalEditCartInputDescription" name="description" type="text" class="form-control form-control-solid" required>
                        </div>
                        <div class="fv-row mt-6" @if(session()->get('app_user_role_id') == 'D_H3') hidden @endif>
                            <label class="fs-7 fw-bold form-label required">TPC:</label>
                            <select id="modalEditCartSelectTpc" class="form-select" data-control="select2" data-placeholder="Select an option" data-hide-search="true">
                                <option value="14">TPC 14</option>
                                <option value="20">TPC 20</option>
                            </select>
                        </div>
                        <div class="fv-row mt-6">
                            <label class="fs-7 fw-bold form-label required">Jumlah Order:</label>
                            <div class="input-group"
                                data-kt-dialer="true"
                                data-kt-dialer-min="1"
                                data-kt-dialer-step="1">
                                <button class="btn btn-icon btn-outline btn-outline-secondary" type="button" data-kt-dialer-control="decrease">
                                    <i class="bi bi-dash fs-1"></i>
                                </button>
                                <input id="modalEditCartInputJmlOrder" type="number" min="1" class="form-control text-end" placeholder="Jumlah Order" value="1" data-kt-dialer-control="input" />
                                <button class="btn btn-icon btn-outline btn-outline-secondary" type="button" data-kt-dialer-control="increase">
                                    <i class="bi bi-plus fs-1"></i>
                                </button>
                            </div>
                        </div>
                        <div class="fv-row mt-6" @if(session()->get('app_user_role_id') == 'D_H3') hidden @endif>
                            <label class="fs-7 fw-bold form-label required">Harga:</label>
                            <input id="modalEditCartInputHarga" name="harga" type="text" maxlength="15" class="form-control text-end" required>
                        </div>

                        <div class="fv-row" @if(session()->get('app_user_role_id') == 'D_H3') hidden @endif>
                            <div class="row">
                                <div class="col-md-6 mt-6">
                                    <label class="fs-7 fw-bold form-label required">Discount:</label>
                                    <input id="modalEditCartInputDiscount" name="discount" inputmode="text" maxlength="5" class="form-control text-end" required>
                                </div>
                                <div class="col-md-6 mt-6">
                                    <label class="fs-7 fw-bold form-label required">Discount Plus:</label>
                                    <input id="modalEditCartInputDiscountPlus" name="discount_plus" inputmode="text" maxlength="5" class="form-control text-end" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="btnSimpanDetailCart" type="button" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="modalCartImportExcel">
        <div class="modal-dialog">
            <div class="modal-content" id="modalCartContentImportExcel">
                <form id="formModalCartImportExcel" name="formModalCartImportExcel" enctype="multipart/form-data" autofill="off" autocomplete="off" method="post" action="#">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Import Excel</h5>
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
                        <div class="fv-row" hidden>
                            <input id="inputImportCartSalesman" name="salesman" type="text" style="text-transform: uppercase" class="form-control form-control-solid"
                                @if(isset($salesman)) value="{{ $salesman }}" @else value="{{ old('salesman') }}"@endif readonly required>
                            <input id="inputImportCartDealer" name="dealer" style="text-transform: uppercase" type="text" class="form-control form-control-solid"
                                @if(isset($dealer)) value="{{ $dealer }}" @else value="{{ old('dealer') }}"@endif readonly required>
                        </div>
                        <div class="fv-row">
                            <label class="form-label required">Pilih File Excel:</label>
                            <div class="row">
                                <label class="control-label">
                                    <input type="file" name="selectFileExcel" id="selectFileExcel" class="form-control"/>
                                </label>
                            </div>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label required">Format Part Number:</label>
                            <input type="radio" class="btn-check" name="jenis_part_number" value="part_number" checked="checked"  id="optionPartNumber"/>
                            <label class="btn btn-outline btn-outline-dashed btn-outline-default p-4 d-flex align-items-center mb-5" for="optionPartNumber">
                                <span class="svg-icon svg-icon-3x me-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z" fill="currentColor"/>
                                        <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="currentColor"/>
                                    </svg>
                                </span>
                                <span class="d-block fw-bold text-start">
                                    <span class="text-dark fw-bolder d-block fs-5">Part Number</span>
                                    <span class="text-muted fw-bold fs-6">Format: 23100KVBBA0</span>
                                </span>
                            </label>

                            <input type="radio" class="btn-check" name="jenis_part_number" value="part_number_reference" checked="checked" id="optionPartNumberReference"/>
                            <label class="btn btn-outline btn-outline-dashed btn-outline-default p-4 d-flex align-items-center mb-5" for="optionPartNumberReference">
                                <span class="svg-icon svg-icon-3x me-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z" fill="currentColor"/>
                                        <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="currentColor"/>
                                    </svg>
                                </span>
                                <span class="d-block fw-bold text-start">
                                    <span class="text-dark fw-bolder d-block fs-5">Part Number Reference</span>
                                    <span class="text-muted fw-bold fs-6">Format: 23100-KVB-BA0</span>
                                </span>
                            </label>

                            <input type="radio" class="btn-check" name="jenis_part_number" value="superseed" checked="checked" id="optionSuperseed"/>
                            <label class="btn btn-outline btn-outline-dashed btn-outline-default p-4 d-flex align-items-center mb-5" for="optionSuperseed">
                                <span class="svg-icon svg-icon-3x me-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z" fill="currentColor"/>
                                        <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="currentColor"/>
                                    </svg>
                                </span>
                                <span class="d-block fw-bold text-start">
                                    <span class="text-dark fw-bolder d-block fs-5">Superseed</span>
                                    <span class="text-muted fw-bold fs-6">Format: 23100$KVB$BA0</span>
                                </span>
                            </label>
                        </div>
                        <div class="fv-row mt-8">
                            <div class="row">
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
                                            <h4 class="text-gray-900 fw-bolder">Aturan Import File Excel</h4>
                                            <table class="table align-middle table-row-dashed fs-6 gy-3 dataTable no-footer">
                                                <tr>
                                                    <td style="text-align:center;vertical-align:top;">1</td>
                                                    <td style="text-align:justify;vertical-align:top;">
                                                        Jika kolom harga pada file excel lebih besar dari 0 (nol),
                                                        maka harga yang dipakai sesuai dengan kolom harga yang ada
                                                        pada file excel dan kode tpc akan di ubah secara otomatis
                                                        menjadi tpc 20.
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:center;vertical-align:top;">2</td>
                                                    <td style="text-align:justify;vertical-align:top;">
                                                        Jika kolom harga pada file excel kosong atau sama dengan 0 (nol),
                                                        maka harga akan secara otomatis berubah menjadi HET.
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:center;vertical-align:top;">3</td>
                                                    <td style="text-align:justify;vertical-align:top;">
                                                        Apabila kolom diskon lebih besar dari 0 (nol) maka data diskon
                                                        yang diambil sesuai dengan kolom diskon yang ada pada file excel.
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:center;vertical-align:top;">4</td>
                                                    <td style="text-align:justify;vertical-align:top;">
                                                        Jika kolom diskon kosong atau sama dengan 0 (nol) maka diskon
                                                        akan di ubah secara otomatis berdasarkan setting diskon yang ada
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="btnImportExcelCartProses">Simpan</button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="modalCartImportExcelResult">
        <div class="modal-dialog">
            <div class="modal-content" id="modalCartContentImportExcelResult">
                <div class="modal-header">
                    <h5 class="modal-title">Import Excel</h5>
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
                    <div class="alert alert-dismissible bg-danger d-flex flex-sm-row w-100 p-5">
                        <div class="d-flex flex-column text-light pe-0 pe-sm-10">
                            <h4 class="mb-2 text-light">Informasi</h4>
                            <span>File excel berhasil di import dan ada beberapa yang di bandingkan tidak cocok</span>
                        </div>
                    </div>
                    <div class="p-2">
                        <div id="tableImportExcelResult"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="modalCartSampleExcel">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="modalCartSampleExcel" name="modalCartSampleExcel">
                    <div class="modal-header">
                        <h5 class="modal-title">Format Import Excel</h5>
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
                            @if($role_id == 'D_H3')
                            <img src="{{ asset('assets/images/background/sample_format_excel_dealer.jpg') }}" class="card-img-top img-thumbnail" style="max-height: 100%;max-width: 100%;">
                            @else
                            <img src="{{ asset('assets/images/background/sample_format_excel_internal.jpg') }}" class="card-img-top img-thumbnail" style="max-height: 100%;max-width: 100%;">
                            @endif
                        </div>
                        <div class="fv-row mt-6">
                            <div class="row ms-2 me-2">
                                <p>
                                    <span class="fs-6">Pada kolom baris 1 di isi dengan nama kolom</span>
                                    @if($role_id == 'D_H3')
                                    <span class="fs-6 fw-bold text-danger">part_number, order</span>
                                    @else
                                    <span class="fs-6 fw-bold text-danger">part_number, order, harga, disc, disc_plus</span>
                                    @endif
                                </p>
                                <div class="table-responsive">
                                    <table class="table table-row-dashed table-row-gray-300">
                                        <thead>
                                            <tr class="fw-bolder fs-6 text-gray-800">
                                                <th>No</th>
                                                <th>Kolom</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="fs-6">1</td>
                                                <td class="fs-6 fw-boldest">part_number</td>
                                                <td>Diisi data part number yang akan di order</td>
                                            </tr>
                                            <tr>
                                                <td class="fs-6">2</td>
                                                <td class="fs-6 fw-boldest">order</td>
                                                <td>
                                                    <span class="fs-6">Diisi data quantity order berdasarkan satuan</span>
                                                    <span class="fs-6 fw-boldest text-danger">pcs</span>
                                                </td>
                                            </tr>
                                            @if($role_id != 'D_H3')
                                            <tr>
                                                <td class="fs-6">3</td>
                                                <td class="fs-6 fw-boldest">harga</td>
                                                <td>
                                                    <span class="fs-6">Jika kolom harga diisi</span>
                                                    <span class="fs-6 fw-boldest text-danger">lebih besar dari 0 (nol)</span>
                                                    <span class="fs-6">maka part tersebut akan di anggap sebagai</span>
                                                    <span class="fs-6 fw-boldest text-danger">part tpc 20</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fs-6">4</td>
                                                <td class="fs-6 fw-boldest">disc</td>
                                                <td>Diisi data diskon toko untuk pembelian per-part number</td>
                                            </tr>
                                            <tr>
                                                <td class="fs-6">5</td>
                                                <td class="fs-6 fw-boldest">disc_plus</td>
                                                <td>Diisi data diskon tambahan toko untuk pembelian per-part number</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="fv-row mt-8">
                            <div class="row ms-2 me-2">
                                <span class="fs-6 fw-boldest text-danger">* Penamaan nama kolom tidak boleh salah</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="modalCartCheckOut">
        <div class="modal-dialog">
            <div class="modal-content" id="modalContentCartCheckOut">
                <form id="formModalCartCheckOut" name="formModalCartCheckOut" enctype="multipart/form-data" autofill="off" autocomplete="off" method="post" action="{{ route('orders.cart-check-out') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Check Out</h5>
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
                            <label class="form-label mb-3 required">Password:</label>
                            <input id="password" type="password" class="form-control form-control-lg" name="password" placeholder="Masukkan kata sandi" value="" required>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label mb-3 required">Konfirmasi Password:</label>
                            <input id="password_confirm" type="password" class="form-control form-control-lg" name="password_confirm" placeholder="Masukkan ulang kata sandi" value="" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="btnCheckOutProses">Simpan</button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('layouts.option.optionsalesman')
    @include('layouts.option.optiondealersalesman')

    @push('scripts')
        <script src="{{ asset('assets/js/suma/option/option.js') }}"></script>
        <script type="text/javascript">
            var harga = document.getElementById("modalEditCartInputHarga");
                harga.addEventListener("keyup", function(e) {
                harga.value = formatRupiah(this.value, "");
            });

            function formatRupiah(nominal, prefix) {
                var number_string = nominal.replace(/[^.\d]/g, "").toString(),
                    split = number_string.split("."),
                    sisa = split[0].length % 3,
                    angka = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    separator = sisa ? "," : "";
                    angka += separator + ribuan.join(",");
                }

                angka = split[1] != undefined ? angka + "." + split[1] : angka;
                return prefix == undefined ? angka : angka ? "" + angka : "";
            }

            var discount = document.getElementById("modalEditCartInputDiscount");
                discount.addEventListener("keyup", function(e) {
                discount.value = formatDiscount(this.value, "");
            });

            var discountPlus = document.getElementById("modalEditCartInputDiscountPlus");
                discountPlus.addEventListener("keyup", function(e) {
                discountPlus.value = formatDiscount(this.value, "");
            });

            var btnSimpanHeader = document.querySelector("#btnSimpanHeaderCart");
            btnSimpanHeader.addEventListener("click", function(e) {
                e.preventDefault();
                blockIndex.block();
                document.getElementById("formModalCartHeader").submit();
            });

            var btnTambahCart = document.querySelector("#btnTambahCart");
            btnTambahCart.addEventListener("click", function(e) {
                blockIndex.block();
            });

            function formatDiscount(nominal, prefix) {
                var number_string = nominal.replace(/[^,\d]/g, "").toString(),
                    split = number_string.split(","),
                    sisa = split[0].length % 2,
                    angka = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{2}/gi);

                if (ribuan) {
                    separator = sisa ? "." : "";
                    angka += separator + ribuan.join(".");
                }

                angka = split[1] != undefined ? angka + "." + split[1] : angka;
                return prefix == undefined ? angka : angka ? "" + angka : "";
            }

            $('form').on('keydown', 'input, select', function(e) {
                if (e.key === "Enter") {
                    var self = $(this), form = self.parents('form:eq(0)'), focusable, next;
                    focusable = form.find('input,a,select,textarea').filter(':visible:not([readonly]):enabled');
                    next = focusable.eq(focusable.index(this)+1);

                    if (next.length) {
                        next.focus();
                    }
                    return false;
                }
            });

            var xInputKeyUser = document.getElementById("inputKodeCart").value;

            function daftarDetailCart() {
                blockIndex.block();
                $.ajax({
                    url: "{{ route('orders.daftar-cart-detail') }}",
                    method: "GET",

                    success:function(response) {
                        blockIndex.release();

                        if (response.status == true) {
                            $('#tableDetailCart').html(response.data);
                        } else {
                            Swal.fire({
                                text: response.message,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-danger"
                                }
                            });
                        }
                    },
                    error:function() {
                        blockIndex.release();
                    }
                })
            }

            $(document).ready(function() {
                daftarDetailCart();

                $('#btnEditCart').click(function () {
                    var salesman = $('#inputViewSalesman').val();
                    var dealer = $('#inputViewDealer').val();
                    var back_order = $('#inputViewBackOrder').val();
                    var back_order_val = 'B';
                    if(back_order == 'BO') {
                        back_order_val = 'B';
                    } else {
                        back_order_val = 'T';
                    }
                    var keterangan = $('#inputViewKeterangan').val();

                    $('#inputSalesman').val(salesman);
                    $('#inputDealer').val(dealer);
                    $('#selectBo option[value="'+back_order_val+'"]').prop('selected', true);
                    $('#inputKeterangan').val(keterangan);

                    $('#modalCartHeader').modal({backdrop: 'static', keyboard: false});
                    $('#modalCartHeader').modal('show');
                });

                $('body').on('click', '#btnPilihSalesman', function(e) {
                    e.preventDefault();
                    loadDataSalesman();
                    $('#searchSalesmanForm').trigger('reset');
                    $('#salesmanSearchModal').modal('show');
                });

                $('body').on('click', '#salesmanContentModal #selectSalesman', function(e) {
                    e.preventDefault();
                    $('#inputSalesman').val($(this).data('kode_sales'));
                    $('#inputDealer').val('');
                    $('#salesmanSearchModal').modal('hide');
                });

                $('#btnPilihDealer').on('click', function(e) {
                    e.preventDefault();

                    var salesman = $('#inputSalesman').val();

                    if(salesman == null || salesman == '') {
                        Swal.fire({
                            text: response.message,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-danger"
                            }
                        });
                    } else {
                        loadDataDealerSalesman(salesman, 1, 10, '');
                        $('#searchDealerSalesmanForm').trigger('reset');
                        $('#dealerSalesmanSearchModal').modal('show');
                    }
                });

                $('body').on('click', '#dealerSalesmanContentModal #selectDealerSalesman', function(e) {
                    e.preventDefault();
                    $('#inputDealer').val($(this).data('kode_dealer'));
                    $('#dealerSalesmanSearchModal').modal('hide');
                });

                $('body').on('click', '#btnEditPartCart', function () {
                    var part_number = $(this).data('kode');
                    var tpc = $('#selectKodeTpc').val();
                    var _token = $('input[name="_token"]').val();

                    blockIndex.block();
                    $.ajax({
                        url: "{{ route('orders.cart-detail-edit') }}",
                        method: "POST",
                        data: { part_number: part_number, _token: _token },

                        success:function(response) {
                            blockIndex.release();

                            if (response.status == true) {
                                $('#modalTitle').html("Edit Data Cart");
                                var picture = response.data.images;
                                var picture_not_found = "'{{ URL::asset('assets/images/background/part_image_not_found.png') }}'";
                                var part = '<center><img src="'+picture+'" class="overlay-layer card-rounded bg-gray-400 bg-opacity-25 img-thumbnail" onerror="this.onerror=null; this.src='+picture_not_found+';"'+
                                                    'style="width: auto;height: 200px;">'+
                                                '</center>';
                                $('#modalEditCartPicturePart').html(part);
                                $('#modalEditCartInputPartNumber').val(response.data.part_number);
                                $('#modalEditCartInputProduk').val(response.data.produk);
                                $('#modalEditCartInputDescription').val(response.data.description);
                                $("#modalEditCartSelectTpc").val(response.data.tpc).trigger("change");
                                $('#modalEditCartInputJmlOrder').val(response.data.jml_order);
                                $('#modalEditCartInputHarga').val(formatRupiah(response.data.harga.toString(), ""));
                                $('#modalEditCartInputDiscount').val(formatDiscount(response.data.disc1.toString(), ""));
                                $('#modalEditCartInputDiscountPlus').val(formatDiscount(response.data.disc2.toString(), ""));

                                if(response.data.tpc == 14) {
                                    $('#modalEditCartInputHarga').attr('readonly','true');
                                    $('#modalEditCartInputHarga').addClass('form-control-solid');

                                    $('#modalEditCartInputDiscount').attr('readonly','false');
                                    $('#modalEditCartInputDiscount').removeClass('form-control-solid');

                                    $('#modalEditCartInputDiscountPlus').attr('readonly','false');
                                    $('#modalEditCartInputDiscountPlus').removeClass('form-control-solid');
                                } else {
                                    $('#modalEditCartInputHarga').attr('readonly','false');
                                    $('#modalEditCartInputHarga').removeClass('form-control-solid');

                                    $('#modalEditCartInputDiscount').attr('readonly','true');
                                    $('#modalEditCartInputDiscount').addClass('form-control-solid');

                                    $('#modalEditCartInputDiscountPlus').attr('readonly','true');
                                    $('#modalEditCartInputDiscountPlus').addClass('form-control-solid');
                                }

                                $('#modalCartDetail').modal('show');
                            } else {
                                Swal.fire({
                                    text: response.message,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-danger"
                                    }
                                });
                            }
                        },
                        error:function() {
                            blockIndex.release();
                        }
                    })
                });

                $('#modalEditCartSelectTpc').on('change', function() {
                    blockIndex.block();

                    var part_number = $('#modalEditCartInputPartNumber').val();
                    var tpc = this.value;
                    var _token = $('input[name="_token"]').val();

                    $.ajax({
                        url: "{{ route('orders.cart-detail-edit') }}",
                        method: "POST",
                        data: { part_number: part_number, _token: _token },

                        success:function(response) {
                            blockIndex.release();

                            if(tpc == 14) {
                                $('#modalEditCartInputHarga').val(formatRupiah(response.data.het.toString(), ""));
                                $('#modalEditCartInputDiscount').val(formatDiscount(response.data.disc1.toString(), ""));
                                $('#modalEditCartInputDiscountPlus').val(formatDiscount(response.data.disc2.toString(), ""));

                                $('#modalEditCartInputHarga').attr('readonly','true');
                                $('#modalEditCartInputHarga').addClass('form-control-solid');

                                $('#modalEditCartInputDiscount').removeAttr('readonly');
                                $('#modalEditCartInputDiscount').removeClass('form-control-solid');

                                $('#modalEditCartInputDiscountPlus').removeAttr('readonly');
                                $('#modalEditCartInputDiscountPlus').removeClass('form-control-solid');
                            } else {
                                $('#modalEditCartInputHarga').val(formatRupiah(response.data.harga.toString(), ""));
                                $('#modalEditCartInputDiscount').val(0);
                                $('#modalEditCartInputDiscountPlus').val(0);

                                $('#modalEditCartInputHarga').removeAttr('readonly');
                                $('#modalEditCartInputHarga').removeClass('form-control-solid');

                                $('#modalEditCartInputDiscount').attr('readonly','true');
                                $('#modalEditCartInputDiscount').addClass('form-control-solid');

                                $('#modalEditCartInputDiscountPlus').attr('readonly','true');
                                $('#modalEditCartInputDiscountPlus').addClass('form-control-solid');
                            }
                        },
                        error:function() {
                            blockIndex.release();
                        }
                    });
                });

                $('#btnSimpanDetailCart').click(function(e) {
                    var part_number = $('#modalEditCartInputPartNumber').val();
                    var tpc = $('#modalEditCartSelectTpc').val();
                    var jml_order = $('#modalEditCartInputJmlOrder').val();
                    var harga = $('#modalEditCartInputHarga').val();
                    var discount = $('#modalEditCartInputDiscount').val();
                    var discount_plus = $('#modalEditCartInputDiscountPlus').val();
                    var _token = $('input[name="_token"]').val();

                    blockIndex.block();
                    $.ajax({
                        url: "{{ route('orders.insert-cart-detail') }}",
                        method: "POST",
                        data: {
                            part_number: part_number, tpc: tpc, jml_order: jml_order, harga: harga,
                            discount: discount, discount_plus: discount_plus, _token: _token
                        },
                        success:function(response) {
                            blockIndex.release();

                            if (response.status == false) {
                                Swal.fire({
                                    text: response.message,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-danger"
                                    }
                                });
                            } else {
                                Swal.fire({
                                    text: response.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-danger"
                                    }
                                });
                                $('#modalCartDetail').modal('hide');
                                daftarDetailCart();
                            }
                        },
                        error:function() {
                            blockIndex.release();
                        }
                    });
                });

                $('body').on('click', '#btnDeletePartCart', function (event) {
                    var part_number = $(this).data("kode");
                    var _token = $('input[name="_token"]').val();

                    event.preventDefault();
                    const url = $(this).attr('href');

                    Swal.fire({
                        html: `Data yang <strong>dihapus</strong> tidak dapat dikembalikan.
                                Apa anda yakin akan <strong>menghapus data</strong> part number
                                <strong>`+part_number+`</strong> ?`,
                        icon: "info",
                        buttonsStyling: false,
                        showCancelButton: true,
                        confirmButtonText: "Yes",
                        cancelButtonText: 'No',
                        customClass: {
                            confirmButton: "btn btn-danger",
                            cancelButton: 'btn btn-primary'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            blockIndex.block();

                            $.ajax({
                                url: "{{ route('orders.delete-cart-detail') }}",
                                method: "POST",
                                data: { part_number: part_number, _token: _token },

                                success:function(response) {
                                    blockIndex.release();

                                    if (response.status == true) {
                                        Swal.fire({
                                            text: response.message,
                                            icon: "success",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn btn-danger"
                                            }
                                        });
                                        daftarDetailCart();
                                    } else {
                                        Swal.fire({
                                            text: response.message,
                                            icon: "error",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn btn-danger"
                                            }
                                        });
                                    }
                                },
                                error:function() {
                                    blockIndex.release();
                                }
                            })
                        }
                    });
                });

                $('#btnResetCart').click(function(e) {
                    var _token = $('input[name="_token"]').val();
                    e.preventDefault();
                    const url = $(this).attr('href');

                    Swal.fire({
                        html: `Data yang telah <strong>di hapus tidak dapat dikembalikan lagi.</strong>
                                Apa anda yakin akan <strong>mengosongkan data cart anda</strong> ?`,
                        icon: "info",
                        buttonsStyling: false,
                        showCancelButton: true,
                        confirmButtonText: "Yes",
                        cancelButtonText: 'No',
                        customClass: {
                            confirmButton: "btn btn-danger",
                            cancelButton: 'btn btn-primary'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            blockIndex.block();

                            $.ajax({
                                url: "{{ route('orders.reset-cart') }}",
                                method: "POST",
                                data: { _token: _token },

                                success:function(response) {
                                    blockIndex.release();

                                    if (response.status == true) {
                                        Swal.fire({
                                            text: response.message,
                                            icon: "success",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn btn-danger"
                                            }
                                        });
                                        daftarDetailCart();
                                    } else {
                                        Swal.fire({
                                            text: response.message,
                                            icon: "error",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn btn-danger"
                                            }
                                        });
                                    }
                                },
                                error:function() {
                                    blockIndex.release();
                                }
                            })
                        }
                    });
                });

                $('#btnImportExcelCart').click(function(e) {
                    var salesman = $('#inputViewSalesman').val();
                    var dealer = $('#inputViewDealer').val();

                    $('#inputImportCartSalesman').val(salesman);
                    $('#inputImportCartDealer').val(dealer);
                    $('#optionPartNumber').prop('checked', true);
                    $('#selectFileExcel').val('');
                    $('#modalCartImportExcel').modal('show');
                });

                $('#btnImportExcelCartProses').click(function(e) {
                    e.preventDefault();
                    var salesman = $('#inputViewSalesman').val();
                    var dealer = $('#inputViewDealer').val();
                    var fileExcel = $('#selectFileExcel').val();


                    if (salesman == '' || dealer == '') {
                        Swal.fire({
                            text: "Kode sales dan kode dealer tidak boleh kosong",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-danger"
                            }
                        });
                    } else if(fileExcel == '') {
                        Swal.fire({
                            text: "Pilih file excel yang akan di import terlebih dahulu",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-danger"
                            }
                        });
                    } else {
                        blockIndex.block();
                        var formData = new FormData(document.getElementById("formModalCartImportExcel"));
                        $.ajax({
                            type:'POST',
                            url: "{{ route('orders.import-excel-cart') }}",
                            data: formData,
                            cache: false,
                            contentType: false,
                            processData: false,

                            success:function(response) {
                                blockIndex.release();

                                if (response.status == false) {
                                    Swal.fire({
                                        text: response.message,
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn btn-danger"
                                        }
                                    });
                                } else {
                                    if(response.message == 'SUCCESS_WITH_MESSAGE') {
                                        daftarDetailCart();
                                        $('#modalCartImportExcel').modal('hide');

                                        $('#tableImportExcelResult').html('');
                                        $('#tableImportExcelResult').html(response.data);
                                        $("#tableImportExcelResultCart").DataTable();
                                        $('#modalCartImportExcelResult').modal('show');

                                    } else {
                                        Swal.fire({
                                            text: response.message,
                                            icon: "success",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn btn-success"
                                            }
                                        });

                                        daftarDetailCart();
                                        $('#modalCartImportExcel').modal('hide');
                                    }
                                }
                            },
                            error:function() {
                                blockIndex.release();
                            }
                        });
                    }
                });

                $('#btnSampleExcelCart').click(function(e) {
                    $('#modalCartSampleExcel').modal('show');
                });

                $('#btnCheckOut').click(function(e) {
                    e.preventDefault();
                    blockIndex.block();
                    var _token = $('input[name="_token"]').val();
                    $.ajax({
                        url: "{{ route('orders.cart-check-out-cek-aturan-harga') }}",
                        method: "POST",
                        data: { _token: _token },

                        success:function(response) {
                            blockIndex.release();

                            if(response.status == false) {
                                Swal.fire({
                                    text: response.message,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                    confirmButton: "btn btn-danger"
                                    }
                                });
                            } else {
                                blockIndex.release();

                                if(response.message == 'STATUS_OK') {
                                    $('#modalCartCheckOut').modal('show');
                                } else {
                                    Swal.fire({
                                        html: `Harga cart kurang dari <strong>Rp. 500,000</strong>. Apakah anda tetap
                                                akan melanjutkan transaksi ini ?`,
                                        icon: "info",
                                        buttonsStyling: false,
                                        showCancelButton: true,
                                        confirmButtonText: "Yes",
                                        cancelButtonText: 'No',
                                        customClass: {
                                            confirmButton: "btn btn-danger",
                                            cancelButton: 'btn btn-primary'
                                        }
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $('#modalCartCheckOut').modal('show');
                                        }
                                    });
                                }
                            }
                        },
                        error:function() {
                            blockIndex.release();
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
