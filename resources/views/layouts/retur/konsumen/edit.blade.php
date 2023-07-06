@extends('layouts.main.index')
@section('title','Edit')
@section('subtitle','Retur Konsumen')
@push('styles')
@endpush

@section('container')
<!--begin::Row-->
<div class="row gy-5 g-xl-8">
    <div class="card card-xl-stretch shadow">
        <div class="card-body">
            <h3>1. Informasi Dokumen</h3>
            <div class="mb-3 border rounded p-3">
                <div class="form-group row mb-2">
                    <label for="no_retur" class="col-sm-2 col-form-label">No Retur</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control bg-secondary" id="no_retur" name="no_retur" placeholder="Masukkan No Retur" value="{{ $data->no_retur }}" readonly>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label for="kd_sales" class="col-sm-2 col-form-label">Kd Sales</label>
                    <div class="col-sm-4">
                        <select name="kd_sales" id="kd_sales" class="form-select form-control" data-control="select2" data-placeholder="Pilih kode Sales">
                            <option></option>
                            {!! $sales !!}
                        </select>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label for="kd_dealer" class="col-sm-2 col-form-label required">Kd Dealer</label>
                    <div class="col-sm-4">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="kd_dealer" name="kd_dealer" placeholder="Masukkan Kd Dealer" value="{{ $data->kd_dealer??null }}" required>
                            <button class="btn btn-primary list-dealer" type="button">Pilih</button>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="nm_dealer" name="nm_dealer" placeholder="Masukkan Nama Dealer" value="{{ $data->nm_dealer??null }}" disabled>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label for="alamat1" class="col-sm-2 col-form-label">Alamat</label>
                    <div class="col-sm-4">
                        <textarea type="text" class="form-control" data-kt-autosize="true" id="alamat1" name="alamat1" disabled>{{ $data->alamat1??null }}</textarea>
                    </div>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="kotasj" name="kotasj" placeholder="Masukkan Dealer" value="{{ $data->kota??null }}" disabled>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label for="tgl_terima" class="col-sm-2 col-form-label">Tanggal Terima</label>
                    <div class="col-sm-4">
                        <input class="form-control" id="tgl_terima" name="tgl_terima" placeholder="Masukkan No Dokumen" value="{{date('Y-m-d', strtotime(($data->tgl_terima == null)?date('Y-m-d'):$data->tgl_terima)) }}" required>
                    </div>

                    <label for="tgl_claim" class="col-sm-2 col-form-label">Tanggal Claim</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="tgl_claim" name="tgl_claim" placeholder="Masukkan Tanggal" value="{{date('Y-m-d', strtotime(($data->tanggal == null)?date('Y-m-d'):$data->tanggal)) }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <a role="button" class="btn-sm btn-icon btn-primary text-white" id="btn-update-retur">Update</a>
                </div>
            </div>
            <div class="mb-3">
                <a role="button" id="add_detail" class="btn btn-primary" data-bs-toggle="modal" href="#detail_modal">Tambah Detail</a>
            </div>

            <div id="list_detail" class="table-responsive border rounded-3">
                <table id="datatable_classporduk" class="table table-row-dashed table-row-gray-300 align-middle border">
                    <thead class="border">
                        <tr class="fs-8 fw-bolder text-muted text-center">
                            <th class="w-50px ps-3 pe-3">No</th>
                            <th class="w-100px ps-3 pe-3">TGL Faktur</th>
                            <th class="w-100px ps-3 pe-3">No Faktur</th>
                            <th class="w-100px ps-3 pe-3">Part Number</th>
                            <th class="w-50px ps-3 pe-3">Lokasi</th>
                            <th class="w-50px ps-3 pe-3">QTY Faktur</th>
                            <th class="w-50px ps-3 pe-3">QTY Claim</th>
                            <th class="w-100px ps-3 pe-3">Harga</th>
                            <th class="w-100px ps-3 pe-3">DESC</th>
                            <th class="w-100px ps-3 pe-3">Jumlah</th>
                            <th class="w-100px ps-3 pe-3">Status</th>
                            <th class="min-w-150px ps-3 pe-3">Keterangan</th>
                            <th class="min-w-150px ps-3 pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody id="list-retur">
                        @if (count($data->detail) == 0)
                            <tr class="fw-bolder fs-8 border">
                                <td colspan="13" class="text-center">Tidak ada data</td>
                            </tr>

                        @else
                            @foreach ($data->detail as $detail)
                            @php
                                $dta_edt = json_encode((object)[
                                    'no_faktur' => $detail->no_faktur,
                                    'kd_part' => $detail->kd_part,
                                    'jumlah' => $detail->jumlah,
                                    'ket' => $detail->ket,
                                    'status' => $detail->status,
                                    'disc' => $detail->disc,
                                    'qty_faktur' => $detail->qty_faktur
                                ]);
                                $dta_del = json_encode((object)[
                                    'no_faktur' => $detail->no_faktur,
                                    'kd_part' => $detail->kd_part
                                ]);
                            @endphp

                            <tr class="fw-bolder fs-8 border">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ ($detail->tgl_faktur??'-') }}</td>
                                <td>{{ ($detail->no_faktur??'-') }}</td>
                                <td>{{ ($detail->kd_part??'-') }}</td>
                                <td>{{ ($detail->kd_lokasi??'-') }}</td>
                                <td>{{ ($detail->qty_faktur??'-') }}</td>
                                <td>{{ ($detail->jumlah??'-') }}</td>
                                {{-- <td>{{ $detail->harga??'-' }}</td> format number dengan , --}}
                                <td>{{ number_format($detail->harga, 0, '.', ',')??'-' }}</td>
                                <td>{{ ($detail->disc??'-') }}</td>
                                {{-- <td>{{ $detail->nilai??'-' }}</td> format number dengan ,--}}
                                <td>{{ number_format($detail->nilai, 0, '.', ',')??'-' }}</td>
                                <td>{{ ($detail->status??'-') }}</td>
                                <td>{{ ($detail->ket??'-') }}</td>
                                <td class="text-center">
                                    <a role="button" data-bs-toggle="modal" href="#detail_modal" data-a="{{ base64_encode($dta_edt) }}" class="btn_dtl_edit btn-sm btn-icon btn-warning text-dark"><i class="fas fa-edit"></i></a>
                                    <a role="button" data-a="{{ base64_encode($dta_del) }}" class="btn_dtl_delete btn-sm btn-icon btn-danger text-white" data-bs-toggle="modal" data-bs-target="#delet-retur"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            @endforeach
                            
                            
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <a href="{{ Route('retur.konsumen.index') }}" id="btn-back" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</div>
<!--end::Row-->

<!-- Modal Detail -->
<div class="modal fade" id="detail_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-2" aria-labelledby="staticBackdropLabel" aria-hidden="true">
<div class="modal-dialog modal-xl modal-fullscreen-md-down">
    <div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="detail_modal"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <form action="" id="form_detail">
            <h3>2. Informasi Produk</h3>
            <div class="col-xl-12 border rounded mb-3 p-2">
                <div class="form-group row mb-2">
                    <label for="no_faktur" class="col-sm-2 col-form-label">No Faktur</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="no_faktur" name="no_faktur" placeholder="Masukkan No Faktur" value="" required>
                    </div>
                    <label for="tgl_faktur" class="col-sm-2 col-form-label">Tanggal Faktur</label>
                    <div class="col-sm-3">
                        <input class="form-control" id="tgl_faktur" name="tgl_faktur" placeholder="Tanggal Faktur" value="" disabled>
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
                    <div class="col-sm-5">
                        <input type="text" class="form-control bg-secondary" id="ket_part" name="ket_part" placeholder="Nama Part" value="" readonly>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label for="qty_faktur" class="col-sm-2 col-form-label">QTY Faktur</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="qty_faktur" name="qty_faktur" placeholder="Masukkan QTY Faktur" value="" disabled>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label for="qty_claim" class="col-sm-2 col-form-label">QTY Claim</label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control" id="qty_claim" name="qty_claim" min="1" placeholder="Masukkan QTY Claim" value="" required>
                    </div>
                    <label for="harga" class="col-sm-2 col-form-label">Harga</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="harga" name="harga" placeholder="Masukkan Harga" value="" required>
                        </div>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label for="disc" class="col-sm-2 col-form-label">Disc</label>
                    <div class="col-sm-4">
                        <div class="input-group mb-3">
                            <input type="number" class="form-control" id="disc" name="disc" min="0" max="100" placeholder="Masukkan Diskon" value="">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <label for="total" class="col-sm-2 col-form-label">Total</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="total" name="total" placeholder="Masukkan Total" value="" disabled>
                        </div>
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
                <div class="form-group row mb-2">
                    <label for="sts" class="col-sm-2 col-form-label required">Status</label>
                    <div class="col-sm-auto">
                        <select name="sts" id="sts" class="form-select form-control" required>
                            <option value="">Pilih Keterangan Ganti</option>
                            <option value="RETUR">Retur (Ganti Uang)</option>
                            <option value="GANTI BARANG">Ganti barang</option>
                            <option value="CLAIM ke Supplier">Claim ke Supplier</option>
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
        </form>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" id="btn-update-detail" class="btn btn-primary">Simpan</button>
    </div>
    </div>
</div>
</div>

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
<!-- script tambanhan -->
<script language="JavaScript" src="{{ asset('assets/js/suma/retur/konsumen/getDealer.js') }}?v={{ time() }}"></script>
<script language="JavaScript" src="{{ asset('assets/js/suma/retur/konsumen/btn-post.js') }}?v={{ time() }}"></script>
<script language="JavaScript" src="{{ asset('assets/js/suma/retur/konsumen/getFaktur.js') }}?v={{ time() }}"></script>
<script language="JavaScript" src="{{ asset('assets/js/suma/retur/konsumen/getpart.js') }}?v={{ time() }}"></script>
<script language="JavaScript" src="{{ asset('assets/js/suma/retur/konsumen/edit.js') }}?v={{ time() }}"></script>
@endpush