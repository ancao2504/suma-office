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
                        <button class="btn btn-primary" id="btnTambahPartNumber" name="btnTambahPartNumber" data-backdrop="static" data-keyboard="false">Tambah Part Number</button>
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
            $('ul.pagination').hide();
            $(function() {
                $('.scrolling-pagination').jscroll({
                    autoTrigger: true,
                    padding: 0,
                    nextSelector: '.pagination li.active + li a',
                    contentSelector: 'div.scrolling-pagination',
                    callback: function() {
                        $('ul.pagination').remove();
                    }
                });
            });

            $(document).ready(function() {
                $('#modalEntryPartNumber').modal({
                    backdrop: 'static', keyboard: false
                });

                // $("form").bind("keypress", function (e) {
                //     if (e.keyCode == 13) {
                //         return false;
                //     }
                // });

                loadDetailPurchaseOrder();

                function loadDetailPurchaseOrder() {
                    var nomorPof = $('#inputNomorPof').val();
                    $('#detailPurchaseOrderForm').load('<?php echo url("orders/purchaseorder/view/detail/'+nomorPof+'");?>');
                }

                var tablePartNumber = $('#tableSearchPartNumber').DataTable();

                function loadDataPartNumber() {
                    part_number = $('#modalPofPartInputPartNumber').val();

                    tablePartNumber.destroy();
                    tablePartNumber = $("#tableSearchPartNumber").DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: "{{ route('option.option-part-number') }}",
                            data: { part_number: part_number }
                        },
                        columns: [
                            { data:'part_number', name: 'part_number', render: function(data) { return '<span class="text-gray-700 fw-bold fs-6">'+data+'</span>' }},
                            { data:'description', name: 'description', render: function(data) { return '<span class="text-gray-700 fw-bold fs-6">'+data+'</span>' }},
                            { data:'produk', name: 'produk', render: function(data) { return '<span class="text-gray-700 fw-bold fs-6">'+data+'</span>' }},
                            { data:'het', name: 'het', render: function(data) { return '<span class="text-gray-700 fw-bold fs-6">'+data+'</span>' }},
                            { data:'action', name:'action', orderable: false, searchable: false }
                        ]
                    });
                }

                function clearModalPofPart() {
                    $('#modalPofPartInputPartNumber').val('');
                    $('#modalPofPartInputNamaPart').val('');
                    $('#modalPofPartInputProduk').val('');
                    $('#modalPofPartInputJmlOrder').val(1);
                    $('#modalPofPartInputHarga').val(0);
                    $('#modalPofPartInputDiscount').val(0);
                }

                $('input').on('keydown',function(e){
                    var keyCode = e.keyCode || e.which;
                    if(e.keyCode === 13) {
                    e.preventDefault();
                        $('input')[$('input').index(this)+1].focus();
                    }
                });

                $('#modalPofPartInputPartNumber').blur(function() {
                    var part_number = $('#modalPofPartInputPartNumber').val();
                    var _token = $('input[name="_token"]').val();

                    if(part_number == "") {
                        clearModalPofPart();
                    } else {
                        if (part_number.includes('?')) {
                            loadDataPartNumber();
                            $('#modalTitlePartNumber').html("Pilih Data Part Number");
                            $('#partNumberSearchForm').trigger('reset');
                            $('#partNumberSearchModal').modal('show');
                        } else {
                            $.ajax({
                                url: "{{ route('validasi.validasi-part-number') }}",
                                method: "POST",
                                dataType: "JSON",
                                data: { part_number: part_number, _token : _token },

                                success:function(response) {
                                    if (response.status == false) {
                                        $('#messageErrorPofPart').html('<div class="alert alert-dismissible bg-danger d-flex flex-column flex-sm-row p-5 mb-10">'+
                                            '<span class="svg-icon svg-icon-2hx svg-icon-light me-4 mb-5 mb-sm-0">'+
                                                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">'+
                                                    '<path opacity="0.3" d="M12 22C13.6569 22 15 20.6569 15 19C15 17.3431 13.6569 16 12 16C10.3431 16 9 17.3431 9 19C9 20.6569 10.3431 22 12 22Z" fill="currentColor"></path>'+
                                                    '<path d="M19 15V18C19 18.6 18.6 19 18 19H6C5.4 19 5 18.6 5 18V15C6.1 15 7 14.1 7 13V10C7 7.6 8.7 5.6 11 5.1V3C11 2.4 11.4 2 12 2C12.6 2 13 2.4 13 3V5.1C15.3 5.6 17 7.6 17 10V13C17 14.1 17.9 15 19 15ZM11 10C11 9.4 11.4 9 12 9C12.6 9 13 8.6 13 8C13 7.4 12.6 7 12 7C10.3 7 9 8.3 9 10C9 10.6 9.4 11 10 11C10.6 11 11 10.6 11 10Z" fill="currentColor"></path>'+
                                                '</svg>'+
                                            '</span>'+
                                            '<div class="d-flex flex-column text-light pe-0 pe-sm-10">'+
                                                '<h4 class="mb-2 text-light">Informasi</h4>'+
                                                '<span>'+response.message+'</span>'+
                                            '</div>'+
                                            '<button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">'+
                                                '<span class="svg-icon svg-icon-2x svg-icon-light">'+
                                                    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">'+
                                                        '<rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor"></rect>'+
                                                            '<rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor"></rect>'+
                                                    '</svg>'+
                                                '</span>'+
                                            '</button>'+
                                        '</div>');
                                        clearModalPofPart();

                                        $('#modalPofPartInputPartNumber').focus();
                                    } else {
                                        $('#messageErrorPofPart').html('');
                                        $('#modalPofPartInputPartNumber').val(response.data.part_number);
                                        $('#modalPofPartInputNamaPart').val(response.data.description);
                                        $('#modalPofPartInputProduk').val(response.data.produk);
                                        $('#modalPofPartInputHarga').val(response.data.het);
                                        $('#modalPofPartInputJmlOrder').focus();
                                    }
                                }
                            })
                        }
                    }
                });

                $('body').on('click', '#selectPartNumber', function(e) {
                    e.preventDefault();
                    var part_number = $(this).data('part_number');
                    var description = $(this).data('description');
                    var produk = $(this).data('produk');
                    var het = $(this).data('het');

                    $('#modalPofPartInputPartNumber').val(part_number);
                    $('#modalPofPartInputNamaPart').val(description);
                    $('#modalPofPartInputProduk').val(produk);
                    $('#modalPofPartInputHarga').val(het);

                    $('#partNumberSearchModal').modal('hide');
                    $('#modalPofPartInputPartNumber').focus();
                });

                $('body').on('click', '#btnTambahPartNumber', function () {
                    var tpc = $('#selectKodeTpc option:selected').val();
                    $('#modalEntryPartTitle').html("Tambah Part Number");

                    clearModalPofPart();

                    $('#modalEntryPartNumber').trigger('reset');
                    $('#modalPofPartInputPartNumber').attr('readonly', false);
                    $('#modalPofPartInputPartNumber').removeClass('form-control-solid');

                    if(tpc == '14') {
                        $('#modalPofPartInputHarga').attr('readonly', true);
                        $('#modalPofPartInputDiscount').attr('readonly', false);

                        $('#modalPofPartInputHarga').addClass('form-control-solid');
                        $('#modalPofPartInputDiscount').removeClass('form-control-solid');
                    } else {
                        $('#modalPofPartInputHarga').attr('readonly', false);
                        $('#modalPofPartInputDiscount').attr('readonly', true);

                        $('#modalPofPartInputHarga').removeClass('form-control-solid');
                        $('#modalPofPartInputDiscount').addClass('form-control-solid');
                    }

                    $('#modalEntryPartNumber').modal('show');

                    $('#modalEntryPartNumber').on('shown.bs.modal', function () {
                        $("#modalPofPartInputPartNumber").focus();
                    });
                });

                $('body').on('click', '#btnEditPofPart', function () {
                    var nomor_pof = $('#inputNomorPof').val();;
                    var part_number = $(this).data('kode');
                    var _token = $('input[name="_token"]').val();

                    $.ajax({
                        url: "{{ route('orders.purchase-order-form-edit-part') }}",
                        method: "POST",
                        data: { nomor_pof: nomor_pof, part_number: part_number, _token: _token },

                        success:function(response) {
                            if (response.status == true) {
                                $('#modalEntryPartTitle').html("Edit Part Number");

                                $('#modalPofPartInputPartNumber').val(response.data.part_number);
                                $('#modalPofPartInputNamaPart').val(response.data.nama_part);
                                $('#modalPofPartInputProduk').val(response.data.produk);
                                $('#modalPofPartInputJmlOrder').val(response.data.jml_order);
                                $('#modalPofPartInputHarga').val(response.data.harga);
                                $('#modalPofPartInputDiscount').val(response.data.disc_detail);

                                $('#modalPofPartInputPartNumber').attr('readonly', true);
                                $('#modalPofPartInputPartNumber').addClass('form-control-solid');

                                if(response.data.tpc == '14') {
                                    $('#modalPofPartInputHarga').attr('readonly', true);
                                    $('#modalPofPartInputDiscount').attr('readonly', false);

                                    $('#modalPofPartInputHarga').addClass('form-control-solid');
                                    $('#modalPofPartInputDiscount').removeClass('form-control-solid');
                                } else {
                                    $('#modalPofPartInputHarga').attr('readonly', false);
                                    $('#modalPofPartInputDiscount').attr('readonly', true);

                                    $('#modalPofPartInputHarga').removeClass('form-control-solid');
                                    $('#modalPofPartInputDiscount').addClass('form-control-solid');
                                }

                                $('#modalEntryPartNumber').modal('show');

                                $('#modalEntryPartNumber').on('shown.bs.modal', function () {
                                    $("#modalPofPartInputJmlOrder").focus();
                                });
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
                        }
                    })
                });

                $('body').on('click', '#btnDeletePofPart', function () {
                    var nomor_pof = $('#inputNomorPof').val();;
                    var part_number = $(this).data('kode');
                    var _token = $('input[name="_token"]').val();

                    Swal.fire({
                        html: `Apa anda yakin akan menghapus part number <strong>`+part_number+`</strong> ?`,
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
                            $.ajax({
                                url: "{{ route('orders.purchase-order-form-hapus-part') }}",
                                method: "POST",
                                data: { nomor_pof: nomor_pof, part_number: part_number, _token: _token },

                                success:function(response) {
                                    if (response.status == true) {
                                        Swal.fire({
                                            text: response.message,
                                            icon: "success",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn btn-success"
                                            }
                                        });
                                        loadDetailPurchaseOrder();
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
                                }
                            })
                        }
                    });
                });

                $('body').on('click', '#btnSimpanPofPart', function () {
                    var nomor_pof = $('#inputNomorPof').val();
                    var part_number = $('#modalPofPartInputPartNumber').val();
                    var jml_order = $('#modalPofPartInputJmlOrder').val();
                    var harga = $('#modalPofPartInputHarga').val();
                    var discount = $('#modalPofPartInputDiscount').val();
                    var _token = $('input[name="_token"]').val();

                    $.ajax({
                        url: "{{ route('orders.purchase-order-form-simpan-part') }}",
                        method: "POST",
                        data: { nomor_pof: nomor_pof, part_number: part_number, jml_order: jml_order,
                                harga: harga, discount: discount, _token: _token },

                        success:function(response) {
                            if (response.status == true) {
                                Swal.fire({
                                    text: response.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-success"
                                    }
                                });
                                $('#modalEntryPartNumber').modal('hide');
                                loadDetailPurchaseOrder();
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
                        }
                    })
                });

                $('body').on('click', '#btnEditPofDiscount', function () {
                    var nomor_pof = $('#inputNomorPof').val();
                    var _token = $('input[name="_token"]').val();

                    $.ajax({
                        url: "{{ route('orders.purchase-order-form-edit-discount') }}",
                        method: "POST",
                        data: { nomor_pof: nomor_pof, _token: _token },

                        success:function(response) {
                            if (response.status == true) {
                                $('#modalDiscountTitle').html("Edit Discount");
                                $('#modalPofDiscountInputDiscount').val(response.data.discount);

                                $('#modalEntryDiscount').modal('show');

                                $('#modalEntryDiscount').on('shown.bs.modal', function () {
                                    $("#modalPofDiscountInputDiscount").focus();
                                });
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
                        }
                    })
                });

                $('body').on('click', '#btnSimpanPofDiscount', function () {
                    var nomor_pof = $('#inputNomorPof').val();
                    var discount = $('#modalPofDiscountInputDiscount').val();
                    var _token = $('input[name="_token"]').val();

                    $.ajax({
                        url: "{{ route('orders.purchase-order-form-update-discount') }}",
                        method: "POST",
                        data: { nomor_pof: nomor_pof, discount: discount, _token: _token },

                        success:function(response) {
                            if (response.status == true) {
                                Swal.fire({
                                    text: response.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-success"
                                    }
                                });
                                $('#modalEntryDiscount').modal('hide');
                                loadDetailPurchaseOrder();
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
                        }
                    })
                });

                $('#selectKodeTpc').change(function() {
                    var nomor_pof = $('#inputNomorPof').val();
                    var tpc = $('#selectKodeTpc option:selected').val();
                    var _token = $('input[name="_token"]').val();

                    event.preventDefault();
                    const url = $(this).attr('href');

                    var tpc_sebelumnya = '19';

                    if(tpc == '14') {
                        tpc_sebelumnya = '20';
                    } else {
                        tpc_sebelumnya = '14';
                    }

                    Swal.fire({
                        html: `Apa anda yakin akan mengubah <strong>Kode TPC `+tpc_sebelumnya+`</strong> akan
                                diubah menjadi <strong>Kode TPC `+tpc+`</strong> ?`,
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
                            $.ajax({
                                url: "{{ route('orders.purchase-order-form-update-tpc') }}",
                                method: "POST",
                                data: {
                                    nomor_pof: nomor_pof, tpc: tpc, _token: _token
                                },
                                success:function(response) {
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
                                        loadDetailPurchaseOrder();
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
                                        if(tpc_sebelumnya == '14') {
                                            $("#selectKodeTpc").html('<option value="14" selected>14</option>'+
                                                                    '<option value="20">20</option>');
                                        } else {
                                            $("#selectKodeTpc").html('<option value="14">14</option>'+
                                                                    '<option value="20" selected>20</option>');
                                        }
                                    }
                                }
                            })
                        } else {
                            if(tpc_sebelumnya == '14') {
                                $("#selectKodeTpc").html('<option value="14" selected>14</option>'+
                                                        '<option value="20">20</option>');
                            } else {
                                $("#selectKodeTpc").html('<option value="14">14</option>'+
                                                        '<option value="20" selected>20</option>');
                            }
                        }
                    });
                });

                $("#btnBatalApprovePof").click(function(e) {
                    e.preventDefault();
                    var nomor_pof = $('#inputNomorPof').val();
                    var _token = $('input[name="_token"]').val();

                    Swal.fire({
                        html: `Apa anda yakin akan membatalkan <strong>Status Approve nomor pof `+nomor_pof+`</strong> ini ?`,
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
                            $.ajax({
                                url: "{{ route('orders.purchase-order-form-batal-approve') }}",
                                method: "POST",
                                data: { nomor_pof: nomor_pof, _token: _token },

                                success:function(response) {
                                    if (response.status == true) {
                                        Swal.fire({
                                            text: response.message,
                                            icon: 'success',
                                            showCancelButton: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn btn-success"
                                            }
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = window.location.href;
                                            }
                                        })
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
                                }
                            })
                        }
                    });
                });

                $('body').on('click', '#viewDetailPofTerlayani', function () {
                    var nomor_pof = $('#inputNomorPof').val();
                    var part_number = $(this).data('kode');
                    var _token = $('input[name="_token"]').val();

                    $('#modalTitle').html(nomor_pof);
                    $('#modalSubTitle').html(part_number);

                    $.ajax({
                        url: "{{ route('orders.purchase-order-form-terlayani') }}",
                        method: "POST",
                        data: { nomor_pof: nomor_pof, part_number: part_number, _token: _token },

                        success:function(response) {
                            if (response.status == true) {
                                list_faktur = response.data;
                                $('#modalListFaktur').html(list_faktur);
                                $('#FormPofTerlayani').trigger('reset');
                                $('#modalPofPartTerlayani').modal('show');
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
                        }
                    })
                });
            });
        </script>
    @endpush
@endsection
