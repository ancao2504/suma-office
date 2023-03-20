@extends('layouts.main.index')
@section('title', 'Online')
@section('subtitle', 'Update stok shopee detail')
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
@endpush
@section('container')

    <div class="tab-content">
        {{-- @if (\Agent::isDesktop()) --}}
        <div id="view_table" class="tab-pane fade active show">
            <div class="card card-flush">
                <!--start::container-->
                <div class="card-header align-items-center border-0 mt-4">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="fw-bolder mb-2 text-dark">Pemindahan Antar Lokasi </span>
                        <span class="text-muted fw-bold fs-7">Form pemindahan antar lokasi Shopee </span>
                    </h3>
                    <div class="card-toolbar">
                        {{-- <img src=" {{ asset('assets/images/logo/shopee_lg.png') }} " class="h-75px" /> --}}
                    </div>
                </div>
                <div class="card-body">
                    <div id="daftar_table" data-no=" {{ trim($filter_header->nomor_dokumen) }} ">
                        <div class="row mb-3 table_delete">
                            <div class="col-6">
                                <div class="fw-bolder text-gray-400">Nomor Dokumen : </div>
                                <div class="fs-3 fw-bolder text-gray-800"> {{ $data_header->nomor_dokumen }} </div>
                            </div>
                            <div class="col-6">
                                <div class="fw-bolder text-gray-400">Tanggal : </div>
                                <div class="fs-5 fw-bolder text-gray-800">
                                    {{ date('d F Y', strtotime($data_header->tanggal)) }} </div>
                            </div>
                            <div class="col-6 mt-5">
                                <div class="fw-bolder text-gray-400">Dari : </div>
                                <div class="fs-4 fw-bolder text-gray-800">
                                    {{ $data_header->lokasi_awal->kode_lokasi }} -
                                    {{ $data_header->lokasi_awal->nama_lokasi }} </div>
                                <div class="fs-6 fw-bolder text-gray-800"> {{ $data_header->lokasi_awal->alamat->alamat1 }}
                                    {{ $data_header->lokasi_awal->alamat->alamat2 }} </div>
                                <div class="fs-6 fw-bolder text-gray-800"> {{ $data_header->lokasi_awal->kota }}
                                </div>
                            </div>
                            <div class="col-6 mt-5">
                                <div class="fw-bolder text-gray-400">Ke : </div>
                                <div class="fs-4 fw-bolder text-gray-800">
                                    {{ $data_header->lokasi_tujuan->kode_lokasi }} -
                                    {{ $data_header->lokasi_tujuan->nama_lokasi }} </div>
                                <div class="fs-6 fw-bolder text-gray-800">
                                    {{ $data_header->lokasi_tujuan->alamat->alamat1 }}
                                    {{ $data_header->lokasi_tujuan->alamat->alamat2 }} </div>
                                <div class="fs-6 fw-bolder text-gray-800"> {{ $data_header->lokasi_tujuan->kota }}
                                </div>
                            </div>
                            <div class="col-6 mt-5">
                                <div class="fw-bolder text-gray-400">Keterangan : </div>
                                <div class="fs-6 fw-bolder text-gray-800">
                                    {{ !empty($data_header->keterangan) ? $data_header->keterangan : '-' }} </div>
                            </div>
                            <div class="col-6 mt-5">
                                <div class="fw-bolder text-gray-400">User : </div>
                                <div class="fs-6 fw-bolder text-gray-800">
                                    {{ !empty($data_header->usertime) ? explode('=', $data_header->usertime)[2] : '-' }}
                                </div>
                            </div>
                            <div class="col-12 mt-5">
                                <div class="fw-bolder text-gray-400">Status : </div>
                                <span
                                    class="fs-8 fw-boldest badge badge-light-{{ $data_header->status->cetak == 1 ? 'success' : 'danger' }}  me-2">
                                    <i
                                        class="fa fa-check text-{{ $data_header->status->cetak == 1 ? 'success' : 'danger' }}  me-2">
                                    </i>CETAK
                                </span>
                                <span
                                    class="fs-8 fw-boldest badge badge-light-{{ $data_header->status->in == 1 ? 'success' : 'danger' }}  me-2">
                                    <i
                                        class="fa fa-check text-{{ $data_header->status->in == 1 ? 'success' : 'danger' }}  me-2">
                                    </i>IN
                                </span>
                                <span
                                    class="fs-8 fw-boldest badge badge-light-{{ $data_header->status->sj == 1 ? 'success' : 'danger' }}  me-2">
                                    <i
                                        class="fa fa-check text-{{ $data_header->status->sj == 1 ? 'success' : 'danger' }}  me-2">
                                    </i>SJ
                                </span>
                                <span
                                    class="fs-8 fw-boldest badge badge-light-{{ !empty($data_header->status->validasi) && $data_header->status->validasi == 1 ? 'success' : 'danger' }}  me-2">
                                    <i
                                        class="fa fa-check text-{{ !empty($data_header->status->validasi) && $data_header->status->validasi == 1 ? 'success' : 'danger' }}  me-2">
                                    </i>VALIDASI
                                </span>
                                <span
                                    class="fs-8 fw-boldest badge badge-light-{{ $data_header->status->mp_header == 1 ? 'success' : 'danger' }}  me-2">
                                    <i
                                        class="fa fa-check text-{{ $data_header->status->mp_header == 1 ? 'success' : 'danger' }}  me-2">
                                    </i>MARKETPLACE
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive table_delete">
                        <table class="table table-row-dashed table-row-gray-300 align-middle">
                            <thead class="border">
                                <tr class="fs-8 fw-bolder text-muted">
                                    <th rowspan="3" class="w-30px text-center">No </th>
                                    <th rowspan="3" class="w-30px text-center">Kode Part </th>
                                    <th rowspan="3" class="w-30px text-center">Status </th>
                                    <th rowspan="3" class="w-30px text-center">Pindah </th>
                                    <th colspan="5" class="w-30px text-center">Stock </th>
                                    <th colspan="2" rowspan="2" class="w-30px text-center">Action </th>
                                </tr>
                                <tr class="fs-8 fw-bolder text-muted">
                                    <th colspan="3" class="w-30px text-center">Sekarang </th>
                                    <th colspan="2" class="w-30px text-center">Total Hasil Pindah </th>
                                </tr>
                                <tr class="fs-8 fw-bolder text-muted">
                                    <th class="w-30px text-center">SUMA </th>
                                    <th class="w-30px text-center">Shopee </th>
                                    <th class="w-30px text-center">Tokopedia </th>
                                    <th class="w-30px text-center">Shopee </th>
                                    <th class="w-30px text-center">Tokopedia </th>
                                    <th class="w-30px text-center">Marketplace </th>
                                    <th class="w-30px text-center">Internal </th>
                                </tr>
                            </thead>
                            <tbody class="border">
                                {{--  {{dd($data_header->detail)}}  --}}
                                @if (count($data_header->detail) < 0 || empty($data_header->detail))
                                    <tr class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                        <td class="fs-7 fw-bolder text-gray-800 text-center" colspan="6"> Data Tidak
                                            Ditemukan </td>
                                    </tr>
                                @else
                                    @php
                                        $no = 1;
                                        // dd($data_header->detail);
                                    @endphp
                                    @foreach ($data_header->detail as $data)
                                        @php
                                            $data_update = base64_encode(
                                                json_encode([
                                                    'nomor_dokumen' => $data_header->nomor_dokumen,
                                                    'kode_part' => $data->part_number,
                                                ]),
                                            );
                                        @endphp
                                        <tr>
                                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                                <span class="fs-7 fw-bolder text-dark"> {{ $no }} </span>
                                            </td>
                                            <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                                <span class="fs-7 fw-boldest text-gray-800 d-block">
                                                    {{ strtoupper(trim($data->part_number)) }} </span>
                                                <span class="fs-7 fw-bolder text-gray-700 d-block">
                                                    {{ trim($data->nama_part) }} </span>
                                                {{-- <span class="fs-8 fw-bolder text-gray-600 d-block"> {{ strtoupper(trim($data->product_id)) }}  </span> --}}

                                                <span class="fs-8 fw-boldest text-dark mt-5 d-block">MARKETPLACE : </span>
                                                <span class="fs-8 fw-boldest text-dark mt-3 d-block">Shopee : </span>
                                                <span class="fs-8 fw-bold text-gray-600">
                                                    SKU : <span class="fs-7 fw-bolder text-danger ms-2">
                                                        {{ strtoupper(trim(empty($data->marketplace->shopee->sku) ? '' : $data->marketplace->shopee->sku)) }}
                                                    </span>
                                                    <br>
                                                    ProductID : <span class="fs-7 fw-bolder text-danger ms-2">
                                                        {{ strtoupper(trim(empty($data->marketplace->shopee->product_id) ? '' : $data->marketplace->shopee->product_id)) }}
                                                    </span>
                                                    <br>
                                                    Status :
                                                    @if (strtoupper(trim(empty($data->marketplace->shopee->status) ? '' : $data->marketplace->shopee->status)) == 'BANNED')
                                                        <span class="fs-8 fw-boldest badge badge-danger ms-2">
                                                            {{ trim(empty($data->marketplace->shopee->status) ? '' : $data->marketplace->shopee->status) }}
                                                        </span>
                                                    @elseif(strtoupper(trim(empty($data->marketplace->shopee->status) ? '' : $data->marketplace->shopee->status)) == 'UNLIST')
                                                        <span class="fs-8 fw-boldest badge badge-warning ms-2">
                                                            {{ trim(empty($data->marketplace->shopee->status) ? '' : $data->marketplace->shopee->status) }}
                                                        </span>
                                                    @elseif(strtoupper(trim(empty($data->marketplace->shopee->status) ? '' : $data->marketplace->shopee->status)) == 'DELETED')
                                                        <span class="fs-8 fw-boldest badge badge-danger ms-2">
                                                            {{ trim(empty($data->marketplace->shopee->status) ? '' : $data->marketplace->shopee->status) }}
                                                        </span>
                                                    @elseif(strtoupper(trim(empty($data->marketplace->shopee->status) ? '' : $data->marketplace->shopee->status)) == 'NORMAL')
                                                        <span class="fs-8 fw-boldest badge badge-success ms-2">
                                                            {{ trim(empty($data->marketplace->shopee->status) ? '' : $data->marketplace->shopee->status) }}
                                                        </span>
                                                    @else
                                                        <span class="fs-8 fw-boldest badge badge-danger ms-2">
                                                            {{ trim(empty($data->marketplace->shopee->status) ? '' : $data->marketplace->shopee->status) }}
                                                        </span>
                                                    @endif
                                                </span>
                                                <span class="fs-8 fw-boldest text-dark mt-3 d-block">Tokopedia : </span>
                                                <span class="fs-8 fw-bold text-gray-600">
                                                    SKU : <span class="fs-7 fw-bolder text-danger ms-2">
                                                        {{ strtoupper(trim(empty($data->marketplace->tokopedia->sku) ? '' : $data->marketplace->tokopedia->sku)) }}
                                                    </span>
                                                    <br>
                                                    ProductID : <span class="fs-7 fw-bolder text-danger ms-2">
                                                        {{ strtoupper(trim(empty($data->marketplace->tokopedia->product_id) ? '' : $data->marketplace->tokopedia->product_id)) }}
                                                    </span>
                                                    <br>
                                                    Status :
                                                    @if (strtoupper(trim(empty($data->marketplace->tokopedia->status) ? '' : $data->marketplace->tokopedia->status)) ==
                                                            'BANNED')
                                                        <span class="fs-8 fw-boldest badge badge-danger ms-2">
                                                            {{ trim(empty($data->marketplace->tokopedia->status) ? '' : $data->marketplace->tokopedia->status) }}
                                                        </span>
                                                    @elseif(strtoupper(trim(empty($data->marketplace->tokopedia->status) ? '' : $data->marketplace->tokopedia->status)) ==
                                                            'PENDING')
                                                        <span class="fs-8 fw-boldest badge badge-danger ms-2">
                                                            {{ trim(empty($data->marketplace->tokopedia->status) ? '' : $data->marketplace->tokopedia->status) }}
                                                        </span>
                                                    @elseif(strtoupper(trim(empty($data->marketplace->tokopedia->status) ? '' : $data->marketplace->tokopedia->status)) ==
                                                            'DELETED')
                                                        <span class="fs-8 fw-boldest badge badge-danger ms-2">
                                                            {{ trim(empty($data->marketplace->tokopedia->status) ? '' : $data->marketplace->tokopedia->status) }}
                                                        </span>
                                                    @elseif(strtoupper(trim(empty($data->marketplace->tokopedia->status) ? '' : $data->marketplace->tokopedia->status)) ==
                                                            'ARCHIVE')
                                                        <span class="fs-8 fw-boldest badge badge-success ms-2">
                                                            {{ trim(empty($data->marketplace->tokopedia->status) ? '' : $data->marketplace->tokopedia->status) }}
                                                        </span>
                                                    @elseif(strtoupper(trim(empty($data->marketplace->tokopedia->status) ? '' : $data->marketplace->tokopedia->status)) ==
                                                            'BEST (FEATURE PRODUCT)')
                                                        <span class="fs-8 fw-boldest badge badge-success ms-2">
                                                            {{ trim(empty($data->marketplace->tokopedia->status) ? '' : $data->marketplace->tokopedia->status) }}
                                                        </span>
                                                    @elseif(strtoupper(trim(empty($data->marketplace->tokopedia->status) ? '' : $data->marketplace->tokopedia->status)) ==
                                                            'INACTIVE (WAREHOUSE)')
                                                        <span class="fs-8 fw-boldest badge badge-warning ms-2">
                                                            {{ trim(empty($data->marketplace->tokopedia->status) ? '' : $data->marketplace->tokopedia->status) }}
                                                        </span>
                                                    @else
                                                        <span class="fs-8 fw-boldest badge badge-danger ms-2">
                                                            {{ trim(empty($data->marketplace->tokopedia->status) ? '' : $data->marketplace->tokopedia->status) }}
                                                        </span>
                                                    @endif
                                                    </br>

                                                    @if (trim(empty($data->marketplace->shopee->sku) ? '' : $data->marketplace->shopee->sku) != '')
                                                        @if (strtoupper(trim($data->part_number)) !=
                                                                strtoupper(trim(empty($data->marketplace->shopee->sku) ? '' : $data->marketplace->shopee->sku)))
                                                            <span
                                                                class="badge badge-danger fs-8 fw-boldest animation-blink">PART
                                                                NUMBER DAN SKU TIDAK SAMA </span>
                                                        @endif
                                                    @else
                                                        <span
                                                            class="badge badge-danger fs-8 fw-boldest animation-blink">PART
                                                            NUMBER DAN SKU TIDAK SAMA </span>
                                                    @endif
                                            </td>
                                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                                @if ($data->status_mp->status_mp_awal == 1 && $data->status_mp->status_mp_tujuan == 1)
                                                    <span class="fs-7 fw-bolder badge badge-light-success">
                                                        <i class="fa fa-check text-success"></i>
                                                    </span>
                                                @elseif ($data->status_mp->status_mp_awal == 0 && $data->status_mp->status_mp_tujuan == 0)
                                                    <span class="fs-7 fw-bolder badge badge-light-danger">
                                                        <i class="fa fa-minus-circle text-gray-400"></i>
                                                    </span>
                                                @elseif ($data_header->lokasi_awal->kode_lokasi == 'OS')
                                                    @if ($data->status_mp->status_mp_awal == 1)
                                                        <span class="fs-7 fw-bolder text-success">Shopee <i class="fa fa-check text-success"></i></span>
                                                    @elseif ($data->status_mp->status_mp_tujuan == 1)
                                                        <span class="fs-7 fw-bolder text-success">Tokopedia <i class="fa fa-check text-success"></i></span>
                                                    @endif
                                                @elseif ($data_header->lokasi_awal->kode_lokasi == 'OL')
                                                    @if ($data->status_mp->status_mp_awal == 1)
                                                        <span class="fs-7 fw-bolder text-success">Tokopedia <i class="fa fa-check text-success"></i>
                                                        </span>
                                                    @elseif ($data->status_mp->status_mp_tujuan == 1)
                                                        <span class="fs-7 fw-bolder text-success">Shopee <i class="fa fa-check text-success"></i></span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="ps-3 pe-3 text-end" style="vertical-align:top;">
                                                <span class="fs-7 fw-bolder text-dark"> {{ $data->pindah }} </span>
                                            </td>
                                            <td class="ps-3 pe-3 text-end" style="vertical-align:top;">
                                                <span class="fs-7 fw-bolder text-dark"> {{ $data->stock_suma }} </span>
                                            </td>
                                            <td class="ps-3 pe-3 text-end" style="vertical-align:top;">
                                                @if ($data->marketplace->shopee->stock !== null)
                                                    <span class="fs-7 fw-boldest text-dark">
                                                        {{ $data->marketplace->shopee->stock }}
                                                    </span>
                                                @else
                                                    <span class="fs-7 fw-bolder text-dark">
                                                        <i class="bi bi-database-slash fs-1 text-danger"> </i>
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="ps-3 pe-3 text-end" style="vertical-align:top;">
                                                @if ($data->marketplace->shopee->stock !== null)
                                                    <span class="fs-7 fw-boldest text-dark">
                                                        {{ $data->marketplace->tokopedia->stock }}
                                                    </span>
                                                @else
                                                    <span class="fs-7 fw-bolder text-dark">
                                                        <i class="bi bi-database-slash fs-1 text-danger"> </i>
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="ps-3 pe-3 text-end" style="vertical-align:top;">
                                                @if ($data->marketplace->shopee->stock !== null)
                                                    @if (trim($data_header->lokasi_tujuan->kode_lokasi) === 'OS')
                                                        <span class="fs-7 fw-boldest text-success">
                                                            <i class="fa fa-arrow-up me-2 text-success"
                                                                aria-hidden="true"> </i>
                                                            {{ $data->stock_update->shopee }}
                                                        </span>
                                                    @else
                                                        <span class="fs-7 fw-boldest text-danger">
                                                            <i class="fa fa-arrow-down me-2 text-danger"
                                                                aria-hidden="true"> </i>
                                                            {{ $data->stock_update->shopee }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="fs-7 fw-bolder text-dark">
                                                        <i class="bi bi-database-slash fs-1 text-danger"> </i>
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="ps-3 pe-3 text-end" style="vertical-align:top;">
                                                @if ($data->marketplace->tokopedia->stock !== null)
                                                    @if (trim($data_header->lokasi_tujuan->kode_lokasi) === 'OL')
                                                        <span class="fs-7 fw-boldest text-success">
                                                            <i class="fa fa-arrow-up me-2 text-success"
                                                                aria-hidden="true"> </i>
                                                            {{ $data->stock_update->tokopedia }}
                                                        </span>
                                                    @else
                                                        <span class="fs-7 fw-boldest text-danger">
                                                            <i class="fa fa-arrow-down me-2 text-danger"
                                                                aria-hidden="true"> </i>
                                                            {{ $data->stock_update->tokopedia }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="fs-7 fw-bolder text-dark">
                                                        <i class="bi bi-database-slash fs-1 text-danger"> </i>
                                                    </span>
                                                @endif
                                            </td>
                                            <td style="text-align:center;vertical-align:top;">
                                                @if (($data->status_mp->status_mp_awal == 0 || $data->status_mp->status_mp_tujuan == 0) && $data_header->status->validasi == 1)
                                                    <button class="btn btn-icon btn-sm btn-danger"
                                                        onclick="updateDetail(' {{ $data_update }} ')">
                                                        <i class="fa fa-refresh text-white"> </i>
                                                    </button>
                                                @else
                                                    @if (strtoupper(trim($data->status_mp->keterangan)) == 'DATA BELUM DI VALIDASI')
                                                        <span class="fs-8 fw-boldest text-danger">
                                                            {{ strtoupper(trim($data->status_mp->keterangan)) }} </span>
                                                    @else
                                                        <span class="fs-8 fw-boldest text-success">
                                                            {{ strtoupper(trim($data->status_mp->keterangan)) }} </span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td style="text-align:center;vertical-align:top;">
                                                @if (($data->status_mp->status_mp_awal == 0 || $data->status_mp->status_mp_tujuan == 0) && $data_header->status->validasi == 1)
                                                    <a href="#" class="btn btn-icon btn-sm btn-warning"
                                                        onclick="updateDetailInternal(' {{ $data_update }} ')">
                                                        <i class="fa fa-database" aria-hidden="true"> </i>
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-6">
                            @if ($data_header->status->mp_header == 0)
                                @if ($data_header->status->validasi == 1)
                                    <button class="btn btn-danger" onclick="updateSemuaDetail()">
                                        Update Semua <i class="fa fa-refresh text-white"></i>
                                    </button>
                                @endif
                            @endif
                        </div>
                        <div class="col-6 text-end">
                            <a class="btn btn-secondary btn-hover-rise" data-focus="0"
                                href=" {{ route('online.pemindahan.daftar', [
                                    'param' => base64_encode(
                                        json_encode([
                                            'search' => $filter_header->filter->search,
                                            'start_date' => $filter_header->filter->start_date,
                                            'end_date' => $filter_header->filter->end_date,
                                            'page' => $filter_header->filter->page,
                                            'per_page' => $filter_header->filter->per_page,
                                        ]),
                                    ),
                                ]) }} ">
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <!--end::container-->
            </div>
        </div>
        {{-- @else --}}
        {{-- <div id="daftar_table" class="tab-pane fade active show"
                data-no=" {{ trim($filter_header->nomor_dokumen) }} ">
                <div class="card mt-10 p-5">
                    <div class="row">
                        <div class="col-8">
                            @if ($filter_header->filter->marketplace->shopee == 0)
                                <a class="btn btn-light-dark btn-hover-rise btn_detail" data-focus="0"
                                    onclick="updateSemuaDetail()">
                                    Update Semua <img alt="Logo"
                                        src="http://localhost:2022/suma-pmo/public/assets/images/logo/shopee.png"
                                        class="h-20px me-3" />
                                 </a>
                            @endif
                         </div>
                        <div class="col-4 text-end">
                            <a class="btn btn-secondary btn-hover-rise" data-focus="0"
                                href=" {{ route('online.pemindahan.shopee.daftar', [
                                    'param' => base64_encode(
                                        json_encode([
                                            'search' => $filter_header->filter->search,
                                            'start_date' => $filter_header->filter->start_date,
                                            'end_date' => $filter_header->filter->end_date,
                                            'page' => $filter_header->filter->page,
                                            'per_page' => $filter_header->filter->per_page,
                                        ]),
                                    ),
                                ]) }} ">
                                Kembali
                             </a>
                         </div>
                     </div>
                 </div>
             </div> --}}
        {{-- @endif --}}
    </div>
    <div id="respon_container">
    </div>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script language="JavaScript"
        src=" {{ asset('assets/js/suma/online/pemindahan/PemindahanDetail.js') }}?v={{ time() }}">
    </script>
@endpush
