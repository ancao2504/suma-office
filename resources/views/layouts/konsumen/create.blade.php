@extends('layouts.main.index')
@section('title','Konsumen')
@section('subtitle','Create')
@push('styles')
@endpush

@section('container')
    {{-- @dd($data) --}}
	<!--begin::Post-->
	<div class="post d-flex flex-column-fluid" id="kt_post">
		<!--begin::Container-->
		<div id="kt_content_container" class="container-xxl">
			<!--begin::Row-->
			<div class="row gy-5 g-xl-8">
				<div class="card card-xl-stretch shadow">
                    <div class="card-body">
                        <form class="form-horizontal" autocomplete="off" enctype="multipart/form-data" method="POST">
                            <div class="row p-3">
                                <div class="col-lg-12 mb-20 text-center">
                                    <div class="col-xl-12">
                                        <div class="row g-9 justify-content-center" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                                            <div class="col-md-4 col-lg-4 col-xxl-4" @if (Str::contains(session('app_user_name'), 'EFO')) hidden @endif>
                                                <label class="btn btn-outline btn-outline-dashed btn-outline-default d-flex text-start p-6 @if (empty($data->divisi) || $data->divisi == 'honda') active @endif" data-kt-button="true">
                                                    <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                        <input class="form-check-input" type="radio" name="divisi" value="honda" required @if (empty($data->divisi) || $data->divisi == 'honda') checked @endif>
                                                    </span>
                                                    <span class="ms-5">
                                                        <span class="fs-4 fw-bolder mb-1 d-block">HONDA</span>
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-lg-4 col-xxl-4">
                                                <label class="btn btn-outline btn-outline-dashed btn-outline-default d-flex text-start p-6 @if (Str::contains(session('app_user_name'), 'EFO') || ($data->divisi??null) == 'fdr') active @endif" data-kt-button="true">
                                                    <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                        <input class="form-check-input" type="radio" name="divisi" value="fdr" required @if (Str::contains(session('app_user_name'), 'EFO') || ($data->divisi??null) == 'fdr') checked @endif>
                                                    </span>
                                                    <span class="ms-5">
                                                        <span class="fs-4 fw-bolder mb-1 d-block">FDR</span>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        {{-- @error('divisi')
                                            <div class="row g-9 justify-content-center">
                                                <div class="text-start fs-8 text-danger col-md-8 col-lg-8 col-xxl-8"><span class="required"></span>{{ $message }}</div>
                                            </div>
                                        @enderror --}}
                                        @if (!Str::contains(session('app_user_name'), 'EFO'))
                                        <div class="row g-9 justify-content-center">
                                            <div class="text-start fs-8 text-gray-600 col-md-8 col-lg-8 col-xxl-8"><span class="required"></span>Pilih <b>DEVISI</b> untuk menentukan data akan di <b>simpan pada DEVISI apa</b> !</div>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <h6>Id Konsumen</h6>
                                        <input id="inputId" name="id" type="text" class="form-control" value="{{ $data->id??'' }}" disabled>
                                    </div>
                                    <div class="form-group mb-3">
                                        <div class="row">
                                            <div class="form-group col-lg-6">
                                                <h6>Cabang</h6>
                                                <select name="company" id="company" class="form-select">
                                                </select>
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <h6>Lokasi</h6>
                                                <select name="lokasi" id="lokasi" class="form-select">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <h6 class="required">Nomor Faktur</h6>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <input id="inputNomorFaktur" name="nomor_faktur" type="text" style="text-transform: uppercase" class="form-control" value="{{ $data->no_faktur??'' }}">
                                            </div>
                                            <div class="col-lg-6 mt-lg-0 mt-3">
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input id="inputTotalFaktur" name="total" type="text" class="form-control bg-secondary" readonly value="{{ $data->total??'' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <h6>NIK</h6>
                                        <div class="input-group">
                                            <input id="inputNIK" name="nik" type="text" class="form-control" oninput="this.value=this.value.replace(/[^0-9]/g,'');" maxlength="16" value="{{ $data->nik??'' }}">
                                            <span class="input-group-text bg-primary" style="cursor: pointer;" id="btn_nik"><i class="bi bi-search text-white"></i></span>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <h6 class="required">Nama Pelanggan</h6>
                                        <input id="inputNamaPelanggan" name="nama_pelanggan" type="text" class="form-control" required value="{{ $data->nama??'' }}" onkeyup="this.value = this.value.toUpperCase(); this.value = this.value.replace(/[^a-zA-Z .,']/g, '');">
                                    </div>
                                    <div class="form-group mb-3">
                                        <h6 class="required">Tempat dan Tanggal Lahir</h6>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="input-group">
                                                    <input id="inputTempatLahir" name="tempat_lahir" type="text" class="form-control required" placeholder="Tempat Lahir" required value="{{ $data->tempat_lahir??'' }}" onkeyup="this.value = this.value.toUpperCase();">
                                                    <input id="inputTanggalLahir" name="tanggal_lahir" class="form-control" type="text" placeholder="Tanggal Lahir" required value="{{ $data->tgl_lahir??'' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <h6 class="required">Alamat Pelanggan</h6>
                                        <input id="inputAlamat" name="alamat" type="text" class="form-control" required value="{{ $data->alamat??'' }}" onkeyup="this.value = this.value.toUpperCase()">
                                    </div>
                                    <div class="form-group mb-3">
                                        <h6 class="required">Telepon</h6>
                                        <input id="inputTelepon" name="telepon" type="text" class="form-control required" value="{{ $data->telepon??'' }}" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <h6>Email</h6>
                                        <input id="inputEmail" name="email" type="text" class="form-control" value="{{ $data->email??'' }}">
                                    </div>
                                    <div class="form-group mb-3">
                                        <h6 class="required">Nomor Polisi</h6>
                                        <input id="inputNopol" name="nopol" type="text" class="form-control" style="text-transform: uppercase" required value="{{ $data->nopol??'' }}" oninput="this.value=this.value.replace(/[^0-9a-zA-Z]/g,''); this.value = this.value.toUpperCase();">
                                    </div>
                                    <div class="form-group mb-3">
                                        <h6 class="required">Merk Motor</h6>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <select class="form-select" name="merk_motor" id="merk_motor" data-control="select2" data-placeholder="Pilih Merek Motor" required>
                                                    <option></option>
                                                    {!! $merk_motor_list !!}
                                                    <option value="Lainya">Lainya</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-6 mt-sm-3 mt-md-0">
                                                <input type="text" value="{{ $data->merk??'' }}" placeholder="" name="merk_motor_lainya" id="merk_motor_lainya" class="form-control" hidden>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <h6 class="required">Tipe Motor</h6>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <select class="form-select" data-control="select2" name="tipe_motor" id="tipe_motor" data-placeholder="Pilih Type Motor" required>
                                                    <option></option>
                                                </select>
                                            </div>
                                            <div class="col-lg-6 mt-sm-3 mt-md-0">
                                                <input type="text" value="{{$data->type??''}}" placeholder="" name="tipe_motor_lainya" id="tipe_motor_lainya" class="form-control" hidden>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <h6 class="required">Jenis Motor</h6>
                                        <select id="inputJenisMotor" name="jenis_motor" class="form-select col-12 @error('jenis_motor') is-invalid @enderror" required>
                                            <option value="Matic" @if (!empty($data->jenis) && $data->jenis == 'MATIC') selected @endif >Matic</option>
                                            <option value="Bebek" @if (!empty($data->jenis) && $data->jenis == 'BEBEK') selected @endif >Bebek</option>
                                            <option value="Sport" @if (!empty($data->jenis) && $data->jenis == 'SPORT') selected @endif >Sport</option>
                                            <option value="Super Sport" @if (!empty($data->jenis) && $data->jenis == 'SUPER SPORT') selected @endif >Super Sport</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <h6>Tahun Motor</h6>
                                        <input id="inputTahunMotor" name="tahun_motor" class="form-control" type="text" oninput="this.value=this.value.replace(/[^0-9]/g,'');" maxlength="4" value="{{ $data->tahun_motor??'' }}">
                                    </div>
                                    <div class="form-group mb-3">
                                        <h6>Keterangan</h6>
                                        <input id="inputKeterangan" name="keterangan" type="text" class="form-control" value="{{ $data->keterangan??'' }}">
                                    </div>
                                    <div class="form-group mb-3">
                                        <h6>Mengetahui Cabang dari</h6>
                                        <div class="row">
                                            <div class="@if (!empty($data->mengetahui) && in_array($data->mengetahui, ['SOSIAL MEDIA', 'LAIN-LAIN'])) col-lg-6 @else col-lg-12 @endif">
                                                <select id="selectMengetahui" type="select" name="mengetahui" class="form-select col-12">
                                                    <option value="SPANDUK" @if (!empty($data->mengetahui) && $data->mengetahui == 'SPANDUK') selected @endif>Spanduk</option>
                                                    <option value="BROSUR" @if (!empty($data->mengetahui) && $data->mengetahui == 'BROSUR') selected @endif>Brosur</option>
                                                    <option value="SOSIAL MEDIA" @if (!empty($data->mengetahui) && $data->mengetahui == 'SOSIAL MEDIA') selected @endif>Sosial Media</option>
                                                    <option value="TEMAN/KERABAT" @if (!empty($data->mengetahui) && $data->mengetahui == 'TEMAN/KERABAT') selected @endif>Teman/Kerabat</option>
                                                    <option value="LAIN-LAIN" @if (!empty($data->mengetahui) && $data->mengetahui == 'LAIN-LAIN') selected @endif>Lain-Lain</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-6 mt-lg-0 mt-3">
                                                <div class="input-group">
                                                    <input id="keterangan_mengetahui" name="keterangan_mengetahui" class="form-control" type="text" value="{{ $data->keterangan_mengetahui??'' }}" @if (!empty($data->mengetahui) && in_array($data->mengetahui, ['SOSIAL MEDIA', 'LAIN-LAIN'])) '' @else hidden @endif>
                                                </div>
                                                {{-- <div class="position-absolute invisible" id="form1_complete"></div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary waves-effect text-left" id="btnSimpan">Simpan</button>
                                <a href="{{ Route('konsumen.index') }}?page={{ app('request')->input('page') }}&paginate={{ app('request')->input('paginate') }}&divisi={{ app('request')->input('divisi') }}&lokasi={{ app('request')->input('lokasi') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
				</div>
			</div>
        </div>
    </div>
	<!--end::Row-->
    <!-- Modal -->
    <div class="modal fade" id="autocomplateKonsumen" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    </div>
@endsection
@push('scripts')
    <script language="JavaScript">
        const company = {
            @if (!empty(session('app_user_company')->honda))
                honda : @json(session('app_user_company')->honda),
            @endif
            fdr : @json(session('app_user_company')->fdr)
        }
        const tipemotor = @json($type_motor);
        const old = {
            merk: '{{ $data->merk??'' }}',
            tipe: '{{ $data->type??'' }}',
        };
    </script>

	<script language="JavaScript" src="{{ asset('assets/js/suma/konsumen/Create.js') }}?v={{ time() }}"></script>
@endpush