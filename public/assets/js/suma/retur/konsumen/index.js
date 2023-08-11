$(function () {
    $('#per_page').on('change', function () {
        window.location = window.location.origin + window.location.pathname + '?page=1&per_page='+$(this).val()+'&no_retur=' + $('#cari').val();
        loading.block()
    });
    $('#cari').on('change', function () {
        window.location = window.location.origin + window.location.pathname + '?page=1&per_page='+$('#per_page').val()+'&no_retur=' + $('#cari').val();
        loading.block()
    });

    // delete data -----------------------------------------------------------
    $('.btnDelete').on('click', function () {
        Swal.fire({
            html: 'Apakah Anda Yakin Menghapus Retur dengan <b>No Retur : ' + $(this).data('id') +'</b>',
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
                        no_retur: $(this).data('id')
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
                            toastr.warning(response.message, "Peringatan");
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
})