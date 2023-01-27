$(document).ready(function () {
    // ===============================================================
    // Load Data
    // ===============================================================
    function loadMasterData(year = '', month = '', fields = '', level = '', produk = '') {
        loading.block();
        window.location.href = window.location.origin + window.location.pathname + '?year=' + year.trim() + '&month=' + month.trim() +
            '&fields=' + fields.trim() + '&level=' + level.trim() + '&produk=' + produk.trim();
    }

    // ===============================================================
    // Filter
    // ===============================================================
    $('#btnFilterMasterData').on('click', function (e) {
        e.preventDefault();

        $('#inputFilterYear').val(data_filter.year);
        $('#selectFilterMonth').prop('selectedIndex', data_filter.month - 1).change();
        $('#selectFilterFields').val(data_filter.fields);
        $('#selectFilterLevelProduk').val(data_filter.level);
        $('#inputFilterKodeProduk').val(data_filter.produk);

        $('#modalFilter').modal('show');
    });

    $('#btnFilterProses').on('click', function (e) {
        e.preventDefault();

        var year = $('#inputFilterYear').val();
        var month = $('#selectFilterMonth').val();
        var fields = $('#selectFilterFields').val();
        var level = $('#selectFilterLevelProduk').val();
        var produk = $('#inputFilterKodeProduk').val();

        loading.block();
        loadMasterData(year, month, fields, level, produk);
    });

    $('#btnFilterReset').on('click', function (e) {
        e.preventDefault();
        var dateObj = new Date();
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
                    $('#selectFilterFields').prop('selectedIndex', 2).change();
                    $('#selectFilterLevelProduk').prop('selectedIndex', 0).change();
                    $('#inputFilterKodeProduk').val('');
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

    // ===============================================================
    // Filter Produk
    // ===============================================================
    $('#selectFilterLevelProduk').change(function () {
        $('#inputFilterKodeProduk').val('');
    });

    $('#inputFilterKodeProduk').on('click', function (e) {
        e.preventDefault();
        var selectFilterLevelProduk = $('#selectFilterLevelProduk').val();
        loadDataOptionProduk(1, 10, selectFilterLevelProduk, '');
        $('#formOptionGroupProduk').trigger('reset');
        $('#modalOptionGroupProduk').modal('show');
    });

    $('#btnFilterProduk').on('click', function (e) {
        e.preventDefault();
        var selectFilterLevelProduk = $('#selectFilterLevelProduk').val();
        loadDataOptionProduk(1, 10, selectFilterLevelProduk, '');
        $('#formOptionGroupProduk').trigger('reset');
        $('#modalOptionGroupProduk').modal('show');
    });

    $('body').on('click', '#optionProdukContentModal #selectedOptionProduk', function (e) {
        e.preventDefault();
        $('#inputFilterKodeProduk').val($(this).data('kode_produk'));
        $('#modalOptionGroupProduk').modal('hide');
    });
});
