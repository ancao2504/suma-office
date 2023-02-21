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
                            <span class="fw-bolder mb-2 text-dark">Pemindahan Antar Lokasi</span>
                            <span class="text-muted fw-bold fs-7">Form pemindahan antar lokasi Shopee</span>
                        </h3>
                        <div class="card-toolbar">
                            <img src="{{ asset('assets/images/logo/shopee_lg.png') }}" class="h-75px" />
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="daftar_table" data-no="{{ trim($filter_header->nomor_dokumen) }}">
                            <div class="row mb-3 table_delete">
                                <div class="col-6">
                                    <div class="fw-bolder text-gray-400">Nomor Dokumen :</div>
                                    <div class="fs-3 fw-bolder text-gray-800">{{ $data_header->nomor_dokumen }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="fw-bolder text-gray-400">Tanggal :</div>
                                    <div class="fs-5 fw-bolder text-gray-800">
                                        {{ date('d F Y', strtotime($data_header->tanggal)) }}</div>
                                </div>
                                <div class="col-6 mt-5">
                                    <div class="fw-bolder text-gray-400">Dari :</div>
                                    <div class="fs-4 fw-bolder text-gray-800">
                                        {{ $data_header->lokasi_awal->kode_lokasi }} -
                                        {{ $data_header->lokasi_awal->nama_lokasi }}</div>
                                    <div class="fs-6 fw-bolder text-gray-800">{{ $data_header->lokasi_awal->alamat->alamat1 }}
                                        {{ $data_header->lokasi_awal->alamat->alamat2 }}</div>
                                    <div class="fs-6 fw-bolder text-gray-800">{{ $data_header->lokasi_awal->kota }}
                                    </div>
                                </div>
                                <div class="col-6 mt-5">
                                    <div class="fw-bolder text-gray-400">Ke :</div>
                                    <div class="fs-4 fw-bolder text-gray-800">
                                        {{ $data_header->lokasi_tujuan->kode_lokasi }} -
                                        {{ $data_header->lokasi_tujuan->nama_lokasi }}</div>
                                    <div class="fs-6 fw-bolder text-gray-800">
                                        {{ $data_header->lokasi_tujuan->alamat->alamat1 }}
                                        {{ $data_header->lokasi_tujuan->alamat->alamat2 }}</div>
                                    <div class="fs-6 fw-bolder text-gray-800">{{ $data_header->lokasi_tujuan->kota }}
                                    </div>
                                </div>
                                <div class="col-6 mt-5">
                                    <div class="fw-bolder text-gray-400">Keterangan :</div>
                                    <div class="fs-6 fw-bolder text-gray-800">
                                        {{ !empty($data_header->keterangan) ? $data_header->keterangan : '-' }}</div>
                                </div>
                                <div class="col-6 mt-5">
                                    <div class="fw-bolder text-gray-400">User :</div>
                                    <div class="fs-6 fw-bolder text-gray-800">
                                        {{ !empty($data_header->usertime) ? explode('=', $data_header->usertime)[2] : '-' }}
                                    </div>
                                </div>
                                <div class="col-12 mt-5">
                                    <div class="fw-bolder text-gray-400">Status :</div>
                                        <span class="fs-8 fw-boldest badge badge-light-{{ $data_header->status->cetak == 1 ? 'success' : 'danger' }} me-2">
                                            <i class="fa fa-check text-{{ $data_header->status->cetak == 1 ? 'success' : 'danger' }} me-2"></i>CETAK
                                        </span>
                                        <span class="fs-8 fw-boldest badge badge-light-{{ $data_header->status->in == 1 ? 'success' : 'danger' }} me-2">
                                            <i class="fa fa-check text-{{ $data_header->status->in == 1 ? 'success' : 'danger' }} me-2"></i>IN
                                        </span>
                                        <span class="fs-8 fw-boldest badge badge-light-{{ $data_header->status->sj == 1 ? 'success' : 'danger' }} me-2">
                                            <i class="fa fa-check text-{{ $data_header->status->sj == 1 ? 'success' : 'danger' }} me-2"></i>SJ
                                        </span>
                                        <span class="fs-8 fw-boldest badge badge-light-{{ !empty($data_header->validasi) && $data_header->validasi == 1 ? 'success' : 'danger' }} me-2">
                                            <i class="fa fa-check text-{{ !empty($data_header->validasi) && $data_header->validasi == 1 ? 'success' : 'danger' }} me-2"></i>VALIDASI
                                        </span>
                                        <span class="fs-8 fw-boldest badge badge-light-{{ $data_header->status->mp_header == 1 ? 'success' : 'danger' }} me-2">
                                            <i class="fa fa-check text-{{ $data_header->status->mp_header == 1 ? 'success' : 'danger' }} me-2"></i>MARKETPLACE
                                        </span>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive table_delete">
                            <table class="table table-row-dashed table-row-gray-300 align-middle">
                                <thead class="border">
                                    <tr class="fs-8 fw-bolder text-muted">
                                        <th rowspan="2" class="w-20px text-center">No</th>
                                        <th rowspan="2" class="w-50px text-center">Kode Part</th>
                                        <th rowspan="2" class="w-20px text-center">Status Marketplace</th>
                                        <th rowspan="2" class="w-50px text-center">Pindah</th>
                                        <th colspan="3" class="w-50px text-center">Stock</th>
                                        <th colspan="2" class="w-50px text-center">Action</th>
                                    </tr>
                                    <tr class="fs-8 fw-bolder text-muted">
                                        <th class="w-50px text-center">SUMA</th>
                                        <th class="w-50px text-center">Shopee</th>
                                        <th class="w-50px text-center">Total</th>
                                        <th class="w-50px text-center">Shopee</th>
                                        <th class="w-50px text-center">Internal</th>
                                    </tr>
                                </thead>
                                <tbody class="border">
                                    @if (count($data_header->detail) < 0 || empty($data_header->detail))
                                        <tr class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                            <td class="fs-7 fw-bolder text-gray-800 text-center" colspan="6"> Data Tidak Ditemukan </td>
                                        </tr>
                                    @else
                                        @php
                                            $no = 1;
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
                                                    <span class="fs-7 fw-bolder text-dark">{{ $no }}</span>
                                                </td>
                                                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                                    <span
                                                        class="fs-7 fw-boldest text-gray-800 d-block">{{ strtoupper(trim($data->part_number)) }}</span>
                                                    <span
                                                        class="fs-7 fw-bolder text-gray-700 d-block">{{ trim($data->nama_part) }}</span>
                                                    <span class="fs-8 fw-bolder text-gray-400 mt-4 d-block">Product ID :</span>
                                                    <span class="fs-8 fw-bolder text-gray-600 d-block">
                                                        @if (empty($data->product_id) || $data->product_id == '')
                                                        <span class="badge badge-light-danger">(Product ID masih kosong)</span>
                                                        @else
                                                        {{ strtoupper(trim($data->product_id)) }}
                                                        @endif
                                                    </span>
                                                </td>
                                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                                    @if ($data->status_mp_detail == 1)
                                                        <span class="fs-7 fw-bolder badge badge-light-success">Sudah di Perbarui</span>
                                                    @else
                                                        <span class="fs-7 fw-bolder badge badge-light-danger">Belum di Perbarui</span>
                                                    @endif
                                                </td>
                                                <td class="ps-3 pe-3 text-end" style="vertical-align:top;">
                                                    <span class="fs-7 fw-bolder text-dark">{{ $data->pindah }}</span>
                                                </td>
                                                <td class="ps-3 pe-3 text-end" style="vertical-align:top;">
                                                    <span class="fs-7 fw-bolder text-dark">{{ $data->stock_suma }}</span>
                                                </td>
                                                <td class="ps-3 pe-3 text-end" style="vertical-align:top;">
                                                    <span class="fs-7 fw-bolder text-dark">
                                                        @if ($data->stok_shopee)
                                                            {{ $data->stok_shopee }}
                                                        @else
                                                            <i class="bi bi-database-slash fs-1 text-danger"></i>
                                                        @endif
                                                    </span>
                                                </td>
                                                <td class="ps-3 pe-3 text-end" style="vertical-align:top;">
                                                    <span class="fs-7 fw-bolder text-dark">
                                                        @if ($data->stok_update)
                                                            {{ $data->stok_update }}
                                                        @else
                                                            <i class="bi bi-database-slash fs-1 text-danger"></i>
                                                        @endif
                                                    </span>
                                                </td>
                                                <td style="text-align:center;vertical-align:top;">
                                                    @if ($data->status_mp_detail == 0)
                                                        <a href="#"
                                                            class="btn btn-sm btn-light-dark btn-hover-rise btn_detail"
                                                            data-focus="0"
                                                            onclick="updateDetail('{{ $data_update }}')">
                                                            <img alt="Logo"
                                                                src="{{ asset('assets/images/logo/shopee.png') }}"
                                                                class="h-20px" />
                                                        </a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td style="text-align:center;vertical-align:top;">
                                                    @if ($data->status_mp_detail == 0)
                                                        <a href="#" class="btn btn-sm btn-danger btn_detail"
                                                            data-focus="0"
                                                            onclick="updateDetailInternal('{{ $data_update }}')">
                                                            <i class="fa fa-database" aria-hidden="true"></i>
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
                                {{-- @if ($data_header->status->cetak == 1 && $data_header->status->sj == 1 && $data_header->status->validasi == 1 && $data_header->status->mp_header == 0) --}}
                                @if ($data_header->status->mp_header == 0)
                                    <a class="btn btn-light-dark btn-hover-rise btn_detail" data-focus="0"
                                        onclick="updateSemuaDetail()">
                                        Update Semua <img alt="Logo"
                                            src="{{ asset('assets/images/logo/shopee.png') }}" class="h-20px me-3" />
                                    </a>
                                @endif
                            </div>
                            <div class="col-6 text-end">
                                <a class="btn btn-secondary btn-hover-rise" data-focus="0"
                                    href="{{ route('online.pemindahan.shopee.daftar', [
                                        'param' => base64_encode(
                                            json_encode([
                                                'search' => $filter_header->filter->search,
                                                'start_date' => $filter_header->filter->start_date,
                                                'end_date' => $filter_header->filter->end_date,
                                                'page' => $filter_header->filter->page,
                                                'per_page' => $filter_header->filter->per_page,
                                            ]),
                                        ),
                                    ]) }}">
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
                data-no="{{ trim($filter_header->nomor_dokumen) }}">
                <div class="card mt-10 p-5">
                    <div class="row">
                        <div class="col-8">
                            @if ($filter_header->filter->marketplace == 0)
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
                                href="{{ route('online.pemindahan.shopee.daftar', [
                                    'param' => base64_encode(
                                        json_encode([
                                            'search' => $filter_header->filter->search,
                                            'start_date' => $filter_header->filter->start_date,
                                            'end_date' => $filter_header->filter->end_date,
                                            'page' => $filter_header->filter->page,
                                            'per_page' => $filter_header->filter->per_page,
                                        ]),
                                    ),
                                ]) }}">
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
        src="{{ asset('assets/js/suma/online/shopee/pemindahan/PemindahanDetail.js') }}?v={{ time() }}"></script>
@endpush
