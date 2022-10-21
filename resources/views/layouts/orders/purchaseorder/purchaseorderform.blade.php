@extends('layouts.main.index')
@section('title','Orders')
@section('subtitle','Purchase Order')
@section('container')
    <div class="row g-0">
        <form action="{{ route('orders.purchase-order-form-save') }}" autocomplete="off" method="POST">
            @csrf
            <div class="card card-flush">
                <div class="card-header align-items-center border-0 mt-4">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="fw-bolder mb-2 text-dark">Purchase Order</span>
                        <span class="text-muted fw-bold fs-7">Entry data purchase order form</span>
                    </h3>
                </div>
                <div id="cardPofHeader" class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="row mt-5">
                                <div class="col-12">
                                    <div class="form-input-group">
                                        <label class="form-label required">Nomor Purchase Order</label>
                                        <input id="inputNomorPof" name="nomor_pof" type="text" class="form-control form-control-solid" readonly
                                            @if(isset($nomor_pof)) value="{{ $nomor_pof }}" @else value="{{ old('nomor_pof') }}"@endif>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-5">
                                <div class="col-12">
                                    <div class="form-input-group">
                                        <label class="form-label required">Tanggal</label>
                                        <input id="inputTanggal" name="tanggal_pof" type="text" class="form-control form-control-solid" readonly
                                            @if(isset($tanggal_pof)) value="{{ $tanggal_pof }}" @else value="{{ old('tanggal_pof') }}"@endif>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-5">
                                <div class="col-12">
                                    <div class="form-input-group">
                                        <label class="form-label required">Approve</label>
                                        <div class="row d-flex align-items-center">
                                            <div class="col-1">
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    @if ($approve == 1)
                                                    <input class="form-check-input" type="checkbox" value="1" data-kt-check="true" checked data-kt-check-target=".widget-9-check" disabled>
                                                    @else
                                                    <input class="form-check-input" type="checkbox" value="0" data-kt-check="false" data-kt-check-target=".widget-9-check" disabled>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-11">
                                                <input id="inputApproveUser" name="approve_user" type="text" class="form-control form-control-solid" readonly
                                                    @if(isset($approve_user)) value="{{ $approve_user }}" @else value="{{ old('approve_user') }}"@endif>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row mt-5">
                                <div class="col-12">
                                    <div class="form-input-group">
                                        <label class="form-label required">Kode Sales</label>
                                        <input id="inputSalesman" name="salesman" type="text" class="form-control form-control-solid" readonly
                                            @if(isset($kode_sales)) value="{{ $kode_sales }}" @else value="{{ old('kode_sales') }}"@endif>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-5">
                                <div class="col-12">
                                    <div class="form-input-group">
                                        <div class="form-input-group">
                                            <label class="form-label required">Kode Dealer</label>
                                            <input id="inputDealer" name="dealer" type="text" class="form-control form-control-solid" readonly
                                                @if(isset($kode_dealer)) value="{{ $kode_dealer }}" @else value="{{ old('kode_dealer') }}"@endif>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-5">
                                <div class="col-12">
                                    <div class="form-input-group">
                                        <div class="form-input-group">
                                            <label class="form-label required">Keterangan</label>
                                            <input id="inputKeterangan" name="keterangan" type="text" class="form-control
                                                @if($status_faktur == 1) form-control-solid @else @if($approve == 1) form-control-solid @endif @endif"
                                                @if($status_faktur == 1) readonly @else @if($approve == 1) readonly @endif @endif
                                                @if(isset($keterangan)) value="{{ $keterangan }}" @else value="{{ old('keterangan') }}"@endif>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row mt-5">
                                <div class="col-lg-12">
                                    <div class="form-input-group">
                                        <label class="form-label required">Kode TPC</label>
                                        <select id="selectKodeTpc" name="tpc" data-control="select2" data-hide-search="true" class="form-select
                                            @if($status_faktur == 1) form-select-solid @else @if($approve == 1) form-select-solid @endif @endif"
                                            @if($status_faktur == 1) disabled @else @if($approve == 1) disabled @endif @endif>
                                            <option value="14" @if($kode_tpc == '14') {{"selected"}} @endif>14</option>
                                            <option value="20" @if($kode_tpc == '20') {{"selected"}} @endif>20</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-5">
                                <div class="col-lg-12">
                                    <div class="form-input-group">
                                        <label class="form-label required">Umur Faktur</label>
                                        <input id="inputUmurPof" name="umur_pof" type="number" min="0" class="form-control
                                            @if($status_faktur == 1) form-control-solid @else @if($approve == 1) form-control-solid @endif @endif"
                                            @if($status_faktur == 1) readonly @else @if($approve == 1) readonly @endif @endif
                                            @if(isset($umur_pof)) value="{{ $umur_pof }}" @else value="{{ old('umur_pof') }}"@endif required>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-5">
                                <div class="col-lg-12">
                                    <div class="form-input-group">
                                        <label class="form-label required">BO / Tidak BO</label>
                                        <select id="selectBo" name="back_order" data-control="select2" data-hide-search="true" class="form-select
                                            @if($status_faktur == 1) form-select-solid @else @if($approve == 1) form-select-solid @endif @endif"
                                            @if($status_faktur == 1) disabled @else @if($approve == 1) disabled @endif @endif>
                                            <option value="B" @if($bo == 'B') {{"selected"}} @endif>BO</option>
                                            <option value="T" @if($bo == 'T') {{"selected"}} @endif>Tidak BO</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($status_faktur != 1)
                @if($approve != 1)
                <div class="card card-flush mt-5">
                    <div class="d-flex p-4">
                        <button id="btnTambahPartNumber" name="btnTambahPartNumber" class="btn btn-primary" type="button" data-backdrop="static" data-keyboard="false">Tambah Part Number</button>
                    </div>
                </div>
                @endif
            @endif

            <div id='detailPurchaseOrderForm'></div>

            @if($role_id != 'D_H3')
                @if(strtoupper(trim($device)) == 'DESKTOP')
                    @if($status_faktur == 0 || $approve == 0)
                    <div class="card card-flush mt-5">
                        <div class="d-flex p-4">
                            <div class="col-6">
                                @if($status_faktur != 1)
                                    @if($approve != 1)
                                        <button class="btn btn-success" name="btnSimpanPof" value="1">Simpan</button>
                                    @endif
                                @endif
                            </div>
                            <div class="col-6 text-end">
                            @if($status_faktur != 1)
                                @if($approve == 0)
                                <button class="btn btn-danger" name="btnSimpanPof" value="2">Simpan & Approve</button>
                                @else
                                <button class="btn btn-danger" id="btnBatalApprovePof" name="btnBatalApprovePof">Batal Approve</button>
                                @endif
                            @endif
                            </div>
                        </div>
                    </div>
                    @endif
                @else
                    @if($status_faktur == 0 || $approve == 0)
                    <div class="card card-flush mt-5">
                        <div class="d-flex p-4">
                            <div class="col-12">
                                <div class="row m-2">
                                @if($status_faktur != 1)
                                    @if($approve != 1)
                                    <button class="btn btn-success" name="btnSimpanPof" value="1">Simpan</button>
                                    <button class="btn btn-danger mt-4" name="btnSimpanPof" value="2">Simpan & Approve</button>
                                    @else
                                    <button class="btn btn-danger" name="btnBatalApprovePof">Batal Approve</button>
                                    @endif
                                @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endif
            @endif
        </form>
    </div>

    <div class="modal fade" tabindex="-1" id="modalEntryPartNumber" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="modalEntryPartNumber" name="modalEntryPartNumber" autofill="off" autocomplete="off" method="POST" action="#">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        <h5 id="modalEntryPartTitle" name="modalEntryPartTitle" class="modal-title"></h5>
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
                            <span id="messageErrorPofPart"></span>
                            <div class="row fv-row mb-2 fv-plugins-icon-container">
                                <label class="fs-7 fw-bold form-label mt-3 required">Part Number :</label>
                                <input id="modalPofPartInputPartNumber" name="part_number" type="text" class="form-control" required>
                            </div>
                            <div class="row fv-row mb-2 fv-plugins-icon-container">
                                <label class="fs-7 fw-bold form-label mt-3">Nama Part :</label>
                                <input id="modalPofPartInputNamaPart" name="nama_part" type="text" class="form-control form-control-solid" readonly>
                            </div>
                            <div class="row fv-row mb-2 fv-plugins-icon-container">
                                <label class="fs-7 fw-bold form-label mt-3">Produk :</label>
                                <input id="modalPofPartInputProduk" name="produk" type="text" class="form-control form-control-solid" readonly>
                            </div>
                            <div class="row fv-row mb-2 fv-plugins-icon-container">
                                <label class="fs-7 fw-bold form-label mt-3 required">Jumlah Order</label>
                                <input id="modalPofPartInputJmlOrder" name="jml_order" type="number" min="1" class="form-control text-end" required>
                            </div>
                            <div class="row fv-row mb-2 fv-plugins-icon-container">
                                <label class="fs-7 fw-bold form-label mt-3 required">Harga</label>
                                <input id="modalPofPartInputHarga" name="harga" type="number" class="form-control text-end" required>
                            </div>
                            <div class="row fv-row mb-2 fv-plugins-icon-container">
                                <label class="fs-7 fw-bold form-label mt-3 required">Discount</label>
                                <input id="modalPofPartInputDiscount" name="discount" type="number" min="0" max="100" class="form-control text-end" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="btnSimpanPofPart" name="btnSimpanPofPart" type="button" class="btn btn-primary">Simpan</button>
                        <button id="btnClose" name="btnClose" type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="modalEntryDiscount" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="modalEntryDiscount" name="modalEntryDiscount" autofill="off" autocomplete="off" method="POST" action="#">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        <h5 id="modalDiscountTitle" name="modalDiscountTitle" class="modal-title"></h5>
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
                            <span id="messageErrorPofDiscount"></span>
                            <div class="row fv-row mb-2 fv-plugins-icon-container">
                                <label class="fs-7 fw-bold form-label mt-3 required">Discount</label>
                                <input id="modalPofDiscountInputDiscount" name="discount" type="number" min="0" max="100" class="form-control text-end" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="btnSimpanPofDiscount" name="btnSimpanPofDiscount" type="button" class="btn btn-primary">Simpan</button>
                        <button id="btnClose" name="btnClose" type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('layouts.orders.purchaseorder.purchaseorderformfaktur')
    @include('layouts.option.optionpartnumber')

    @push('scripts')
        <script type="text/javascript">
            const url_route = {
                'option_part_number' : "{{ route('option.option-part-number') }}",
                'validasi_part_number' : "{{ route('validasi.validasi-part-number') }}",
                'purchase_order_form_edit_part' : "{{ route('orders.purchase-order-form-edit-part') }}",
                'purchase_order_form_hapus_part' : "{{ route('orders.purchase-order-form-hapus-part') }}",
                'purchase_order_form_simpan_part' : "{{ route('orders.purchase-order-form-simpan-part') }}",
                'purchase_order_form_edit_discount' : "{{ route('orders.purchase-order-form-edit-discount') }}",
                'purchase_order_form_update_discount' : "{{ route('orders.purchase-order-form-update-discount') }}",
                'purchase_order_form_update_tpc' : "{{ route('orders.purchase-order-form-update-tpc') }}",
                'purchase_order_form_batal_approve' : "{{ route('orders.purchase-order-form-batal-approve') }}",
                'purchase_order_form_terlayani' : "{{ route('orders.purchase-order-form-terlayani') }}",
            }

        </script>
        <script src="{{ asset('assets/js/suma/orders/purchaseorder/purchaseorderform.js') }}?v={{ time() }}"></script>
    @endpush
@endsection
