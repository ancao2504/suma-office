var minDateProses = moment(new Date()).format('YYYY-MM-DD');

function getMinDateFaktur() {
    $.ajax({
        url: url.clossing_marketing,
        method: "get",
        success: function(response) {
            loading.release();
            if (response.status == false) {
                Swal.fire({
                    text: response.message,
                    icon: "warning",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-warning"
                    }
                });
            } else {
                var bulanBerjalan = moment(new Date(response.data.close_mkr)).format('MM');
                var bulanSekarang = moment(new Date()).format('MM');

                if(bulanSekarang != bulanBerjalan) {
                    var tanggal = moment([ moment(new Date(response.data.close_mkr)).format('YYYY') + '-' +
                                    moment(new Date(response.data.close_mkr)).format('MM') + '-' + '01']);
                    minDateProses = moment(tanggal).endOf('month').format('YYYY-MM-DD');
                } else {
                    minDateProses = moment(new Date()).format('YYYY-MM-DD');
                }
            }
        },
        error: function() {
            Swal.fire({
                text: 'Server not responding',
                icon: "danger",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-danger"
                }
            });
            return;
        }
    });
}

$(document).ready(function () {
    getMinDateFaktur();


    $('#btnSimulasiFaktur').on('click', function (e) {
        e.preventDefault();
        $('#modalSimulasiOrder').modal('show');
    });

    $('#btnProsesOrder').on('click', function (e) {
        e.preventDefault();
        var nomor_invoice = data.nomor_invoice;
        var tanggal = moment($('#inputTanggalProses').val()).format('YYYY-MM-DD');
        var close_mkr = moment(new Date()).format('YYYY-MM-DD');
        var _token = $('input[name="_token"]').val();

        loading.block();
        $.ajax({
            url: url.clossing_marketing,
            method: "get",
            success: function(response) {
                loading.release();

                if (response.status == false) {
                    Swal.fire({
                        text: response.message,
                        icon: "warning",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-warning"
                        }
                    });
                } else {
                    close_mkr = response.data.close_mkr;

                    if(tanggal <= close_mkr) {
                        Swal.fire({
                            html: 'Tanggal yang dipilih harus lebih besar dari tanggal clossing.'+
                                    '<br>Tanggal Clossing : <strong>'+close_mkr+'</strong>',
                            icon: "warning",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-warning"
                            }
                        });
                    } else {
                        Swal.fire({
                            html: `Apakah anda yakin akan memproses data order nomor invoice
                                    <strong>`+ nomor_invoice + `</strong> ?`,
                            icon: "info",
                            buttonsStyling: false,
                            showCancelButton: true,
                            confirmButtonText: "Yes",
                            cancelButtonText: 'No',
                            customClass: {
                                confirmButton: "btn btn-danger",
                                cancelButton: 'btn btn-primary'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#btnProsesOrder').prop('disabled', true);

                                loading.block();
                                $.ajax({
                                    url: url.proses_order,
                                    method: "POST",
                                    data: { nomor_invoice: nomor_invoice, tanggal: tanggal, _token: _token },

                                    success: function (response) {
                                        loading.release();

                                        if (response.status == true) {
                                            Swal.fire({
                                                html: response.message,
                                                icon: 'success',
                                                buttonsStyling: false,
                                                allowOutsideClick: false,
                                                allowEscapeKey: false,
                                                confirmButtonText: 'Ok, got it!',
                                                customClass: {
                                                    confirmButton: 'btn btn-success'
                                                }
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = window.location.origin + window.location.pathname;
                                                }
                                            });
                                        } else {
                                            $('#btnProsesOrder').prop('disabled', false);

                                            Swal.fire({
                                                html: response.message,
                                                icon: 'warning',
                                                buttonsStyling: false,
                                                allowOutsideClick: false,
                                                allowEscapeKey: false,
                                                confirmButtonText: 'Ok, got it!',
                                                customClass: {
                                                    confirmButton: 'btn btn-warning'
                                                }
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = window.location.origin + window.location.pathname;
                                                }
                                            });
                                        }
                                    },
                                    error: function () {
                                        $('#btnProsesOrder').prop('disabled', false);
                                        loading.release();
                                        Swal.fire({
                                            text: 'Server Not Responding',
                                            icon: 'error',
                                            buttonsStyling: false,
                                            allowOutsideClick: false,
                                            allowEscapeKey: false,
                                            confirmButtonText: 'Ok, got it!',
                                            customClass: {
                                                confirmButton: 'btn btn-danger'
                                            }
                                        });
                                    }
                                })
                            }
                        });
                    }
                }
            },
            error: function() {
                loading.release();
                Swal.fire({
                    text: 'Server not responding',
                    icon: "danger",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-danger"
                    }
                });
                window.location.href = window.location.origin + window.location.pathname;
            }
        });
    });

    $('#btnCetakLabel').on('click', function (e) {
        e.preventDefault();
        var nomor_invoice = $(this).data("nomor_invoice");
        var _token = $('input[name="_token"]').val();

        loading.block();
        $.ajax({
            url: url.proses_cetak_label,
            method: "POST",
            data: { nomor_invoice: nomor_invoice, _token: _token },

            success: function (response) {
                loading.release();

                if (response.status == true) {
                    var newWindow = window.open('url', '_blank');
                    newWindow.document.open();
                    newWindow.document.write(response.data);
                    newWindow.document.close();
                } else {
                    Swal.fire({
                        html: response.message,
                        icon: 'warning',
                        buttonsStyling: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'Ok, got it!',
                        customClass: {
                            confirmButton: 'btn btn-warning'
                        }
                    });
                }
            },
            error: function () {
                loading.release();
                Swal.fire({
                    text: 'Server Not Responding',
                    icon: 'error',
                    buttonsStyling: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonText: 'Ok, got it!',
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    }
                });
            }
        });
    });

    $('#btnRequestPickup').on('click', function (e) {
        e.preventDefault();
        var nomor_invoice = $(this).data("nomor_invoice");
        var _token = $('input[name="_token"]').val();

        Swal.fire({
            html: `Apakah anda yakin akan memproses request pickup nomor invoice
                    <strong>`+ nomor_invoice + `</strong> ?`,
            icon: "info",
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: 'No',
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: 'btn btn-primary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                loading.block();
                $.ajax({
                    url: url.proses_request_pickup_tokopedia,
                    method: "post",
                    data: {
                        nomor_invoice: nomor_invoice, _token: _token
                    },
                    success: function(response) {
                        loading.release();

                        if (response.status == true) {
                            Swal.fire({
                                text: response.message,
                                icon: 'success',
                                buttonsStyling: false,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                confirmButtonText: 'Ok, got it!',
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                text: response.message,
                                icon: 'warning',
                                buttonsStyling: false,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                confirmButtonText: 'Ok, got it!',
                                customClass: {
                                    confirmButton: 'btn btn-warning'
                                }
                            });
                        }
                    },
                    error: function() {
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
                });
            }
        });
    });

    $('#btnUpdateKurir').on('click', function (e) {
        e.preventDefault();
        var nomor_invoice = $(this).data("nomor_invoice");
        var _token = $('input[name="_token"]').val();

        loading.block();
        $.ajax({
            url: url.proses_update_kurir,
            method: "POST",
            data: { nomor_invoice: nomor_invoice, _token: _token },

            success: function (response) {
                loading.release();

                if (response.status == true) {
                    Swal.fire({
                        html: response.message,
                        icon: 'success',
                        buttonsStyling: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'Ok, got it!',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = window.location.origin + window.location.pathname;
                        }
                    });
                } else {
                    Swal.fire({
                        html: response.message,
                        icon: 'warning',
                        buttonsStyling: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'Ok, got it!',
                        customClass: {
                            confirmButton: 'btn btn-warning'
                        }
                    });
                }
            },
            error: function () {
                loading.release();
                Swal.fire({
                    text: 'Server Not Responding',
                    icon: 'error',
                    buttonsStyling: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonText: 'Ok, got it!',
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    }
                });
            }
        });
    });
});
