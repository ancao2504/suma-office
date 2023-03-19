$(document).ready(function () {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $('body').on('click', '#btnUpdateStatus', function (e) {
        e.preventDefault();
        var nomor_faktur = $(this).data("nomor_faktur");
        var _token = $('input[name="_token"]').val();

        loading.block();
        $.ajax({
            url: url.proses_update_status,
            method: "post",
            data: {
                nomor_faktur: nomor_faktur, _token: _token
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
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
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
    });

    $('body').on('click', '#btnRequestPickupTokopedia', function (e) {
        e.preventDefault();

        var nomor_invoice =  $(this).data("nomor_invoice");
        var _token = $('input[name="_token"]').val();

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
    });

    $('body').on('click', '#btnRequestPickupShopee', function (e) {
        e.preventDefault();

        var nomor_invoice =  $(this).data("nomor_invoice");
        var _token = $('input[name="_token"]').val();

        loading.block();
        $.ajax({
            url: url.data_request_pickup_shopee,
            method: "post",
            data: {
                nomor_invoice: nomor_invoice, _token: _token
            },
            success: function(response) {
                loading.release();

                if (response.status == true) {
                    $('#inputNomorInvoice').val(nomor_invoice);
                    $('#selectTanggalJamPickup').find('option').remove().end().append('<option value="">Pilih tanggal & jam pickup</option>').val();
                    $('#inputKeteranganPickup').val();

                    var datetimePickup = response.data.pickup.address_list[0].time_slot_list;
                    var infoSeller = response.data.pickup.address_list[0];

                    $.each(datetimePickup, function(key, value) {
                        moment.locale('id');
                        var tanggal = moment.unix(value.date).format("dddd, DD MMMM YYYY");
                        var jam = value.time_text;
                        var newoption = new Option(tanggal.toString()+ ' = '+jam, value.pickup_time_id);

                        $("#selectTanggalJamPickup").append(newoption);
                    });

                    $('#inputIdAlamatSeller').text(infoSeller.address_id);
                    $('#inputAlamatSeller').text(infoSeller.address);
                    $('#inputKotaSeller').text(infoSeller.district+', '+infoSeller.city);
                    $('#inputProvinsiSeller').text(infoSeller.state);
                    $('#inputKodePosSeller').text(infoSeller.zipcode);

                    $('#modalRequestPickupShopee').modal('show');
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
    });

    $('#btnSimpanRequestPickupShopee').on('click', function (e) {
        e.preventDefault();
        var nomor_invoice = $('#inputNomorInvoice').val();
        var pickup_time_id = $('#selectTanggalJamPickup').find(":selected").val();
        var address_id = $('#inputIdAlamatSeller').text();
        var _token = $('input[name="_token"]').val();

        if(address_id == '' || nomor_invoice == '' || pickup_time_id == '') {
            Swal.fire({
                text: 'Isi data secara lengkap',
                icon: "warning",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-warning"
                }
            });
        } else {
            loading.block();
            $.ajax({
                url: url.proses_request_pickup_shopee,
                method: "post",
                data: {
                    nomor_invoice: nomor_invoice,
                    address_id: address_id, pickup_time_id: pickup_time_id,
                    _token: _token
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

    $('body').on('click', '#btnCetakLabelTokopedia', function (e) {
        e.preventDefault();
        var nomor_invoice = $(this).data("nomor_invoice");
        var _token = $('input[name="_token"]').val();

        loading.block();
        $.ajax({
            url: url.proses_cetak_label_tokopedia,
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

    $('body').on('click', '#btnCetakLabelShopee', function (e) {
        e.preventDefault();
        var nomor_invoice = $(this).data("nomor_invoice");
        var _token = $('input[name="_token"]').val();

        loading.block();
        $.ajax({
            url: url.proses_cetak_label_shopee,
            method: "POST",
            data: { nomor_invoice: nomor_invoice, _token: _token },

            success: function (response) {
                console.log(response);
                loading.release();

                if(response.status == true) {
                    console.log('oke');

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
