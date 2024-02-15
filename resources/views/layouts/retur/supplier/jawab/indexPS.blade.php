@extends('layouts.main.index')
@section('title', $title_menu)
@section('subtitle', $title_page)
@push('styles')
    <style>
        .swal-height {
            height: 80vh;
        }
    </style>
@endpush

@section('container')
    <!--begin::Row-->
    <div class="row gy-5 g-xl-8">
        <div class="card card-xl-stretch shadow">
            <div class="card-body">
                <h3>Form Input Packing Sheet</h3>
                <div class="mb-3 border rounded p-3" id="form_ps">
                    <div class="form-group row mb-2 g-3">
                        <div class="col-sm-3">
                            <label for="no_ps" class="form-label required">No Packing Sheet</label>
                            <input type="text" class="form-control" id="no_ps" name="no_ps" value=""
                                placeholder="Masukkan No Packing Sheet">
                            <div class="invalid-feedback" id="error_no_ps"></div>
                        </div>
                        <div class="col-sm-3">
                            <label for="tgl_retur" class="form-label required">Tanggal Packing Sheet</label>
                            <input type="text" class="form-control" id="tgl_ps" name="tgl_ps"
                                placeholder="Masukkan Tanggal" value="{{ date('Y-m-d') }}">
                            <div class="invalid-feedback" id="error_tgl_ps"></div>
                        </div>
                        <div class="col-sm-6">
                        </div>
                        <div class="col-sm-3">
                            <label for="kd_part" class="form-label required">Part Number</label>
                            <input type="text" class="form-control" id="kd_part" name="kd_part" value=""
                                placeholder="Masukkan Part Number">
                            <div class="invalid-feedback" id="error_kd_part"></div>
                        </div>
                        <div class="col-sm-3">
                            <label for="nm_part" class="form-label">Nama Part</label>
                            <input type="text" class="form-control" id="nm_part" name="nm_part" value=""
                                disabled>
                        </div>
                        <div class="col-sm-3">
                            <label for="qty" class="form-label required">Jumlah</label>
                            <input type="text" class="form-control" id="qty" name="qty" value=""
                                placeholder="qty Part" onkeyup="this.value = formatRibuan(this.value)">
                            <div class="invalid-feedback" id="error_qty"></div>
                        </div>
                    </div>
                    <div class="form-group row mb-2 g-3">
                        <div class="col-sm-9">
                            <label for="qty" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="ket" name="ket" data-kt-autosize="true" placeholder="Masukkan Keterangan"
                                rows="1" maxlength="255"></textarea>
                            <div class="invalid-feedback" id="error_ket"></div>
                        </div>
                        <div class="col-sm-3 d-flex justify-content-end align-items-end">
                            <a role="button" id="tambah_ps" class="btn btn-success">Tambah</a>
                        </div>
                    </div>
                </div>

                <h3>Daftar Packing Sheet</h3>
                <div class="my-3 row justify-content-between gap-2" id="filter_table">
                    <div class="input-group w-lg-500px" id="filter_search">
                        <input type="search" class="form-control w-md-150px" placeholder="Search" aria-label="Search" id="search_input">
                        <select class="form-select" id="select_search">
                            <option value="">Pilih Kolom</option>
                            <option value="no_ps">No PS</option>
                            <option value="kd_part">Part Number</option>
                            <option value="no_retur">No Retur (Supplier)</option>
                            <option value="no_klaim">No Retur (Toko)</option>
                            <option value="kd_dealer">Dealer</option>
                        </select>
                        <button class="btn btn-secondary" type="button" id="btn_search">Cari</button>
                    </div>
                    <div class="input-group w-md-250px">
                        <input type="text" class="form-control" placeholder="tgl" aria-label="tgl" id="tgl_input" value="">
                    </div>
                </div>
                <div class="table-responsive border rounded-3">
                    <table class="table table-row-dashed table-row-gray-300 align-middle border">
                        <thead class="border">
                            <tr class="fs-8 fw-bolder text-muted text-center">
                                <th rowspan="2" class="w-50px ps-3 pe-3">No</th>
                                <th rowspan="2" class="w-100px ps-3 pe-3">No PS</th>
                                <th rowspan="2" class="w-100px ps-3 pe-3">Tanggal PS</th>
                                <th rowspan="2" class="w-100px ps-3 pe-3">Part Number</th>
                                <th rowspan="2" class="w-auto ps-3 pe-3">Nama Part</th>
                                <th colspan="2" class="w-80px ps-3 pe-3">Jumlah</th>
                                <th rowspan="2" class="w-auto ps-3 pe-3">keterangan</th>
                                <th colspan="2" class="w-auto ps-3 pe-3">Detail</th>
                                <th rowspan="2" class="w-100px ps-3 pe-3">Dealer</th>
                                <th rowspan="2" class="w-auto">Action</th>
                            </tr>
                            <tr class="fs-8 fw-bolder text-muted text-center">
                                <th class="w-80px ps-3 pe-3">Part PS</th>
                                <th class="w-80px ps-3 pe-3">Terpakai</th>
                                <th class="w-auto ps-3 pe-3">No Retur (Supplier)</th>
                                <th class="w-auto ps-3 pe-3">No Retur (Toko)</th>
                            </tr>
                        </thead>
                        <tbody id="daftarReturSupplier">
                            <tr class="fw-bolder fs-8 border">
                                <td colspan="99" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                {{-- tombol Simpan --}}
                <a role="button" id="simpan_jwb" class="btn btn-primary">Simpan</a>
                <a href="{{ URL::previous() }}" id="btn-back" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
    <!--end::Row-->

    <div class="modal fade" id="add_modal_retur" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-2"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="">
                        <span class="d-block mb-0 pb-0 fs-4 fw-bold" id="jwb_no_ps">-</span>
                        <div>
                            <span class="d-inline fw-bold mt-0 pt-0 fs-5" id="jwb_kd_part">-</span>
                            <span class="d-inline mt-0 pt-0 fs-5" id="jwb_nm_part">-</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="card-body">
                    <h3 class="mt-10">Form Jawab Retur By Packing Sheet</h3>
                    <div class="border rounded-3 p-3 mb-10 mt-3 row g-3">
                        <div class="col-sm-4">
                            <label for="no_retur" class="form-label required">No Retur Toko (no Klaim)</label>
                            <div class="input-group mb-3 has-validation">
                                <button class="btn btn-primary list-klaim" type="button"><i
                                        class="fas fa-search"></i></button>
                                <input type="text" class="form-control" id="no_retur" name="no_retur"
                                    placeholder="Masukkan No Retur Toko" value="">
                                <div class="invalid-feedback" id="error_no_retur"></div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label for="no_ca" class="form-label">No Retur Supplier (No CA)</label>
                            <input type="text" class="form-control" id="no_ca" name="no_ca" value=""
                                disabled>
                        </div>
                        <div class="col-sm-4">
                            <label for="jml" class="form-label required">Qty Ganti</label>
                            <input type="text" class="form-control" id="jml" name="jml"
                                placeholder="Masukkan qty Ganti" value="" min="1"
                                onkeyup="this.value = formatRibuan(this.value)" required>
                            <div class="invalid-feedback" id="error_jml"></div>
                        </div>
                        <div class="col-sm-4">
                            <label for="alasan" class="form-label required">Alasan</label>
                            <select name="alasan" id="alasan" class="form-select form-control"
                                aria-label="Default select example" required>
                                <option value="RETUR">Ganti barang</option>
                                <option value="CA">Ganti Uang</option>
                            </select>
                            <div class="invalid-feedback" id="error_alasan"></div>
                        </div>
                        <div class="col-sm-4">
                            <label for="ca" class="form-label">Jumlah Uang</label>
                            <input type="text" class="form-control" id="ca" name="ca" min="1"
                                placeholder="Masukkan Jumlah Uang Jika Ganti Uang" value=""
                                onkeyup="this.value = formatRibuan(this.value)" disabled>
                            <div class="invalid-feedback" id="error_ca"></div>
                        </div>
                        <div class="col-sm-4">
                            <label for="keputusan" class="form-label required">Keputusan</label>
                            <select name="keputusan" id="keputusan" class="form-select"
                                aria-label="Default select example" required>
                                <option value="TERIMA">TERIMA</option>
                                <option value="TOLAK">TOLAK</option>
                            </select>
                            <div class="invalid-feedback" id="error_keputusan"></div>
                        </div>
                        <div class="col-sm-6">
                            <label for="ket" class="form-label required">Keterangan</label>
                            <textarea type="text" class="form-control" data-kt-autosize="true" id="ket" name="ket"
                                rows="1"></textarea>
                            <div class="invalid-feedback" id="error_ket"></div>
                        </div>
                        <div class="col-sm-6 d-flex justify-content-end align-items-end">
                            <button type="button" class="btn btn-success text-white" id="simpan_ps_detail"
                                data-a="">Tambah</button>
                        </div>
                    </div>
                    <h3>List Jawaban</h3>
                    <div id="list_jwb" class="table-responsive border rounded-3">
                        <table id="datatable_classporduk"
                            class="table table-row-dashed table-row-gray-300 align-middle border">
                            <thead class="border">
                                <tr class="fs-8 fw-bolder text-muted text-center">
                                    <th rowspan="2" class="w-125px ps-3 pe-3">Tanggal</th>
                                    <th colspan="2" class="w-50px ps-3 pe-3">No Retur</th>
                                    <th rowspan="2" class="w-50px ps-3 pe-3">Qty Jawab</th>
                                    <th rowspan="2" class="w-100px ps-3 pe-3">Alasan</th>
                                    <th rowspan="2" class="w-100px ps-3 pe-3">Ganti Uang</th>
                                    <th rowspan="2" class="w-100px ps-3 pe-3">Keputusan</th>
                                    <th rowspan="2" class="w-auto ps-3 pe-3">Keterangan</th>
                                    <th rowspan="2" class="w-auto">Action</th>
                                </tr>
                                <tr class="fs-8 fw-bolder text-muted text-center">
                                    <th class="w-50px ps-3 pe-3">CA</th>
                                    <th class="w-50px ps-3 pe-3">Toko</th>
                                </tr>
                            </thead>
                            <tbody id="list-jwb">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                </div>
            </div>
        </div>
    </div>


    <!--begin::Modal Part data-->
    <div class="modal fade" tabindex="-1" id="klaim-list">
    </div>
    <!--end::Modal Part data-->
@endsection

@push('scripts')
    <!-- script tambanhan -->
    <script language="JavaScript" src="{{ asset('assets/js/custom/serviceApi.js') }}?v={{ time() }}"></script>
    <script language="JavaScript"
        src="{{ asset('assets/js/suma/retur/supplier/jawaban/indexPS.js') }}?v={{ time() }}"></script>
    <script language="JavaScript"
        src="{{ asset('assets/js/suma/retur/supplier/jawaban/indexPSDetail.js') }}?v={{ time() }}"></script>
@endpush
