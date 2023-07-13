@extends('layouts.main.index')
@section('title','Form')
@section('subtitle','Retur Konsumen')
@push('styles')
@endpush

@section('container')

<!--begin::Row-->
<div class="row gy-5 g-xl-8">
    <div class="card card-xl-stretch shadow">
        <form action="" id="form-main" method="POST" enctype="multipart/form-data">
            <div class="card-body">
                @csrf
                <h3>1. Informasi Dokumen</h3>
                <div class="mb-3 border rounded p-2">
                    <div class="form-group row mb-2">
                        <label for="jml" class="col-sm-2 col-form-label">No Dokumen</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="jml" name="jml" placeholder="No dokumen" value="" disabled>
                        </div>
                        <label for="tgl_claim" class="col-sm-2 col-form-label">Tanggal Claim</label>
                        <div class="col-sm-4">
                            <input class="form-control" id="tgl_claim" name="tgl_claim" placeholder="Tanggal" value="">
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="kd_supp" class="col-sm-2 col-form-label">Supplier</label>
                        <div class="col-sm-4">
                            <select name="kd_supp" id="kd_supp" class="form-select form-control" data-control="select2" data-placeholder="Pilih Supplier">
                                <option value="">Pilih Supplier</option>
                            </select>
                        </div>
                    </div>
                </div>

                <h3>2. Informasi Produk</h3>
                <div class="mb-3 border rounded p-2">
                    <div class="form-group row mb-2">
                        <label for="no_retur" class="col-sm-2 col-form-label">No Retur</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="no_retur" name="no_retur" placeholder="Masukkan No Retur" value="" required>
                        </div>

                        <label for="tgl_Retur" class="col-sm-2 col-form-label">Tanggal Retur</label>
                        <div class="col-sm-4">
                            <input class="form-control bg-secondary" id="tgl_Retur" name="tgl_Retur" placeholder="Tanggal Retur" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="kd_part" class="col-sm-2 col-form-label required">Part Number</label>
                        <div class="col-sm-4">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="kd_part" name="kd_part" placeholder="Part Number" value="" required>
                                <button class="btn btn-primary list-part" type="button">Pilih</button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" class="form-control bg-secondary" id="ket_part" name="ket_part" placeholder="Nama Part" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="qty_claim" class="col-sm-2 col-form-label required">QTY Claim</label>
                        <div class="col-sm-4">
                            <input type="number" class="form-control" id="qty_claim" name="qty_claim" placeholder="QTY Claim" value="" required>
                        </div>
                    </div>
                </div>
                
                <h3>3. Informasi Retur</h3>
                <div class="mb-3 border rounded p-2">
                    <div class="form-group row mb-2">
                        <label for="ket" class="col-sm-2 col-form-label">Keterangan</label>
                        <div class="col-sm-10">
                            <textarea type="text" class="form-control" data-kt-autosize="true" id="ket" name="ket" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btn-submit" class="btn btn-primary">Simpan</button>
                <a href="{{ url()->previous() }}" id="btn-back" class="btn btn-secondary" onclick="blockUI.block();">Kembali</a>
            </div>
        </form>
    </div>
</div>
<!--end::Row-->

<!--begin::Modal Dealer data-->
<div class="modal fade" tabindex="-1" id="dealer-list">
</div>
<!--end::Modal Dealer data-->


<!--begin::Modal Part data-->
<div class="modal fade" tabindex="-1" id="part-list">
</div>
<!--end::Modal Part data-->

@endsection

@push('scripts')
<!-- JQuery CDN -->
{{-- <script src="https://code.jquery.com/jquery-3.6.0.js"></script> --}}
<script>
    $('body').on('keydown', 'input, select, textarea', function(e) {
        var self = $(this),
            form = self.parents('form:eq(0)'),
            focusable,
            next;
        if (e.keyCode == 13) {
            focusable = form.find('input,a,select,button,textarea').filter(':visible');
            next = focusable.eq(focusable.index(this) + 1);
            if (next.length) {
                next.focus();
            } else {
                form.submit();
            }
            return false;
        }
    });
</script>
<!-- module Custum -->
{{-- <script language="JavaScript" src="{{ asset('assets/js/moduls/dpicker.js') }}?v={{ time() }}"></script>
<script language="JavaScript" src="{{ asset('assets/js/moduls/formatRp.js') }}?v={{ time() }}"></script> --}}

<!-- script tambanhan -->
<script language="JavaScript" src="{{ asset('assets/js/suma/retur/konsumen/form.js') }}?v={{ time() }}"></script>
<script language="JavaScript" src="{{ asset('assets/js/suma/retur/konsumen/getDealer.js') }}?v={{ time() }}"></script>
<script language="JavaScript" src="{{ asset('assets/js/suma/retur/konsumen/getFaktur.js') }}?v={{ time() }}"></script>
<script language="JavaScript" src="{{ asset('assets/js/suma/retur/konsumen/getPart.js') }}?v={{ time() }}"></script>

{{-- <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script> --}}
@endpush