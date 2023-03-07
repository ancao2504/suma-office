// function loadMasterData(page = 1, per_page = 10, year = '', month = '') {
//     loading.block();
//     window.location.href = window.location.origin + window.location.pathname + '?year=' + year.trim() + '&month=' + month.trim() +
//         '&per_page=' + per_page + '&page=' + page;
// }

function relodPage(page, per_page, year, month){
    $.ajax({
        url: base_url + '/online/updateharga/shopee/daftar',
        method: "GET",
        data: { page: page, per_page: per_page, year: year, month: month },
        success: function (response) {
                console.log(response);
                $('#kt_content_container').html(response.data);
        },
        error: function (response) {
            console.log(response);
        }
    });
}

$(document).ready(function () {
    window.history.pushState("", "", window.location.href.split('?')[0]);
    $('body').attr('data-kt-aside-minimize', 'on');
    $('#kt_aside_toggle').addClass('active');

    $(document).ajaxStart(function () {
        loading.block();
    });
    $(document).ajaxStop(function () {
        loading.release();
    });

    $('body').on('click','#btnFilterProses', function (e) {
        e.preventDefault();
        var per_page = $('body').find('#selectPerPageMasterData').val();
        var year = $('body').find('#inputFilterYear').val();
        var month = $('body').find('#selectFilterMonth').val();
        relodPage(1, per_page, year, month);
    });

    // ===============================================================
    // Buat Dokumen
    // ===============================================================
    $('body').on('click','#btnBuatDokumen', function (e) {
        e.preventDefault();
        $('body').find('#inputKodeUpdateHarga').val('');
        $('body').find('#modalBuatDokumen').modal('show');
    });

    $('body').on('click','#inputKodeUpdateHarga', function (e) {
        e.preventDefault();
        if(data_user.role_id != 'D_H3' && data_user.role_id != 'MD_H3_SM') {
            loadDataOptionUpdateHarga(data_filter.kode_lokasi);
            $('body').find('#formOptionUpdateHarga').trigger('reset');
            $('body').find('#modalOptionUpdateHarga').modal('show');
        }
    });

    $('body').on('click','#btnKodeUpdateHarga', function (e) {
        e.preventDefault();
        if(data_user.role_id != 'D_H3' && data_user.role_id != 'MD_H3_SM') {
            loadDataOptionUpdateHarga(data_filter.kode_lokasi);
            $('body').find('#formOptionUpdateHarga').trigger('reset');
            $('body').find('#modalOptionUpdateHarga').modal('show');
        }
    });

    $('body').on('click', '#optionUpdateHargaContentModal #selectedOptionUpdateHarga', function (e) {
        e.preventDefault();
        $('body').find('#inputKodeUpdateHarga').val($(this).data('kode'));
        $('body').find('#modalOptionUpdateHarga').modal('hide');
    });

    $('body').on('click','#btnCreateDocument', function (e) {
        e.preventDefault();
        var kode = $('body').find('#inputKodeUpdateHarga').val();
        var _token = $('body').find('input[name="_token"]').val();

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
                confirmButtonText: "Iya, Buat Dokumen",
                cancelButtonText: 'Batalkan',
                customClass: {
                    confirmButton: "btn btn-info",
                    cancelButton: 'btn btn-secondary'
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
                                // window.location.href = url.daftar_update_harga;
                                $('body').find('#modalBuatDokumen').modal('hide');
                                var page = $('body').find('#paginationMasterData .page-item.active .page-link').text();
                                var per_page = $('body').find('#selectPerPageMasterData').val();
                                var year = $('body').find('#inputFilterYear').val();
                                var month = $('body').find('#selectFilterMonth').val();
                                relodPage(page, per_page, year, month);
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
        var _token = $('body').find('input[name="_token"]').val();

        Swal.fire({
            html: `Apakah anda yakin akan mengupdate stock nomor dokumen
                    <strong>`+ nomor_dokumen + `</strong> ?`,
            icon: "warning",
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: "Iya, Update Stock",
            cancelButtonText: 'Batalkan',
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
                        console.log(response);
                        loading.release();
                        if (response.status == true) {
                            if(response.data) {
                                $('#respon_container').html(response.data.modal_respown);
                                $('#respon_container').find('#modal_respown').modal('show');

                                $('#respon_container').find('#modal_respown').on('hidden.bs.modal', function (e) {
                                    
                                    var page = $('body').find('#paginationMasterData .page-item.active .page-link').text();
                                    var per_page = $('body').find('#selectPerPageMasterData').val();
                                    var year = $('body').find('#inputFilterYear').val();
                                    var month = $('body').find('#selectFilterMonth').val();
                                    relodPage(page, per_page, year, month);
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
                                        
                                        var page = $('body').find('#paginationMasterData .page-item.active .page-link').text();
                                        var per_page = $('body').find('#selectPerPageMasterData').val();
                                        var year = $('body').find('#inputFilterYear').val();
                                        var month = $('body').find('#selectFilterMonth').val();
                                        relodPage(page, per_page, year, month);
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
                                    
                                    var page = $('body').find('#paginationMasterData .page-item.active .page-link').text();
                                    var per_page = $('body').find('#selectPerPageMasterData').val();
                                    var year = $('body').find('#inputFilterYear').val();
                                    var month = $('body').find('#selectFilterMonth').val();
                                    relodPage(page, per_page, year, month);
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

    $('body').on('change', '#selectPerPageMasterData', function (e) {
        e.preventDefault();
        var per_page = $(this).val();
        var year = $('body').find('#inputFilterYear').val();
        var month = $('body').find('#selectFilterMonth').val();
        relodPage(1, per_page, year, month);
    });

    // boady #paginationMasterData a on click ambil data-page
    $('body').on('click', '#paginationMasterData a', function (e) {
        e.preventDefault();
        var page = $(this).attr('data-page');
        var per_page = $('body').find('#selectPerPageMasterData').val();
        var year = $('body').find('#inputFilterYear').val();
        var month = $('body').find('#selectFilterMonth').val();
        relodPage(page, per_page, year, month);
    });
});
