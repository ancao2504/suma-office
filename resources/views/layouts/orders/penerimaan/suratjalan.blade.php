@extends('layouts.main.index')
@section('title','Orders')
@section('subtitle','Surat Jalan')
@push('styles')
@endpush
@section('container')
    {{-- include  --}}
    @include('layouts.orders.penerimaan.menu')
    {{-- end include  --}}
            <div class="row">
                <div class="card card-xl-stretch shadow">
                    <form action="" method="POST" enctype="multipart/form-data" id="form_sj">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row mb-2">
                                <div class="col-sm-6">
                                    <label for="no_sj" class="form-label">No Surat Jalan</label>
                                    <input type="text" class="form-control" id="no_sj" name="no_sj" placeholder="No Surat Jalan" value="{{ old('no_sj') }}" autofocus autocomplete="off" required>
                                    @error('no_sj')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-sm-6">
                                    <label for="tgl" class="form-label">Tanggal</label>
                                    <input type="text" class="form-control bg-secondary" id="tgl" name="tgl" placeholder="Tanggal" value="{{ old('tgl') }}" readonly>
                                    @error('tgl')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <div class="col-sm-6">
                                    <label for="tgl_terima" class="form-label">Tanggal Terima</label>
                                    <input type="text" class="form-control" id="tgl_terima" name="tgl_terima" placeholder="Tanggal" value="{{ old('tgl_terima')??date('d-m-Y') }}" required>
                                    @error('tgl_terima')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-sm-6">
                                    <label for="jam_terima" class="form-label">Jam Terima</label>
                                    <input type="time" class="form-control" id="jam_terima" name="jam_terima" placeholder="Jam Terima" value="{{ old('jam_terima') }}" required>
                                    @error('jam_terima')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <div class="col-sm-6">
                                    <label for="dealer" class="form-label">Dealer</label>
                                    <input type="text" class="form-control bg-secondary" id="dealer" name="dealer" placeholder="Dealer" value="{{ old('dealer') }}" readonly>
                                    @error('dealer')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-sm-6">
                                    <label for="nm_dealer" class="form-label">Nama Dealer</label>
                                    <input type="text" class="form-control bg-secondary" id="nm_dealer" name="nm_dealer" placeholder="Nama Dealer" value="{{ old('nm_dealer') }}" readonly>
                                    @error('nm_dealer')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <div class="col-sm-6">
                                    <label for="alamat_dealer" class="form-label">Alamat Dealer</label>
                                    <input type="text" class="form-control bg-secondary" id="alamat_dealer" name="alamat_dealer" placeholder="Alamat" value="{{ old('alamat_dealer') }}" readonly>
                                    @error('alamat_dealer')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-sm-6">
                                    <label for="kota_dealer" class="form-label">Kota Dealer</label>
                                    <input type="text" class="form-control bg-secondary" id="kota_dealer" name="kota_dealer" placeholder="Nama Dealer" value="{{ old('kota_dealer') }}" readonly>
                                    @error('kota_dealer')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <div class="mb-3 col-sm-6">
                                    <label for="foto" class="form-label d-block">Upload Gambar</label>
                                    <input class="form-control" type="file" id="foto" name="foto">
                                    @error('foto')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btn_kirim" class="btn btn-success col-md-2 col-12">Di Terima</button>
                        </div>
                    </form>
                </div>
            </div>

    @push('scripts')

        <script type="text/javascript">
            let url = {
                'cek_penerimaan_sj':"{{ route('orders.cek_penerimaan_sj') }}",
                'surat_jalan_simpan':"{{ route('orders.surat_jalan_simpan') }}",
                'surat_jalan_hapus':"{{ route('orders.surat_jalan_hapus') }}",
                // 'url_image': "{{ config('constants.app.app_images_url_SJ') }}",
                'url_image': "{{ asset('assets/images/SJ/') }}",
            }
        </script>
        <script src="{{ asset('assets/js/suma/orders/penerimaan/suratjalan.js') }}?v={{ time() }}"></script>
    @endpush
@endsection
