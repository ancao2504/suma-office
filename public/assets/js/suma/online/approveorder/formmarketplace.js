$(document).ready(function () {
    $('#btnApproveFakturMarketplace').on('click', function (e) {
        e.preventDefault();
        var nomor_invoice =  $(this).data("nomor_invoice");
        var _token = $('input[name="_token"]').val();

        Swal.fire({
            html: `Apakah anda yakin akan memproses approve nomor invoice
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
                    url: url.proses_approve_marketplace,
                    method: 'post',
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
                                    window.location.href = url.daftar_approve_order;
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
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = window.location.origin + window.location.pathname;
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
                })
            }
        });
    });
});
