function loadDaftarDetailUpdateHarga() {
    loading.block();
    $.ajax({
        url: url.daftar_update_harga,
        method: "get",
        // data: { nomor_dokumen: data.nomor_dokumen },

        success: function (response) {
            loading.release();
            if (response.status == true) {
                $('#kt_content_container').html(response.data);
            } else {
                Swal.fire({
                    html: response.message,
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
    $('body').attr('data-kt-aside-minimize', 'on');
    $('#kt_aside_toggle').addClass('active');
    // loadDaftarDetailUpdateHarga();
    $(document).ajaxStart(function () {
        loading.block();
    });
    $(document).ajaxStop(function () {
        loading.release();
    });
    
    $('body').on('click', '#btnUpdatePerPartNumber', function (e) {
        e.preventDefault();

        var nomor_dokumen = $(this).data('nomor_dokumen');
        var part_number = $(this).data('part_number');
        var _token = $('input[name="_token"]').val();
        
        Swal.fire({
            html: `Apakah anda yakin mengubah harga pada Shopee pada part number <strong>` + part_number + `</strong> ?`,
            icon: 'warning',
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: "Iya, ubah harga",
            cancelButtonText: 'batalkan',
            customClass: {
                confirmButton: "btn btn-warning",
                cancelButton: "btn btn-light"
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url.update_per_part_number,
                    method: "POST",
                    data: { nomor_dokumen: nomor_dokumen, part_number: part_number, _token: _token },
                    success: function (response) {
                        loading.release();
                        if (response.status == true) {
                            if(response.data) {
                                $('#respon_container').html(response.data.modal_respown);
                                $('#respon_container').find('#modal_respown').modal('show');

                                $('#respon_container').find('#modal_respown').on('hidden.bs.modal', function (e) {
                                    loadDaftarDetailUpdateHarga();
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
                                        loadDaftarDetailUpdateHarga();
                                    }
                                });
                            }
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
                                    loadDaftarDetailUpdateHarga();
                                }
                            });
                        }
                    },
                    error: function () {
                        loading.release();
                        Swal.fire({
                            html: 'Server Not Responding',
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
            }
        });
    });

    $('body').on('click', '#btnUpdateStatusPerPartNumber', function (e) {
        e.preventDefault();

        var nomor_dokumen = $(this).data('nomor_dokumen');
        var part_number = $(this).data('part_number');
        var _token = $('input[name="_token"]').val();

        swal.fire({
            html: `Apakah anda yakin mengubah harga pada Shopee secara Manual pada part number <strong>` + part_number + `</strong> ?`,
            icon: "warning",
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: "Iya, ubah manual",
            cancelButtonText: 'batalkan',
            customClass: {
                confirmButton: "btn btn-warning",
                cancelButton: "btn btn-light"
            }
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: url.update_status_per_part_number,
                    method: "POST",
                    data: { nomor_dokumen: nomor_dokumen, part_number: part_number, _token: _token },

                    success: function (response) {

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
                                    loadDaftarDetailUpdateHarga();
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
                                    loadDaftarDetailUpdateHarga();
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
            }
        });
    });

    $('#kt_content_container').on('click','#btnUpdateHargaAll', function (e) {
        e.preventDefault();
        var nomor_dokumen = $(this).data('nomor_dokumen');
        var _token = $('input[name="_token"]').val();
        Swal.fire({
            html: `Apakah anda yakin akan mengupdate stock pada Nomor Dokumen :
                    <strong>`+ nomor_dokumen + `</strong>?`,
            icon: "warning",
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: 'No',
            customClass: {
                confirmButton: "btn btn-warning",
                cancelButton: 'btn btn-secondary'
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
                            if(response.data) {
                                $('#respon_container').html(response.data.modal_respown);
                                $('#respon_container').find('#modal_respown').modal('show');

                                $('#respon_container').find('#modal_respown').on('hidden.bs.modal', function (e) {
                                    loadDaftarDetailUpdateHarga();
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
                                        loadDaftarDetailUpdateHarga();
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
                                    loadDaftarDetailUpdateHarga();
                                }
                            });
                        }
                    },
                    error: function (error) {
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
