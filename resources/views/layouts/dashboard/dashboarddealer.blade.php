@extends('layouts.main.index')
@section('title','Home')
@section('subtitle','Dashboard')
@section('container')
    <form id="formDashboardDealer" action="{{ route('dashboard.dashboard-dealer') }}" method="get">
        <div class="card card-flush">
            <div class="card-header align-items-center border-0 mt-4 mb-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Dealer</span>
                    <span class="text-muted fw-boldest fs-7">Dashboard Dealer
                        @if(isset($kode_dealer))
                        {{ strtoupper($kode_dealer) }}
                        @endif
                        @if($month == 1) Januari
                        @elseif($month == 2) Februari
                        @elseif($month == 3) Maret
                        @elseif($month == 4) April
                        @elseif($month == 5) Mei
                        @elseif($month == 6) Juni
                        @elseif($month == 7) Juli
                        @elseif($month == 8) Agustus
                        @elseif($month == 9) September
                        @elseif($month == 10) Oktober
                        @elseif($month == 11) November
                        @elseif($month == 12) Desember
                        @endif {{ $year }}
                    </span>
                </h3>
                <div class="card-toolbar">
                    <button class="btn btn-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="bi bi-funnel-fill fs-4 me-2"></i>Filter
                    </button>

                    <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_6244763d95a3a" style="">
                        <div class="px-7 py-5">
                            <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                        </div>
                        <div class="separator border-gray-200"></div>
                        <div class="px-7 py-5">
                            <div class="mb-5">
                                <label class="form-label required">Bulan:</label>
                                <select id="selectMonth" name="month" class="form-select" data-control="select2" data-hide-search="true">
                                    <option value="1" @if($month == 1) {{"selected"}} @endif>Januari</option>
                                    <option value="2" @if($month == 2) {{"selected"}} @endif>Februari</option>
                                    <option value="3" @if($month == 3) {{"selected"}} @endif>Maret</option>
                                    <option value="4" @if($month == 4) {{"selected"}} @endif>April</option>
                                    <option value="5" @if($month == 5) {{"selected"}} @endif>Mei</option>
                                    <option value="6" @if($month == 6) {{"selected"}} @endif>Juni</option>
                                    <option value="7" @if($month == 7) {{"selected"}} @endif>Juli</option>
                                    <option value="8" @if($month == 8) {{"selected"}} @endif>Agustus</option>
                                    <option value="9" @if($month == 9) {{"selected"}} @endif>September</option>
                                    <option value="10" @if($month == 10) {{"selected"}} @endif>Oktober</option>
                                    <option value="11" @if($month == 11) {{"selected"}} @endif>November</option>
                                    <option value="12" @if($month == 12) {{"selected"}} @endif>Desember</option>
                                </select>
                            </div>
                            <div class="mb-5">
                                <label class="form-label required">Tahun:</label>
                                <input type="text" id="inputYear" name="year" class="form-control" placeholder="Tahun" autocomplete="off"
                                    @if(isset($year)) value="{{ $year }}" @else value="{{ old('year') }}"@endif>
                            </div>
                            <div class="mb-5">
                                <label class="form-label">Dealer:</label>
                                <input type="text" id="inputKodeDealer" name="kode_dealer" class="form-control @if(strtoupper(trim($role_id)) == 'D_H3') form-control-solid @endif" placeholder="Semua Dealer" autocomplete="off"
                                    @if(isset($kode_dealer)) value="{{ $kode_dealer }}" @else value="{{ old('kode_dealer') }}"@endif
                                    @if(strtoupper(trim($role_id)) == 'D_H3') readonly @endif>
                            </div>
                            <div class="mb-5">
                                <div class="d-flex align-items-center">
                                    <button id="btnFilterProses" class="btn btn-sm btn-primary me-2" type="submit">Terapkan</button>
                                    <a id="btnFilterReset" href="{{ route('dashboard.dashboard-dealer') }}" class="btn btn-sm btn-danger me-2" role="button">Reset Filter</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4 mt-4">
                <div class="card card-flush"
                    style="background-color: #f1416c;background-image:url('{{ asset('assets/media/svg/shapes/wave-bg-dark.svg') }}');background-size: 500px;">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <div class="d-flex align-items-center">
                                <span class="fs-6 fw-bolder text-white me-1 align-self-start">Rp.</span>
                                <span class="fs-2hx fw-bolder text-white me-2 lh-1 ls-n2">@if(isset($sisa_piutang)) {{ number_format($sisa_piutang) }} @endif</span>
                            </div>
                            <span class="text-white pt-1 fw-bolder fs-6">Grand Total</span>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-end pt-0">
                        <div class="d-flex align-items-center flex-column mt-3 w-100">
                            <div class="d-flex justify-content-between fw-bolder fs-6 text-white w-100 mt-auto mb-2">
                                <span>Piutang Pembayaran</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mt-4">
                <div class="card card-flush"
                    style="background-color: #50cd89;background-image:url('{{ asset('assets/media/svg/shapes/wave-bg-dark.svg') }}');background-size: 500px;">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <div class="d-flex align-items-center">
                                <span class="fs-2hx fw-bolder text-white me-2 lh-1 ls-n2">@if(isset($poin_campaign)) {{ number_format($poin_campaign) }} @endif</span>
                                <span class="fs-6 fw-bolder text-white me-1 align-self-end ms-2">Poin</span>
                            </div>
                            <span class="text-white pt-1 fw-bolder fs-6">Grand Total</span>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-end pt-0">
                        <div class="d-flex align-items-center flex-column mt-3 w-100">
                            <div class="d-flex justify-content-between fw-bolder fs-6 text-white w-100 mt-auto mb-2">
                                <span>Poin Campaign</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mt-4">
                <div class="card card-flush"
                    style="background-color: #009ef7;background-image:url('{{ asset('assets/media/svg/shapes/wave-bg-dark.svg') }}');background-size: 500px;">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-infinity fs-2hx text-white"></i>
                            </div>
                            <span class="text-white pt-1 fw-bolder fs-6">Grand Total</span>
                        </div>
                    </div>

                    <div class="card-body d-flex align-items-end pt-0">
                        <div class="d-flex align-items-center flex-column mt-3 w-100">
                            <div class="d-flex justify-content-between fw-bolder fs-6 text-white w-100 mt-auto mb-2">
                                <span>Sisa Limit Piutang</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <div class="row gy-5 g-xl-10">
                <div class="col-xl-4 mb-xl-10">
                    <div class="card h-lg-100">
                        <div class="card-header p-6">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder text-gray-800">Pembayaran</span>
                                <span class="text-muted mt-1 fw-boldest fs-7">Daftar faktur belum terbayar</span>
                            </h3>
                        </div>
                        <div class="card-body pt-6">
                        @foreach ($detail_piutang as $data)
                           <div class="d-flex flex-stack">
                            <div class="d-flex align-items-center me-5">
                                <div class="symbol symbol-40px me-4">
                                    @if ($data->sisa_hari < 0)
                                    <span class="symbol-label bg-light-danger">
                                        <i class="bi bi-x-circle-fill fs-1 p-0 text-danger"></i>
                                    </span>
                                    @else
                                        @if ($data->sisa_hari == 0)
                                        <span class="symbol-label bg-light-primary">
                                            <i class="bi bi-stop-circle-fill fs-1 p-0 text-primary"></i>
                                        </span>
                                        @elseif ($data->sisa_hari > 0 && $data->sisa_hari <= 7)
                                        <span class="symbol-label bg-light-warning">
                                            <i class="bi bi-exclamation-circle-fill fs-1 p-0 text-warning"></i>
                                        </span>
                                        @else
                                        <span class="symbol-label bg-light-info">
                                            <i class="bi bi-clock-fill fs-1 p-0 text-info"></i>
                                        </span>
                                        @endif
                                    @endif
                                </div>
                                <div class="me-5">
                                    <a href="#" class="text-gray-800 fw-bolder text-hover-primary fs-6">{{ trim($data->nomor_faktur) }}</a>
                                    @if ($data->sisa_hari < 0)
                                    <span class="text-danger fw-bolder fs-7 d-block text-start ps-0">{{ trim($data->tanggal_jtp) }}</span>
                                    @else
                                        @if ($data->sisa_hari == 0)
                                        <span class="text-primary fw-bolder fs-7 d-block text-start ps-0">{{ trim($data->tanggal_jtp) }}</span>
                                        @elseif ($data->sisa_hari > 0 && $data->sisa_hari <= 7)
                                        <span class="text-warning fw-bolder fs-7 d-block text-start ps-0">{{ trim($data->tanggal_jtp) }}</span>
                                        @else
                                        <span class="text-info fw-bolder fs-7 d-block text-start ps-0">{{ trim($data->tanggal_jtp) }}</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div class="text-gray-400 fw-bolder fs-7 text-end">
                                <span class="text-gray-800 fw-bolder fs-6 d-block">Rp. {{ number_format($data->sisa_pembayaran) }}</span>
                                @if ($data->sisa_hari < 0)
                                <span class="badge badge-light-danger fs-8">Lewat Jatuh Tempo</span>
                                @else
                                    @if ($data->sisa_hari == 0)
                                    <span class="badge badge-light-primary fs-8">Jatuh Tempo</span>
                                    @elseif ($data->sisa_hari > 0 && $data->sisa_hari <= 7)
                                    <span class="badge badge-light-warning fs-8">Kurang {{ number_format($data->sisa_hari) }} Hari</span>
                                    @else
                                    <span class="badge badge-light-info fs-8">Kurang {{ number_format($data->sisa_hari) }} Hari</span>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="separator separator-dashed my-5"></div>
                        @endforeach
                        </div>
                        <div class="card-footer">
                            <div class="text-center">
                                <a href="{{ route('orders.pembayaran-faktur-belum-terbayar') }}" class="btn btn-danger">Lihat Pembayaran</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8 mb-5 mb-xl-10">
                    <div class="row g-lg-5 g-xl-10">
                        <div class="col-md-6 col-xl-6 mb-md-5 mb-xl-10">
                            <div class="card overflow-hidden h-md-50 mb-5 mb-xl-10">
                                <div class="card-body d-flex justify-content-between flex-column px-0 pb-0">
                                    <div class="mb-4 px-9">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="m-0">
                                                <div class="d-flex flex-center w-60px h-60px rounded-3 bg-light-success bg-opacity-90 mb-10">
                                                    <span class="svg-icon svg-icon-success svg-icon-3x">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <path opacity="0.3" d="M18.041 22.041C18.5932 22.041 19.041 21.5932 19.041 21.041C19.041 20.4887 18.5932 20.041 18.041 20.041C17.4887 20.041 17.041 20.4887 17.041 21.041C17.041 21.5932 17.4887 22.041 18.041 22.041Z" fill="currentColor"/>
                                                        <path opacity="0.3" d="M6.04095 22.041C6.59324 22.041 7.04095 21.5932 7.04095 21.041C7.04095 20.4887 6.59324 20.041 6.04095 20.041C5.48867 20.041 5.04095 20.4887 5.04095 21.041C5.04095 21.5932 5.48867 22.041 6.04095 22.041Z" fill="currentColor"/>
                                                        <path opacity="0.3" d="M7.04095 16.041L19.1409 15.1409C19.7409 15.1409 20.141 14.7409 20.341 14.1409L21.7409 8.34094C21.9409 7.64094 21.4409 7.04095 20.7409 7.04095H5.44095L7.04095 16.041Z" fill="currentColor"/>
                                                        <path d="M19.041 20.041H5.04096C4.74096 20.041 4.34095 19.841 4.14095 19.541C3.94095 19.241 3.94095 18.841 4.14095 18.541L6.04096 14.841L4.14095 4.64095L2.54096 3.84096C2.04096 3.64096 1.84095 3.04097 2.14095 2.54097C2.34095 2.04097 2.94096 1.84095 3.44096 2.14095L5.44096 3.14095C5.74096 3.24095 5.94096 3.54096 5.94096 3.84096L7.94096 14.841C7.94096 15.041 7.94095 15.241 7.84095 15.441L6.54096 18.041H19.041C19.641 18.041 20.041 18.441 20.041 19.041C20.041 19.641 19.641 20.041 19.041 20.041Z" fill="currentColor"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="row">
                                                <div class="d-flex align-items-center">
                                                    <span class="fs-6 fw-bolder text-gray-500 me-1 align-self-start">Rp.</span>
                                                    <span class="fs-2hx fw-bolder text-gray-800 me-2 lh-1 ls-n2">@if(isset($omset_penjualan)) {{ number_format($omset_penjualan) }} @endif</span>
                                                </div>
                                                <span class="fs-6 fw-bolder text-gray-600 mt-6">Omset Penjualan</span>
                                                <span class="fs-6 fw-bold text-gray-400">Per-Bulan</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card overflow-hidden h-md-50 mb-5 mb-xl-10">
                                <div class="card-body d-flex justify-content-between flex-column px-0 pb-0">
                                    <div class="mb-4 px-9">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="m-0">
                                                <div class="d-flex flex-center w-60px h-60px rounded-3 bg-light-primary bg-opacity-90 mb-10">
                                                    <span class="svg-icon svg-icon-primary svg-icon-3x">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path opacity="0.5" d="M12.8956 13.4982L10.7949 11.2651C10.2697 10.7068 9.38251 10.7068 8.85731 11.2651C8.37559 11.7772 8.37559 12.5757 8.85731 13.0878L12.7499 17.2257C13.1448 17.6455 13.8118 17.6455 14.2066 17.2257L21.1427 9.85252C21.6244 9.34044 21.6244 8.54191 21.1427 8.02984C20.6175 7.47154 19.7303 7.47154 19.2051 8.02984L14.061 13.4982C13.7451 13.834 13.2115 13.834 12.8956 13.4982Z" fill="currentColor"/>
                                                            <path d="M7.89557 13.4982L5.79487 11.2651C5.26967 10.7068 4.38251 10.7068 3.85731 11.2651C3.37559 11.7772 3.37559 12.5757 3.85731 13.0878L7.74989 17.2257C8.14476 17.6455 8.81176 17.6455 9.20663 17.2257L16.1427 9.85252C16.6244 9.34044 16.6244 8.54191 16.1427 8.02984C15.6175 7.47154 14.7303 7.47154 14.2051 8.02984L9.06096 13.4982C8.74506 13.834 8.21146 13.834 7.89557 13.4982Z" fill="currentColor"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="row">
                                                <div class="row">
                                                    <span class="mb-3">
                                                        <span class="fs-2qx fw-bolder text-gray-800 me-2 lh-1 ls-n2">@if(isset($terlayani)) {{ number_format($terlayani) }} @endif</span>
                                                        <span class="fs-3 fw-bolder text-gray-500 me-2 lh-1 ls-n2">TERLAYANI</span>
                                                    </span>
                                                    <span>
                                                        <span class="fs-2x fw-bolder text-gray-800 me-2 lh-1 ls-n2">@if(isset($order)) {{ number_format($order) }} @endif</span>
                                                        <span class="fs-3 fw-bolder text-gray-500 me-2 lh-1 ls-n2">ORDER</span>
                                                    </span>

                                                    <span class="fs-6 fw-bolder text-gray-600 mt-6">Terlayani VS Order</span>
                                                    <span class="fs-6 fw-bold text-gray-400">Per-Bulan</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-6 mb-md-5 mb-xl-10">
                            <div class="card overflow-hidden h-md-50 mb-5 mb-xl-10">
                                <div class="card-body d-flex justify-content-between flex-column px-0 pb-0">
                                    <div class="mb-4 px-9">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="m-0">
                                                <div class="d-flex flex-center w-60px h-60px rounded-3 bg-light-info bg-opacity-90 mb-10">
                                                    <span class="svg-icon svg-icon-info svg-icon-3x"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <path d="M20 8H16C15.4 8 15 8.4 15 9V16H10V17C10 17.6 10.4 18 11 18H16C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18H21C21.6 18 22 17.6 22 17V13L20 8Z" fill="currentColor"/>
                                                        <path opacity="0.3" d="M20 18C20 19.1 19.1 20 18 20C16.9 20 16 19.1 16 18C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18ZM15 4C15 3.4 14.6 3 14 3H3C2.4 3 2 3.4 2 4V13C2 13.6 2.4 14 3 14H15V4ZM6 16C4.9 16 4 16.9 4 18C4 19.1 4.9 20 6 20C7.1 20 8 19.1 8 18C8 16.9 7.1 16 6 16Z" fill="currentColor"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="row">
                                                <span>
                                                    <span class="fs-2hx fw-bolder text-gray-800 me-2 lh-1 ls-n2">@if(isset($order_on_process)) {{ number_format($order_on_process) }} @endif</span>
                                                    <span class="fs-2 fw-bolder text-gray-500 me-2 lh-1 ls-n2">PCS</span>
                                                </span>
                                                <span class="fs-6 fw-bolder text-gray-600 mt-6">On Order Process</span>
                                                <span class="fs-6 fw-bold text-gray-400">Grand Total</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card overflow-hidden h-md-50 mb-5 mb-xl-10">
                                <div class="card-body d-flex justify-content-between flex-column px-0 pb-0">
                                    <div class="mb-4 px-9">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="m-0">
                                                <div class="d-flex flex-center w-60px h-60px rounded-3 bg-light-danger bg-opacity-90 mb-10">
                                                    <span class="svg-icon svg-icon-danger svg-icon-2hx">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path opacity="0.5" d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z" fill="currentColor"/>
                                                            <path d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z" fill="currentColor"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="row">
                                                <span class="mb-3">
                                                    <span class="fs-2qx fw-bolder text-gray-800 me-2 lh-1 ls-n2">@if(isset($bo_pcs)) {{ number_format($bo_pcs) }} @endif</span>
                                                    <span class="fs-3 fw-bolder text-gray-500 me-2 lh-1 ls-n2">PCS</span>
                                                </span>
                                                <span>
                                                    <span class="fs-2x fw-bolder text-gray-800 me-2 lh-1 ls-n2">@if(isset($bo_item)) {{ number_format($bo_item) }} @endif</span>
                                                    <span class="fs-3 fw-bolder text-gray-500 me-2 lh-1 ls-n2">ITEM</span>
                                                </span>

                                                <span class="fs-6 fw-bolder text-gray-600 mt-6">Back Order</span>
                                                <span class="fs-6 fw-bold text-gray-400">Grand Total</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @push('scripts')
        <script src="{{ asset('assets/js/suma/dashboard/dashboarddealer.js') }}?v={{ time() }}"></script>
    @endpush
@endsection
