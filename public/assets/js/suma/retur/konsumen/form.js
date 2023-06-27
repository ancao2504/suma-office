let disc2, disc1;

let date = {
    tgl_claim: '',
    tgl_terima: '',
}

$(function () {
    $("#tgl_claim").flatpickr({
        dateFormat: "d/m/Y",
        defaultDate: date.tgl_claim,
        onChange: function (selectedDates, dateStr, instance) {
            date.tgl_claim = moment(dateStr, 'DD/MM/YYYY').format('YYYY-MM-DD');
        }
    });
    $("#tgl_terima").flatpickr({
        dateFormat: "d/m/Y",
        defaultDate: date.tgl_terima,
        onChange: function (selectedDates, dateStr, instance) {
            date.tgl_terima = moment(dateStr, 'DD/MM/YYYY').format('YYYY-MM-DD');
        }
    });

    $('#total').on('keyup', () => $('#total').val(formatRupiah($('#total').val())));
    $('#harga').on('keyup', () => $('#harga').val(formatRupiah($('#harga').val())));

    $('#kd_sales').on('change', function () {
        $('#kd_dealer').val('');
        $('#nm_dealer').val('');
        $('#alamat1').val('');
        $('#kotasj').val('');
        $('#kd_dealer').trigger('change');
    });


    $('#qty_claim').on('change', function () {
        switch (true) {
            case (parseInt($(this).val()) > parseInt($('#qty_faktur').val())):
                toastr.error('QTY Claim Tidak boleh melebihi QTY Faktur!', "info");
                $('#qty_claim').addClass('is-invalid');
                $('#qty_claim').removeClass('is-valid');
                $(this).val($('#qty_faktur').val());
                $('#qty_claim').removeClass('is-invalid');
                $('#qty_claim').addClass('is-valid');
                $('#qty_claim').trigger('keyup');
                break;
            case (parseInt($(this).val()) <= 0 || $(this).val() == ""):
                toastr.error('QTY Claim Tidak boleh kurang dari 1 atau kososng!', "info");

                $('#qty_claim').addClass('is-invalid');
                $('#qty_claim').removeClass('is-valid');
                $(this).val('1');
                $('#qty_claim').removeClass('is-invalid');
                $('#qty_claim').addClass('is-valid');
                break;
            default:
                $('#qty_claim').addClass('is-valid');
                $('#qty_claim').removeClass('is-invalid');
                break;
        }
    });

    $("#btn-submit").click(function (e) {
        e.preventDefault();

        // swal tanya apakah yakin akan menyimpan
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Anda akan menyimpan data ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Tidak, Batalkan!',
            customClass: {
                confirmButton: 'btn btn-warning',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: window.location.href,
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        'no_retur':'',
                        tgl_claim: date.tgl_claim,
                        kd_sales: $('#kd_sales').val(),
                        no_ps: $('#no_ps').val(),
                        kd_dealer: $('#kd_dealer').val(),
                        no_faktur: $('#no_faktur').val(),
                        tgl_faktur: $('#tgl_faktur').val(),
                        kd_part: $('#kd_part').val(),
                        qty_faktur: $('#qty_faktur').val(),
                        qty_claim: $('#qty_claim').val(),
                        harga: $('#harga').val(),
                        disc: $('#disc').val(),
                        ket: $('#ket').val(),
                        sts: $('#sts').val(),
                        tgl_terima: date.tgl_terima,
                        // total: $('#total').val(),
                        // terbayar: $('#terbayar').val(),
                    },
                    success: function (response) {
                        if (response.status == '1') {
                            Swal.fire({
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK !",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                },
                                allowOutsideClick: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = base_url + '/retur/konsumen/edit?no_retur=' + response.data;
                                }
                            });
                        } else {
                            $('#dealer-list .close').trigger('click')
                        }
                        if (response.status == '0') {
                            toastr.error(response.message, "Error");
                        }
                        if (response.status == '2') {
                            swal.fire({
                                title: 'Perhatian!',
                                text: response.message,
                                icon: 'warning',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn btn-secondary'
                                },
                                allowOutsideClick: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        }
                    }
                }).fail(function (jqXHR, textStatus, error) {
                    $('#dealer-list .close').trigger('click')
                    Swal.fire({
                        title: 'Error ' + jqXHR.status,
                        text: textStatus,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-secondary'
                        },
                        allowOutsideClick: false
                    });
                });
            }
        });
    });
}); 