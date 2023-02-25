@extends('layouts.main.index')
@section('title', 'Shopee')
@section('subtitle', 'Update Harga')
@section('container')
<!--start::container-->
    <div class="row g-0">
        <form action="#">
            @csrf
            <div class="card card-flush">
                <div class="card-header align-items-center border-0 mt-4">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="fw-bolder mb-2 text-dark">Update Harga</span>
                        <span class="text-muted fw-bold fs-7">Form update harga Shopee</span>
                    </h3>
                    <div class="card-toolbar">
                        <img src="{{ asset('assets/images/logo/shopee_lg.png') }}" class="h-75px" />
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-5 mb-8">
                        <div class="col-sm-6">
                            <div class="fw-bold fs-7 text-gray-600 mb-1">Nomor Dokumen:</div>
                            <div class="fw-bolder text-dark">{{ strtoupper(trim($dataApi->nomor_dokumen)) }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="fw-bold fs-7 text-gray-600 mb-1">Tanggal:</div>
                            <div class="fw-bolder text-dark">{{ date('d F Y', strtotime($dataApi->tanggal)) }}</div>
                        </div>
                    </div>
                    <div class="row">
                        <div id="tableDetailUpdateHarga">
                            <div class="table-responsive">
                                <table class="table table-row-dashed table-row-gray-300 align-middle">
                                    <thead class="border">
                                        <tr class="fs-8 fw-bolder text-muted">
                                            <th rowspan="2" class="w-50px ps-3 pe-3 text-center">No</th>
                                            <th rowspan="2" class="w-200px ps-3 pe-3 text-center">Part Number</th>
                                            <th rowspan="2" class="w-50px ps-3 pe-3 text-center">Status</th>
                                            <th rowspan="2" class="min-w-100px ps-3 pe-3 text-center">Keterangan</th>
                                            <th colspan="4" class="w-100px ps-3 pe-3 text-center">HET</th>
                                            <th colspan="2" class="w-100px ps-3 pe-3 text-center">Action</th>
                                        </tr>
                                        <tr class="fs-8 fw-bolder text-muted">
                                            <th class="w-100px ps-3 pe-3 text-center">Lama</th>
                                            <th class="w-100px ps-3 pe-3 text-center">Baru</th>
                                            <th class="w-100px ps-3 pe-3 text-center">Selisih</th>
                                            <th class="w-100px ps-3 pe-3 text-center">Status</th>
                                            <th class="w-50px ps-3 pe-3 text-center">Marketplace</th>
                                            <th class="w-50px ps-3 pe-3 text-center">Internal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border">
                                        @php
                                            $nomor_urut = 0;
                                            // $jumlah_data = 0;
                                        @endphp
                                        {{-- @if ((float) $jumlah_data <= 0) --}}
                                        @if (count($dataApi->detail) <= 0)
                                            <tr>
                                                <td colspan="9" class="pt-12 pb-12">
                                                    <div class="row text-center pe-10">
                                                        <span class="svg-icon svg-icon-muted">
                                                            <svg class="h-100px w-100px" xmlns="http://www.w3.org/2000/svg"
                                                                width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none">
                                                                <path
                                                                    d="M21.7 18.9L18.6 15.8C17.9 16.9 16.9 17.9 15.8 18.6L18.9 21.7C19.3 22.1 19.9 22.1 20.3 21.7L21.7 20.3C22.1 19.9 22.1 19.3 21.7 18.9Z"
                                                                    fill="currentColor" />
                                                                <path opacity="0.3"
                                                                    d="M11 20C6 20 2 16 2 11C2 6 6 2 11 2C16 2 20 6 20 11C20 16 16 20 11 20ZM11 4C7.1 4 4 7.1 4 11C4 14.9 7.1 18 11 18C14.9 18 18 14.9 18 11C18 7.1 14.9 4 11 4ZM8 11C8 9.3 9.3 8 11 8C11.6 8 12 7.6 12 7C12 6.4 11.6 6 11 6C8.2 6 6 8.2 6 11C6 11.6 6.4 12 7 12C7.6 12 8 11.6 8 11Z"
                                                                    fill="currentColor" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="row text-center pt-8">
                                                        <span class="fs-6 fw-bolder text-gray-500">- Tidak ada data yang
                                                            ditampilkan
                                                            -</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @else
                                            @foreach ($dataApi->detail as $data)
                                                @php
                                                    $nomor_urut = (float) $nomor_urut + 1;
                                                    // $jumlah_data = (float) $jumlah_data + 1;
                                                @endphp
                                                <tr>
                                                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                                        <span class="fs-7 fw-bold text-gray-800">{{ number_format($nomor_urut) }}</span>
                                                    </td>
                                                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                                        <span class="fs-7 fw-boldest text-gray-800 d-block">{{ strtoupper(trim($data->part_number)) }}</span>
                                                        <span class="fs-8 fw-bolder text-gray-600 d-block">{{ strtoupper(trim($data->nama_part)) }}</span>
                                                        <span class="fs-8 fw-bolder text-gray-400 mt-4 d-block">Product ID :</span>
                                
                                                        @if(trim($data->product_id) == '')
                                                            <span class="badge badge-light-danger">(Product ID masih kosong)</span>
                                                        @else
                                                            <span class="fs-8 fw-boldest text-gray-800">{{ strtoupper(trim($data->product_id)) }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                
                                                        @if((int)$data->update == 1) 
                                                        <i class="fa fa-check text-success"></i>
                                                        @else
                                                        <i class="fa fa-minus-circle text-gray-400"></i>
                                                        @endif
                
                                                    </td>
                                                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                                        <span class="fs-7 fw-bolder text-gray-800">{{ $data->keterangan }}</span>
                                                    </td>
                                                    <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                                        <span
                                                            class="fs-7 fw-bolder text-gray-800">{{ number_format($data->het_lama) }}</span>
                                                    </td>
                                                    <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                                        <span
                                                            class="fs-7 fw-bolder text-gray-800">{{ number_format($data->het_baru) }}</span>
                                                    </td>
                                                    <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                                        <span
                                                            class="fs-7 fw-bolder text-gray-800">{{ number_format($data->selisih) }}</span>
                                                    </td>
                                                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                
                                                        @if (strtoupper(trim($data->status)) == 'NAIK')
                                                            <span class="fs-7 fw-boldest text-success">
                                                                <i class="fa fa-arrow-up me-2 text-success"
                                                                    aria-hidden="true"></i>{{ number_format($data->prosentase, 2) }} %
                                                            </span>
                                                        @else
                                                            <span class="fs-7 fw-boldest text-danger">
                                                                <i class="fa fa-arrow-down me-2 text-danger"
                                                                    aria-hidden="true"></i>{{ number_format($data->prosentase, 2) }} %
                                                            </span>
                                                        @endif
                
                                                    </td>
                                                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                                        @if((int)$data->update == 0)
                                                            <button id="btnUpdatePerPartNumber" class="btn btn-icon btn-sm btn-secondary"
                                                                type="button" data-nomor_dokumen="{{ strtoupper(trim($dataApi->nomor_dokumen)) }}"
                                                                data-part_number="{{ strtoupper(trim($data->part_number)) }}">
                                                                <img src="{{ asset('assets/images/logo/shopee_lg.png') }}" class="h-30px" />
                                                            </button>
                                                        @else
                                                            <span class="badge badge-light-success">diupdate</span>
                                                        @endif
                                                    </td>
                                                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                                        @if((int)$data->update == 0)
                                                            <button id="btnUpdateStatusPerPartNumber" class="btn btn-icon btn-sm btn-danger" type="button"
                                                                    data-nomor_dokumen="{{ strtoupper(trim($dataApi->nomor_dokumen)) }}"
                                                                    data-part_number="{{ strtoupper(trim($data->part_number)) }}">
                                                                    <i class="fa fa-database" aria-hidden="true"></i>
                                                            </button>
                                                        @else
                                                            <span class="badge badge-light-success">diupdate</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row g-5 mb-8">
                        <div class="d-flex justify-content-between">
                            @if ($dataApi->status_header == 0)
                            <button id="btnUpdateHargaAll" class="btn btn-light-dark btn-hover-rise"
                                data-nomor_dokumen="{{ strtoupper(trim($dataApi->nomor_dokumen)) }}">Update Semua<img src="{{ asset('assets/images/logo/shopee_lg.png') }}" class="h-20px me-3"/></button>
                            @endif
                            <a href="{{ route('online.updateharga.shopee.daftar', $filter_old) }}" class="btn btn-secondary ms-3">KEMBALI</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!--end::container-->

</div>
<div id="respon_container">
@endsection
{{-- // 'daftar_update_harga': "{{ route('online.updateharga.shopee.form.detail') }}", --}}
@push('scripts')
    <script>
        const url = {
            'daftar_update_harga' : window.location.href,
            'update_per_part_number': "{{ route('online.updateharga.shopee.form.update.part-number') }}",
            'update_status_per_part_number': "{{ route('online.updateharga.shopee.form.update.status-part-number') }}",
            'update_per_dokumen': "{{ route('online.updateharga.shopee.form.update.dokumen') }}",
        }
        const data = {
            'nomor_dokumen': "{{ strtoupper(trim($dataApi->nomor_dokumen)) }}",
        }
    </script>
    <script src="{{ asset('assets/js/suma/online/shopee/updateharga/form.js') }}?v={{ time() }}"></script>
@endpush
