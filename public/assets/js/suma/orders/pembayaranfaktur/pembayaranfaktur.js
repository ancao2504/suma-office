
$(document).ready(function () {


    // jika terdapat form submit
    $('form').submit(function () {
        loading.block();
    });
    // end jika terdapat form submit

    // ajax start
    $(document).ajaxStart(function () {
        loading.block();
    });
    // end ajax start
    // ajax stop
    $(document).ajaxStop(function () {
        loading.release();
    });
    // end ajax stop

    // terdapat elemet a click yang href tidak kosong
    $('a').click(function () {
        var href = $(this).attr('href');
        if (href != undefined && href != '') {
            loading.block();
        }
    });

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

    $('body').on('click', '#viewPembayaranFaktur', function () {
        var nomor_faktur = $(this).data('kode');
        var _token = $('input[name="_token"]').val();

        loading.block();

        $.ajax({
            url: url.pembayaran_faktur_detail_per_faktur,
            method: "POST",
            data: { nomor_faktur: nomor_faktur, _token: _token },

            success: function (response) {
                loading.release();

                if (response.status == true) {
                    $('#modalTitlePerNomorFaktur').html('Pembayaran Per-Faktur');
                    $('#textNomorFaktur').html(response.data.nomor_faktur);
                    $('#textTanggalFaktur').html(response.data.tanggal_faktur);
                    $('#textKodeSales').html(response.data.kode_sales);
                    $('#textNamaSales').html(response.data.nama_sales);
                    $('#textKodeDealer').html(response.data.kode_dealer);
                    $('#textNamaDealer').html(response.data.nama_dealer);
                    $('#textTotalFaktur').html(formatNumber('Rp. ' + formatNumber(response.data.total_faktur)));

                    if (response.data.total_faktur > response.data.total_pembayaran) {
                        $('#total_pembayaran').html('<span class="fw-bolder fs-7 text-danger">' + 'Rp. ' + formatNumber(response.data.total_pembayaran) + '</span>');
                    } else {
                        $('#total_pembayaran').html('<span class="fw-bolder fs-7 text-success">' + 'Rp. ' + formatNumber(response.data.total_pembayaran) + '</span>');
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
            error: function () {
                loading.release();
            }
        })
    });

    $('body').on('click', '#viewPembayaranPerNomorBpk', function () {
        var nomor_bpk = $(this).data('kode');
        var _token = $('input[name="_token"]').val();

        loading.block();

        $.ajax({
            url: url.pembayaran_faktur_detail_per_bpk,
            method: "POST",
            data: { nomor_bpk: nomor_bpk, _token: _token },

            success: function (response) {
                loading.release();

                if (response.status == true) {
                    $('#modalTitlePerNomorBpk').html('Bukti Pembayaran Kas');
                    $('#textNomorBukti').html(response.data.nomor_bukti);
                    $('#textTanggalInput').html(response.data.tanggal_input);
                    $('#textKodeSalesman').html(response.data.kode_sales);
                    $('#textNamaSalesman').html(response.data.nama_sales);
                    $('#textKodeDlr').html(response.data.kode_dealer);
                    $('#textNamaDlr').html(response.data.nama_dealer);
                    if (response.data.tunai_giro == 'G') {
                        $('#textTunaiGiro').html('<span class="badge badge-info fs-8 fw-bolder">GIRO</span>');
                    } else {
                        $('#textTunaiGiro').html('<span class="badge badge-success fs-8 fw-bolder">TUNAI</span>');
                    }
                    $('#textNomorGiro').html(response.data.nomor_giro);
                    $('#textTanggalJtpGiro').html(response.data.tanggal_jtp_giro);
                    $('#textAccountBank').html(response.data.account_bank);
                    $('#textNamaBank').html(response.data.nama_bank);
                    $('#textTotalPembayaran').html(formatNumber('Rp. ' + formatNumber(response.data.total_pembayaran)));

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
            error: function () {
                loading.release();
            }
        })
    });
});