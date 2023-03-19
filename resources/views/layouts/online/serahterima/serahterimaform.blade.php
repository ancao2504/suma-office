@extends('layouts.main.index')
@section('title','Tokopedia')
@section('subtitle','Serah Terima')
@section('container')
<div class="row g-0">
    <form action="#">
        @csrf
        <div class="card card-flush">
            <div class="card-header align-items-center border-0 mt-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Serah Terima Ekspedisi</span>
                    <span class="text-muted fw-bold fs-7">Form serah terima ekspedisi online</span>
                </h3>
            </div>
            <div class="card-body">
                <div class="row g-5 mb-8">
                    <div class="col-sm-6">
                        <div class="fs-7 fw-bold text-gray-600 mb-1">Nomor Dokumen:</div>
                        <div class="fs-6 fw-bolder text-dark d-block">{{ strtoupper(trim($data->nomor_dokumen)) }}</div>
                        <div class="fs-7 fw-bolder text-muted">{{ date('d F Y', strtotime($data->tanggal)) }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="fs-7 fw-bold text-gray-600 mb-1">Ekspedisi:</div>
                        <div class="fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap">
                            <span class="pe-2">{{ trim($data->ekspedisi->nama) }}</span>
                            <span class="fs-7 text-danger d-flex align-items-center">
                                <span class="bullet bullet-dot bg-danger me-2"></span>{{ strtoupper(trim($data->ekspedisi->kode)) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row g-5 mb-8">
                    <div class="col-sm-6">
                        <div class="fs-7 fw-bold text-gray-600 mb-1">Tanggal & Jam:</div>
                        <div class="fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap d-block">
                            <span class="pe-2">{{ date('d M Y', strtotime($data->mulai->tanggal)) }}</span>
                            <span class="fs-7 text-danger d-flex align-items-center">
                                <span class="bullet bullet-dot bg-danger me-2"></span>{{ trim($data->mulai->jam) }}
                            </span>
                        </div>
                        <div class="fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap">
                            <span class="pe-2">{{ date('d M Y', strtotime($data->selesai->tanggal)) }}</span>
                            <span class="fs-7 text-danger d-flex align-items-center">
                                <span class="bullet bullet-dot bg-danger me-2"></span>{{ trim($data->selesai->jam) }}
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="fs-7 fw-bold text-gray-600 mb-1">Keterangan:</div>
                        <div class="fs-6 fw-bolder text-dark">@if(strtoupper(trim($data->keterangan)) == '')-@else {{ strtoupper(trim($data->keterangan)) }} @endif</div>
                    </div>
                </div>
                <div class="row g-5 mb-8">
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-300 align-middle">
                            <thead class="border">
                                <tr class="fs-8 fw-bolder text-muted">
                                    <th rowspan="2" class="w-50px ps-3 pe-3 text-center">No</th>
                                    <th rowspan="2" class="w-100px ps-3 pe-3 text-center">Nomor SJ</th>
                                    <th rowspan="2" class="w-100px ps-3 pe-3 text-center">Nomor Faktur</th>
                                    <th rowspan="2" class="w-200px ps-3 pe-3 text-center">Nomor Invoice</th>
                                    <th rowspan="2" class="w-100px ps-3 pe-3 text-center">Jml Koli</th>
                                    <th colspan="3" class="w-100px ps-3 pe-3 text-center">Action</th>
                                </tr>
                                <tr class="fs-8 fw-bolder text-muted">
                                    <th class="w-50px ps-3 pe-3 text-center">Marketplace</th>
                                    <th class="w-50px ps-3 pe-3 text-center">Internal</th>
                                    <th class="w-75px ps-3 pe-3 text-center">Cetak Label</th>
                                </tr>
                            </thead>
                            <tbody class="border">
                                @foreach($data->detail as $data_detail)
                                <tr>
                                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:center;">
                                        <span class="fs-7 fw-bolder text-gray-800">{{ $loop->iteration }}</span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:center;">
                                        <span class="fs-7 fw-bolder text-gray-800">{{ $data_detail->nomor_sj }}</span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:center;">
                                        <span class="fs-7 fw-bolder text-gray-800">{{ $data_detail->nomor_faktur }}</span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:tcenterop;">
                                        <span class="fs-7 fw-bolder text-gray-800">{{ $data_detail->nomor_invoice }}</span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                        <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data_detail->jumlah_koli) }}</span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:center;">
                                    @if($data_detail->status_mp_detail == 0)
                                    @if($data_detail->status_mp_aktif == 1)
                                        @if(strtoupper(trim($data_detail->marketplace)) == 'TOKOPEDIA')
                                        <button id="btnRequestPickupTokopedia" class="btn btn-icon btn-sm btn-secondary" type="button"
                                            data-nomor_dokumen="{{ strtoupper(trim($data->nomor_dokumen)) }}"
                                            data-nomor_invoice="{{ strtoupper(trim($data_detail->nomor_invoice)) }}">
                                            <img src="{{ asset('assets/images/logo/tokopedia_lg.png') }}" class="h-30px">
                                        </button>
                                        @endif
                                        @if(strtoupper(trim($data_detail->marketplace)) == 'SHOPEE')
                                        <button id="btnRequestPickupShopee" class="btn btn-icon btn-sm btn-secondary" type="button"
                                            data-nomor_dokumen="{{ strtoupper(trim($data->nomor_dokumen)) }}"
                                            data-nomor_invoice="{{ strtoupper(trim($data_detail->nomor_invoice)) }}">
                                            <img src="{{ asset('assets/images/logo/shopee_lg.png') }}" class="h-30px">
                                        </button>
                                        @endif
                                    @endif
                                    @endif
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:center;">
                                        @if($data_detail->status_mp_detail == 0)
                                        <button id="btnUpdateStatus" class="btn btn-icon btn-sm btn-danger" type="button"
                                            data-nomor_faktur="{{ strtoupper(trim($data_detail->nomor_faktur)) }}">
                                            <i class="fa fa-database" aria-hidden="true"></i>
                                        </button>
                                        @endif
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:center;">
                                        @if($data_detail->status_mp_detail == 0)
                                            @if(strtoupper(trim($data_detail->marketplace)) == 'TOKOPEDIA')
                                            <button id="btnCetakLabelTokopedia" class="btn btn-icon btn-sm btn-success" type="button"
                                                data-nomor_invoice="{{ strtoupper(trim($data_detail->nomor_invoice)) }}">
                                                <i class="fa fa-file-text" aria-hidden="true"></i>
                                            </button>
                                            @endif
                                            @if(strtoupper(trim($data_detail->marketplace)) == 'SHOPEE')
                                            <button id="btnCetakLabelShopee" class="btn btn-icon btn-sm btn-warning" type="button"
                                                data-nomor_invoice="{{ strtoupper(trim($data_detail->nomor_invoice)) }}">
                                                <i class="fa fa-file-text" aria-hidden="true"></i>
                                            </button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" tabindex="-2" id="modalRequestPickupShopee">
    <div class="modal-dialog">
        <div class="modal-content" id="modalRequestPickupShopeeContent">
            <form action="#">
                <div class="modal-header">
                    <h5 id="modalTitle" name="modalTitle" class="modal-title">Request Pickup Shopee</h5>
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
                        <label for="inputNomorInvoice" class="form-label">Nomor Invoice</label>
                        <input id="inputNomorInvoice" type="text" class="form-control" placeholder="" readonly/>
                    </div>
                    <div class="fv-row mt-8">
                        <div class="w-lg-100" data-kt-buttons="true">
                            <label class="d-flex flex-stack mb-5 cursor-pointer">
                                <span class="d-flex align-items-center me-2">
                                    <span class="symbol symbol-50px me-6">
                                        <span class="symbol-label bg-light-danger">
                                            <span class="svg-icon svg-icon-1 svg-icon-danger">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M18.041 22.041C18.5932 22.041 19.041 21.5932 19.041 21.041C19.041 20.4887 18.5932 20.041 18.041 20.041C17.4887 20.041 17.041 20.4887 17.041 21.041C17.041 21.5932 17.4887 22.041 18.041 22.041Z" fill="currentColor"/>
                                                    <path opacity="0.3" d="M6.04095 22.041C6.59324 22.041 7.04095 21.5932 7.04095 21.041C7.04095 20.4887 6.59324 20.041 6.04095 20.041C5.48867 20.041 5.04095 20.4887 5.04095 21.041C5.04095 21.5932 5.48867 22.041 6.04095 22.041Z" fill="currentColor"/>
                                                    <path opacity="0.3" d="M7.04095 16.041L19.1409 15.1409C19.7409 15.1409 20.141 14.7409 20.341 14.1409L21.7409 8.34094C21.9409 7.64094 21.4409 7.04095 20.7409 7.04095H5.44095L7.04095 16.041Z" fill="currentColor"/>
                                                    <path d="M19.041 20.041H5.04096C4.74096 20.041 4.34095 19.841 4.14095 19.541C3.94095 19.241 3.94095 18.841 4.14095 18.541L6.04096 14.841L4.14095 4.64095L2.54096 3.84096C2.04096 3.64096 1.84095 3.04097 2.14095 2.54097C2.34095 2.04097 2.94096 1.84095 3.44096 2.14095L5.44096 3.14095C5.74096 3.24095 5.94096 3.54096 5.94096 3.84096L7.94096 14.841C7.94096 15.041 7.94095 15.241 7.84095 15.441L6.54096 18.041H19.041C19.641 18.041 20.041 18.441 20.041 19.041C20.041 19.641 19.641 20.041 19.041 20.041Z" fill="currentColor"/>
                                                </svg>
                                            </span>
                                        </span>
                                    </span>
                                    <span class="d-flex flex-column">
                                        <span class="fw-bolder fs-6">Drop Off</span>
                                        <span class="fs-7 text-muted">Penjual mengantar paket ke pihak logistic</span>
                                    </span>
                                </span>
                                <span class="form-check form-check-custom form-check-solid">
                                    <input id="inputJenisMetodePengiriman" class="form-check-input" type="radio" name="category" value="0" disabled>
                                </span>
                            </label>
                            <label class="d-flex flex-stack mb-5 cursor-pointer">
                                <span class="d-flex align-items-center me-2">
                                    <span class="symbol symbol-50px me-6">
                                        <span class="symbol-label bg-light-primary">
                                            <span class="svg-icon svg-icon-1 svg-icon-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M20 8H16C15.4 8 15 8.4 15 9V16H10V17C10 17.6 10.4 18 11 18H16C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18H21C21.6 18 22 17.6 22 17V13L20 8Z" fill="currentColor"/>
                                                    <path opacity="0.3" d="M20 18C20 19.1 19.1 20 18 20C16.9 20 16 19.1 16 18C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18ZM15 4C15 3.4 14.6 3 14 3H3C2.4 3 2 3.4 2 4V13C2 13.6 2.4 14 3 14H15V4ZM6 16C4.9 16 4 16.9 4 18C4 19.1 4.9 20 6 20C7.1 20 8 19.1 8 18C8 16.9 7.1 16 6 16Z" fill="currentColor"/>
                                                </svg>
                                            </span>
                                        </span>
                                    </span>
                                    <span class="d-flex flex-column">
                                        <span class="fw-bolder fs-6">Pickup</span>
                                        <span class="fs-7 text-muted">Pihak logistic mengambil paket ke penjual</span>
                                    </span>
                                </span>
                                <span class="form-check form-check-custom form-check-solid">
                                    <input id="inputJenisMetodePengiriman" class="form-check-input" type="radio" name="category" value="1" checked>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="fv-row mt-8">
                        <label for="selectTanggalJamPickup" class="form-label">Tanggal & Jam Pickup</label>
                        <select id="selectTanggalJamPickup" class="form-select" aria-label="Select example">
                            <option value="">Pilih tanggal & jam pickup</option>
                        </select>
                    </div>
                    <div class="fv-row mt-8">
                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                            <span class="svg-icon svg-icon-2tx svg-icon-warning me-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"></rect>
                                    <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="currentColor"></rect>
                                    <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="currentColor"></rect>
                                </svg>
                            </span>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-bold">
                                    <h4 class="text-gray-900 fw-bolder">Alamat Toko:</h4>
                                    <div id="inputIdAlamatSeller" class="fs-6 text-gray-700"></div>
                                    <div id="inputAlamatSeller" class="fs-6 text-gray-700"></div>
                                    <div id="inputKotaSeller" class="fs-6 text-gray-700"></div>
                                    <div id="inputProvinsiSeller" class="fs-6 text-gray-700"></div>
                                    <div id="inputKodePosSeller" class="fs-6 text-gray-700"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="text-end">
                        <button id="btnSimpanRequestPickupShopee" name="btnSimpanRequestPickupShopee" type="button" class="btn btn-primary text-end">Simpan</button>
                        <button id="btnFilterClose" name="btnClose" type="button" class="btn btn-light text-end" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const url = {
        'proses_update_status': "{{ route('online.serahterima.form.update-status') }}",
        'proses_request_pickup_tokopedia': "{{ route('online.serahterima.form.tokopedia-request-pickup') }}",
        'proses_cetak_label_tokopedia': "{{ route('online.serahterima.form.cetak-label-tokopedia') }}",
        'proses_request_pickup_shopee': "{{ route('online.serahterima.form.shopee-request-pickup') }}",
        'data_request_pickup_shopee': "{{ route('online.serahterima.form.data-shopee-request-pickup') }}",
        'proses_cetak_label_shopee': "{{ route('online.serahterima.form.cetak-label-shopee') }}",
    }
</script>
<script src="{{ asset('assets/js/suma/online/serahterima/form.js') }}?v={{ time() }}"></script>
@endpush
@endsection
