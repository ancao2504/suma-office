function loadDaftarDetailUpdateHarga() {
    loading.block();
    $.ajax({
        url: url.daftar_update_harga,
        method: "get",
        data: { nomor_dokumen: data.nomor_dokumen },

        success: function (response) {
            loading.release();
            if (response.status == true) {
                $('#tableDetailUpdateHarga').html(response.data);
            } else {
                Swal.fire({
                    text: response.message,
                    icon: "error",
                    buttonsStyling: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
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
}

$(document).ready(function () {
    loadDaftarDetailUpdateHarga();

    $('body').on('click', '#btnUpdatePerPartNumber', function (e) {
        e.preventDefault();

        var nomor_dokumen = $(this).data('nomor_dokumen');
        var part_number = $(this).data('part_number');
        var _token = $('input[name="_token"]').val();

        loading.block();
        $.ajax({
            url: url.update_per_part_number,
            method: "POST",
            data: { nomor_dokumen: nomor_dokumen, part_number: part_number, _token: _token },

            success: function (response) {
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
    });

    $('body').on('click', '#btnUpdateStatusPerPartNumber', function (e) {
        e.preventDefault();

        var nomor_dokumen = $(this).data('nomor_dokumen');
        var part_number = $(this).data('part_number');
        var _token = $('input[name="_token"]').val();

        loading.block();
        $.ajax({
            url: url.update_status_per_part_number,
            method: "POST",
            data: { nomor_dokumen: nomor_dokumen, part_number: part_number, _token: _token },

            success: function (response) {
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
    });

    $('#btnUpdateHargaAll').on('click', function (e) {
        e.preventDefault();
        var nomor_dokumen = $(this).data('nomor_dokumen');
        var _token = $('input[name="_token"]').val();

        Swal.fire({
            html: `Apakah anda yakin akan mengupdate stock nomor dokumen
                    <strong>`+ nomor_dokumen + `</strong> ?`,
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
                    url: url.update_per_dokumen,
                    method: "POST",
                    data: { nomor_dokumen: nomor_dokumen, _token: _token },

                    success: function (response) {
                        loading.release();

                        if (response.status == true) {
                            if(response.data.update.harga.error.jumlah > 0) {
                                Swal.fire({
                                    text: 'Data yang berhasil disimpan sejumlah : '+response.data.update.harga.success.jumlah+' Item.'
                                            +' Dan gagal disimpan sejumlah : '+response.data.update.harga.error.jumlah+' Item',
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
                            } else {
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
                            }

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
