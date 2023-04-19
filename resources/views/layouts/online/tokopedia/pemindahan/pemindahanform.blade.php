@extends('layouts.main.index')
@section('title','Tokopedia')
@section('subtitle','Pemindahan')
@section('container')
<div class="row g-0">
    <form action="#">
        @csrf
        <div class="card card-flush shadow">
            <div class="card-header align-items-center border-0 mt-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Pemindahan Antar Lokasi</span>
                    <span class="text-muted fw-bold fs-7">Form pemindahan antar lokasi tokopedia</span>
                </h3>
                <div class="card-toolbar">
                    <img src="{{ asset('assets/images/logo/tokopedia_lg.png') }}" class="h-75px" />
                </div>
            </div>
            <div class="card-body">
                <div class="row g-5 mb-8">
                    <div class="col-sm-6">
                        <div class="fw-bold fs-7 text-gray-600 mb-1">Nomor Dokumen:</div>
                        <div class="fw-bolder text-dark">{{ strtoupper(trim($data->nomor_dokumen)) }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="fw-bold fs-7 text-gray-600 mb-1">Tanggal:</div>
                        <div class="fw-bolder text-dark">{{ date('d F Y', strtotime($data->tanggal)) }}</div>
                    </div>
                </div>
                <div class="row g-5 mb-8">
                    <div class="col-sm-6">
                        <div class="fw-bold fs-7 text-gray-600 mb-1">Dari Lokasi:</div>
                        <div class="fw-bolder fs-6 text-dark d-flex align-items-center flex-wrap">
                            <span class="pe-2">{{ $data->lokasi->awal->keterangan }}</span>
                            @if(strtoupper(trim($data->lokasi->awal->kode)) == 'RK')
                            <span class="fs-7 fw-boldest text-primary d-flex align-items-center">
                                <span class="bullet bullet-dot bg-primary me-2"></span>{{ $data->lokasi->awal->kode }}
                            </span>
                            @elseif(strtoupper(trim($data->lokasi->awal->kode)) == 'OL')
                            <span class="fs-7 fw-boldest text-success d-flex align-items-center">
                                <span class="bullet bullet-dot bg-success me-2"></span>{{ $data->lokasi->awal->kode }}
                            </span>
                            @elseif(strtoupper(trim($data->lokasi->awal->kode)) == 'OB')
                            <span class="fs-7 fw-boldest text-danger d-flex align-items-center">
                                <span class="bullet bullet-dot bg-danger me-2"></span>{{ $data->lokasi->awal->kode }}
                            </span>
                            @elseif(strtoupper(trim($data->lokasi->awal->kode)) == 'OS')
                            <span class="fs-7 fw-boldest text-warning d-flex align-items-center">
                                <span class="bullet bullet-dot bg-warning me-2"></span>{{ $data->lokasi->awal->kode }}
                            </span>
                            @elseif(strtoupper(trim($data->lokasi->awal->kode)) == 'OP')
                            <span class="fs-7 fw-boldest text-info d-flex align-items-center">
                                <span class="bullet bullet-dot bg-info me-2"></span>{{ $data->lokasi->awal->kode }}
                            </span>
                            @else
                            <span class="fs-7 fw-boldest text-secondary d-flex align-items-center">
                                <span class="bullet bullet-dot bg-secondary me-2"></span>{{ $data->lokasi->awal->kode }}
                            </span>
                            @endif
                        </div>
                        <div class="fw-bold fs-7 text-gray-600">{{ $data->lokasi->awal->alamat }}
                            <br>{{ $data->lokasi->awal->kota }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="fw-bold fs-7 text-gray-600 mb-1">Ke Lokasi:</div>
                        <div class="fw-bolder fs-6 text-dark d-flex align-items-center flex-wrap">
                            <span class="pe-2">{{ $data->lokasi->tujuan->keterangan }}</span>
                            @if(strtoupper(trim($data->lokasi->tujuan->kode)) == 'RK')
                            <span class="fs-7 fw-boldest text-primary d-flex align-items-center">
                                <span class="bullet bullet-dot bg-primary me-2"></span>{{ $data->lokasi->tujuan->kode }}
                            </span>
                            @elseif(strtoupper(trim($data->lokasi->tujuan->kode)) == 'OL')
                            <span class="fs-7 fw-boldest text-success d-flex align-items-center">
                                <span class="bullet bullet-dot bg-success me-2"></span>{{ $data->lokasi->tujuan->kode }}
                            </span>
                            @elseif(strtoupper(trim($data->lokasi->tujuan->kode)) == 'OB')
                            <span class="fs-7 fw-boldest text-danger d-flex align-items-center">
                                <span class="bullet bullet-dot bg-danger me-2"></span>{{ $data->lokasi->tujuan->kode }}
                            </span>
                            @elseif(strtoupper(trim($data->lokasi->tujuan->kode)) == 'OS')
                            <span class="fs-7 fw-boldest text-warning d-flex align-items-center">
                                <span class="bullet bullet-dot bg-warning me-2"></span>{{ $data->lokasi->tujuan->kode }}
                            </span>
                            @elseif(strtoupper(trim($data->lokasi->tujuan->kode)) == 'OP')
                            <span class="fs-7 fw-boldest text-info d-flex align-items-center">
                                <span class="bullet bullet-dot bg-info me-2"></span>{{ $data->lokasi->tujuan->kode }}
                            </span>
                            @else
                            <span class="fs-7 fw-boldest text-secondary d-flex align-items-center">
                                <span class="bullet bullet-dot bg-secondary me-2"></span>{{ $data->lokasi->tujuan->kode }}
                            </span>
                            @endif
                        </div>
                        <div class="fw-bold fs-7 text-gray-600">{{ $data->lokasi->tujuan->alamat }}
                            <br>{{ $data->lokasi->tujuan->kota }}
                        </div>
                    </div>
                </div>
                <div class="row g-5 mb-8">
                    <div class="col-sm-6">
                        <div class="fw-bold fs-7 text-gray-600 mb-1">Keterangan:</div>
                        <div class="fw-bolder text-dark">@if($data->keterangan == '')-@else {{ $data->keterangan }} @endif</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="fw-bold fs-7 text-gray-600 mb-1">User:</div>
                        <div class="fw-bolder text-dark">{{ $data->users }}</div>
                    </div>
                </div>
                <div class="row g-5 mb-8">
                    <div class="fw-bold fs-7 text-gray-600">Status:</div>
                    <div class="d-flex">
                        @if((double)$data->status->cetak == 1)
                        <span class="fs-8 fw-boldest badge badge-light-success me-2">
                            <i class="fa fa-check text-success me-2"></i>CETAK
                        </span>
                        @else
                        <span class="fs-8 fw-boldest badge badge-light-danger me-2">
                            <i class="fa fa-remove text-danger me-2"></i>CETAK
                        </span>
                        @endif
                        @if((double)$data->status->sj == 1)
                        <span class="fs-8 fw-boldest badge badge-light-success me-2">
                            <i class="fa fa-check text-success me-2"></i>SURAT JALAN
                        </span>
                        @else
                        <span class="fs-8 fw-boldest badge badge-light-danger me-2">
                            <i class="fa fa-remove text-danger me-2"></i>SURAT JALAN
                        </span>
                        @endif
                        @if((double)$data->status->validasi == 1)
                        <span class="fs-8 fw-boldest badge badge-light-success me-2">
                            <i class="fa fa-check text-success me-2"></i>VALIDASI
                        </span>
                        @else
                        <span class="fs-8 fw-boldest badge badge-light-danger me-2">
                            <i class="fa fa-remove text-danger me-2"></i>VALIDASI
                        </span>
                        @endif
                        @if((double)$data->status->marketplace->update == 1)
                        <span class="fs-8 fw-boldest badge badge-light-success me-2">
                            <i class="fa fa-check text-success me-2"></i>UPDATE MARKETPLACE
                        </span>
                        @else
                        <span class="fs-8 fw-boldest badge badge-light-danger me-2">
                            <i class="fa fa-remove text-danger me-2"></i>UPDATE MARKETPLACE
                        </span>
                        @endif
                    </div>
                </div>
                <div class="row g-5 mb-8">
                    <div id="tableDetailPemindahan"></div>
                </div>
                @if((double)$data->status->marketplace->show == 1)
                <div class="row g-5 mb-8">
                    <div class="d-flex">
                        <button id="btnUpdateStockAll" class="btn btn-primary" data-nomor_dokumen="{{ strtoupper(trim($data->nomor_dokumen)) }}">UPDATE STOCK MARKETPLACE</button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </form>
</div>

<div class="modal fade" tabindex="-2" id="modalResultPindahLokasi">
    <div class="modal-dialog">
        <div class="modal-content" id="modalResultPindahLokasiContent">
            <div class="modal-header">
                <h5 id="modalTitle" name="modalTitle" class="modal-title">Result Marketplace</h5>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <span class="svg-icon svg-icon-muted svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path opacity="0.3" d="M6 19.7C5.7 19.7 5.5 19.6 5.3 19.4C4.9 19 4.9 18.4 5.3 18L18 5.3C18.4 4.9 19 4.9 19.4 5.3C19.8 5.7 19.8 6.29999 19.4 6.69999L6.7 19.4C6.5 19.6 6.3 19.7 6 19.7Z" fill="currentColor"/>
                            <path d="M18.8 19.7C18.5 19.7 18.3 19.6 18.1 19.4L5.40001 6.69999C5.00001 6.29999 5.00001 5.7 5.40001 5.3C5.80001 4.9 6.40001 4.9 6.80001 5.3L19.5 18C19.9 18.4 19.9 19 19.5 19.4C19.3 19.6 19 19.7 18.8 19.7Z" fill="currentColor"/>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="modal-body">
                <div class="fv-row">
                    <div class="fw-bolder fs-7 text-gray-600 mb-4">Update Stock Marketplace:</div>
                    <div id="resultUpdateStock"></div>
                </div>
                <div class="fv-row mt-8">
                    <div class="fw-bolder fs-7 text-gray-600 mb-4">Update Status Product Marketplace:</div>
                    <div id="resultUpdateStatus"></div>
                </div>
                <div class="fv-row">
                    <div class="fw-bold fs-7 text-danger mb-4">* Data status product hanya mengupdate
                        <span class="fw-bolder fs-7 text-danger">"data update stock"</span> yang ber-status success
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-end">
                    <button id="btnFilterClose" name="btnClose" type="button" class="btn btn-light text-end" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const url = {
        'daftar_pemindahan': "{{ route('online.pemindahan.tokopedia.form.detail') }}",
        'update_per_part_number': "{{ route('online.pemindahan.tokopedia.form.update.part-number') }}",
        'update_status_per_part_number': "{{ route('online.pemindahan.tokopedia.form.update.status-part-number') }}",
        'update_per_dokumen': "{{ route('online.pemindahan.tokopedia.form.update.dokumen') }}",
    }
    const data = {
        'nomor_dokumen': "{{ strtoupper(trim($data->nomor_dokumen)) }}",
    }
</script>
<script src="{{ asset('assets/js/suma/online/tokopedia/pemindahan/form.js') }}?v={{ time() }}"></script>
@endpush
@endsection
