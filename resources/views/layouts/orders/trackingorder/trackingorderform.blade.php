@extends('layouts.main.index')
@section('title','Orders')
@section('subtitle','Tracking Order')
@section('container')
    <div class="card">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Tracking Order</span>
                <span class="text-muted fw-bold fs-7">Tracking order per-nomor faktur</span>
            </h3>
        </div>
        <div class="card-body">
            <div class="d-flex flex-column flex-xl-row">
                <div class="flex-lg-row-fluid me-xl-18 mb-10 mb-xl-0">
                    <div class="mt-n1">
                        <div class="m-0">
                            <div class="row g-5 mb-8">
                                <div class="col-sm-6">
                                    <div class="fw-bold fs-7 text-gray-600 mb-1">Nomor Faktur:</div>
                                    <div class="fw-bold fs-6 text-dark">{{ $nomor_faktur }}</div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="fw-bold fs-7 text-gray-600 mb-1">Tanggal Faktur:</div>
                                    <div class="fw-bold fs-6 text-dark">{{ date('j F Y', strtotime($tanggal_faktur)) }}</div>
                                </div>
                            </div>
                            <div class="row g-5 mb-8">
                                <div class="col-sm-6">
                                    <div class="fw-bold fs-7 text-gray-600 mb-1">Salesman:</div>
                                    <div class="fw-bold fs-6 text-dark">
                                        <div class="d-flex align-items-center flex-wrap">
                                            <span class="pe-2">{{ $nama_sales }}</span>
                                            <span class="fs-7 fw-boldest text-info d-flex align-items-center">
                                                <span class="bullet bullet-dot bg-info me-2"></span>{{ $kode_sales }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="fw-bold fs-7 text-gray-600 mb-1">Dealer:</div>
                                    <div class="fw-bold fs-6 text-dark">
                                        <div class="d-flex align-items-center flex-wrap">
                                            <span class="pe-2">{{ $nama_dealer }}</span>
                                            <span class="fs-7 fw-boldest text-primary d-flex align-items-center">
                                                <span class="bullet bullet-dot bg-primary me-2"></span>{{ $kode_dealer }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-5 mb-8">
                                <div class="col-sm-6">
                                    <div class="fw-bold fs-7 text-gray-600 mb-1">No Purchase Order:</div>
                                    <div class="fw-bold fs-6 text-dark">{{ $nomor_pof }}</div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="fw-bold fs-7 text-gray-600 mb-1">Keterangan:</div>
                                    <div class="fw-bold fs-6 text-dark">@if(isset($keterangan)) {{ $keterangan }} @else - @endif</div>
                                </div>
                            </div>
                            <div class="row g-5 mb-8">
                                <div class="col-sm-6">
                                    <div class="fw-bold fs-7 text-gray-600 mb-1">Kode TPC:</div>
                                    <div class="fw-bold fs-6 text-dark">{{ $kode_tpc }}</div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="fw-bold fs-7 text-gray-600 mb-1">BO / Tidak BO:</div>
                                    <div class="fw-bold fs-6 text-dark">@if($bo == 'B') BO @else Tidak BO @endif</div>
                                </div>
                            </div>
                            @if(strtoupper(trim($device)) == 'DESKTOP')
                            <div class="flex-grow-1">
                                <div class="table-responsive">
                                    <table class="table align-middle table-row-bordered fs-6">
                                        <thead>
                                            <tr class="text-gray-400 fw-boldest fs-7 text-uppercase gy-5 mb-0">
                                                <th class="min-w-175px">Part Number</th>
                                                <th class="min-w-70px text-end">Qty</th>
                                                <th class="min-w-100px text-end">Harga</th>
                                                <th class="min-w-70px text-end">Disc</th>
                                                <th class="min-w-100px text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="fs-6 fw-bold text-dark">
                                            @foreach($detail_faktur as $data)
                                            <tr class="text-dark">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="symbol symbol-50px">
                                                            <span class="symbol-label" style="background-image:url({{ $data->image_part }}), url({{ URL::asset('assets/images/background/part_image_not_found.png') }});"></span>
                                                        </span>
                                                        <div class="ms-5">
                                                            <span class="fs-7">{{ $data->nama_part }}</a>
                                                            <div class="fs-7 text-muted">{{ $data->part_number }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-end">{{ number_format($data->jml_jual) }}</td>
                                                <td class="text-end">{{ number_format($data->harga) }}</td>
                                                <td class="text-end">{{ number_format($data->disc_detail, 2) }}</td>
                                                <td class="text-end">{{ number_format($data->total_detail) }}</td>
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="3" class="text-end fw-bolder text-muted text-uppercase fs-7">Subtotal</td>
                                                <td colspan="2" class="text-end fw-bold">{{ number_format($sub_total) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-end fw-bolder text-muted text-uppercase fs-7">Discount (%)</td>
                                                <td class="text-end fw-bold">{{ number_format($disc_header, 2) }}</td>
                                                <td class="text-end fw-bold">{{ number_format($nominal_disc_header) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-end fw-bolder text-muted text-uppercase fs-7">Discount (Rp.)</td>
                                                <td colspan="2" class="text-end fw-bold">{{ number_format($disc_rupiah) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-end fw-bolder text-muted text-uppercase fs-7">Grand Total</td>
                                                <td colspan="2" class="text-end fw-bold text-danger">{{ number_format($grand_total) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="m-0">
                    <div class="d-print-none border border-dashed border-gray-300 card-rounded h-lg-100 min-w-md-350px p-9 bg-lighten">
                        <h6 class="mb-8 fw-boldest text-gray-600 text-hover-primary">STATUS PENGIRIMAN</h6>
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
                                        <div class="fw-bold fs-6 text-dark mt-1">Faktur sudah tercetak</div>
                                        <div class="d-flex align-items-center fs-6">
                                            <div class="fw-bold text-gray-600 fs-7">{{ trim($detail_pengiriman->usertime_cetak_faktur) }}</div>
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
                                        <div class="fw-bold fs-6 text-dark mt-1">Gudang mempersiapkan barang</div>
                                        <div class="d-flex align-items-center mt-1 fs-6">
                                            <div class="fw-bold text-gray-600 fs-7">{{ trim($detail_pengiriman->usertime_surat_jalan) }}</div>
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
                                        <div class="fw-bold fs-6 text-dark mt-1">Surat jalan sudah tercetak</div>
                                        <div class="d-flex align-items-center mt-1 fs-6">
                                            <div class="fw-bold text-gray-600 fs-7">{{ trim($detail_pengiriman->usertime_cetak_surat_jalan) }}</div>
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
                                                <div class="fw-bold fs-6 text-dark mt-1">No Surat Jalan :</div>
                                                <div class="d-flex align-items-center mt-1 fs-6">
                                                    <div class="fw-bold text-gray-600 fs-7">{{ trim($detail_pengiriman->nomor_surat_jalan) }}</div>
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
                                        <div class="fw-bold fs-6 text-dark mt-1">Serah terima dengan ekspedisi</div>
                                        <div class="d-flex align-items-center mt-1 fs-6">
                                            <div class="fw-bold text-gray-600 fs-7 me-2">{{ $detail_pengiriman->usertime_serah_terima }}</div>
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
                                                <div class="fw-bold fs-6 text-dark mt-1">No Serah Terima :</div>
                                                <div class="d-flex align-items-center mt-1 fs-6">
                                                    <div class="fw-bold text-gray-600 fs-7 me-2">{{ $detail_pengiriman->nomor_serah_terima }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-250px px-7 py-3 mb-5">
                                            <div class="pe-3">
                                                <div class="fw-bold fs-6 text-dark mt-1">Kendaraan :</div>
                                                <div class="d-flex align-items-center mt-1 fs-6">
                                                    <div class="fw-bold text-gray-600 fs-7 me-2">{{ $detail_pengiriman->kendaraan_serah_terima }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-250px px-7 py-3 mb-5">
                                            <div class="pe-3">
                                                <div class="fw-bold fs-6 text-dark mt-1">Sopir :</div>
                                                <div class="d-flex align-items-center mt-1 fs-6">
                                                    <div class="fw-bold text-gray-600 fs-7 me-2">{{ $detail_pengiriman->sopir_serah_terima }}</div>
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
                                        <div class="fw-bold fs-6 text-dark mt-1">Toko terima barang</div>
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
                                                <div class="fw-bold fs-6 text-dark mt-1">Tanggal terima :</div>
                                                <div class="d-flex align-items-center mt-1 fs-6">
                                                    <div class="fw-bold text-gray-600 fs-7 me-2">{{ trim($detail_pengiriman->tanggal_terima_toko) }}</div>
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
            </div>
        </div>
    </div>

    <div class="row g-0">
        @if(strtoupper(trim($device)) == 'MOBILE')
        @foreach($detail_faktur as $data)
        <div class="card card-flush mt-5">
            <div class="card-body">
                <div class="d-flex">
                    <div class="symbol symbol-50px me-5">
                        <span class="symbol-label" style="background-image:url({{ $data->image_part }}), url({{ URL::asset('assets/images/background/part_image_not_found.png') }});"></span>
                    </div>
                    <div class="flex-grow-1">
                        <span class="fs-6 text-dark fw-bolder text-hover-primary mb-2">{{ trim($data->part_number) }}</span>
                        <span class="fs-6 text-dark fw-bold d-block descriptionpart mb-2">{{ $data->nama_part }}</span>
                        <span class="fs-6 text-danger fw-bolder">Rp. {{ number_format($data->harga) }}</span>
                        <div class="flex-grow-1 mt-4">
                            <div class="row">
                                <div class="col-6">
                                    <span class="text-muted fs-7 d-block fw-bold">Quantity :</span>
                                    <span class="text-dark fs-6 fw-bold">{{ number_format($data->jml_jual) }}</span>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted fs-7 d-block fw-bold">Disc(%) :</span>
                                    <span class="text-dark fs-6 fw-bold">{{ number_format($data->disc_detail, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <span class="text-muted fs-7 d-block fw-bold mt-4">Total :</span>
                        <span class="text-danger fs-6 fw-bolder">Rp. {{ number_format($data->total_detail) }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        <div class="card card-flush mt-5">
            <div class="card-body">
                <div class="d-flex">
                    <div class="d-flex align-items-center flex-wrap flex-grow-1 mt-n2 mt-lg-n1">
                        <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pe-3">
                            <span class="text-muted fs-7 d-block fw-bold">Total Detail</span>
                        </div>
                        <div class="text-end py-lg-0 py-2">
                            <span class="text-dark fs-6 fw-bold">Rp. {{ number_format($sub_total) }}</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="d-flex align-items-center flex-wrap flex-grow-1 mt-n2 mt-lg-n1">
                        <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pe-3">
                            <span class="text-muted fs-7 d-block fw-bold">Discount(%)</span>
                        </div>
                        <div class="text-end py-lg-0 py-2">
                            <span class="text-dark fs-6 fw-bold">{{ number_format($disc_header, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="d-flex align-items-center flex-wrap flex-grow-1 mt-n2 mt-lg-n1">
                        <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pe-3">
                            <span class="text-muted fs-7 d-block fw-bold">Discount(Rp.)</span>
                        </div>
                        <div class="text-end py-lg-0 py-2">
                            <span class="text-dark fs-6 fw-bold">Rp. {{ number_format($disc_rupiah) }}</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="d-flex align-items-center flex-wrap flex-grow-1 mt-n2 mt-lg-n1">
                        <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pe-3">
                            <span class="text-muted fs-7 d-block fw-bold">Grand Total</span>
                        </div>
                        <div class="text-end py-lg-0 py-2">
                            <span class="text-danger fs-6 fw-bolder">Rp. {{ number_format($grand_total) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @push('scripts')
        <script type="text/javascript">
        </script>
    @endpush
@endsection
