@extends('layouts.main.index')
@section('title','Orders')
@section('subtitle','Cart')
@section('container')
@foreach($faktur as $faktur)
<div class="card" style="page-break-before: always;">
    <div class="card-body p-lg-20">
        <div class="d-flex flex-column flex-xl-row">
            <div class="flex-lg-row-fluid me-xl-18 mb-10 mb-xl-0">
                <div class="mt-n1">
                    <div class="row mb-10">
                        <div class="col-6">
                            <img alt="Logo" src="{{ asset('assets/images/logo/bg_logo_suma.png') }}" class="h-60px">
                        </div>
                        <div class="col-6">
                            <div class="text-end fw-bolder fs-7 text-muted">
                                <div>{{ strtoupper(trim($faktur->company->nama_company)) }}</div>
                                <div>{{ strtoupper(trim($faktur->company->alamat_company)) }}, {{ strtoupper(trim($faktur->company->kota_company)) }}</div>
                                <div>TELP. {{ strtoupper(trim($faktur->company->telp_company)) }} / FAX. {{ strtoupper(trim($faktur->company->fax_company)) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="m-0">
                        <h4 class="fw-boldest text-gray-800 fs-2qx pe-5 mb-8">FAKTUR</h4>
                        <div class="row mb-8">
                            <div class="col-6">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Nomor Faktur:</div>
                                <div class="fw-bolder fs-6 text-gray-800">{{ strtoupper(trim($faktur->nomor_faktur)) }}</div>
                            </div>
                            <div class="col-6">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Tanggal Faktur:</div>
                                <div class="fw-bolder fs-6 text-gray-800">{{ date('j F Y', strtotime($faktur->tgl_faktur)) }}</div>
                            </div>
                        </div>
                        <div class="row mb-8">
                            <div class="col-6">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Jenis Order:</div>
                                <div class="fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap">
                                    <span class="pe-2">
                                        @if(strtoupper(trim($faktur->rh)) == 'H')
                                            HOTLINE
                                        @else
                                            REGULER
                                        @endif
                                    </span>
                                    <span class="fs-6 text-danger d-flex align-items-center">
                                        <span class="bullet bullet-dot bg-danger me-2"></span>{{ strtoupper(trim($faktur->rh)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">TPC:</div>
                                <div class="fw-bolder fs-6 text-gray-800">TPC-{{ trim($faktur->kode_tpc) }}</div>
                            </div>
                        </div>
                        <div class="row mb-8">
                            <div class="col-6">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Salesman:</div>
                                <div class="fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap">
                                    <span class="pe-2">{{ strtoupper(trim($faktur->salesman->nama_sales)) }}</span>
                                    <span class="fs-6 text-danger d-flex align-items-center">
                                        <span class="bullet bullet-dot bg-danger me-2"></span>{{ strtoupper(trim($faktur->salesman->kode_sales)) }}
                                    </span>
                                </div>
                                <div class="fw-bold fs-7 text-gray-600">{{ strtoupper(trim($faktur->company->alamat_company)) }}
                                <br>{{ strtoupper(trim($faktur->company->kota_company)) }}</div>
                            </div>
                            <div class="col-6">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Dealer:</div>
                                <div class="fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap">
                                    <span class="pe-2">{{ strtoupper(trim($faktur->dealer->nama_dealer)) }}</span>
                                    <span class="fs-6 text-danger d-flex align-items-center">
                                        <span class="bullet bullet-dot bg-danger me-2"></span>{{ strtoupper(trim($faktur->dealer->kode_dealer)) }}
                                    </span>
                                </div>
                                <div class="fw-bold fs-7 text-gray-600">{{ (strtoupper(trim($faktur->dealer->alamat_dealer)) == '') ? '-' : strtoupper(trim($faktur->dealer->alamat_dealer)) }}
                                <br>{{ (strtoupper(trim($faktur->dealer->kota_dealer)) == '') ? '-' : strtoupper(trim($faktur->dealer->kota_dealer)) }}</div>
                                @if(trim($faktur->dealer->npwp_dealer) != '' && trim($faktur->dealer->npwp_dealer) != '-')
                                    <div class="fw-bolder fs-7 text-gray-800 mt-2">(NPWP: {{ trim($faktur->dealer->npwp_dealer) }})</div>
                                @else
                                    @if(trim($faktur->dealer->ktp_dealer) != '' && trim($faktur->dealer->ktp_dealer) != '-')
                                        <div class="fw-bolder fs-7 text-gray-800 mt-2">(NIK: {{ trim($faktur->dealer->ktp_dealer) }})</div>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="row mb-8">
                            <div class="col-6">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Status BO:</div>
                                <div class="fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap">
                                    <span class="pe-2">
                                        @if(strtoupper(trim($faktur->bo)) == 'B')
                                            BACK ORDER
                                        @else
                                            TIDAK BO
                                        @endif
                                    </span>
                                    <span class="fs-6 text-danger d-flex align-items-center">
                                        <span class="bullet bullet-dot bg-danger me-2"></span>{{ strtoupper(trim($faktur->bo)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Umur Faktur:</div>
                                <div class="fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap">
                                    <span class="pe-2">{{ date('j F Y', strtotime($faktur->tanggal_jatuh_tempo)) }}</span>
                                    <span class="fs-6 text-danger d-flex align-items-center">
                                        <span class="bullet bullet-dot bg-danger me-2"></span>{{ number_format($faktur->umur_faktur) }}-Hari
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-8">
                            <div class="col-12">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Keterangan:</div>
                                <div class="fw-bolder fs-6 text-gray-800" style="text-align: justify">{{ (strtoupper(trim($faktur->keterangan)) == '') ? '-' : strtoupper(trim($faktur->keterangan)) }}</div>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="table-responsive border-bottom mb-9">
                                <table class="table align-middle table-row-dashed fs-7 mb-0">
                                    <thead>
                                        <tr class="border-bottom fs-7 fw-bolder text-muted">
                                            <th class="min-w-175px pb-2 text-muted">Part Number</th>
                                            <th class="min-w-70px text-end pb-2 text-muted">Order</th>
                                            <th class="min-w-70px text-end pb-2 text-muted">Terlayani</th>
                                            <th class="min-w-80px text-end pb-2 text-muted">Harga</th>
                                            <th class="min-w-80px text-end pb-2 text-muted">Disc(%)</th>
                                            <th class="min-w-100px text-end pb-2 text-muted">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-bold text-gray-600">
                                        @foreach($faktur->detail as $detail_faktur)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol symbol-45px me-5">
                                                        <img src="{{ $detail_faktur->image_part }}"
                                                            onerror="this.onerror=null; this.src='{{ asset('assets/images/background/part_image_not_found.png') }}'"
                                                            alt="{{ strtoupper(trim($detail_faktur->part_number)) }}">
                                                    </div>
                                                    <div class="d-flex justify-content-start flex-column">
                                                        <span class="text-dark fw-bolder">{{ strtoupper(trim($detail_faktur->part_number)) }}</span>
                                                        <span class="text-muted fw-bold">{{ strtoupper(trim($detail_faktur->nama_part)) }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end text-dark">{{ number_format($detail_faktur->jml_order) }}</td>
                                            <td class="text-end text-dark">{{ number_format($detail_faktur->jml_jual) }}</td>
                                            <td class="text-end text-dark">{{ number_format($detail_faktur->harga) }}</td>
                                            <td class="text-end text-dark">{{ number_format($detail_faktur->disc_detail, 2) }}</td>
                                            <td class="text-end text-dark">{{ number_format($detail_faktur->jumlah) }}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="4" class="fw-bolder text-end text-muted">Subtotal</td>
                                            <td colspan="2" class="text-end text-dark">{{ number_format($faktur->sub_total) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="fw-bolder text-end text-muted">Disc(%)</td>
                                            <td class="text-end text-dark">{{ number_format($faktur->disc_header, 2) }}</td>
                                            <td class="text-end text-dark">{{ number_format($faktur->disc_header_rupiah) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="fw-bolder text-end text-muted">Disc(Rp)</td>
                                            <td colspan="2" class="text-end text-dark">{{ number_format($faktur->disc_rupiah) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="fw-bolder text-end text-muted">Grand Total</td>
                                            <td colspan="2" class="text-end text-dark">{{ number_format($faktur->total) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="m-0">
                <div class="d-print-none border border-dashed border-gray-300 card-rounded h-lg-100 min-w-md-350px p-9 bg-lighten">
                    <h6 class="mb-8 fw-boldest text-primary">ORDER DETAILS</h6>
                    <div class="mb-6">
                        <div class="fw-bold text-gray-600 fs-7">Status:</div>
                        <span class="badge badge-success fs-5 mt-1">SUCCESS</span>
                    </div>
                    <div class="mb-20">
                        <div class="fw-bold text-gray-600 fs-7">Purchase Order:</div>
                        <div class="fw-bolder text-gray-800 fs-6">{{ strtoupper(trim($faktur->nomor_pof)) }}</div>
                    </div>

                    <h6 class="mb-6 fw-boldest text-primary">DEALER DETAILS</h6>
                    <div class="mb-6">
                        <div class="fw-bold text-gray-600 fs-7">Nama Dealer SJ:</div>
                        <div class="fw-bolder text-gray-800 fs-6">{{ (strtoupper(trim($faktur->dealer->nama_dealer_sj)) == '') ? '-' : strtoupper(trim($faktur->dealer->nama_dealer_sj)) }}</div>
                    </div>
                    <div class="mb-6">
                        <div class="fw-bold text-gray-600 fs-7">Alamat SJ:</div>
                        <div class="fw-bolder fs-6 text-gray-800">{{ (strtoupper(trim($faktur->dealer->alamat_dealer_sj)) == '') ? '-' : strtoupper(trim($faktur->dealer->alamat_dealer_sj)) }}</div>
                    </div>
                    <div class="mb-6">
                        <div class="fw-bold text-gray-600 fs-7">Kota SJ:</div>
                        <div class="fw-bolder fs-6 text-gray-800">{{ (strtoupper(trim($faktur->dealer->kota_dealer_sj)) == '') ? '-' : strtoupper(trim($faktur->dealer->kota_dealer_sj)) }}</div>
                    </div>
                    <div class="mb-6">
                        <div class="fw-bold text-gray-600 fs-7">Keterangan 1:</div>
                        <div class="fw-bolder fs-6 text-gray-800">{{ (strtoupper(trim($faktur->dealer->keterangan_dealer_sj1)) == '') ? '-' : strtoupper(trim($faktur->dealer->keterangan_dealer_sj1)) }}</div>
                    </div>
                    <div class="mb-6">
                        <div class="fw-bold text-gray-600 fs-7">Keterangan 2:</div>
                        <div class="fw-bolder fs-6 text-gray-800">{{ (strtoupper(trim($faktur->dealer->keterangan_dealer_sj2)) == '') ? '-' : strtoupper(trim($faktur->dealer->keterangan_dealer_sj2)) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
@endforeach
@foreach($pof as $pof)
<div class="card" style="page-break-before: always;">
    <div class="card-body p-lg-20">
        <div class="d-flex flex-column flex-xl-row">
            <div class="flex-lg-row-fluid me-xl-18 mb-10 mb-xl-0">
                <div class="mt-n1">
                    <div class="row mb-10">
                        <div class="col-6">
                            <img alt="Logo" src="{{ asset('assets/images/logo/bg_logo_suma.png') }}" class="h-60px">
                        </div>
                        <div class="col-6">
                            <div class="text-end fw-bolder fs-7 text-muted">
                                <div>{{ strtoupper(trim($pof->company->nama_company)) }}</div>
                                <div>{{ strtoupper(trim($pof->company->alamat_company)) }}, {{ strtoupper(trim($pof->company->kota_company)) }}</div>
                                <div>TELP. {{ strtoupper(trim($pof->company->telp_company)) }} / FAX. {{ strtoupper(trim($pof->company->fax_company)) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="m-0">
                        <h4 class="fw-boldest text-gray-800 fs-2qx pe-5 mb-8">PURCHASE ORDER</h4>
                        <div class="row mb-8">
                            <div class="col-6">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Nomor Purchase Order:</div>
                                <div class="fw-bolder fs-6 text-gray-800">{{ strtoupper(trim($pof->nomor_pof)) }}</div>
                            </div>
                            <div class="col-6">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Tanggal:</div>
                                <div class="fw-bolder fs-6 text-gray-800">{{ date('j F Y', strtotime($pof->tanggal_pof)) }}</div>
                            </div>
                        </div>
                        <div class="row mb-8">
                            <div class="col-6">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">TPC:</div>
                                <div class="fw-bolder fs-6 text-gray-800">TPC-{{ trim($pof->kode_tpc) }}</div>
                            </div>
                            <div class="col-6">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Umur Faktur:</div>
                                <div class="fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap">
                                    <span class="pe-2">{{ date('j F Y', strtotime($pof->tanggal_akhir_pof)) }}</span>
                                    <span class="fs-6 text-danger d-flex align-items-center">
                                        <span class="bullet bullet-dot bg-danger me-2"></span>{{ number_format($pof->umur_pof) }}-Hari
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-8">
                            <div class="col-6">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Salesman:</div>
                                <div class="fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap">
                                    <span class="pe-2">{{ strtoupper(trim($pof->salesman->nama_sales)) }}</span>
                                    <span class="fs-6 text-danger d-flex align-items-center">
                                        <span class="bullet bullet-dot bg-danger me-2"></span>{{ strtoupper(trim($pof->salesman->kode_sales)) }}
                                    </span>
                                </div>
                                <div class="fw-bold fs-7 text-gray-600">{{ strtoupper(trim($pof->company->alamat_company)) }}
                                <br>{{ strtoupper(trim($pof->company->kota_company)) }}</div>
                            </div>
                            <div class="col-6">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Dealer:</div>
                                <div class="fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap">
                                    <span class="pe-2">{{ strtoupper(trim($pof->dealer->nama_dealer)) }}</span>
                                    <span class="fs-6 text-danger d-flex align-items-center">
                                        <span class="bullet bullet-dot bg-danger me-2"></span>{{ strtoupper(trim($pof->dealer->kode_dealer)) }}
                                    </span>
                                </div>
                                <div class="fw-bold fs-7 text-gray-600">{{ (strtoupper(trim($pof->dealer->alamat_dealer)) == '') ? '-' : strtoupper(trim($pof->dealer->alamat_dealer)) }}
                                <br>{{ (strtoupper(trim($pof->dealer->kota_dealer)) == '') ? '-' : strtoupper(trim($pof->dealer->kota_dealer)) }}</div>
                                @if(trim($pof->dealer->npwp_dealer) != '' && trim($pof->dealer->npwp_dealer) != '-')
                                    <div class="fw-bolder fs-7 text-gray-800 mt-2">(NPWP: {{ trim($pof->dealer->npwp_dealer) }})</div>
                                @else
                                    @if(trim($pof->dealer->ktp_dealer) != '' && trim($pof->dealer->ktp_dealer) != '-')
                                        <div class="fw-bolder fs-7 text-gray-800 mt-2">(NIK: {{ trim($pof->dealer->ktp_dealer) }})</div>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="row mb-8">
                            <div class="col-6">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Status BO:</div>
                                <div class="fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap">
                                    <span class="pe-2">
                                        @if(strtoupper(trim($pof->bo)) == 'B')
                                            BACK ORDER
                                        @else
                                            TIDAK BO
                                        @endif
                                    </span>
                                    <span class="fs-6 text-danger d-flex align-items-center">
                                        <span class="bullet bullet-dot bg-danger me-2"></span>{{ strtoupper(trim($pof->bo)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Keterangan:</div>
                                <div class="fw-bolder fs-6 text-gray-800" style="text-align: justify;">{{ (strtoupper(trim($pof->keterangan)) == '') ? '-' : strtoupper(trim($pof->keterangan)) }}</div>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="table-responsive border-bottom mb-9">
                                <table class="table align-middle table-row-dashed fs-7 mb-0">
                                    <thead>
                                        <tr class="border-bottom fs-7 fw-bolder text-muted">
                                            <th class="min-w-175px pb-2 text-muted">Part Number</th>
                                            <th class="min-w-70px text-end pb-2 text-muted">Order</th>
                                            <th class="min-w-80px text-end pb-2 text-muted">Harga</th>
                                            <th class="min-w-80px text-end pb-2 text-muted">Disc(%)</th>
                                            <th class="min-w-100px text-end pb-2 text-muted">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-bold text-gray-600">
                                        @foreach ($pof->detail as $pof_detail)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol symbol-45px me-5">
                                                        <img src="{{ trim($pof_detail->image_part) }}"
                                                            onerror="this.onerror=null; this.src='{{ asset('assets/images/background/part_image_not_found.png') }}'"
                                                            alt="{{ strtoupper(trim($pof_detail->part_number)) }}">
                                                    </div>
                                                    <div class="d-flex justify-content-start flex-column">
                                                        <span class="text-dark fw-bolder">{{ strtoupper(trim($pof_detail->part_number)) }}</span>
                                                        <span class="text-muted fw-bold">{{ strtoupper(trim($pof_detail->nama_part)) }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end text-dark">{{ number_format($pof_detail->jml_order) }}</td>
                                            <td class="text-end text-dark">{{ number_format($pof_detail->harga) }}</td>
                                            <td class="text-end text-dark">{{ number_format($pof_detail->disc_detail, 2) }}</td>
                                            <td class="text-end text-dark">{{ number_format($pof_detail->jumlah) }}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="3" class="fw-bolder text-end text-muted">Subtotal</td>
                                            <td colspan="2" class="text-end text-dark">{{ number_format($pof->sub_total) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="fw-bolder text-end text-muted">Disc(%)</td>
                                            <td class="text-end text-dark">{{ number_format($pof->disc_header, 2) }}</td>
                                            <td class="text-end text-dark">{{ number_format($pof->disc_header_rupiah) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="fw-bolder text-end text-muted">Grand Total</td>
                                            <td colspan="2" class="text-end text-dark">{{ number_format($pof->total) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="m-0">
                <div class="d-print-none border border-dashed border-gray-300 card-rounded h-lg-100 min-w-md-350px p-9 bg-lighten">
                    <h6 class="mb-8 fw-boldest text-primary">ORDER DETAILS</h6>
                    <div class="mb-6">
                        <div class="fw-bold text-gray-600 fs-7">Status:</div>
                        <span class="badge badge-success fs-5 mt-1">SUCCESS</span>
                    </div>
                    <div class="mb-20">
                        <div class="fw-bold text-gray-600 fs-7">Cart Order:</div>
                        <div class="fw-bolder text-gray-800 fs-6">{{ strtoupper(trim($pof->kode_cart)) }}</div>
                    </div>
                    <h6 class="mb-6 fw-boldest text-primary">DEALER DETAILS</h6>
                    <div class="mb-6">
                        <div class="fw-bold text-gray-600 fs-7">Nama Dealer SJ:</div>
                        <div class="fw-bolder text-gray-800 fs-6">{{ (strtoupper(trim($pof->dealer->nama_dealer_sj)) == '') ? '-' : strtoupper(trim($pof->dealer->nama_dealer_sj)) }}</div>
                    </div>
                    <div class="mb-6">
                        <div class="fw-bold text-gray-600 fs-7">Alamat SJ:</div>
                        <div class="fw-bolder fs-6 text-gray-800">{{ (strtoupper(trim($pof->dealer->alamat_dealer_sj)) == '') ? '-' : strtoupper(trim($pof->dealer->alamat_dealer_sj)) }}</div>
                    </div>
                    <div class="mb-6">
                        <div class="fw-bold text-gray-600 fs-7">Kota SJ:</div>
                        <div class="fw-bolder fs-6 text-gray-800">{{ (strtoupper(trim($pof->dealer->kota_dealer_sj)) == '') ? '-' : strtoupper(trim($pof->dealer->kota_dealer_sj)) }}</div>
                    </div>
                    <div class="mb-6">
                        <div class="fw-bold text-gray-600 fs-7">Keterangan 1:</div>
                        <div class="fw-bolder fs-6 text-gray-800">{{ (strtoupper(trim($pof->dealer->keterangan_dealer_sj1)) == '') ? '-' : strtoupper(trim($pof->dealer->keterangan_dealer_sj1)) }}</div>
                    </div>
                    <div class="mb-6">
                        <div class="fw-bold text-gray-600 fs-7">Keterangan 2:</div>
                        <div class="fw-bolder fs-6 text-gray-800">{{ (strtoupper(trim($pof->dealer->keterangan_dealer_sj2)) == '') ? '-' : strtoupper(trim($pof->dealer->keterangan_dealer_sj2)) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
@endforeach
@foreach ($bo as $bo)
<div class="card" style="page-break-before: always;">
    <div class="card-body p-lg-20">
        <div class="d-flex flex-column flex-xl-row">
            <div class="flex-lg-row-fluid me-xl-18 mb-10 mb-xl-0">
                <div class="mt-n1">
                    <div class="row mb-10">
                        <div class="col-6">
                            <img alt="Logo" src="{{ asset('assets/images/logo/bg_logo_suma.png') }}" class="h-60px">
                        </div>
                        <div class="col-6">
                            <div class="text-end fw-bolder fs-7 text-muted">
                                <div>{{ strtoupper(trim($bo->company->nama_company)) }}</div>
                                <div>{{ strtoupper(trim($bo->company->alamat_company)) }}, {{ strtoupper(trim($bo->company->kota_company)) }}</div>
                                <div>TELP. {{ strtoupper(trim($bo->company->telp_company)) }} / FAX. {{ strtoupper(trim($bo->company->fax_company)) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="m-0">
                        <h4 class="fw-boldest text-gray-800 fs-2qx pe-5 mb-8">BACK ORDER</h4>
                        <div class="row mb-8">
                            <div class="col-6">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Kode Cart:</div>
                                <div class="fw-bolder fs-6 text-gray-800">{{ strtoupper(trim($bo->kode_cart)) }}</div>
                            </div>
                            <div class="col-6">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Tanggal:</div>
                                <div class="fw-bolder fs-6 text-gray-800">{{ date('j F Y', strtotime($bo->tanggal_cart)) }}</div>
                            </div>
                        </div>
                        <div class="row mb-8">
                            <div class="col-6">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Salesman:</div>
                                <div class="fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap">
                                    <span class="pe-2">{{ strtoupper(trim($bo->salesman->nama_sales)) }}</span>
                                    <span class="fs-6 text-danger d-flex align-items-center">
                                        <span class="bullet bullet-dot bg-danger me-2"></span>{{ strtoupper(trim($bo->salesman->kode_sales)) }}
                                    </span>
                                </div>
                                <div class="fw-bold fs-7 text-gray-600">{{ strtoupper(trim($bo->company->alamat_company)) }}
                                <br>{{ strtoupper(trim($bo->company->kota_company)) }}</div>
                            </div>
                            <div class="col-6">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Dealer:</div>
                                <div class="fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap">
                                    <span class="pe-2">{{ strtoupper(trim($bo->dealer->nama_dealer)) }}</span>
                                    <span class="fs-6 text-danger d-flex align-items-center">
                                        <span class="bullet bullet-dot bg-danger me-2"></span>{{ strtoupper(trim($bo->dealer->kode_dealer)) }}
                                    </span>
                                </div>
                                <div class="fw-bold fs-7 text-gray-600">{{ (strtoupper(trim($bo->dealer->alamat_dealer)) == '') ? '-' : strtoupper(trim($bo->dealer->alamat_dealer)) }}
                                <br>{{ (strtoupper(trim($bo->dealer->kota_dealer)) == '') ? '-' : strtoupper(trim($bo->dealer->kota_dealer)) }}</div>
                                @if(trim($bo->dealer->npwp_dealer) != '' && trim($bo->dealer->npwp_dealer) != '-')
                                    <div class="fw-bolder fs-7 text-gray-800 mt-2">(NPWP: {{ trim($bo->dealer->npwp_dealer) }})</div>
                                @else
                                    @if(trim($bo->dealer->ktp_dealer) != '' && trim($bo->dealer->ktp_dealer) != '-')
                                        <div class="fw-bolder fs-7 text-gray-800 mt-2">(NIK: {{ trim($bo->dealer->ktp_dealer) }})</div>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="row mb-8">
                            <div class="col-6">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Status BO:</div>
                                <div class="fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap">
                                    <span class="pe-2">
                                        @if(strtoupper(trim($bo->bo)) == 'B')
                                            BACK ORDER
                                        @else
                                            TIDAK BO
                                        @endif
                                    </span>
                                    <span class="fs-6 text-danger d-flex align-items-center">
                                        <span class="bullet bullet-dot bg-danger me-2"></span>{{ strtoupper(trim($bo->bo)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Keterangan:</div>
                                <div class="fw-bolder fs-6 text-gray-800" style="text-align: justify;">{{ (strtoupper(trim($bo->keterangan)) == '') ? '-' : strtoupper(trim($bo->keterangan)) }}</div>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="table-responsive border-bottom mb-9">
                                <table class="table align-middle table-row-dashed fs-7 mb-0">
                                    <thead>
                                        <tr class="border-bottom fs-7 fw-bolder text-muted">
                                            <th class="min-w-175px pb-2 text-muted">Part Number</th>
                                            <th class="min-w-70px text-end pb-2 text-muted">Qty</th>
                                            <th class="min-w-80px text-end pb-2 text-muted">Harga</th>
                                            <th class="min-w-80px text-end pb-2 text-muted">Disc 1</th>
                                            <th class="min-w-80px text-end pb-2 text-muted">Disc 2</th>
                                            <th class="min-w-100px text-end pb-2 text-muted">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-bold text-gray-600">
                                        @foreach($bo->detail as $detail_bo)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol symbol-45px me-5">
                                                        <img src="{{ trim($detail_bo->image_part) }}"
                                                            onerror="this.onerror=null; this.src='{{ asset('assets/images/background/part_image_not_found.png') }}'"
                                                            alt="{{ strtoupper(trim($detail_bo->part_number)) }}">
                                                    </div>
                                                    <div class="d-flex justify-content-start flex-column">
                                                        <span class="text-dark fw-bolder">{{ strtoupper(trim($detail_bo->part_number)) }}</span>
                                                        <span class="text-muted fw-bold">{{ strtoupper(trim($detail_bo->nama_part)) }}</span>

                                                        <div class="d-flex align-items-center mt-1">
                                                            <span class="badge badge-light-primary me-2">TPC-{{ strtoupper(trim($detail_bo->kode_tpc)) }}</span>
                                                            <span class="badge badge-light-info me-2">{{ number_format($detail_bo->umur_faktur) }}-Hari</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end text-dark">{{ number_format($detail_bo->jml_order) }}</td>
                                            <td class="text-end text-dark">{{ number_format($detail_bo->harga) }}</td>
                                            <td class="text-end text-dark">{{ number_format($detail_bo->disc1, 2) }}</td>
                                            <td class="text-end text-dark">{{ number_format($detail_bo->disc2, 2) }}</td>
                                            <td class="text-end text-dark">{{ number_format($detail_bo->jumlah) }}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="5" class="text-muted fw-bolder text-end">Grand Total</td>
                                            <td colspan="2" class="text-end text-dark">{{ number_format($bo->total) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="m-0">
                <div class="d-print-none border border-dashed border-gray-300 card-rounded h-lg-100 min-w-md-350px p-9 bg-lighten">
                    <h6 class="mb-8 fw-boldest text-primary">ORDER DETAILS</h6>
                    <div class="mb-20">
                        <div class="fw-bold text-gray-600 fs-7 mb-1">Status:</div>
                        <span class="badge badge-danger fs-5">BACK ORDER</span>
                    </div>
                    <h6 class="mb-6 fw-boldest text-primary">DEALER DETAILS</h6>
                    <div class="mb-6">
                        <div class="fw-bold text-gray-600 fs-7">Nama Dealer SJ:</div>
                        <div class="fw-bolder text-gray-800 fs-6">{{ (strtoupper(trim($bo->dealer->nama_dealer_sj)) == '') ? '-' : strtoupper(trim($bo->dealer->nama_dealer_sj)) }}</div>
                    </div>
                    <div class="mb-6">
                        <div class="fw-bold text-gray-600 fs-7">Alamat SJ:</div>
                        <div class="fw-bolder fs-6 text-gray-800">{{ (strtoupper(trim($bo->dealer->alamat_dealer_sj)) == '') ? '-' : strtoupper(trim($bo->dealer->alamat_dealer_sj)) }}</div>
                    </div>
                    <div class="mb-6">
                        <div class="fw-bold text-gray-600 fs-7">Kota SJ:</div>
                        <div class="fw-bolder fs-6 text-gray-800">{{ (strtoupper(trim($bo->dealer->kota_dealer_sj)) == '') ? '-' : strtoupper(trim($bo->dealer->kota_dealer_sj)) }}</div>
                    </div>
                    <div class="mb-6">
                        <div class="fw-bold text-gray-600 fs-7">Keterangan 1:</div>
                        <div class="fw-bolder fs-6 text-gray-800">{{ (strtoupper(trim($bo->dealer->keterangan_dealer_sj1)) == '') ? '-' : strtoupper(trim($bo->dealer->keterangan_dealer_sj1)) }}</div>
                    </div>
                    <div class="mb-6">
                        <div class="fw-bold text-gray-600 fs-7">Keterangan 2:</div>
                        <div class="fw-bolder fs-6 text-gray-800">{{ (strtoupper(trim($bo->dealer->keterangan_dealer_sj2)) == '') ? '-' : strtoupper(trim($bo->dealer->keterangan_dealer_sj2)) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
@endforeach
@push('scripts')
@endpush
@endsection
