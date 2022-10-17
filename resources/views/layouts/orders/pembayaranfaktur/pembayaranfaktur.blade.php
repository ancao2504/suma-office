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
                <form id="pembayaranPerFakturForm" name="pembayaranPerFakturForm" autofill="off" autocomplete="off" method="POST" action="#">
                    <div class="modal-header">
                        <h5 id="modalTitlePerNomorFaktur" name="modalTitlePerNomorFaktur" class="modal-title"></h5>
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
                        <span id="messageErrorPembayaranPerFaktur"></span>
                        <div class="row">
                            <div class="col-lg-6 mb-6">
                                <div class="row">
                                    <label class="fs-7 fw-bold text-gray-500">Nomor Faktur :</label>
                                    <span id="textNomorFaktur" name="nomor_faktur" class="fs-7 fw-bolder text-dark"></span>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-6">
                                <div class="row">
                                    <label class="fs-7 fw-bold text-gray-500">Tanggal Faktur :</label>
                                    <span id="textTanggalFaktur" name="tanggal_faktur" class="fs-7 fw-bold text-gray-800"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 mb-6">
                                <label class="fs-7 fw-bold text-gray-500">Salesman :</label>
                                <div class="col-lg-12 mt-1">
                                    <span id="textKodeSales" name="kode_sales" class="fs-7 fw-bolder text-info"></span>
                                    <span class="fw-bolder fs-7 text-gray-800 ms-1 me-1">-</span>
                                    <span id="textNamaSales" name="nama_sales" class="fs-7 fw-bold text-gray-800"></span>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-6">
                                <label class="fs-7 fw-bold text-gray-500">Dealer :</label>
                                <div class="col-lg-12 mt-1">
                                    <span id="textKodeDealer" name="kode_dealer" class="fs-7 fw-bolder text-primary"></span>
                                    <span class="fw-bolder fs-7 text-gray-800 ms-1 me-1">-</span>
                                    <span id="textNamaDealer" name="nama_dealer" class="fs-7 fw-bold text-gray-800"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 mb-8">
                                <div class="row">
                                    <label class="fs-7 fw-bold text-gray-500">Total Faktur :</label>
                                    <span id="textTotalFaktur" name="total_faktur" class="fw-bolder fs-7 text-danger"></span>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-8">
                                <div class="row">
                                    <label class="fs-7 fw-bold text-gray-500">Total Pembayaran :</label>
                                    <span id="total_pembayaran"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <span id="detail_pembayaran_pernomor_faktur"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade bs-example-modal-xl" tabindex="-1" id="modalPembayaranPerNomorBpk">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="modalContentPembayaranPerNomorBpk">
                <form id="pembayaranFormPerNomorBpk" name="pembayaranFormPerNomorBpk" autofill="off" autocomplete="off" method="POST" action="#">
                    <div class="modal-header">
                        <h5 id="modalTitlePerNomorBpk" name="modalTitlePerNomorBpk" class="modal-title"></h5>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <span class="svg-icon svg-icon-muted svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M6 19.7C5.7 19.7 5.5 19.6 5.3 19.4C4.9 19 4.9 18.4 5.3 18L18 5.3C18.4 4.9 19 4.9 19.4 5.3C19.8 5.7 19.8 6.29999 19.4 6.69999L6.7 19.4C6.5 19.6 6.3 19.7 6 19.7Z" fill="currentColor"/>
                                    <path d="M18.8 19.7C18.5 19.7 18.3 19.6 18.1 19.4L5.40001 6.69999C5.00001 6.29999 5.00001 5.7 5.40001 5.3C5.80001 4.9 6.40001 4.9 6.80001 5.3L19.5 18C19.9 18.4 19.9 19 19.5 19.4C19.3 19.6 19 19.7 18.8 19.7Z" fill="currentColor"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div id="modalBodyPembayaranPerNomorBpk" name="modalBodyPembayaranPerNomorBpk" class="modal-body">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="col-lg-12">
                                <span id="messageErrorPembayaranPerNomorBpk"></span>
                                <div class="row">
                                    <div class="col-lg-6 mb-6">
                                        <div class="row">
                                            <label class="fs-7 fw-bold text-gray-500">Nomor Bukti :</label>
                                            <span id="textNomorBukti" name="nomor_bukti" class="fs-7 fw-bolder text-dark mt-1"></span>
                                            <span id="textTanggalInput" name="tanggal_input" class="fw-bold fs-7 text-gray-600"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-6">
                                        <div class="row">
                                            <label class="fw-bold text-muted fs-7">Nomor Giro :</label>
                                            <span id="textNomorGiro" name="nomor_giro" class="fs-7 fw-bolder text-dark mt-1"></span>
                                            <span id="textTanggalJtpGiro" name="tanggal_jtp_giro" class="fw-bold fs-7 text-gray-600"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 mb-6">
                                        <label class="fs-7 fw-bold text-gray-500">Salesman :</label>
                                        <div class="col-lg-12 mt-1">
                                            <span id="textKodeSalesman" name="kode_sales" class="fs-7 fw-bolder text-info"></span>
                                            <span class="fw-bolder fs-7 text-gray-800 ms-1 me-1">-</span>
                                            <span id="textNamaSalesman" name="nama_sales" class="fs-7 fw-bold text-gray-800"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-6">
                                        <label class="fs-7 fw-bold text-gray-500">Dealer :</label>
                                        <div class="col-lg-12 mt-1">
                                            <span id="textKodeDlr" name="kode_dealer" class="fs-7 fw-bolder text-primary"></span>
                                            <span class="fw-bolder fs-7 text-gray-800 ms-1 me-1">-</span>
                                            <span id="textNamaDlr" name="nama_dealer" class="fs-7 fw-bold text-gray-800"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 mb-6">
                                        <div class="row">
                                            <label class="fs-7 fw-bold text-gray-500">Tunai/Giro :</label>
                                            <span id="textTunaiGiro" name="tunai_giro"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-6">
                                        <div class="row">
                                            <label class="fs-7 fw-bold text-gray-500">Nama Bank :</label>
                                            <div class="d-flex">
                                                <span id="textNamaBank" name="nama_bank" class="badge badge-primary fs-8 fw-bolder"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 mb-8">
                                        <div class="row">
                                            <label class="fs-7 fw-bold text-gray-500">Total Pembayaran :</label>
                                            <span id="textTotalPembayaran" name="total_pembayaran" class="fw-bolder fs-7 text-gray-800"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 mb-10">
                                        <div class="row">
                                            <span id="status_realisasi"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <span id="detail_pembayaran_per_nomor_bpk"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script type="text/javascript">
            function formatNumber(val) {
                var sign = 1;
                if (val < 0) {
                    sign = -1;
                    val = -val;
                }

                let num = val.toString().includes('.') ? val.toString().split('.')[0] : val.toString();

                while (/(\d+)(\d{3})/.test(num.toString())) {
                    num = num.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
                }

                if (val.toString().includes('.')) {
                    num = num + '.' + val.toString().split('.')[1];
                }

                return sign < 0 ? '-' + num : num;
            }

            $(document).ready(function() {
                $('body').on('click', '#viewPembayaranFaktur', function () {
                    var nomor_faktur = $(this).data('kode');
                    var _token = $('input[name="_token"]').val();

                    blockIndex.block();

                    $.ajax({
                        url: "{{ route('orders.pembayaran-faktur-detail-per-faktur') }}",
                        method: "POST",
                        data: { nomor_faktur: nomor_faktur, _token: _token },

                        success:function(response) {
                            blockIndex.release();

                            if (response.status == true) {
                                $('#modalTitlePerNomorFaktur').html('Pembayaran Per-Faktur');
                                $('#textNomorFaktur').html(response.data.nomor_faktur);
                                $('#textTanggalFaktur').html(response.data.tanggal_faktur);
                                $('#textKodeSales').html(response.data.kode_sales);
                                $('#textNamaSales').html(response.data.nama_sales);
                                $('#textKodeDealer').html(response.data.kode_dealer);
                                $('#textNamaDealer').html(response.data.nama_dealer);
                                $('#textTotalFaktur').html(formatNumber('Rp. '+formatNumber(response.data.total_faktur)));

                                if(response.data.total_faktur > response.data.total_pembayaran) {
                                    $('#total_pembayaran').html('<span class="fw-bolder fs-7 text-danger">'+'Rp. '+formatNumber(response.data.total_pembayaran)+'</span>');
                                } else {
                                    $('#total_pembayaran').html('<span class="fw-bolder fs-7 text-success">'+'Rp. '+formatNumber(response.data.total_pembayaran)+'</span>');
                                }

                                $('#detail_pembayaran_pernomor_faktur').html(response.data.view_detail);

                                $('#modalPembayaranPerFaktur').modal('show');

                                $('#modalPembayaranPerFaktur').on('shown.bs.modal', function () {
                                });
                            } else {
                                Swal.fire({
                                    text: response.message,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-danger"
                                    }
                                });
                            }
                        },
                        error:function() {
                            blockIndex.release();
                        }
                    })
                });

                $('body').on('click', '#viewPembayaranPerNomorBpk', function () {
                    var nomor_bpk = $(this).data('kode');
                    var _token = $('input[name="_token"]').val();

                    blockIndex.block();

                    $.ajax({
                        url: "{{ route('orders.pembayaran-faktur-detail-per-bpk') }}",
                        method: "POST",
                        data: { nomor_bpk: nomor_bpk, _token: _token },

                        success:function(response) {
                            blockIndex.release();

                            if (response.status == true) {
                                $('#modalTitlePerNomorBpk').html('Bukti Pembayaran Kas');
                                $('#textNomorBukti').html(response.data.nomor_bukti);
                                $('#textTanggalInput').html(response.data.tanggal_input);
                                $('#textKodeSalesman').html(response.data.kode_sales);
                                $('#textNamaSalesman').html(response.data.nama_sales);
                                $('#textKodeDlr').html(response.data.kode_dealer);
                                $('#textNamaDlr').html(response.data.nama_dealer);
                                if(response.data.tunai_giro == 'G') {
                                    $('#textTunaiGiro').html('<span class="badge badge-info fs-8 fw-bolder">GIRO</span>');
                                } else {
                                    $('#textTunaiGiro').html('<span class="badge badge-success fs-8 fw-bolder">TUNAI</span>');
                                }
                                $('#textNomorGiro').html(response.data.nomor_giro);
                                $('#textTanggalJtpGiro').html(response.data.tanggal_jtp_giro);
                                $('#textAccountBank').html(response.data.account_bank);
                                $('#textNamaBank').html(response.data.nama_bank);
                                $('#textTotalPembayaran').html(formatNumber('Rp. '+formatNumber(response.data.total_pembayaran)));

                                $('#status_realisasi').html(response.data.status_realisasi);

                                $('#detail_pembayaran_per_nomor_bpk').html(response.data.view_detail);

                                $('#modalPembayaranPerNomorBpk').modal('show');

                                $('#modalPembayaranPerNomorBpk').on('shown.bs.modal', function () {
                                });
                            } else {
                                Swal.fire({
                                    text: response.message,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-danger"
                                    }
                                });
                            }
                        },
                        error:function() {
                            blockIndex.release();
                        }
                    })
                });
            });
        </script>
    @endpush
@endsection
