@extends('layouts.main.index')
@section('title','Orders')
@section('subtitle','Penerimaan Pembayaran')
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
@endpush
@section('container')
{{-- {{ dd(session()->get('_old_input')) }} --}}
@php
    if(session()->get('_old_input')){
        $old = session()->get('_old_input');
    }
    if (session()->get('detail')){
        if (session()->get('_old_input')){
            $old['detail'] = session()->get('detail');
        }else {
            $old = session()->get('detail');
        }
    }

    if($old??false){
        echo '<script>let old = []; old = '.json_encode($old).';</script>';
    }else {
        echo '<script>let old = [];</script>';
    }
@endphp
        <div class="row">
            <div class="col-lg-4">
                <div class="card card-xl-stretch shadow mb-3">
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data" id="form_pp">
                            @csrf   
                            <div class="form-group row">
                                <div class="col-sm-12 mb-2">
                                    <label for="no_kasbank" class="form-label">No Kas/bank</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-secondary @error('no_khasbank') is-invalid @enderror" id="no_kasbank" name="no_kasbank" placeholder="No Kas/bank" value="{{ old('no_khasbank')??'BG'.date('dmYHis').$user }}" autocomplete="off" readonly>
                                    </div>
                                    @error('no_sj')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-sm-12 mb-2">
                                    <label for="tgl" class="form-label">Tanggal</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-secondary @error('tgl') is-invalid @enderror" id="tgl" name="tgl" placeholder="Tanggal" value="{{ old('tgl')??date('d/m/Y') }}" readonly>
                                    </div>
                                    @error('no_sj')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12 mb-2">
                                    <label for="kd_dealer" class="form-label">Kode Dealer</label>
                                    <div class="input-group">
                                        <button id="btnFilterPilihDealer" name="btnFilterPilihDealer" class="btn btn-icon btn-primary input-group-text" type="button"
                                        data-toggle="modal" data-target="#dealerSearchModal">
                                            <i class="fa fa-search"></i>
                                        </button>
                                        <input type="text" class="form-control @error('kd_dealer') is-invalid @enderror" id="kd_dealer" name="kd_dealer" placeholder="Kode Dealer" value="{{ old('kd_dealer') }}" autocomplete="off" readonly required>
                                    </div>
                                    @error('no_sj')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-sm-12 mb-2">
                                    <label for="nm_dealer" class="form-label">Nama Dealer</label>
                                    <input type="text" class="form-control bg-secondary @error('nm_dealer') is-invalid @enderror" id="nm_dealer" name="nm_dealer" placeholder="Nama Dealer" value="{{ old('nm_dealer') }}" autocomplete="off" readonly>
                                    @error('nm_dealer')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12 mb-2">
                                    <label for="jenis_transaksi" class="form-label">Jenis Transaksi</label>
                                    <select name="jenis_transaksi" id="jenis_transaksi" class="form-select form-control" aria-label="Default select example" required>
                                        <option value="">Pilih Jenis Transaksi</option>
                                        <option value="T" {{ old('jenis_transaksi')=='T'?'selected':'' }}>Tunai</option>
                                        <option value="G" {{ old('jenis_transaksi')=='G'?'selected':'' }}>Giro</option>
                                    </select>
                                </div>
                                <div class="col-sm-12 mb-2">
                                    <label for="total" class="form-label">Total</label>
                                    <input type="text" class="form-control @error('total') is-invalid @enderror" id="total" name="total" placeholder="Total" value="{{ old('total')??0 }}" autocomplete="off" required>
                                    @error('total')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="button" id="btn_kirim" class="btn btn-success col-md-12 col-12">Simpan</button>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow">
                    {{-- card-header --}}
                    <div class="card-header">
                        <div class="card-title">
                            List Akan Dibayar
                        </div>
                        <div class="card-title text-end">
                            <button type="button" id="btn_list_pembayaran" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#Modallistpembayaran">List Pembayaran</button>
                        </div>
                    </div>
                    <div id="tabel_list_akan_dibayar" class="card-body py-0" style="max-height: 50vh; overflow: auto;">
                        <div class="table-responsive">
                            <table class="table table-row-dashed table-row-gray-300 table-striped align-middle" id="tableSuratJalan">
                                <thead class="fw-boldest fs-7 text-gray-400 text-uppercase bg-white">
                                    <tr>
                                        <th>No</th>
                                        <th>No Faktur</th>
                                        <th>Tgl Faktur</th>
                                        <th>Jumlah</th>
                                        <th>Dealer</th>
                                    </tr>
                                </thead>
                                <tbody id="PenerimaanPembayaran">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row justify-content-end">
                            <label for="jml_faktur" class="col-form-label col-lg-3 col-6">Jumlah Faktur</label>
                            <div class="col-lg-3 col-6">
                                <input type="text" class="form-control form-control-sm" id="jml_faktur" name="jml_faktur" readonly>
                            </div>
                        </div>
                        <div class="row justify-content-end">
                            <label for="total_bpk" class="col-form-label col-lg-3 col-6">Total BPK</label>
                            <div class="col-lg-3 col-6">
                                <input type="text" class="form-control form-control-sm" value="0" id="total_bpk" name="total_bpk" readonly>
                            </div>
                        </div>
                        <div class="row justify-content-end">
                            <label for="total_pemayaran" class="col-form-label col-lg-3 col-6">Total Pembayaran</label>
                            <div class="col-lg-3 col-6">
                                <input type="text" class="form-control form-control-sm" value="0" id="total_pemayaran" readonly>
                            </div>
                        </div>
                        <div class="row justify-content-end">
                            <label for="sisa" class="col-form-label col-lg-3 col-6">Sisa</label>
                            <div class="col-lg-3 col-6">
                                <input type="text" class="form-control form-control-sm" id="sisa" name="sisa" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

{{-- Modal List Pembayaran --}}
        <div class="modal fade" id="Modallistpembayaran" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" tabindex="-1" role="dialog">
            <div class="modal-dialog @if ($device != 'Mobile') modal-xl @endif">
                <div class="modal-content" style="@if ($device == 'Mobile') background-color: #f5f8fa; @endif">
                    <div class="modal-header @if ($device == 'Mobile') bg-white rounded-3 @endif">
                        <h5 id="modalTitlelistpembayaran" name="modalTitlelistpembayaran" class="modal-title">List Pembayaran</h5>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <span class="svg-icon svg-icon-muted svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M6 19.7C5.7 19.7 5.5 19.6 5.3 19.4C4.9 19 4.9 18.4 5.3 18L18 5.3C18.4 4.9 19 4.9 19.4 5.3C19.8 5.7 19.8 6.29999 19.4 6.69999L6.7 19.4C6.5 19.6 6.3 19.7 6 19.7Z" fill="currentColor"/>
                                    <path d="M18.8 19.7C18.5 19.7 18.3 19.6 18.1 19.4L5.40001 6.69999C5.00001 6.29999 5.00001 5.7 5.40001 5.3C5.80001 4.9 6.40001 4.9 6.80001 5.3L19.5 18C19.9 18.4 19.9 19 19.5 19.4C19.3 19.6 19 19.7 18.8 19.7Z" fill="currentColor"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="modal-body @if ($device == 'Mobile') px-2 @endif">
                        <div class="mb-3 @if ($device == 'Mobile') card bg-white p-6 @else row @endif">
                            <label class="form-label col-12">Cari berdasarkan No Faktur:</label>
                            <div class="position-relative @if ($device == 'Mobile') col-lg-12  @else col-lg-3 @endif mb-3">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                <span class="svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <input type="text" class="form-control form-control-solid ps-10" name="search" id="filterSearch" value="" oninput="this.value = this.value.toUpperCase()" placeholder="Search">
                            </div>
                            <div class="@if ($device == 'Mobile') col-lg-12  @else col-lg-3 @endif">
                                <a role="button" class="btn btn-primary" id="filter_list_pembayaran">Pilih Otomatis</a>
                                @if ($device == 'Mobile')
                                <a role="button" class="btn btn-light" id="filter_select_all">Select All</a>
                                @endif
                            </div>
                        </div>
                        @if ($device == 'Mobile')
                        <div class="mb-2" style="max-height: 70vh; overflow-y: scroll; overflow-x: hidden;">
                            <div id="ListPembayaranCard" class="pb-3">
                                <div id="data_block" class="card border border-secondary col-12 p-6 my-3" style="cursor: pointer;">
                                    <div class="row">
                                        <div class="col-12 text-center">
                                            <span id="card_faktur">Tidak ada data</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div id="tabel_list" class="col-lg-12 mb-3 border-top border-bottom" style="max-height: 50vh; overflow: auto;">
                            <div class="table-responsive">
                                <table class="table table-row-dashed table-row-gray-300 table-striped align-middle" id="tableSuratJalan">
                                    <thead class="fw-boldest fs-7 text-gray-400 text-uppercase bg-white">
                                        <tr>
                                            <th>
                                                <div class="form-check form-check-custom form-check-solid form-check-lg">
                                                    <input class="form-check-input" type="checkbox" value="1" id="filter_select_all">
                                                </div>
                                            </th>
                                            <th >Jml Bayar</th>
                                            <th>No Faktur</th>
                                            <th>Tgl Faktur</th>
                                            <th>Jumlah</th>
                                            <th>Terbayar</th>
                                            <th>Sisa</th>
                                            <th>Dealer</th>
                                            <th>Mkr</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ListPembayaran">
                                        <tr>
                                            <td colspan="10" class="text-center text-muted">Tidak ada data</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                        <div class="mb-3 @if ($device == 'Mobile') bg-white card p-6 @endif">
                            <div class="row justify-content-end">
                                <label for="jml_faktur" class="col-form-label @if ($device == 'Mobile') col-lg-6 @else col-lg-2 @endif col-6">Jumlah Faktur</label>
                                <div class="@if ($device == 'Mobile') col-lg-6 @else col-lg-3 @endif col-6">
                                    <input type="text" class="form-control form-control-sm" id="jml_faktur" name="jml_faktur" readonly>
                                </div>
                            </div>
                            <div class="row justify-content-end">
                                <label for="total_bpk" class="col-form-label @if ($device == 'Mobile') col-lg-6 @else col-lg-2 @endif col-6">Total BPK</label>
                                <div class="@if ($device == 'Mobile') col-lg-6 @else col-lg-3 @endif col-6">
                                    <input type="text" class="form-control form-control-sm" value="0" id="total_bpk" name="total_bpk" readonly>
                                </div>
                            </div>
                            <div class="row justify-content-end">
                                <label for="total_pemayaran" class="col-form-label @if ($device == 'Mobile') col-lg-6 @else col-lg-2 @endif col-6">Total Pembayaran</label>
                                <div class="@if ($device == 'Mobile') col-lg-6 @else col-lg-3 @endif col-6">
                                    <input type="text" class="form-control form-control-sm" value="0" id="total_pemayaran" readonly>
                                </div>
                            </div>
                            <div class="row justify-content-end">
                                <label for="sisa" class="col-form-label @if ($device == 'Mobile') col-lg-6 @else col-lg-2 @endif col-6">Sisa</label>
                                <div class="@if ($device == 'Mobile') col-lg-6 @else col-lg-3 @endif col-6">
                                    <input type="text" class="form-control form-control-sm" id="sisa" name="sisa" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer @if ($device == 'Mobile') bg-white rounded-3 @endif">
                        <button type="button" class="btn btn-primary font-weight-bold @if ($device == 'Mobile') col-lg-12 @else col-lg-1 @endif col-12" id="pilih_pembayaran" >OK</button>
                        <button type="button" class="btn btn-secondary font-weight-bold @if ($device == 'Mobile') col-lg-12 @else col-lg-1 @endif col-12" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal filter list -->
        <div class="modal fade shadow-lg" id="ModalFilter" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Filter</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="tgl_awal" class="col-form-label">Tanggal Awal</label>
                            <div class="">
                                <input type="date" class="form-control" id="tgl_awal" name="tgl_awal" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="tgl_akhir" class="col-form-label">Tanggal Akhir</label>
                            <div class="">
                                <input type="date" class="form-control" id="tgl_akhir" name="tgl_akhir" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="nominal_awal" class="col-form-label">Nilai Faktur</label>
                            <div class="">
                                <input type="text" class="form-control" id="nominal_awal" name="nominal_awal" value="0">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="sales" class="col-form-label">MKR</label>
                            <div class="">
                                <input type="text" class="form-control" id="sales" name="sales" value="" oninput="this.value = this.value.toUpperCase()">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary">Terapkan</button>
                </div>
            </div>
            </div>
        </div>


        

<!-- Modal -->
<div class="modal fade" id="Modalkirim" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-md-down @if ($device == 'Mobile') @else modal-md @endif">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="staticBackdropLabel">Upload bukti pembayaran</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body overflow-auto">
            <input type="file" id="image" name="image" class="d-none" accept="image/*">
            <div class="p-6">
                {{-- <label class="col-form-label">Faktur List :</label> --}}
                <div id="faktur_list_bayar">
                </div>
            </div>
            
            <label class="ms-6 col-12 col-form-label border-top">Upload Gambar Bukti Pembayaran</label>
            <div class="mx-3 overflow-auto row" id="container_upload" style="height: 160px; width: auto;">
                <div class="col-6 col-lg-4 mb-3" id="card_image" style="cursor: pointer; display: flex; justify-content: center; align-items: center;">
                    <div class="card border bg-secondary text-center w-100 h-150px">
                        <div class="m-auto">
                            <i class="bi bi-camera fs-1"></i>
                            <label class="d-block">Upload Gambar</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3 mx-6 @if ($device == 'Mobile') bg-white card @endif">
                <label class="col-form-label border-top">DETAIL PEMBAYARAN</label>
                <div class="row justify-content-start">
                    <div class="col-12">
                        <table class="w-100 table table-row-dashed table-row-gray-300">
                            <thead>
                            </thead>
                            <tbody>
                                <tr id="detail_bayar" class="border-gray-300 border-top-dashed py-10">
                                    <td class="fs-7 text-start">Total Pembayaran</td>
                                    <td class="text-end">Rp</td>
                                    <td id="total_dibayar" class="text-end" id="total_faktur"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-success font-weight-bold @if ($device == 'Mobile') col-lg-12 @else col-lg-3 @endif col-12" id="kirim_simpan">SIMPAN</button>
        </div>
      </div>
    </div>
  </div>

@if ($device == 'Mobile')
    <!-- Modal Edit Jumlah Dibayar -->
        <div class="modal fade shadow-lg" id="ModalEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Jumlah Dibayar dari </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="jml_dibayar" class="col-form-label">Jumlah Dibayar</label>
                            <input type="text" class="form-control" id="jml_dibayar" name="jml_dibayar" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="simpan_jml_dibayar">Simpan</button>
                </div>
            </div>
            </div>
        </div>
@endif
    @include('layouts.option.optiondealer')


    @push('scripts')
        <script src="{{ asset('assets/js/suma/option/option.js') }}"></script>
        <script type="text/javascript">
            let device = '{{ $device }}';
        </script>
        {{-- terdapat session succesPembayaran --}}
        @if (session('successPembayaran'))
            <script type="text/javascript">
            swal.fire({
                title: "{{ session('successPembayaran') }}",
                text: "Total pembayaran : Rp. " + "{{ session('jml') }}",
                icon: 'success',
                // tombol ok hijau
                customClass: {
                    confirmButton: 'btn btn-success'
                },
            })
            </script>
        @endif

        <script src="{{ asset('assets/js/suma/orders/penerimaan/pembayaran/pembayaran.js') }}?v={{ time() }}"></script>
        @if ($device == 'Mobile')
            <script src="{{ asset('assets/js/suma/orders/penerimaan/pembayaran/pembayaranCard.js') }}?v={{ time() }}"></script>
        @else
            <script src="{{ asset('assets/js/suma/orders/penerimaan/pembayaran/pembayaranTabel.js') }}?v={{ time() }}"></script>
        @endif

    @endpush
@endsection
