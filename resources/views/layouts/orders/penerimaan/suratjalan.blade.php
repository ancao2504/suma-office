@extends('layouts.main.index')
@section('title','Orders')
@section('subtitle','Surat Jalan')
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
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
                                    <label for="no_st" class="form-label">No Serah Terima</label>
                                    <input type="text" class="form-control @error('no_st') is-invalid @enderror" id="no_st" name="no_st" placeholder="No Serah Terima" value="{{ old('no_st') }}" autocomplete="off" required>
                                    @error('no_st')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-sm-6">
                                    <label for="no_sj" class="form-label">No Surat Jalan</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('no_sj') is-invalid @enderror" id="no_sj" name="no_sj" placeholder="No Surat Jalan" value="{{ old('no_sj') }}" autocomplete="off" required>
                                        <a role="button" class="btn btn-primary" id="btn_no_sj"><i class="bi bi-search"></i></a>
                                    </div>
                                    <div class="rounded border mb-3 p-3" id="list_ceked_sj" style="display: none">
                                        <table class="table table-row-dashed table-row-gray-300 align-middle">
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                    @error('no_sj')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <div class="col-sm-6">
                                    <label for="tgl" class="form-label">Tanggal</label>
                                    <input type="text" class="form-control bg-secondary" id="tgl" name="tgl" placeholder="Tanggal" value="{{ old('tgl') }}" readonly>
                                </div>
                                <div class="col-sm-6">
                                    <label for="tgl_terima" class="form-label">Tanggal Terima</label>
                                    <input type="text" class="form-control @error('tgl_terima') is-invalid @enderror" id="tgl_terima" name="tgl_terima" placeholder="Tanggal" value="{{ old('tgl_terima')??date('d-m-Y') }}" required>
                                    @error('tgl_terima')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <div class="col-sm-6">
                                    <label for="jam_terima" class="form-label">Jam Terima</label>
                                    <input type="time" class="form-control @error('jam_terima') is-invalid @enderror" id="jam_terima" name="jam_terima" placeholder="Jam Terima" value="{{ old('jam_terima') }}" required>
                                    @error('jam_terima')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-sm-6">
                                    <label for="dealer" class="form-label">Dealer</label>
                                    <input type="text" class="form-control bg-secondary" id="dealer" name="dealer" placeholder="Dealer" value="{{ old('dealer') }}" readonly>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <div class="col-sm-6">
                                    <label for="nm_dealer" class="form-label">Nama Dealer</label>
                                    <input type="text" class="form-control bg-secondary " id="nm_dealer" name="nm_dealer" placeholder="Nama Dealer" value="{{ old('nm_dealer') }}" readonly>
                                </div>
                                <div class="col-sm-6">
                                    <label for="alamat_dealer" class="form-label">Alamat Dealer</label>
                                    <input type="text" class="form-control bg-secondary " id="alamat_dealer" name="alamat_dealer" placeholder="Alamat" value="{{ old('alamat_dealer') }}" readonly>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <div class="col-sm-6">
                                    <label for="kota_dealer" class="form-label">Kota Dealer</label>
                                    <input type="text" class="form-control bg-secondary " id="kota_dealer" name="kota_dealer" placeholder="Nama Dealer" value="{{ old('kota_dealer') }}" readonly>
                                </div>
                                <div class="mb-3 col-sm-6">
                                    <label for="foto" class="form-label d-block">Upload Gambar</label>
                                    <input class="form-control @error('foto') is-invalid @enderror" type="file" id="foto" name="foto" accept="image/*">
                                    @error('foto')
                                        <span class="invalid-feedback">{{ $message }}</span>
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

        <div class="modal fade" id="suratjalanModal" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="modalTitleTipeMotor" name="modalTitleTipeMotor" class="modal-title">Pilih Surat Jalan</h5>
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
                        <div class="fv-row mb-5">
                            <label class="form-label">Cari berdasarkan Dealer:</label>
                            <div class="input-group">
                                <span class="input-group-text">Pencarian</span>
                                <input id="input_search" name="input_search" type="text" class="form-control" placeholder="Cari No Surat Jalan">
                                {{-- <button type="button" id="btn_search" name="btn_search" class="btn btn-primary">Cari</button> --}}
                            </div>
                        </div>
                        <div id="SuratJalanModal">
                            <div class="table-responsive">
                                <table class="table table-row-dashed table-row-gray-300 align-middle" id="tableSuratJalan">
                                    <thead class="fw-boldest fs-7 text-gray-400 text-uppercase">
                                        <tr>
                                            <th>Aksi</th>
                                            <th>No Surat Jalan</th>
                                            <th>Tanggal</th>
                                            <th>Dealer</th>
                                            <th>Nama Dealer</th>
                                            <th>Alamat Dealer</th>
                                            <th>Kota Dealer</th>
                                        </tr>
                                    </thead>
                                    <tbody id="SuratJalanBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-danger font-weight-bold" id="reset" >Reset</button>
                        <div class="text-end">
                            <button type="button" class="btn btn-primary font-weight-bold" id="pilih_sj" >Pilih</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @push('scripts')
    
        <script type="text/javascript">
            let url = {
                'cek_penerimaan_sj':"{{ route('orders.cek_penerimaan_sj') }}",
                'surat_jalan_simpan':"{{ route('orders.surat_jalan_simpan') }}",
                'surat_jalan_hapus':"{{ route('orders.surat_jalan_hapus') }}",
                'url_image': "{{ asset('assets/images/sj/') }}",
            }

            let data_sj;
        </script>

        <script>
            $(document).ready(function() {
                
            });
        </script>
        <script src="{{ asset('assets/js/suma/orders/penerimaan/suratjalan.js') }}?v={{ time() }}"></script>
    @endpush
@endsection
