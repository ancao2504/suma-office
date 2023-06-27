let disc2, disc1;
// dokumen ready
$(document).ready(function () {
    $("#tgl_claim").flatpickr().setDate(moment($("#tgl_claim").val()).format('YYYY-MM-DD'));
    $("#tgl_terima").flatpickr().setDate(moment($("#tgl_terima").val()).format('YYYY-MM-DD'));

    $("#btn-add").click(function (e) {
        $("#dtl_form").toggleClass('d-none');
        if(!$('#dtl_form').hasClass('d-none')){
            $('html, body').animate({
                scrollTop: $(".table-responsive").offset().top
            }, 500);
        }
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
    
    $("#list_detail").on('click','.btn_dtl_delete', function (e) {
        let val = JSON.parse(atob($(this).data('a')));
        Swal.fire({
            html: 'Apakah Anda Yakin Menghapus Detail Retur dengan <b>Kd Part ' + val.kd_part +'</b>',
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Hapus!",
            cancelButtonText: "Batal!",
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-secondary"
            },
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                loading.block();
                $.post(base_url + '/retur/konsumen/delete',
                    {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        no_retur: $('#no_retur').val(),
                        no_faktur: val.no_faktur,
                        kd_part: val.kd_part,
                    },
                    function (response) {
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
                                    location.reload();
                                }
                            });
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
                }).always(function () {
                    loading.release();
                }).fail(function (err) {
                    swal.fire({
                        title: 'Perhatian!',
                        text: 'Terjadi Kesalahan, Silahkan Coba Lagi',
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
                });
            }
        });

    });
    
    $("#list_detail").on('click','.btn_dtl_edit', function () {
        let val = JSON.parse(atob($(this).data('a')));
        $('#list-retur tr').removeClass('bg-secondary');

        $('#no_faktur').val(val.no_faktur);
        if(val.no_faktur){
            $('#no_faktur').trigger('change');
        }
        $('#kd_part').val(val.kd_part);
        $('#kd_part').trigger('change');
        $('#qty_claim').val(val.jumlah);
        $('#ket').val(val.ket);
        $('#sts option[value="' + val.status + '"]').prop('selected', true);
        $(document).ajaxStop(function () {
            $('#disc').val(parseInt(val.disc));
            if (val.disc == 0.00) {
                $('#disc').val(0);
            }
        });
        
        $("#dtl_form").removeClass('d-none');
        if(!$('#dtl_form').hasClass('d-none')){
            $('html, body').animate({
                scrollTop: $(".table-responsive").offset().top
            }, 500);
        }
    });

    $("#btn-update-retur").click(function (e) {
        loading.block();
        e.preventDefault();
        $.post(base_url + "/retur/konsumen/form",
            {
                _token: $('meta[name="csrf-token"]').attr('content'),
                no_retur: $('#no_retur').val(),
                kd_sales: $('#kd_sales').val(),
                kd_dealer: $('#kd_dealer').val(),
                tgl_claim: $('#tgl_claim').val(),
                tgl_terima: $('#tgl_terima').val(),
                total: $('#total').val(),
            },
            function (response) {
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
                            location.reload();
                        }
                    });
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
            }).always(function () {
                loading.release();
            }).fail(function (err) {
                swal.fire({
                    title: 'Perhatian!',
                    text: 'Terjadi Kesalahan, Silahkan Coba Lagi!',
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
            });
    });

    $("#btn-update-detail").click(function (e) {
        loading.block();
        e.preventDefault();
        $.post(window.location.href,
            {
                _token: $('meta[name="csrf-token"]').attr('content'),
                no_retur: $('#no_retur').val(),
                no_faktur: $('#no_faktur').val(),
                kd_part: $('#kd_part').val(),
                qty_claim: $('#qty_claim').val(),
                harga: $('#harga').val(),
                disc: $('#disc').val(),
                ket: $('#ket').val(),
                sts: $('#sts').val(),
            },
            function (response) {
                if (response.status == '1') {
                    $('#detail_modal .btn-close').trigger('click');
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
                            location.reload();
                        }
                    });
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
        }).always(function () {
            loading.release();
        })
        .fail(function (err) {
            swal.fire({
                title: 'Perhatian!',
                text: 'Terjadi kesalahan, silahkan coba lagi nanti!',
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
        });
    });
})