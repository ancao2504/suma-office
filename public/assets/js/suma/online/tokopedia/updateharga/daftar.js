$(document).ready(function () {
    function loadMasterData(page = 1, per_page = 10, year = '', month = '') {
        loading.block();
        window.location.href = window.location.origin + window.location.pathname + '?year=' + year.trim() + '&month=' + month.trim() +
            '&per_page=' + per_page + '&page=' + page;
    }

    // ===============================================================
    // Filter Update Harga
    // ===============================================================
    $('#btnFilterMasterData').on('click', function (e) {
        e.preventDefault();
        $('#inputFilterYear').val(data_filter.year);
        $('#selectFilterMonth').prop('selectedIndex', data_filter.month - 1).change();
        $('#modalFilter').modal('show');
    });

    $('#btnFilterProses').on('click', function (e) {
        e.preventDefault();
        var per_page = $('#selectPerPageMasterData').val();
        var year = $('#inputFilterYear').val();
        var month = $('#selectFilterMonth').val();
        loadMasterData(1, per_page, year, month);
    });

    $('#btnFilterReset').on('click', function (e) {
        e.preventDefault();
        var dateObj = new Date();
        var month = dateObj.getUTCMonth() + 1;
        var year = dateObj.getUTCFullYear();

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
                    month = response.data.bulan_aktif;
                    year = response.data.tahun_aktif;

                    $('#selectFilterMonth').prop('selectedIndex', month - 1).change();
                    $('#inputFilterYear').val(year);

                    if(data_user.role_id == 'D_H3') {
                        $('#inputFilterNomorFaktur').val('');
                    } else if(data_user.role_id == 'MD_H3_SM') {
                        $('#inputFilterDealer').val('');
                        $('#inputFilterNomorFaktur').val('');
                    } else {
                        $('#inputFilterSalesman').val('');
                        $('#inputFilterDealer').val('');
                        $('#inputFilterNomorFaktur').val('');
                    }
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

    $('#btnFilterKodeUpdateHarga').on('click', function (e) {
        e.preventDefault();
        if(data_user.role_id != 'D_H3' && data_user.role_id != 'MD_H3_SM') {
            loadDataOptionUpdateHarga();
            $('#formOptionUpdateHarga').trigger('reset');
            $('#modalOptionUpdateHarga').modal('show');
        }
    });

    $('#inputKodeUpdateHarga').on('click', function (e) {
        e.preventDefault();
        if(data_user.role_id != 'D_H3' && data_user.role_id != 'MD_H3_SM') {
            loadDataOptionUpdateHarga();
            $('#formOptionUpdateHarga').trigger('reset');
            $('#modalOptionUpdateHarga').modal('show');
        }
    });

    $('body').on('click', '#optionUpdateHargaContentModal #selectedOptionUpdateHarga', function (e) {
        e.preventDefault();
        $('#inputKodeUpdateHarga').val($(this).data('kode'));
        $('#modalOptionUpdateHarga').modal('hide');
    });
});
