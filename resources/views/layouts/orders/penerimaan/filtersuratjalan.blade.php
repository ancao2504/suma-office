@extends('layouts.main.index')
@section('title','Orders')
@section('subtitle','Report Surat Jalan')
@push('styles')
    
@endpush
@section('container')
    {{-- include  --}}
    @include('layouts.orders.penerimaan.menu')
    {{-- end include  --}}
            <div class="row">
                <div class="card card-xl-stretch shadow">
                    <form action="{{ route('orders.surat_jalan_report') }}" method="GET" enctype="multipart/form-data" id="form_sj" target="_blank">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row mb-2">
                                <div class="col-sm-6">
                                    <label for="tanggal" class="form-label required">Tanggal Terima</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('tanggal_awal') is-invalid @enderror" id="tanggal_awal" name="tanggal_awal" placeholder="Tanggal Terima" value="{{ old('tanggal_awal') }}" autofocus autocomplete="off" required>
                                        <span class="input-group-text">sd</span>
                                        <input type="text" class="form-control @error('tanggal_akhir') is-invalid @enderror" id="tanggal_akhir" name="tanggal_akhir" placeholder="Tanggal Terima" value="{{ old('tanggal_akhir') }}" autofocus autocomplete="off" required>
                                    </div>
                                    @if ($errors->has('tanggal_awal') || $errors->has('tanggal_akhir'))
                                        <span class="text-danger">{{ $errors->default->first() }}</span>
                                    @endif
                                </div>
                                <div class="col-sm-6">
                                    <label for="driver" class="form-label">Driver</label>
                                    <input type="text" class="form-control" id="driver" name="driver" placeholder="Driver" value="{{ old('driver') }}">
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <div class="col-sm-6">
                                    <label for="no_serah_terima" class="form-label">Nomor serah terima dengan Driver</label>
                                    <input type="text" class="form-control" id="no_serah_terima" name="no_serah_terima" placeholder="Nomor serah terima" value="{{ old('no_serah_terima') }}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btn_kirim" class="btn btn-primary col-md-2 col-12">Cetak</button>
                        </div>
                    </form>
                </div>
            </div>

    @push('scripts')
        <script src="{{ asset('assets/js/suma/orders/penerimaan/filterreportsuratjalan.js') }}?v={{ time() }}"></script>
    @endpush
@endsection
