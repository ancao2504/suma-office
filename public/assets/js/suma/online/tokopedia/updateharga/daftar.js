$(document).ready(function () {
    function loadMasterData(page = 1, per_page = 10, year = '', month = '') {
        loading.block();
        window.location.href = window.location.origin + window.location.pathname + '?year=' + year.trim() + '&month=' + month.trim() +
            '&per_page=' + per_page + '&page=' + page;
    }

    $('#btnFilterProses').on('click', function (e) {
        e.preventDefault();
        var per_page = $('#selectPerPageMasterData').val();
        var year = $('#inputFilterYear').val();
        var month = $('#selectFilterMonth').val();
        loadMasterData(1, per_page, year, month);
    });

    // ===============================================================
    // Buat Dokumen
    // ===============================================================
    $('#btnBuatDokumen').on('click', function (e) {
        e.preventDefault();
        $('#inputKodeUpdateHarga').val('');
        $('#modalBuatDokumen').modal('show');
    });

    $('#inputKodeUpdateHarga').on('click', function (e) {
        e.preventDefault();
        if(data_user.role_id != 'D_H3' && data_user.role_id != 'MD_H3_SM') {
            loadDataOptionUpdateHarga(data_filter.kode_lokasi);
            $('#formOptionUpdateHarga').trigger('reset');
            $('#modalOptionUpdateHarga').modal('show');
        }
    });

    $('#btnKodeUpdateHarga').on('click', function (e) {
        e.preventDefault();
        if(data_user.role_id != 'D_H3' && data_user.role_id != 'MD_H3_SM') {
            loadDataOptionUpdateHarga(data_filter.kode_lokasi);
            $('#formOptionUpdateHarga').trigger('reset');
            $('#modalOptionUpdateHarga').modal('show');
        }
    });

    $('body').on('click', '#optionUpdateHargaContentModal #selectedOptionUpdateHarga', function (e) {
        e.preventDefault();
        $('#inputKodeUpdateHarga').val($(this).data('kode'));
        $('#modalOptionUpdateHarga').modal('hide');
    });

    $('#btnCreateDocument').on('click', function (e) {
        e.preventDefault();
        var kode = $('#inputKodeUpdateHarga').val();
        var _token = $('input[name="_token"]').val();

        if(kode == '') {
            Swal.fire({
                text: 'Pilih nomor dokumen terlebih dahulu',
                icon: 'warning',
                buttonsStyling: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'Ok, got it!',
                customClass: {
                    confirmButton: 'btn btn-warning'
                }
            });
        } else {
            Swal.fire({
                html: `Apakah anda yakin akan membuat dokumen update harga
                        berdasarkan data user <strong>`+ kode + `</strong> ?`,
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
                        url: url.buat_dokumen,
                        method: "POST",
                        data: { kode: kode, _token: _token },

                        success: function (response) {
                            loading.release();

                            if (response.status == true) {
                                window.location.href = url.daftar_update_harga;
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
        }
    });

    $('body').on('click', '#btnUpdateHarga', function (e) {
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
