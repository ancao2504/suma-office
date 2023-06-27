@extends('tamplate.main')

@push('styles')
@endpush

@section('content')

<!--begin::Row-->
<div class="row gy-5 g-xl-8">
    <div class="card card-xl-stretch shadow">
        <div class="card-body">
            <div class="mb-3 border rounded p-2">
                <div class="form-group row mb-2">
                    <label for="no_retur" class="col-sm-2 col-form-label">No Retur</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control bg-secondary" id="no_retur" name="no_retur" placeholder="Masukkan No Retur" value="{{ $data->no_retur }}" readonly>
                    </div>
                    <div class="col-sm-4">
                        <input type="text" class="form-control bg-secondary" id="kd_sales" name="kd_sales" placeholder="Masukkan kd sales" value="{{ $data->kd_sales }}" hidden>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label for="no_faktur" class="col-sm-2 col-form-label">No Faktur</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="no_faktur" name="no_faktur" placeholder="Masukkan No Faktur" value="{{ $data->no_faktur }}" required>
                    </div>
                    <label for="tgl_fak" class="col-sm-2 col-form-label">Tanggal Faktur</label>
                    <div class="col-sm-3">
                        <input class="form-control" id="tgl_fak" name="tgl_fak" placeholder="Masukkan Part Number" value="{{ $data->tgl_faktur }}" disabled>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label for="kd_part" class="col-sm-2 col-form-label">Part Number</label>
                    <div class="col-sm-4">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="kd_part" name="kd_part" placeholder="Part Number" value="{{ $data->kd_part }}" required>
                            <button class="btn btn-primary list-part" type="button" data-bs-toggle="modal" data-bs-target="#part-list">Pilih</button>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <input type="text" class="form-control bg-secondary" id="ket_part" name="ket_part" placeholder="Nama Part" value="{{ $data->nm_part }}" readonly>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label for="qty_faktur" class="col-sm-2 col-form-label">QTY Faktur</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="qty_faktur" name="qty_faktur" placeholder="Masukkan QTY Faktur" value="{{ $data->qty_faktur }}" disabled>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label for="qty_claim" class="col-sm-2 col-form-label">QTY Claim</label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control" id="qty_claim" name="qty_claim" placeholder="Masukkan QTY Claim" value="{{ $data->jumlah }}" required>
                    </div>
                    <label for="harga" class="col-sm-2 col-form-label">Harga</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="harga" name="harga" placeholder="Masukkan Harga" value="{{ $data->harga }}" required>
                        </div>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label for="disc" class="col-sm-2 col-form-label">Disc</label>
                    <div class="col-sm-4">
                        <div class="input-group mb-3">
                            <input type="number" class="form-control" id="disc" name="disc" placeholder="Masukkan Diskon" value="{{ number_format($data->disc) }}">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <label for="total" class="col-sm-2 col-form-label">Total</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="total" name="total" placeholder="Masukkan Total" value="{{ number_format($data->nilai) }}" disabled>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3 border rounded p-2">
                <div class="form-group row mb-2">
                    <label for="ket" class="col-sm-2 col-form-label">Keterangan</label>
                    <div class="col-sm-10">
                        <textarea type="text" class="form-control" data-kt-autosize="true" id="ket" name="ket" rows="1">{{ $data->ket }}</textarea>
                        @error('ket')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label for="sts" class="col-sm-2 col-form-label">Status</label>
                    <div class="col-sm-4">
                        <select name="sts" id="sts" class="form-select form-control" required>
                            <option>Pilih Keterangan Ganti</option>
                            <option value="RETUR">Retur (Ganti Uang)</option>
                            <option value="GANTI BARANG">Ganti barang</option>
                            <option value="CLAIM AHM">Claim ke AHM</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row mb-2">

                    <label for="terbayar" class="col-sm-2 col-form-label">Terbayar</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <span class="input-group-text" id="terbayar">Rp</span>
                            <input type="text" class="form-control" id="terbayar" name="terbayar" placeholder="Masukkan Jumlah Terbayar" value="0" disabled>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary btn-submit">Update</button>
            <a href="{{ Route('edit.returkonsumen',['no_retur'=> encrypt($data->no_retur)]); }}" id="btn-back" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</div>
<!--end::Row-->

<!--begin::Modal Part data-->
<div class="modal fade" tabindex="-1" id="part-list">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">List Part</h5>
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </div>
                <!--end::Close-->
            </div>
            <div class="modal-body">
                <div class="input-group px-3">
                    <input type="text" class="form-control" id="kd_part_cari" name="kd_part_cari" placeholder="Masukkan Kd Part" value="" required>
                    <button class="btn btn-secondary cari-part" type="button">Cari</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover caption-top px-2">
                        <thead>
                            <tr class="fw-bold fs-7 text-dark border-bottom border-gray-200 py-4">
                                <th scope="col">Action</th>
                                <th scope="col">Kd Part</th>
                                <th scope="col">Nama Part</th>
                            </tr>
                        </thead>
                        <tbody class="tbody">
                        </tbody>
                    </table>
                </div>
                <div class="card-footer justify-content-center">
                    <div colspan="8">
                        <div class="form-group mb-2 w-80px">
                            <select class="form-select form-select-sm" name="page" id="page">
                                <option value="10" selected>10</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="500">500</option>
                                <option value="1000">1000</option>
                            </select>
                        </div>
                        <nav aria-label="...">
                            <ul class="pagination justify-content-center">
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light close" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal Part data-->

@endsection

@push('scripts')
<!-- JQuery CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>

<!-- module Custum -->
<script src="{{ asset('assets/js/custom/custum-js-suma/moduls/formatRp.js') }}"></script>

<!-- script tambanhan -->
<script>
    const old = {
        status: "{{ $data->status }}"
    }
</script>

<script>
    $('#sts option[value="' + old.status + '"]').attr('selected', 'selected');

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
<script language="JavaScript" src="{{ asset('assets/js/custom/custumMaster/kalimKonsumen/getFaktur.js') }}?v={{ time() }}"></script>
<script>
    disc1 = `{{ $data->disc1 }}`
</script>
<script language="JavaScript" src="{{ asset('assets/js/custom/custumMaster/kalimKonsumen/returKonsumenDetailEdit.js') }}?v={{ time() }}"></script>
@endpush