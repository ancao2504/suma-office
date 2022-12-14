@extends('layouts.main.index')
@section('title','Parts')
@section('subtitle','Stock Harian')
@section('container')
    <div class="row g-0">
        <form id="formStockHarian" action="{{ route('parts.stock-harian-proses') }}" method="get" autocomplete="off">
            <div class="card card-flush">
                <div class="card-header align-items-center border-0 mt-4">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="fw-bolder mb-2 text-dark">Stock Harian</span>
                        <span class="text-muted fw-bold fs-7">Daftar stock harian suma honda</span>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-0">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="form-label">Class Produk</label>
                            <select id="selectClassProduk" name="kode_class" class="form-select" data-control="select2">
                                <option value="">SEMUA</option>
                                @foreach ($class_produk as $list)
                                <option value="{{ $list->kode_class }}"
                                    @if(old('kode_class') != "")
                                        @if($list->kode_class == old('kode_class'))
                                            {{"selected"}}
                                        @endif
                                    @else
                                        @if(@isset($kode_class))
                                            @if($kode_class == $list->kode_class)
                                                {{"selected"}}
                                            @endif
                                        @endif
                                    @endif>{{ $list->keterangan }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-0">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="form-label">Group Produk</label>
                            <select id="selectGroupProduk" name="kode_produk" class="form-select" data-control="select2">
                                <option value="">SEMUA</option>
                                @foreach ($group_produk as $list)
                                <option value="{{ $list->kode_produk }}"
                                    @if(old('kode_produk') != "")
                                        @if($list->kode_produk == old('kode_produk'))
                                            {{"selected"}}
                                        @endif
                                    @else
                                        @if(@isset($kode_produk))
                                            @if($kode_produk == $list->kode_produk)
                                                {{"selected"}}
                                            @endif
                                        @endif
                                    @endif>{{ $list->keterangan }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-0">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="form-label">Sub Produk</label>
                            <select id="selectSubProduk" name="kode_sub" class="form-select" data-control="select2">
                                <option value="">SEMUA</option>
                                @foreach ($sub_produk as $list)
                                <option value="{{ $list->kode_sub }}"
                                    @if(old('kode_sub') != "")
                                        @if($list->kode_sub == old('kode_sub'))
                                            {{"selected"}}
                                        @endif
                                    @else
                                        @if(@isset($kode_sub))
                                            @if($kode_sub == $list->kode_sub)
                                                {{"selected"}}
                                            @endif
                                        @endif
                                    @endif>{{ $list->keterangan }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-0">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="form-label">Group Level</label>
                            <select id="selectGroupLevel" name="kode_produk_level" class="form-select" data-control="select2" data-hide-search="true">
                                <option value="">SEMUA</option>
                                @foreach ($group_level as $list)
                                <option value="{{ $list->level }}"
                                    @if(old('level') != "")
                                        @if($list->level == old('level'))
                                            {{"selected"}}
                                        @endif
                                    @else
                                        @if(@isset($level))
                                            @if($level == $list->level)
                                                {{"selected"}}
                                            @endif
                                        @endif
                                    @endif>{{ $list->level }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-0">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="form-label">Fix / Reguler</label>
                            <select id="selectFrg" name="frg" class="form-select" data-control="select2" data-hide-search="true">
                                <option value="">SEMUA</option>
                                <option value="F">FIX</option>
                                <option value="R">REGULER</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-0">
                        <div class="mb-10 d-flex flex-wrap gap-5">
                            <div class="fv-row w-100 flex-md-root fv-plugins-icon-container">
                                <label class="form-label">Kode Lokasi</label>
                                <input id="inputKodeLokasi" name="kode_lokasi" class="form-control form-control-solid" readonly
                                    @if(isset($kode_lokasi)) value="{{ $kode_lokasi }}" @else value="{{ old('kode_lokasi') }}"@endif>
                            </div>
                            <div class="fv-row w-100 flex-md-root">
                                <label class="form-label">Kode Rak</label>
                                <input id="inputKodeRak" name="kode_rak" class="form-control form-control-solid" readonly
                                    @if(isset($kode_rak)) value="{{ $kode_rak }}" @else value="{{ old('kode_rak') }}"@endif>
                            </div>
                        </div>
                    </div>

                    <div class="row g-0">
                        <div class="mb-10 d-flex flex-wrap gap-5">
                            <div class="fv-row w-100 flex-md-root fv-plugins-icon-container">
                                <label class="required form-label">Opsi Stock Sedia</label>
                                <select id="selectStockSedia" name="option_stock_sedia" class="form-select" data-control="select2" data-hide-search="true">
                                    <option value=">">></option>
                                    <option value=">=">>=</option>
                                    <option value="=">=</option>
                                    <option value="<"><</option>
                                    <option value="<="><=</option>
                                </select>
                            </div>
                            <div class="fv-row w-100 flex-md-root">
                                <label class="required form-label">Nilai Stock Sedia</label>
                                <input id="inputNilaiStockSedia" name="nilai_stock_sedia" class="form-control" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required
                                    @if(isset($nilai_stock_sedia)) value="{{ $nilai_stock_sedia }}" @else value="{{ old('nilai_stock_sedia') }}"@endif>
                            </div>
                        </div>
                    </div>
                    <button name="action" value="cetak" class="btn btn-primary" type="submit" formtarget="_blank">
                        <i class="bi bi-printer-fill fs-4 me-2"></i>Cetak
                    </button>
                    <button id="excel" name="action" value="excel" class="btn btn-success" type="submit">
                        <i class="bi bi-file-earmark-excel-fill fs-4 me-2"></i>Excel
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script type="text/javascript">
            // $(document).on('click', '#excel', function(e) {
            //     e.preventDefault();

            //     $.ajax({
            //         type: "get",
            //         url: "{{ route('parts.stock-harian-proses') }}",
            //         success: function (d){
            //             var a = document.createElement("a");
            //             a.download = "filename.xls";
            //             a.href = "data:application/vnd.ms-excel,"+encodeURI(d);
            //             document.body.appendChild(a);
            //             a.click();
            //         }
            //     });
            // });
            // $(document).ready(function() {

            // });
        </script>
    @endpush
@endsection

