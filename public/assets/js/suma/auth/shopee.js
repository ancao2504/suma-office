$(document).ready(function () {
    $('#btnGenerateLinkAuth').on('click', function (e) {
        e.preventDefault();
        loading.block();
        $.ajax({
            url: url.generate_link_authorization,
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
                    var url = response.data.url;
                    window.open(url, '_blank');
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

    $('#btnGenerateAccessToken').on('click', function (e) {
        e.preventDefault();
        var access_code = $('#inputAccessCode').val();
        var _token = $('input[name="_token"]').val();

        if(access_code == '') {
            Swal.fire({
                text: 'Isi access code terlebih dahulu',
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
                url: url.simpan_access_code,
                method: "post",
                data: { access_code: access_code, _token: _token },

                success: function(response) {
                    loading.release();

                    if (response.status == false) {
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
                    } else {
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
