@extends('layouts.main.index')
@section('title','Orders')
@section('subtitle','Faktur')
@section('container')
    <div class="row g-0">
        <div class="card card-flush shadow">
            <div class="card-header align-items-center border-0 mt-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Faktur</span>
                    <span class="text-muted fw-bold fs-7">Data faktur penjualan</span>
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
                                        <div class="fw-bolder text-dark">{{ $data->nomor_faktur }}</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="fw-bold fs-7 text-gray-600 mb-1">Tanggal Faktur:</div>
                                        <div class="fw-bolder text-dark">{{ date('j F Y', strtotime($data->tanggal_faktur)) }}</div>
                                    </div>
                                </div>
                                <div class="row g-5 mb-8">
                                    <div class="col-sm-6">
                                        <div class="fw-bold fs-7 text-gray-600 mb-1">Salesman:</div>
                                        <div class="fw-bolder text-dark">
                                            <div class="d-flex align-items-center flex-wrap">
                                                <span class="pe-2">{{ $data->nama_sales }}</span>
                                                <span class="fs-7 fw-boldest text-info d-flex align-items-center">
                                                    <span class="bullet bullet-dot bg-info me-2"></span>{{ $data->kode_sales }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="fw-bold fs-7 text-gray-600 mb-1">Dealer:</div>
                                        <div class="fw-bolder text-dark">
                                            <div class="d-flex align-items-center flex-wrap">
                                                <span class="pe-2">{{ $data->nama_dealer }}</span>
                                                <span class="fs-7 fw-boldest text-primary d-flex align-items-center">
                                                    <span class="bullet bullet-dot bg-primary me-2"></span>{{ $data->kode_dealer }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-5 mb-8">
                                    <div class="col-sm-6">
                                        <div class="fw-bold fs-7 text-gray-600 mb-1">No Purchase Order:</div>
                                        <div class="fw-bolder text-dark">{{ $data->nomor_pof }}</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="fw-bold fs-7 text-gray-600 mb-1">Keterangan:</div>
                                        <div class="fw-bolder text-dark">@if(isset($data->keterangan)) {{ $data->keterangan }} @else - @endif</div>
                                    </div>
                                </div>
                                <div class="row g-5 mb-8">
                                    <div class="col-sm-6">
                                        <div class="fw-bold fs-7 text-gray-600 mb-1">Kode TPC:</div>
                                        <div class="fw-bolder text-dark">{{ $data->kode_tpc }}</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="fw-bold fs-7 text-gray-600 mb-1">BO / Tidak BO:</div>
                                        <div class="fw-bolder text-dark">@if($data->bo == 'B') BO @else Tidak BO @endif</div>
                                    </div>
                                </div>
                                @if(strtoupper(trim($device)) == 'DESKTOP')
                                <div class="flex-grow-1">
                                    <div class="table-responsive">
                                        <table class="table align-middle table-row-bordered fs-6">
                                            <thead>
                                                <tr class="text-gray-400 fw-boldest fs-7 text-uppercase gy-5 mb-0">
                                                    <th class="min-w-175px">Part Number</th>
                                                    <th class="min-w-70px text-end">Order</th>
                                                    <th class="min-w-70px text-end">Jual</th>
                                                    <th class="min-w-100px text-end">Harga</th>
                                                    <th class="min-w-70px text-end">Disc</th>
                                                    <th class="min-w-100px text-end">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="fs-7 fw-bold text-dark">
                                                @foreach($data->detail_faktur as $data_detail)
                                                <tr class="text-dark">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="symbol symbol-50px">
                                                                <span class="symbol-label" style="background-image:url({{ $data_detail->image_part }}), url({{ URL::asset('assets/images/background/part_image_not_found.png') }});"></span>
                                                            </span>
                                                            <div class="ms-5">
                                                                <span class="fs-7">{{ trim($data_detail->nama_part) }}</a>
                                                                <div class="fs-7 text-muted">{{ $data_detail->part_number }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-end">{{ number_format($data_detail->jml_order) }}</td>
                                                    <td class="text-end">{{ number_format($data_detail->jml_jual) }}</td>
                                                    <td class="text-end">{{ number_format($data_detail->harga) }}</td>
                                                    <td class="text-end">{{ number_format($data_detail->disc_detail, 2) }}</td>
                                                    <td class="text-end">{{ number_format($data_detail->total_detail) }}</td>
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="4" class="text-end fw-bolder text-muted text-uppercase fs-7">Subtotal</td>
                                                    <td colspan="2" class="text-end fw-bold">{{ number_format($data->sub_total) }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="text-end fw-bolder text-muted text-uppercase fs-7">Discount (%)</td>
                                                    <td class="text-end fw-bold">{{ number_format($data->disc_header, 2) }}</td>
                                                    <td class="text-end fw-bold">{{ number_format($data->nominal_disc_header) }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="text-end fw-bolder text-muted text-uppercase fs-7">Discount (Rp.)</td>
                                                    <td colspan="2" class="text-end fw-bold">{{ number_format($data->disc_rupiah) }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="text-end fw-bolder text-muted text-uppercase fs-7">Grand Total</td>
                                                    <td colspan="2" class="text-end fw-bold text-danger">{{ number_format($data->grand_total) }}</td>
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
                            <h6 class="mb-8 fw-boldest text-gray-600">STATUS FAKTUR</h6>
                            <div class="mb-6">
                                <div class="fw-bold text-gray-600 fs-7">Jenis Beli:</div>
                                <div class="fw-boldest text-gray-800 fs-7">
                                    <div class="d-flex align-items-center flex-wrap">
                                        <span class="pe-2">{{ $data->kode_beli }}</span>
                                        <span class="fs-8 fw-boldest text-danger d-flex align-items-center">
                                            <span class="bullet bullet-dot bg-danger me-2"></span>{{ $data->keterangan_beli }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-6">
                                <div class="fw-bold text-gray-600 fs-7">Jenis Order:</div>
                                <div class="fw-boldest text-gray-800 fs-7">
                                    <div class="d-flex align-items-center flex-wrap">
                                        <span class="pe-2">{{ $data->rh }}</span>
                                        <span class="fs-7 fw-boldest text-primary d-flex align-items-center">
                                            @if($data->rh == 'H')
                                            <span class="bullet bullet-dot bg-danger me-2"></span>
                                            <span class="fs-8 fw-boldest text-danger">HOTLINE</span>
                                            @elseif($data->rh == 'P')
                                            <span class="bullet bullet-dot bg-info me-2"></span>
                                            <span class="fs-8 fw-boldest text-info">PMO</span>
                                            @else
                                                @if($data->status_pof == 1)
                                                <span class="bullet bullet-dot bg-primary me-2"></span>
                                                <span class="fs-8 fw-boldest text-primary">POF</span>
                                                @else
                                                <span class="bullet bullet-dot bg-success me-2"></span>
                                                <span class="fs-8 fw-boldest text-success">REGULER</span>
                                                @endif
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-14">
                                <div class="fw-bold text-gray-600 fs-7">TOP:</div>
                                <div class="fw-boldest text-gray-800 fs-7">
                                    <div class="d-flex align-items-center flex-wrap">
                                        <span class="pe-2">{{ $data->umur_faktur }} Hari</span>
                                        <span class="fs-7 fw-boldest text-primary d-flex align-items-center">
                                            <span class="bullet bullet-dot bg-primary me-2"></span>{{ $data->tanggal_akhir_faktur }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <h6 class="mb-8 fw-boldest text-gray-600">SURAT JALAN</h6>
                            <div class="mb-6">
                                <div class="fw-bold text-gray-600 fs-7">Nama Dealer SJ:</div>
                                <div class="fw-bolder text-gray-800 fs-7">{{ trim($data->dealer_sj->nama_dealer) }}</div>
                            </div>
                            <div class="mb-6">
                                <div class="fw-bold text-gray-600 fs-7">Alamat SJ:</div>
                                <div class="fw-bolder text-gray-800 fs-7">{{ trim($data->dealer_sj->alamat) }}</div>
                            </div>
                            <div class="mb-6">
                                <div class="fw-bold text-gray-600 fs-7">Kota SJ:</div>
                                <div class="fw-bolder text-gray-800 fs-7">{{ trim($data->dealer_sj->kota) }}</div>
                            </div>
                            <div class="mb-6">
                                <div class="fw-bold text-gray-600 fs-7">Keterangan 1:</div>
                                <div class="fw-bolder text-gray-800 fs-7">
                                    @if(trim($data->dealer_sj->keterangan1) == '') - @else {{ trim($data->dealer_sj->keterangan1) }} @endif
                                </div>
                            </div>
                            <div class="mb-6">
                                <div class="fw-bold text-gray-600 fs-7">Keterangan 2:</div>
                                <div class="fw-bolder text-gray-800 fs-7">
                                    @if(trim($data->dealer_sj->keterangan2) == '') - @else {{ trim($data->dealer_sj->keterangan2) }} @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if(strtoupper(trim($device)) == 'MOBILE')
        <div class="card card-flush mt-8">
            <div class="card-header align-items-center border-0 mt-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Detail Faktur</span>
                    <span class="text-muted fw-bold fs-7">Daftar detail part number</span>
                </h3>
            </div>
            <div class="card-body">
                @foreach($data->detail_faktur as $data_detail)
                <div class="d-flex mb-7">
                    <span class="symbol symbol-100px me-5">
                        <span class="symbol-label" style="background-image:url({{ $data_detail->image_part }}), url({{ URL::asset('assets/images/background/part_image_not_found.png') }});"></span>
                    </span>
                    <div class="flex-grow-1">
                        <div class="row">
                            <span class="fs-6 text-dark fw-bolder">{{ trim($data_detail->part_number) }}</span>
                            <span class="fs-7 text-muted fw-bold ">{{ trim($data_detail->nama_part) }}</span>
                            <span class="fs-5 text-dark fw-bolder mt-4">Rp. {{ number_format($data_detail->harga) }}</span>
                            @if((double)$data_detail->harga != (double)$data_detail->het)
                            <div class="d-flex align-items-center">
                                @if((double)$data_detail->disc_detail > 0)
                                <div class="badge badge-light-danger fw-bolder fs-7 p-1">{{ number_format($data_detail->disc_detail, 2) }}%</div>
                                @endif
                                <del class="text-gray-600 fw-bolder fs-7 ms-2">Rp. {{ number_format($data_detail->het) }}</del>
                            </div>
                            @endif
                            <div class="row mt-4">
                                <div class="col-6">
                                    <span class="text-muted d-block fw-bold">Order:</span>
                                    <div class="align-items-center">
                                        <span class="fs-6 text-gray-800 fw-bolder">{{ number_format($data_detail->jml_order) }}</span>
                                        <span class="fs-7 text-gray-600 fw-bolder ms-2">PCS</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted d-block fw-bold">Terlayani:</span>
                                    <div class="align-items-center">
                                        <span class="fs-6 @if((double)$data_detail->jml_order > (double)$data_detail->jml_jual) text-danger @else text-gray-800 @endif fw-bolder">{{ number_format($data_detail->jml_jual) }}</span>
                                        <span class="fs-7 text-gray-600 fw-bolder ms-2">PCS</span>
                                    </div>
                                </div>
                            </div>
                            <span class="fs-5 text-danger fw-boldest mt-4">Rp. {{ number_format($data_detail->total_detail) }}</span>
                        </div>
                    </div>
                </div>
                <div class="separator my-10"></div>
                @endforeach
                <div class="row">
                    <div class="d-flex flex-stack mb-3">
                        <div class="fw-boldest pe-10 text-gray-600 fs-7">SUBTOTAL:</div>
                        <div class="text-end fw-bolder fs-6 text-gray-800">Rp. {{ number_format($data->sub_total) }}</div>
                    </div>
                    <div class="d-flex flex-stack mb-3">
                        <div class="fw-boldest pe-10 text-gray-600 fs-7">DISCOUNT (%):</div>
                        <div class="text-end fw-bolder fs-6 text-gray-800">{{ number_format($data->disc_header, 2) }} % / Rp. {{ number_format($data->nominal_disc_header) }}</div>
                    </div>
                    <div class="d-flex flex-stack mb-3">
                        <div class="fw-boldest pe-10 text-gray-600 fs-7">DISCOUNT (Rp.):</div>
                        <div class="text-end fw-bolder fs-6 text-gray-800">Rp. {{ number_format($data->disc_rupiah) }}</div>
                    </div>
                    <div class="d-flex flex-stack mb-3">
                        <div class="fw-boldest pe-10 text-gray-600 fs-7">TOTAL:</div>
                        <div class="text-end fw-boldest fs-6 text-danger">Rp. {{ number_format($data->grand_total) }}</div>
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
