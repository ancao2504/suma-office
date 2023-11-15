@extends('layouts.main.index')
@section('title', $title_menu)
@section('subtitle', $title_page)
@push('styles')
@endpush

@section('container')
<!--begin::Row-->
<div class="row gy-5 g-xl-8">
    <div class="card card-xl-stretch shadow">
        <div class="card-body">
            <h3><span class="text-muted"></span>{{ request('no_retur')??'-'}}</h3>
            <div id="list_detail" class="table-responsive border rounded-3">
                <table id="datatable_classporduk" class="table table-row-dashed table-row-gray-300 align-middle border">
                    <thead class="border">
                        <tr class="fs-8 fw-bolder text-muted text-center">
                            <th rowspan="2" class="w-50px ps-3 pe-3">No</th>
                            <th rowspan="2" class="w-100px ps-3 pe-3">Tanggal Klaim</th>
                            <th rowspan="2" class="w-100px ps-3 pe-3">No Klaim</th>
                            <th rowspan="2" class="w-150px ps-3 pe-3">Part Number</th>
                            <th rowspan="2" class="w-300px ps-3 pe-3">Nama Part</th>
                            <th colspan="2" class="w-100px ps-3 pe-3">Qty</th>
                            <th rowspan="2" class="w-150px ps-3 pe-3">Ket Jawab</th>
                            <th rowspan="2" class="min-w-100px ps-3 pe-3">Action</th>
                        </tr>
                        <tr class="fs-8 fw-bolder text-muted text-center">
                            <th class="ps-3 pe-3">Klaim</th>
                            <th class="ps-3 pe-3">Jawab</th>
                        </tr>
                    </thead>
                    <tbody id="list-klaim">
                        @if (empty($data->detail) || count($data->detail) == 0)
                            <tr class="fw-bolder fs-8 border text_not_data">
                                <td colspan="9" class="text-center">Tidak ada data</td>
                            </tr>
                        @else
                            @foreach ($data->detail as $detail)
                            @php
                                $jwb_detail = (object)[
                                    'tamp'=>1,
                                    'no_retur' => $data->no_retur,
                                    'no_klaim' => $detail->no_klaim,
                                    'kd_part' => $detail->kd_part,
                                    'no_produksi_list' => $detail->no_produksi_list,
                                    'harga' => (float)$detail->het,
                                    'detail_jwb' => $detail->detail_jwb
                                ]
                            @endphp
                            <tr class="fw-bolder fs-8 border" data-key="{{ ($detail->no_klaim.$detail->kd_part) }}">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td >{{ $detail->tgl_claim?date('Y/m/d', strtotime($detail->tgl_claim)):'-' }}</td>
                                <td >{{ $detail->no_klaim }}</td>
                                <td>{{ ($detail->kd_part??'-') }}</td>
                                <td>{{ ($detail->nm_part??'-') }}</td>
                                <td class="text-end">{{ number_format($detail->jmlretur, 0, '.', ',')??'-' }}</td>
                                <td class="text-end">{{ (!empty($detail->qty_jwb))?number_format($detail->qty_jwb, 0, '.', ','):0 }}</td>
                                <td>{{ ($detail->ket_jwb) }}</td>
                                <td class="text-center">
                                    <a role="button" data-bs-toggle="modal" href="#jwb_modal" data-a="{{ base64_encode(json_encode($jwb_detail)) }}" class="btn_jwb btn-sm btn-icon btn-success my-1"><span class="bi bi-envelope text-white"></span></a>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-success text-white btn_simpan" data-a="{{ base64_encode(json_encode((object)['no_retur' => $data->no_retur,'tamp'=>0])) }}">Simpan Semua Jawaban</button>
            <a href="{{(strtok(URL::previous(),'?') == strtok(URL::current(),'?'))?route('retur.supplier.index'):URL::previous()}}" id="btn-back" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</div>
<!--end::Row-->

<!-- Modal Detail -->
<div class="modal fade" id="jwb_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-2" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card-body">
                <div class="w-100">
                    <span class="d-block mb-0 pb-0 fs-4 fw-bold" id="jwb_no_klaim">-</span>
                    <span class="d-block mt-0 pt-0 fs-5" id="jwb_kd_part">-</span>
                </div>
                <div class="border rounded-3 p-3 mb-10">
                    <div class="form-group row mb-2">
                        <label for="jml" class="col-sm-2 col-form-label required">Qty</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="jml" name="jml" placeholder="Masukkan qty" value="" required>
                            <div class="invalid-feedback" id="error_jml"></div>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="alasan" class="col-sm-2 col-form-label required">Alasan</label>
                        <div class="col-sm-4">
                            <select name="alasan" id="alasan" class="form-select form-control" aria-label="Default select example" required>
                                <option value="RETUR">Ganti barang</option>
                                <option value="CA">Ganti Uang</option>
                            </select>
                            <div class="invalid-feedback" id="error_alasan"></div>
                        </div>
                        <div id="input_ca" class="col-sm-6" hidden>
                            <div class="row">
                                <label for="ca" class="col-sm-4 col-form-label">Jumlah Uang</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="ca" name="ca" min="1" placeholder="Masukkan Jumlah Uang" value="" required>
                                    <div class="invalid-feedback" id="error_ca"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="ket" class="col-sm-2 col-form-label">Keterangan</label>
                        <div class="col-sm-10">
                            <textarea type="text" class="form-control" data-kt-autosize="true" id="ket" name="ket" rows="3"></textarea>
                        </div>
                        <div class="invalid-feedback" id="error_ket"></div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="keputusan" class="col-sm-2 col-form-label required">Keputusan</label>
                        <div class="col-sm-4">
                            <select name="keputusan" id="keputusan" class="form-select" aria-label="Default select example" required>
                                <option value="TERIMA">TERIMA</option>
                                <option value="TOLAK">TOLAK</option>
                            </select>
                            <div class="invalid-feedback" id="error_keputusan"></div>
                        </div>
                    </div>
                </div>
                <h5 class="modal-title">List Jawaban</h5>
                <div id="list_jwb" class="table-responsive border rounded-3">
                    <table id="datatable_classporduk" class="table table-row-dashed table-row-gray-300 align-middle border">
                        <thead class="border">
                            <tr class="fs-8 fw-bolder text-muted text-center">
                                <th rowspan="2" class="w-100px ps-3 pe-3">Tanggal</th>
                                <th rowspan="2" class="w-50px ps-3 pe-3">Qty</th>
                                <th rowspan="2" class="w-100px ps-3 pe-3">Alasan</th>
                                <th rowspan="2" class="w-100px ps-3 pe-3">CA</th>
                                <th rowspan="2" class="w-100px ps-3 pe-3">Keputusan</th>
                                <th rowspan="2" class="w-auto ps-3 pe-3">Keterangan</th>
                                <th rowspan="2" class="w-auto ps-3 pe-3">Action</th>
                            </tr>
                            <tr class="fs-8 fw-bolder text-muted text-center">
                            </tr>
                        </thead>
                        <tbody id="list-jwb">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success text-white btn_simpan_tmp" data-a="">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- <div class="modal fade" id="modal_produksi" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-2" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih No Produksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="card-body">
                <div class="w-100">
                    <button type="button" class="btn btn-primary mb-3" id="btn_pilih_otomatis">
                        <span class="bi bi-check-circle"></span>
                        Pilih Otomatis
                    </button>
                </div>
                <div id="list_produksi" class="table-responsive border rounded-3 mb-10">
                    <table id="datatable_produksi" class="table table-row-dashed table-row-gray-300 align-middle border">
                        <thead class="border">
                            <tr class="fs-8 fw-bolder text-muted text-center">
                                <th rowspan="2" class="w-50px ps-3 pe-3">No</th>
                                <th rowspan="2" class="w-100px ps-3 pe-3">No Produksi</th>
                                <th rowspan="2" class="w-100px ps-3 pe-3">Action</th>
                            </tr>
                            <tr class="fs-8 fw-bolder text-muted text-center">
                            </tr>
                        </thead>
                        <tbody id="list-produksi">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_pilih_produksi" class="btn btn-success text-white">Pilih</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#jwb_modal">Close</button>
            </div>
        </div>
    </div>
</div> --}}



@endsection

@push('scripts')
<script language="JavaScript" src="{{ asset('assets/js/suma/retur/supplier/jawaban/form.js') }}?v={{ time() }}"></script>
@endpush
