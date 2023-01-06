$(document).ready(function () {
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

    $('body').on('click', '#btnFormPembayaranFaktur', function () {
        var nomor_faktur = $(this).data('kode');
        var _token = $('input[name="_token"]').val();

        loading.block();
        $.ajax({
            url: url.detail_per_faktur,
            method: "post",
            data: { nomor_faktur: nomor_faktur, _token: _token },

            success: function (response) {
                loading.release();

                if(response.status == true) {
                    $('#modalTitlePerNomorFaktur').html('Pembayaran Per-Faktur');
                    $('#modalPembayaranFakturNomorFaktur').html(response.data.nomor_faktur);
                    $('#modalPembayaranFakturTanggalFaktur').html(moment(response.data.tanggal_faktur).format('DD MMMM YYYY'));
                    $('#modalPembayaranFakturKodeSales').html(response.data.kode_sales);
                    $('#modalPembayaranFakturNamaSales').html(response.data.nama_sales);
                    $('#modalPembayaranFakturKodeDealer').html(response.data.kode_dealer);
                    $('#modalPembayaranFakturNamaDealer').html(response.data.nama_dealer);
                    $('#modalPembayaranFakturKeterangan').html(response.data.keterangan);
                    $('#modalPembayaranFakturTotalFaktur').html(formatNumber(response.data.total_faktur));

                    if (response.data.total_faktur > response.data.total_pembayaran) {
                        $('#modalPembayaranFakturTotalPembayaran').html('<span class="fs-xl-2x fs-3 fw-boldest text-danger">' + formatNumber(response.data.total_pembayaran) + '</span>');
                    } else {
                        $('#modalPembayaranFakturTotalPembayaran').html('<span class="fs-xl-2x fs-3 fw-boldest text-success">' + formatNumber(response.data.total_pembayaran) + '</span>');
                    }

                    $('#detail_pembayaran_per_faktur').html(response.data.view_detail);

                    $('#modalPembayaranPerFaktur').modal('show');

                    $('#modalPembayaranPerFaktur').on('shown.bs.modal', function () {
                    });
                } else {
                    Swal.fire({
                        text: response.message,
                        icon: "warning",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-warning"
                        }
                    });
                }
            },
            error: function () {
                loading.release();
                Swal.fire({
                    text: 'Server tidak merespon, coba lagi',
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-danger"
                    }
                });
            }
        })
    });

    $('body').on('click', '#formPembayaranNomorBPK', function () {
        var nomor_bpk = $(this).data('kode');
        var _token = $('input[name="_token"]').val();

        loading.block();

        $.ajax({
            url: url.detail_per_bpk,
            method: "post",
            data: { nomor_bpk: nomor_bpk, _token: _token },

            success: function (response) {
                loading.release();

                if (response.status == true) {
                    $('#modalPembayaranBpkNomorBukti').html(response.data.nomor_bukti);
                    $('#modalPembayaranBpkTanggalInput').html(moment(response.data.tanggal_input).format('DD MMMM YYYY'));
                    $('#modalPembayaranBpkKodeSales').html(response.data.kode_sales);
                    $('#modalPembayaranBpkNamaSales').html(response.data.nama_sales);
                    $('#modalPembayaranBpkKodeDealer').html(response.data.kode_dealer);
                    $('#modalPembayaranBpkNamaDealer').html(response.data.nama_dealer);
                    if (response.data.tunai_giro == 'G') {
                        $('#modalPembayaranBpkTunaiGiro').html('GIRO');
                    } else {
                        $('#modalPembayaranBpkTunaiGiro').html('TUNAI');
                    }
                    $('#modalPembayaranBpkNomorGiro').html(response.data.nomor_giro);
                    $('#modalPembayaranBpkTanggalJatuhTempo').html(moment(response.data.tanggal_jtp_giro).format('DD MMMM YYYY'));
                    $('#modalPembayaranBpkBank').html(response.data.nama_bank);
                    $('#modalPembayaranBpkTotal').html(formatNumber(response.data.total_pembayaran));

                    $('#status_realisasi').html(response.data.status_realisasi);

                    $('#detail_pembayaran_per_nomor_bpk').html(response.data.view_detail);

                    $('#modalPembayaranBpk').modal('show');

                    $('#modalPembayaranBpk').on('shown.bs.modal', function () {
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
