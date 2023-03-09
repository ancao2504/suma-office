$(document).ready(function () {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $('body').on('click', '#btnRequestPickup', function (e) {
        var nomor_faktur = $(this).data("nomor_faktur");
        var _token = $('input[name="_token"]').val();

        loading.block();
        $.ajax({
            url: url.proses_request_pickup,
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

    $('body').on('click', '#btnUpdateStatus', function (e) {
        var nomor_faktur = $(this).data("nomor_faktur");
        var _token = $('input[name="_token"]').val();

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
});
