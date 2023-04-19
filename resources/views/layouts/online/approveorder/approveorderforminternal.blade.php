@extends('layouts.main.index')
@section('title','Internal')
@section('subtitle','Orders')
@section('container')
<div class="row g-0">
    @forelse($data as $data_internal)
    <div class="card card-flush shadow mt-6">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Data Faktur</span>
                <span class="text-muted fw-bold fs-7">Data faktur internal</span>
            </h3>
            <div class="card-toolbar">
                <img src="{{ asset('assets/images/logo/bg_logo_suma.png') }}" class="h-50px" />
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 pt-6 pb-6">
                    <span class="fs-7 fw-bolder text-gray-500 d-block">No Faktur:</span>
                    <span class="fs-7 fw-bolder text-dark d-block mt-1">{{ $data_internal->nomor_faktur }}</span>
                    <span class="fs-7 fw-bolder text-danger d-block">{{ date('d F Y', strtotime($data_internal->tanggal)) }}</span>

                    <div class="row mt-6">
                        <div class="col-lg-4">
                            <span class="fs-7 fw-bolder text-gray-500">Jenis Beli:</span>
                        </div>
                        <div class="col-lg-8">
                            <div class="fw-bolder fs-7 text-gray-800 d-flex align-items-center flex-wrap mt-1">
                                <span class="pe-2">{{ $data_internal->jenis_beli->keterangan }}</span>
                                <span class="fs-7 text-danger d-flex align-items-center">
                                <span class="bullet bullet-dot bg-danger me-2"></span>{{ $data_internal->jenis_beli->kode }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <span class="fs-7 fw-bolder text-gray-500">Salesman:</span>
                        </div>
                        <div class="col-lg-8">
                            <div class="fw-bolder fs-7 text-gray-800 d-flex align-items-center flex-wrap mt-1">
                                <span class="pe-2">{{ $data_internal->salesman->nama }}</span>
                                <span class="fs-7 text-danger d-flex align-items-center">
                                <span class="bullet bullet-dot bg-danger me-2"></span>{{ $data_internal->salesman->kode }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <span class="fs-7 fw-bolder text-gray-500">Dealer:</span>
                        </div>
                        <div class="col-lg-8">
                            <div class="fw-bolder fs-7 text-gray-800 d-flex align-items-center flex-wrap mt-1">
                                <span class="pe-2">{{ $data_internal->dealer->nama }}</span>
                                <span class="fs-7 text-danger d-flex align-items-center">
                                <span class="bullet bullet-dot bg-danger me-2"></span>{{ $data_internal->dealer->kode }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <span class="fs-7 fw-bolder text-gray-500">Ekspedisi:</span>
                        </div>
                        <div class="col-lg-8">
                            <div class="fw-bolder fs-7 text-gray-800 d-flex align-items-center flex-wrap mt-1">
                                <span class="pe-2">{{ $data_internal->ekspedisi->nama }}</span>
                                <span class="fs-7 text-danger d-flex align-items-center">
                                <span class="bullet bullet-dot bg-danger me-2"></span>{{ $data_internal->ekspedisi->kode }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 pt-6 pb-6 ps-6">
                    <span class="fs-7 fw-bolder text-gray-500 d-block">Keterangan:</span>
                    <span class="fs-7 fw-bolder text-gray-800 d-block mt-1">{{ $data_internal->keterangan }}</span>


                    <span class="fs-7 fw-bolder text-gray-500 d-block mt-6">Umur Faktur:</span>
                    <div class="fw-bolder fs-7 text-gray-800 d-flex align-items-center flex-wrap mt-1">
                        <span class="pe-2">{{ $data_internal->jatuh_tempo->tanggal }}</span>
                        <span class="fs-7 text-danger d-flex align-items-center">
                        <span class="bullet bullet-dot bg-danger me-2"></span>{{ $data_internal->jatuh_tempo->umur_faktur }} Hari</span>
                    </div>

                    <span class="fs-7 fw-bolder text-gray-500 d-block mt-6">Status:</span>
                    @if($data_internal->kode_tpc == '14')
                    <span class="fs-8 fw-boldest badge badge-primary mt-2">TPC {{ $data_internal->kode_tpc }}</span>
                    @else
                    <span class="fs-8 fw-boldest badge badge-danger mt-2">TPC {{ $data_internal->kode_tpc }}</span>
                    @endif
                    @if($data_internal->status->rh == 'H')
                    <span class="fs-8 fw-boldest badge badge-danger mt-2">HOTLINE</span>
                    @else
                    <span class="fs-8 fw-boldest badge badge-success mt-2">REGULER</span>
                    @endif
                    @if($data_internal->status->bo == 'B')
                    <span class="fs-8 fw-boldest badge badge-danger mt-2">BACKORDER</span>
                    @else
                    <span class="fs-8 fw-boldest badge badge-success mt-2">TIDAK BO</span>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle">
                        <thead class="border">
                            <tr class="fs-8 fw-bolder text-muted">
                                <th class="w-50px ps-3 pe-3 text-center">No</th>
                                <th class="w-300px ps-3 pe-3 text-center">Part Number</th>
                                <th class="w-100px ps-3 pe-3 text-center">Jml Order</th>
                                <th class="w-100px ps-3 pe-3 text-center">Jml Jual</th>
                                <th class="w-100px ps-3 pe-3 text-center">Harga</th>
                                <th class="w-100px ps-3 pe-3 text-center">Disc (%)</th>
                                <th class="w-100px ps-3 pe-3 text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody class="border">
                            @forelse($data_internal->detail as $data_internal_detail)
                            <tr>
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                    <span class="fs-7 fw-bold text-gray-800">{{ $loop->iteration }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                    <div class="d-flex">
                                        <div class="symbol symbol-45px me-5">
                                            <img src="{{ $data_internal_detail->pictures }}" alt="{{ $data_internal_detail->part_number }}"
                                                onerror="this.onerror=null; this.src='{{ URL::asset('assets/images/background/part_image_not_found.png') }}'">
                                        </div>
                                        <div class="d-flex justify-content-start flex-column">
                                            <span class="fs-7 fw-bolder text-dark">{{ $data_internal_detail->nama_part }}</span>
                                            <span class="fs-8 fw-bolder text-gray-600 d-block">{{ $data_internal_detail->part_number }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data_internal_detail->jml_order) }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data_internal_detail->jml_jual) }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data_internal_detail->harga) }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data_internal_detail->disc_detail, 2) }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data_internal_detail->total_detail) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7">
                                    <div class="fs-6 fw-boldest text-muted p-20 text-center">- TIDAK ADA DATA YANG DITAMPILKAN -</div>
                                </td>
                            </tr>
                            @endforelse
                            <tr>
                                <td colspan="5" class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-muted">Sub Total</span>
                                </td>
                                <td colspan="2" class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data_internal->total->sub_total) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-muted">Discount (%)</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data_internal->total->disc_header, 2) }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data_internal->total->disc_header_rp) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-muted">Discount (Rp)</span>
                                </td>
                                <td colspan="2" class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data_internal->total->disc_rp1) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-muted">Total</span>
                                </td>
                                <td colspan="2" class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data_internal->total->total) }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="card card-flush shadow mt-6">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Data Faktur</span>
                <span class="text-muted fw-bold fs-7">Data faktur internal</span>
            </h3>
            <div class="card-toolbar">
                <img src="{{ asset('assets/images/logo/bg_logo_suma.png') }}" class="h-50px" />
            </div>
        </div>
        <div class="card-body">
            <h4 class="text-muted">- TIDAK ADA DATA YANG DITAMPILKAN -</h4>
        </div>
    </div>
    @endforelse
</div>

<div class="row g-0">
    <div class="d-flex">
        <button id="btnApproveFakturInternal" name="approve_faktur_internal" data-nomor_faktur="{{ $data_internal->nomor_faktur }}"
            type="button" class="btn btn-primary mt-6">
            <i class="fa fa-check" aria-hidden="true"></i> Approve Faktur
        </button>
    </div>
</div>

@push('scripts')
<script>
    const url = {
        'daftar_approve_order': "{{ route('online.orders.approveorder.daftar') }}",
        'proses_approve_marketplace': "{{ route('online.orders.approveorder.form.proses.internal') }}",
    }
</script>
<script src="{{ asset('assets/js/suma/online/approveorder/forminternal.js') }}?v={{ time() }}"></script>
@endpush
@endsection
