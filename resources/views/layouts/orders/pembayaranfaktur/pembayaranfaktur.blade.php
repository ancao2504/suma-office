@extends('layouts.main.index')
@section('title','Orders')
@section('subtitle','Pembayaran')
@section('container')
<div class="row g-0">
    <div class="card card-flush">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Pembayaran Faktur</span>
                <span class="text-muted fw-bold fs-7">Daftar pembayaran per-nomor faktur</span>
            </h3>
        </div>
        <div class="card-header">
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder">
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ ($title_menu == 'Belum Terbayar') ? 'active' : '' }}"
                        href="{{ url('/orders/pembayaranfaktur/belumterbayar') }}">Belum Terbayar</a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ ($title_menu == 'Terbayar') ? 'active' : '' }}"
                        href="{{ url('/orders/pembayaranfaktur/terbayar') }}">Terbayar</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="mt-4"></div>
    @yield('containerpembayaranfaktur')
</div>

<div class="modal fade bs-example-modal-xl" tabindex="-1" id="modalPembayaranPerFaktur">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="modalContentPembayaranPerFaktur">
            <div class="modal-header">
                <h5 class="modal-title">Pembayaran Faktur</h5>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <span class="svg-icon svg-icon-muted svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path opacity="0.3" d="M6 19.7C5.7 19.7 5.5 19.6 5.3 19.4C4.9 19 4.9 18.4 5.3 18L18 5.3C18.4 4.9 19 4.9 19.4 5.3C19.8 5.7 19.8 6.29999 19.4 6.69999L6.7 19.4C6.5 19.6 6.3 19.7 6 19.7Z" fill="currentColor"/>
                            <path d="M18.8 19.7C18.5 19.7 18.3 19.6 18.1 19.4L5.40001 6.69999C5.00001 6.29999 5.00001 5.7 5.40001 5.3C5.80001 4.9 6.40001 4.9 6.80001 5.3L19.5 18C19.9 18.4 19.9 19 19.5 19.4C19.3 19.6 19 19.7 18.8 19.7Z" fill="currentColor"/>
                        </svg>
                    </span>
                </div>
            </div>
            <div id="modalBodyPembayaranPerFaktur" name="modalBodyPembayaranPerFaktur" class="modal-body">
                <div class="d-flex justify-content-between flex-column flex-md-row">
                    <div class="flex-grow-1 mb-13">
                        <div id="detail_pembayaran_per_faktur"></div>
                        <div class="d-flex flex-column mw-md-300px w-100">
                            <div class="fw-bold fs-5 mb-3 text-dark00">DATA FAKTUR:</div>
                            <div class="d-flex flex-stack text-gray-800 mb-3 fs-6">
                                <div class="fw-bold pe-5">Nomor Faktur:</div>
                                <div id="modalPembayaranFakturNomorFaktur" class="fw-norma"></div>
                            </div>
                            <div class="d-flex flex-stack text-gray-800 mb-3 fs-6">
                                <div class="fw-bold pe-5">Tanggal:</div>
                                <div id="modalPembayaranFakturTanggalFaktur" class="fw-norma"></div>
                            </div>
                            <div class="d-flex flex-stack text-gray-800 fs-6">
                                <div class="fw-bold pe-5">Keterangan:</div>
                                <div id="modalPembayaranFakturKeterangan" class="fw-norma"></div>
                            </div>
                        </div>
                    </div>
                    <div class="border-end d-none d-md-block mh-450px mx-9"></div>
                    <div class="pt-2 text-end">
                        <div class="fs-5 fw-bold text-gray-400">PEMBAYARAN</div>
                        <div id="modalPembayaranFakturTotalPembayaran"></div>

                        <div class="fs-5 fw-bold text-gray-400 mt-6">FAKTUR</div>
                        <div id="modalPembayaranFakturTotalFaktur" class="fs-xl-2x fs-3 fw-boldest"></div>

                        <div class="border-bottom w-100 my-7 my-lg-16"></div>
                        <div class="text-gray-400 fs-6 fw-bold mb-3">SALESMAN</div>
                        <div id="modalPembayaranFakturNamaSales" class="fs-6 text-gray-800 fw-bold"></div>
                        <div id="modalPembayaranFakturKodeSales" class="fs-6 text-gray-600 fw-boldest mb-8"></div>
                        <div class="text-gray-400 fs-6 fw-bold mb-3">DEALER</div>
                        <div id="modalPembayaranFakturNamaDealer" class="fs-6 text-gray-800 fw-bold"></div>
                        <div id="modalPembayaranFakturKodeDealer" class="fs-6 text-gray-600 fw-boldest mb-8"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-xl" tabindex="-1" id="modalPembayaranBpk">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="modalContentNomorBpk">
            <div class="modal-header">
                <h5 class="modal-title">Bukti Penerimaan Kas</h5>
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
                <div class="d-flex justify-content-between flex-column flex-md-row">
                    <div class="flex-grow-1 mb-13">
                        <div id="detail_pembayaran_per_nomor_bpk"></div>
                        <div class="d-flex flex-column mw-md-300px w-200">
                            <div class="fw-bold fs-5 mb-3 text-dark">DATA PEMBAYARAN:</div>
                            <div class="d-flex flex-stack text-gray-800 mb-3 fs-6">
                                <div class="fw-bold pe-5">Nomor Bukti:</div>
                                <div id="modalPembayaranBpkNomorBukti" class="fw-norma"></div>
                            </div>
                            <div class="d-flex flex-stack text-gray-800 mb-3 fs-6">
                                <div class="fw-bold pe-5">Tanggal:</div>
                                <div id="modalPembayaranBpkTanggalInput" class="fw-norma"></div>
                            </div>
                            <div class="d-flex flex-stack text-gray-800 mb-3 fs-6">
                                <div class="fw-bold pe-5">Bank:</div>
                                <div id="modalPembayaranBpkBank" class="fw-norma"></div>
                            </div>
                            <div class="d-flex flex-stack text-gray-800 mb-3 fs-6">
                                <div class="fw-bold pe-5">Tunai/Giro:</div>
                                <div id="modalPembayaranBpkTunaiGiro" class="fw-norma"></div>
                            </div>
                        </div>
                        <div class="d-flex flex-column mw-md-300px w-100 mt-12">
                            <div class="fw-bold fs-5 mb-3 text-dark">DATA GIRO:</div>
                            <div class="d-flex flex-stack text-gray-800 mb-3 fs-6">
                                <div class="fw-bold pe-5">Nomor Giro:</div>
                                <div id="modalPembayaranBpkNomorGiro" class="fw-norma"></div>
                            </div>
                            <div class="d-flex flex-stack text-gray-800 mb-3 fs-6">
                                <div class="fw-bold pe-5">Jatuh Tempo Giro:</div>
                                <div id="modalPembayaranBpkTanggalJatuhTempo" class="fw-norma"></div>
                            </div>
                        </div>
                    </div>
                    <div class="border-end d-none d-md-block mh-450px mx-9"></div>
                    <div class="text-end pt-2">
                        <div class="fs-5 fw-bold text-gray-400">TOTAL</div>
                        <div id="modalPembayaranBpkTotal" class="fs-xl-2x fs-3 fw-boldest text-dark"></div>

                        <div class="border-bottom w-100 my-7 my-lg-16"></div>
                        <div class="text-gray-400 fs-6 fw-bold mb-3">SALESMAN</div>
                        <div id="modalPembayaranBpkNamaSales" class="fs-6 text-gray-800 fw-bold"></div>
                        <div id="modalPembayaranBpkKodeSales" class="fs-6 text-gray-600 fw-boldest mb-8"></div>
                        <div class="text-gray-400 fs-6 fw-bold mb-3">DEALER</div>
                        <div id="modalPembayaranBpkNamaDealer" class="fs-6 text-gray-800 fw-bold"></div>
                        <div id="modalPembayaranBpkKodeDealer" class="fs-6 text-gray-600 fw-boldest mb-8"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script type="text/javascript">
    const url = {
        'detail_per_faktur': "{{ route('orders.pembayaranfaktur.detail.faktur') }}",
        'detail_per_bpk': "{{ route('orders.pembayaranfaktur.detail.bpk') }}",
    }
</script>
<script src="{{ asset('assets/js/suma/orders/pembayaranfaktur/pembayaranfaktur.js') }}?v={{ time() }}"></script>
@endpush
@endsection
