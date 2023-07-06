@extends('layouts.main.index')
@section('title','Gudang Online')
@section('subtitle','Packing')
@push('styles')
@endpush

@section('container')
{{-- @dd($errors->all()) --}}
<div class="row gy-5 g-xl-8">
    <div class="card card-xl-stretch shadow">
        {{-- matikan default submit --}}
        <form action="{{ Route('gudang.packing.store') }}" method="POST" enctype="multipart/form-data">
            <div class="card-body">
                @csrf
                <div class="mb-3 p-2">
                    <div class="form-group row mb-2">
                        <label for="time" class="text-end" id="time_packer"></label>
                    </div>
                    <div class="form-group row mb-2">
                        <div class="col-8">
                            <label for="no_meja" class="col-form-label">Nomor Meja</label>
                            <select class="form-select fw-bolder @error('no_meja') is-invalid @enderror" name="no_meja" id="no_meja" onkeydown="focusNextElement(event, this)">
                                <option value="">Pilih Nomer Meja</option>
                                {!! $meja !!}
                            </select>
                            @error('no_meja')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div> 
                            @enderror
                        </div>
                        <div class="col-4">
                            <label for="kd_packer" class="col-form-label required">Packer</label>
                            <input type="text" class="form-control @error('kd_packer') is-invalid @enderror" id="kd_packer" name="kd_packer" value="{{ old('kd_packer')??session('kd_packer') }}" onkeydown="focusNextElement(event, this)">
                            @error('kd_packer')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="no_dok" class="col-form-label required">Nomor WH</label>
                        <div class="input-group has-validation">
                            <input type="text" class="form-control @error('no_dok') is-invalid @enderror" id="no_dok" name="no_dok" value="{{ old('no_dok') }}" onkeydown="focusNextElement(event, this)">
                            <span class="input-group-text bg-primary" style="cursor: pointer;" id="btn_nowh" data-bs-toggle="modal" data-bs-target="#autocomplateKonsumen"><i class="bi bi-search text-white"></i></span>
                            @error('no_dok')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row mb-2 mt-3">
                        <label for="table" class="col-form-label">Informasi</label>
                        <div class="table-responsive">
                            <table class="table table-row-dashed table-row-gray-300 align-middle">
                                <tbody class="border">
									<tr class="fw-bolder fs-8 border">
                                        <td class="text-muted text-center w-100px">Keterangan</td>
                                        <td class="ps-3 pe-3" id="keterangan_info">-</td>
                                    </tr>
									<tr class="fw-bolder fs-8 border">
                                        <td class="text-muted text-center w-100px">Expedisi</td>
                                        <td class="ps-3 pe-3" id="Expedisi_info">-</td>
                                    </tr>
									<tr class="fw-bolder fs-8 border">
                                        <td class="text-muted text-center w-100px">Nama</td>
                                        <td class="ps-3 pe-3" id="Nama_info">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_submit">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modal_nowh" tabindex="-1" aria-labelledby="modal_nowhLabel" aria-hidden="true">
</div>
@endsection
@push('scripts')
    <script language="JavaScript">
        const old = {
            no_meja: @json((session('no_meja')??'')),
        }
    </script>
	<script language="JavaScript" src="{{ asset('assets/js/suma/gudang/online/packing/form.js') }}?v={{ time() }}"></script>
@endpush