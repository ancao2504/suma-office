@extends('layouts.main.index')
@section('title','Orders')
@section('subtitle','Tracking Order')
@section('container')
    <div class="row g-0">
        <form action="#" method="get">
            <div class="card card-flush">
                <div class="card-header align-items-center border-0 mt-4">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="fw-bolder mb-2 text-dark">Tracking Order</span>
                        <span class="text-muted fw-bold fs-7">Tracking order per-nomor faktur</span>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mt-2">
                            <div class="row">
                                <span class="text-muted fw-bold d-block fs-7">Nomor Faktur :</span>
                                <span class="text-gray-800 fs-6 fw-bolder">@if(isset($nomor_faktur)) {{ $nomor_faktur }} @else {{ old('nomor_faktur') }}@endif</span>
                            </div>
                            <div class="row mt-4">
                                <span class="text-muted fw-bold d-block fs-7">Tanggal Faktur :</span>
                                <span class="text-gray-800 fs-6 fw-bolder">@if(isset($tanggal_faktur)) {{ $tanggal_faktur }} @else {{ old('tanggal_faktur') }}@endif</span>
                            </div>
                            <div class="row mt-4">
                                <span class="text-muted fw-bold d-block fs-7">Nomor Purchase Order :</span>
                                <span class="text-gray-800 fs-6 fw-bolder">@if(isset($nomor_pof)) {{ $nomor_pof }} @else {{ old('nomor_pof') }}@endif</span>
                            </div>
                        </div>
                        <div class="col-md-4 mt-2">
                            <div class="row">
                                <span class="text-muted fw-bold d-block fs-7">Kode Sales :</span>
                                <span class="text-gray-800 fs-6 fw-bolder">@if(isset($kode_sales)) {{ $kode_sales }} @else {{ old('kode_sales') }}@endif</span>
                            </div>
                            <div class="row mt-4">
                                <span class="text-muted fw-bold d-block fs-7">Kode Dealer :</span>
                                <span class="text-gray-800 fs-6 fw-bolder">@if(isset($kode_dealer)) {{ $kode_dealer }} @else {{ old('kode_dealer') }}@endif</span>
                            </div>
                            <div class="row mt-4">
                                <span class="text-muted fw-bold d-block fs-7">Keterangan :</span>
                                <span class="text-gray-800 fs-6 fw-bolder">@if(isset($keterangan)) {{ $keterangan }} @else - @endif</span>
                            </div>
                        </div>
                        <div class="col-md-4 mt-2">
                            <div class="row">
                                <span class="text-muted fw-bold d-block fs-7">Kode TPC :</span>
                                <span class="text-gray-800 fs-6 fw-bolder">@if(isset($kode_tpc)) {{ $kode_tpc }} @else {{ old('kode_sales') }}@endif</span>
                            </div>
                            <div class="row mt-4">
                                <span class="text-muted fw-bold d-block fs-7">BO / Tidak BO :</span>
                                <span class="text-gray-800 fs-6 fw-bolder">@if($bo == 'B') BO @else Tidak BO @endif</span>
                            </div>
                            <div class="row mt-4">
                                <span class="text-muted fw-bold d-block fs-7">Total Faktur :</span>
                                <span class="text-danger fs-6 fw-bolder">@if(isset($grand_total)) {{ number_format($grand_total) }} @else {{ old('grand_total') }}@endif</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-flush mt-5">
                <div class="card-header align-items-center border-0 mt-4">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="fw-bolder mb-2 text-dark">Status Pengiriman</span>
                        <span class="text-muted fw-bold fs-7">Status pengiriman faktur</span>
                    </h3>
                </div>
                <div class="card-body pt-5">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-line w-40px"></div>
                            <div class="timeline-icon symbol symbol-circle symbol-40px me-4">
                                @if ($detail_pengiriman->status_cetak_faktur == 1)
                                <div id="roundFakturCetak" class="symbol-label bg-light-success">
                                    <i id="icFakturCetak" class="bi bi-printer-fill fs-2x text-success"></i>
                                </div>
                                @else
                                <div id="roundFakturCetak" class="symbol-label bg-light-primary">
                                    <i id="icFakturCetak" class="bi bi-printer-fill fs-2x text-primary"></i>
                                </div>
                                @endif
                            </div>
                            <div class="timeline-content mb-10 mt-n1">
                                <div class="pe-3 mb-5">
                                    <div class="fs-5 fw-bold mt-1">Faktur sudah tercetak</div>
                                    <div class="d-flex align-items-center fs-6">
                                        <div class="text-muted fw-bold fs-7">{{ trim($detail_pengiriman->usertime_cetak_faktur) }}</div>
                                    </div>
                                    @if ($detail_pengiriman->status_cetak_faktur == 1)
                                    <span class="fs-8 fw-boldest text-success text-uppercase">FINISH</span>
                                    @else
                                    <span class="fs-8 fw-boldest text-primary text-uppercase">ON-PROGRESS</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-line w-40px"></div>
                            <div class="timeline-icon symbol symbol-circle symbol-40px me-4">
                                @if ($detail_pengiriman->nomor_surat_jalan != '')
                                <div id="roundGudang" class="symbol-label bg-light-success">
                                    <i id="icGudang" class="bi bi-person-check-fill fs-2x text-success"></i>
                                </div>
                                @elseif ($detail_pengiriman->status_cetak_faktur == 1)
                                <div id="roundGudang" class="symbol-label bg-light-primary">
                                    <i id="icGudang" class="bi bi-person-check-fill fs-2x text-primary"></i>
                                </div>
                                @else
                                <div id="roundGudang" class="symbol-label bg-light">
                                    <i id="icGudang" class="bi bi-person-check-fill fs-2x"></i>
                                </div>
                                @endif
                            </div>
                            <div class="timeline-content mb-10 mt-n1">
                                <div class="pe-3 mb-5">
                                    <div class="fs-5 fw-bold mt-1">Gudang mempersiapkan barang</div>
                                    <div class="d-flex align-items-center mt-1 fs-6">
                                        <div class="text-muted fw-bold fs-7">{{ trim($detail_pengiriman->usertime_surat_jalan) }}</div>
                                    </div>
                                    @if ($detail_pengiriman->nomor_surat_jalan != '')
                                    <span class="fs-8 fw-boldest text-success text-uppercase">FINISH</span>
                                    @elseif ($detail_pengiriman->status_cetak_faktur == 1)
                                    <span class="fs-8 fw-boldest text-primary text-uppercase">ON-PROGRESS</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-line w-40px"></div>
                            <div class="timeline-icon symbol symbol-circle symbol-40px me-4">
                                @if ($detail_pengiriman->status_cetak_surat_jalan == 1)
                                <div id="roundSuratJalan" class="symbol-label bg-light-success">
                                    <i id="icSuratJalan" class="bi bi-file-text-fill fs-2x text-success"></i>
                                </div>
                                @elseif ($detail_pengiriman->nomor_surat_jalan != '')
                                <div id="roundSuratJalan" class="symbol-label bg-light-primary">
                                    <i id="icSuratJalan" class="bi bi-file-text-fill fs-2x text-primary"></i>
                                </div>
                                @else
                                <div id="roundSuratJalan" class="symbol-label bg-light">
                                    <i id="icSuratJalan" class="bi bi-file-text-fill fs-2x"></i>
                                </div>
                                @endif
                            </div>
                            <div class="timeline-content mb-10 mt-n1">
                                <div class="pe-3 mb-5">
                                    <div class="fs-5 fw-bold mt-1">Surat jalan sudah tercetak</div>
                                    <div class="d-flex align-items-center mt-1 fs-6">
                                        <div class="text-muted fw-bold fs-7">{{ trim($detail_pengiriman->usertime_cetak_surat_jalan) }}</div>
                                    </div>
                                    @if ($detail_pengiriman->status_cetak_surat_jalan == 1)
                                    <span class="fs-8 fw-boldest text-success text-uppercase">FINISH</span>
                                    @elseif ($detail_pengiriman->nomor_surat_jalan != '')
                                    <span class="fs-8 fw-boldest text-primary text-uppercase">ON-PROGRESS</span>
                                    @endif
                                </div>
                                @if ($detail_pengiriman->status_cetak_surat_jalan == 1)
                                <div id="detailSuratJalan" class="overflow-auto pb-5">
                                    <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-250px px-7 py-3 mb-5">
                                        <div class="pe-3">
                                            <div class="fs-5 fw-bold mt-1">No Surat Jalan :</div>
                                            <div class="d-flex align-items-center mt-1 fs-6">
                                                <div class="text-muted fw-bold fs-7">{{ trim($detail_pengiriman->nomor_surat_jalan) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-line w-40px"></div>
                            <div class="timeline-icon symbol symbol-circle symbol-40px me-4">
                                @if ($detail_pengiriman->nomor_serah_terima != '')
                                <div id="roundSerahTerimaEkspedisi" class="symbol-label bg-light-success">
                                    <i id="icSerahTerimaEkspedisi" class="bi bi-send-check-fill fs-2x text-success"></i>
                                </div>
                                @elseif($detail_pengiriman->status_cetak_surat_jalan == 1)
                                <div id="roundSerahTerimaEkspedisi" class="symbol-label bg-light-primary">
                                    <i id="icSerahTerimaEkspedisi" class="bi bi-send-check-fill fs-2x text-primary"></i>
                                </div>
                                @else
                                <div id="roundSerahTerimaEkspedisi" class="symbol-label bg-light">
                                    <i id="icSerahTerimaEkspedisi" class="bi bi-send-check-fill fs-2x"></i>
                                </div>
                                @endif
                            </div>
                            <div class="timeline-content mb-10 mt-n1">
                                <div class="pe-3 mb-5">
                                    <div class="fs-5 fw-bold mt-1">Serah terima dengan ekspedisi</div>
                                    <div class="d-flex align-items-center mt-1 fs-6">
                                        <div class="text-muted fw-bold me-2 fs-7">{{ $detail_pengiriman->usertime_serah_terima }}</div>
                                    </div>
                                    @if ($detail_pengiriman->nomor_serah_terima != '')
                                    <span class="fs-8 fw-boldest text-success text-uppercase">FINISH</span>
                                    @elseif ($detail_pengiriman->nomor_surat_jalan != '')
                                    <span class="fs-8 fw-boldest text-primary text-uppercase">ON-PROGRESS</span>
                                    @endif
                                </div>
                                @if($detail_pengiriman->nomor_serah_terima != '')
                                <div id="detailSerahTerimaEkspedisi" class="overflow-auto pb-5">
                                    <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-250px px-7 py-3 mb-5">
                                        <div class="pe-3">
                                            <div class="fs-5 fw-bold mt-1">No Serah Terima :</div>
                                            <div class="d-flex align-items-center mt-1 fs-6">
                                                <div class="text-muted fw-bold me-2 fs-7">{{ $detail_pengiriman->nomor_serah_terima }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-250px px-7 py-3 mb-5">
                                        <div class="pe-3">
                                            <div class="fs-5 fw-bold mt-1">Kendaraan :</div>
                                            <div class="d-flex align-items-center mt-1 fs-6">
                                                <div class="text-muted fw-bold me-2 fs-7">{{ $detail_pengiriman->kendaraan_serah_terima }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-250px px-7 py-3 mb-5">
                                        <div class="pe-3">
                                            <div class="fs-5 fw-bold mt-1">Sopir :</div>
                                            <div class="d-flex align-items-center mt-1 fs-6">
                                                <div class="text-muted fw-bold me-2 fs-7">{{ $detail_pengiriman->sopir_serah_terima }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-line w-40px"></div>
                            <div class="timeline-icon symbol symbol-circle symbol-40px me-4">
                                @if ($detail_pengiriman->status_toko_terima == 1)
                                <div id="roundTokoTerimaFaktur" class="symbol-label bg-light-success">
                                    <i id="icTokoTerimaFaktur" class="bi bi-save-fill fs-2x text-success"></i>
                                </div>
                                @elseif ($detail_pengiriman->nomor_serah_terima != '')
                                <div id="roundTokoTerimaFaktur" class="symbol-label bg-light-primary">
                                    <i id="icTokoTerimaFaktur" class="bi bi-save-fill fs-2x text-primary"></i>
                                </div>
                                @else
                                <div id="roundTokoTerimaFaktur" class="symbol-label bg-light">
                                    <i id="icTokoTerimaFaktur" class="bi bi-save-fill fs-2x"></i>
                                </div>
                                @endif
                            </div>
                            <div class="timeline-content mb-10 mt-n1">
                                <div class="pe-3 mb-5">
                                    <div class="fs-5 fw-bold mt-1">Toko terima barang</div>
                                    @if ($detail_pengiriman->status_toko_terima == 1)
                                    <span class="fs-8 fw-boldest text-success text-uppercase">FINISH</span>
                                    @elseif ($detail_pengiriman->nomor_serah_terima != '')
                                    <span class="fs-8 fw-boldest text-primary text-uppercase">ON-PROGRESS</span>
                                    @endif
                                </div>
                                @if ($detail_pengiriman->status_toko_terima == 1)
                                <div id="detailSuratJalan" class="overflow-auto pb-5">
                                    <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-250px px-7 py-3 mb-5">
                                        <div class="pe-3">
                                            <div class="fs-5 fw-bold mt-1">Tanggal terima :</div>
                                            <div class="d-flex align-items-center mt-1 fs-6">
                                                <div class="text-muted fw-bold fs-7">{{ trim($detail_pengiriman->tanggal_terima_toko) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(strtoupper(trim($device)) == 'DESKTOP')
            <div class="card card-flush mt-5">
                <div class="card-header align-items-center border-0 mt-4">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="fw-bolder mb-2 text-dark">Rincian Barang</span>
                        <span class="text-muted fw-bold fs-7">Daftar barang per-nomor faktur</span>
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-175px">Part Number</th>
                                    <th class="min-w-70px text-end">Jml Jual</th>
                                    <th class="min-w-100px text-end">Harga</th>
                                    <th class="min-w-70px text-end">Disc</th>
                                    <th class="min-w-100px text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody class="fw-bold text-gray-600">
                                @forelse ($detail_faktur as $data)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="symbol symbol-50px">
                                                <span class="symbol-label" style="background-image:url({{ $data->image_part }}), url({{ URL::asset('assets/images/background/part_image_not_found.png') }});"></span>
                                            </span>
                                            <div class="ms-5">
                                                <span class="fw-bolder text-dark">{{ $data->nama_part }}</a>
                                                <div class="fs-7 text-muted">{{ $data->part_number }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">{{ number_format($data->jml_jual) }}</td>
                                    <td class="text-end">{{ number_format($data->harga) }}</td>
                                    <td class="text-end">{{ number_format($data->disc_detail, 2) }}</td>
                                    <td class="text-end">{{ number_format($data->total_detail) }}</td>
                                </tr>
                                @empty
                                <center>
                                    <tr>
                                        <img src="{{ asset('assets/media/illustrations/sketchy-1/4.png') }}" alt="" class="mw-100 mh-150px mb-7">
                                        <div class="fw-bolder fs-3 text-gray-600 text-hover-primary">Data Not Found</div>
                                    </tr>
                                </center>
                                @endforelse
                                <tr>
                                    <td class="text-end fw-bolder text-dark">Total Item</td>
                                    <td colspan="1" class="text-end fw-bolder">{{ number_format($total_jual) }}</td>
                                    <td class="text-end fw-bolder text-dark">Subtotal</td>
                                    <td colspan="2" class="text-end fw-bolder">{{ number_format($sub_total) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bolder text-dark">Discount (%)</td>
                                    <td class="text-end fw-bolder">{{ number_format($disc_header, 2) }}</td>
                                    <td class="text-end fw-bolder">{{ number_format($nominal_disc_header) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bolder text-dark">Discount (Rp.)</td>
                                    <td colspan="2" class="text-end fw-bolder">{{ number_format($disc_rupiah) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="fs-3 text-dark text-end fw-bolder text-dark">Grand Total</td>
                                    <td colspan="2" class="fs-3 text-end fw-bolder text-danger">{{ number_format($grand_total) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            @forelse ($detail_faktur as $data)
            <div class="card card-flush mt-5">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="symbol symbol-50px me-5">
                            <span class="symbol-label" style="background-image:url({{ $data->image_part }}), url({{ URL::asset('assets/images/background/part_image_not_found.png') }});"></span>
                        </div>
                        <div class="flex-grow-1">
                            <span class="text-dark fw-bolder text-hover-primary fs-6">{{ trim($data->part_number) }}</span>
                            <span class="text-muted d-block fw-bold descriptionpart">{{ $data->nama_part }}</span>
                            <span class="text-danger fw-bolder">Rp. {{ number_format($data->harga) }}</span>
                            <div class="flex-grow-1 mt-4">
                                <div class="row">
                                    <div class="col-6">
                                        <span class="text-muted d-block fw-bold">Quantity :</span>
                                        <span class="text-dark fw-bold">{{ number_format($data->jml_jual) }}</span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted d-block fw-bold">Disc(%) :</span>
                                        <span class="text-dark fw-bold">{{ number_format($data->disc_detail, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <span class="text-muted d-block fw-bold mt-4">Total :</span>
                            <span class="text-danger fw-bolder">Rp. {{ number_format($data->total_detail) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <center>
                <tr>
                    <img src="{{ asset('assets/media/illustrations/sketchy-1/4.png') }}" alt="" class="mw-100 mh-150px mb-7">
                    <div class="fw-bolder fs-3 text-gray-600 text-hover-primary">Data Not Found</div>
                </tr>
            </center>
            @endforelse
            <div class="card card-flush mt-5">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="d-flex align-items-center flex-wrap flex-grow-1 mt-n2 mt-lg-n1">
                            <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pe-3">
                                <span class="text-muted d-block fw-bold">Total Detail</span>
                            </div>
                            <div class="text-end py-lg-0 py-2">
                                <span class="text-gray-800 fw-boldest fs-6">Rp. {{ number_format($sub_total) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="d-flex align-items-center flex-wrap flex-grow-1 mt-n2 mt-lg-n1">
                            <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pe-3">
                                <span class="text-muted d-block fw-bold">Discount(%)</span>
                            </div>
                            <div class="text-end py-lg-0 py-2">
                                <span class="text-gray-800 fw-boldest fs-6">{{ number_format($disc_header, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="d-flex align-items-center flex-wrap flex-grow-1 mt-n2 mt-lg-n1">
                            <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pe-3">
                                <span class="text-muted d-block fw-bold">Discount(Rp.)</span>
                            </div>
                            <div class="text-end py-lg-0 py-2">
                                <span class="text-gray-800 fw-boldest fs-6">Rp. {{ number_format($disc_rupiah) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="d-flex align-items-center flex-wrap flex-grow-1 mt-n2 mt-lg-n1">
                            <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pe-3">
                                <span class="text-muted d-block fw-bold">Grand Total</span>
                            </div>
                            <div class="text-end py-lg-0 py-2">
                                <span class="text-danger fw-boldest fs-6">Rp. {{ number_format($grand_total) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </form>
    </div>
    @push('scripts')
        <script type="text/javascript">
        </script>
    @endpush
@endsection
