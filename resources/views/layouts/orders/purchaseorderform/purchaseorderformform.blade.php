@extends('layouts.main.index')
@section('title','Orders')
@section('subtitle','Purchase Order Form')
@section('container')
    <div class="row g-0">
        <form action="{{ route('orders.purchaseorderform.form.simpan') }}" autocomplete="off" method="POST">
            @csrf
            <div class="form d-flex flex-column flex-lg-row fv-plugins-bootstrap5 fv-plugins-framework">
                <div class="w-100 flex-lg-row-auto w-lg-300px mb-7 me-7 me-lg-10">
                    <div class="card card-flush py-4">
                        <div class="card-header">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="fw-bolder mb-2 text-dark">Purchase Order Form</span>
                                <span class="text-muted fw-bold fs-7">Entry data purchase order form</span>
                            </h3>
                        </div>
                        <div class="card-body pt-0">
                            <div class="fv-row mt-6">
                                <label class="form-label required">Nomor Purchase Order:</label>
                                <input id="inputNomorPof" name="nomor_pof" type="text" class="form-control form-control-solid" readonly
                                    @if(isset($nomor_pof)) value="{{ $nomor_pof }}" @else value="{{ old('nomor_pof') }}"@endif>
                            </div>
                            <div class="fv-row mt-8">
                                <label class="form-label required">Tanggal:</label>
                                <input id="inputTanggal" name="tanggal_pof" type="text" class="form-control form-control-solid" readonly
                                    @if(isset($tanggal_pof)) value="{{ $tanggal_pof }}" @else value="{{ old('tanggal_pof') }}"@endif>
                            </div>
                        </div>
                    </div>
                    <div class="card card-flush py-4 mt-8">
                        <div class="card-body pt-0">
                            <div class="fv-row mt-8">
                                <label class="form-label required">Kode Sales:</label>
                                <input id="inputSalesman" name="salesman" type="text" class="form-control form-control-solid" readonly
                                    @if(isset($kode_sales)) value="{{ $kode_sales }}" @else value="{{ old('kode_sales') }}"@endif>
                            </div>
                            <div class="fv-row mt-8">
                                <label class="form-label required">Kode Dealer:</label>
                                <input id="inputDealer" name="dealer" type="text" class="form-control form-control-solid" readonly
                                    @if(isset($kode_dealer)) value="{{ $kode_dealer }}" @else value="{{ old('kode_dealer') }}"@endif>
                            </div>
                        </div>
                    </div>
                    <div class="card card-flush py-4 mt-8">
                        <div class="card-body pt-0">
                            <div class="fv-row mt-8">
                                <label class="form-label required">Kode TPC:</label>
                                <select id="selectKodeTpc" name="tpc" data-control="select2" data-hide-search="true" class="form-select
                                    @if($status_faktur == 1) form-select-solid @else @if($approve == 1) form-select-solid @endif @endif"
                                    @if($status_faktur == 1) disabled @else @if($approve == 1) disabled @endif @endif>
                                    <option value="14" @if($kode_tpc == '14') {{"selected"}} @endif>14</option>
                                    <option value="20" @if($kode_tpc == '20') {{"selected"}} @endif>20</option>
                                </select>
                            </div>
                            <div class="fv-row mt-8">
                                <label class="form-label required">Umur Faktur:</label>
                                <input id="inputUmurPof" name="umur_pof" type="number" min="0" class="form-control
                                    @if($status_faktur == 1) form-control-solid @else @if($approve == 1) form-control-solid @endif @endif"
                                    @if($status_faktur == 1) readonly @else @if($approve == 1) readonly @endif @endif
                                    @if(isset($umur_pof)) value="{{ $umur_pof }}" @else value="{{ old('umur_pof') }}"@endif required>
                            </div>
                            <div class="fv-row mt-8">
                                <label class="form-label required">BO / Tidak BO:</label>
                                <select id="selectBo" name="back_order" data-control="select2" data-hide-search="true" class="form-select
                                    @if($status_faktur == 1) form-select-solid @else @if($approve == 1) form-select-solid @endif @endif"
                                    @if($status_faktur == 1) disabled @else @if($approve == 1) disabled @endif @endif>
                                    <option value="B" @if($bo == 'B') {{"selected"}} @endif>BO</option>
                                    <option value="T" @if($bo == 'T') {{"selected"}} @endif>Tidak BO</option>
                                </select>
                            </div>
                            <div class="fv-row mt-8">
                                <label class="form-label required">Keterangan:</label>
                                <input id="inputKeterangan" name="keterangan" type="text" class="form-control
                                    @if($status_faktur == 1) form-control-solid @else @if($approve == 1) form-control-solid @endif @endif"
                                    @if($status_faktur == 1) readonly @else @if($approve == 1) readonly @endif @endif
                                    @if(isset($keterangan)) value="{{ $keterangan }}" @else value="{{ old('keterangan') }}"@endif>
                            </div>
                            <div class="fv-row mt-8">
                                <label class="form-label required">Approve:</label>
                                <div class="row d-flex align-items-center">
                                    <div class="col-2">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            @if ($approve == 1)
                                            <input class="form-check-input" type="checkbox" value="1" data-kt-check="true" checked data-kt-check-target=".widget-9-check" disabled>
                                            @else
                                            <input class="form-check-input" type="checkbox" value="0" data-kt-check="false" data-kt-check-target=".widget-9-check" disabled>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-10">
                                        <input id="inputApproveUser" name="approve_user" type="text" class="form-control form-control-solid" readonly
                                            @if(isset($approve_user)) value="{{ $approve_user }}" @else value="{{ old('approve_user') }}"@endif>
                                    </div>
                                </div>
                                @if($role_id != 'D_H3')
                                @if((int)$status_faktur == 0)
                                @if(strtoupper(trim($device)) == 'DESKTOP')
                                <div class="separator my-10"></div>
                                <div class="row">
                                    @if($approve == 0)
                                    <button class="btn btn-success" name="btnSimpanPof" value="1">Simpan</button>
                                    <button class="btn btn-danger mt-4" name="btnSimpanPof" value="2">Simpan & Approve</button>
                                    @else
                                    <button class="btn btn-danger" id="btnBatalApprovePof" name="btnBatalApprovePof">Batal Approve</button>
                                    @endif
                                </div>
                                @endif
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-column flex-lg-row-fluid gap-7 gap-lg-10">
                    <div class="card card-flush py-4">
                        <div class="card-header">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="fw-bolder mb-2 text-dark">Detail Purchase Order</span>
                                <span class="text-muted fw-bold fs-7">Daftar detail purchase order</span>
                            </h3>
                            <div class="card-toolbar">
                                @if((int)$status_faktur == 0 && (int)$approve == 0)
                                <button id="btnTambahPartNumber" name="btnTambahPartNumber" class="btn btn-primary" type="button" data-backdrop="static" data-keyboard="false">Tambah Part Number</button>
                                @endif
                            </div>
                        </div>
                        @if(strtoupper(trim($device)) == 'DESKTOP')
                        <div class="card-body">
                            <div id="daftarDetailPurchaseOrderForm"></div>
                        </div>
                        @endif
                    </div>

                    @if(strtoupper(trim($device)) != 'DESKTOP')
                    <div id="daftarDetailPurchaseOrderForm"></div>
                    @endif

                    @if(strtoupper(trim($device)) != 'DESKTOP')
                    @if((int)$status_faktur == 0 || (int)$approve == 0)
                    <div class="fv-row">
                        <div class="card card-flush">
                            <div class="card-body">
                                <div class="row">
                                    @if((int)$approve == 0)
                                    <button class="btn btn-success ps-8 pe-8" name="btnSimpanPof" value="1">Simpan</button>
                                    <button class="btn btn-danger ps-8 pe-8 mt-4" name="btnSimpanPof" value="2">Simpan & Approve</button>
                                    @else
                                    <button class="btn btn-danger ps-4 pe-4" name="btnBatalApprovePof">Batal Approve</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade" tabindex="-1" id="modalEntryPartNumber" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="modalFormPartNumber" autofill="off" autocomplete="off" method="POST" action="#">
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
                            <div class="fv-row mb-6">
                                <label class="form-label required">Part Number:</label>
                                <div class="input-group">
                                    <input id="modalPofPartInputPartNumber" name="part_number" type="text" class="form-control" required>
                                    <button id="modalPofPartBtnPartNumber" name="btnFilterPilihPartNumber" class="btn btn-icon btn-primary" type="button">
                                            <i class="fa fa-search"></i>
                                    </button>
                                </div>

                            </div>
                            <div class="fv-row mb-6">
                                <label class="form-label">Nama Part:</label>
                                <input id="modalPofPartInputNamaPart" name="nama_part" type="text" class="form-control form-control-solid" readonly>
                            </div>
                            <div class="fv-row mb-6">
                                <label class="form-label">Produk:</label>
                                <input id="modalPofPartInputProduk" name="produk" type="text" class="form-control form-control-solid" readonly>
                            </div>
                            <div class="fv-row mb-6">
                                <label class="form-label required">Jumlah Order:</label>
                                <input id="modalPofPartInputJmlOrder" name="jml_order" class="form-control text-end" required
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                            </div>
                            <div class="fv-row mb-6">
                                <label class="form-label required">Harga:</label>
                                <input id="modalPofPartInputHarga" name="harga" class="form-control text-end" required
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                            </div>
                            <div class="fv-row mb-6">
                                <label class="form-label required">Discount:</label>
                                <input id="modalPofPartInputDiscount" name="discount" class="form-control text-end" required
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                    maxlength="5">
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
                                <label class="fs-7 fw-bold form-label mt-3 required">Discount (%):</label>
                                <input id="modalPofDiscountInputDiscount" name="discount" class="form-control text-end" required
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                    maxlength="5">
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

    @include('layouts.orders.purchaseorderform.purchaseorderformfaktur')
    @include('layouts.option.optionpartnumber')

    @push('scripts')
        <script type="text/javascript">
            const url_route = {
                'option_part_number' : "{{ route('option.part-number') }}",
                'validasi_part_number' : "{{ route('validasi.part-number') }}",
                'pof_terlayani' : "{{ route('orders.purchaseorderform.terlayani') }}",
                'pof_edit_discount' : "{{ route('orders.purchaseorderform.form.edit-discount') }}",
                'pof_update_discount' : "{{ route('orders.purchaseorderform.form.update-discount') }}",
                'pof_update_tpc' : "{{ route('orders.purchaseorderform.form.update-tpc') }}",
                'pof_batal_approve' : "{{ route('orders.purchaseorderform.form.batal-approve') }}",
                'pof_detail_daftar' : "{{ route('orders.purchaseorderform.form.detail.daftar') }}",
                'pof_detail_edit_part' : "{{ route('orders.purchaseorderform.form.detail.edit') }}",
                'pof_detail_hapus_part' : "{{ route('orders.purchaseorderform.form.detail.hapus') }}",
                'pof_detail_simpan_part' : "{{ route('orders.purchaseorderform.form.detail.simpan') }}",
            };
        </script>
        <script src="{{ asset('assets/js/custom/autonumeric.js') }}?v={{ time() }}"></script>
        <script src="{{ asset('assets/js/suma/orders/purchaseorderform/purchaseorderformformdetail.js') }}?v={{ time() }}"></script>
        <script src="{{ asset('assets/js/suma/orders/purchaseorderform/purchaseorderformform.js') }}?v={{ time() }}"></script>
        <script src="{{ asset('assets/js/suma/option/partnumber.js') }}?v={{ time() }}"></script>
    @endpush
@endsection
