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
        $('#inputFilterProduk').val(data_filter.produk);

        $('#modalFilter').modal('show');
    });

    // ===============================================================
    // Filter Produk
    // ===============================================================
    $('#selectFilterLevelProduk').change(function () {
        $('#inputFilterProduk').val('');
    });

    $('#inputFilterProduk').on('click', function (e) {
        e.preventDefault();
        var selectFilterLevelProduk = $('#selectFilterLevelProduk').val();
        loadDataOptionProduk(1, 10, '', selectFilterLevelProduk);
        $('#formOptionGroupProduk').trigger('reset');
        $('#modalOptionGroupProduk').modal('show');
    });

    $('#btnFilterProduk').on('click', function (e) {
        e.preventDefault();
        var selectFilterLevelProduk = $('#selectFilterLevelProduk').val();
        loadDataOptionProduk(1, 10, '', selectFilterLevelProduk);
        $('#formOptionGroupProduk').trigger('reset');
        $('#modalOptionGroupProduk').modal('show');
    });

    $('body').on('click', '#optionProdukContentModal #selectedOptionProduk', function (e) {
        e.preventDefault();
        $('#inputFilterProduk').val($(this).data('kode_produk'));
        $('#modalOptionGroupProduk').modal('hide');
    });

    // ===============================================================
    // Filter Proses
    // ===============================================================
    var btnFilterProses = document.querySelector("#btnFilterProses");
    btnFilterProses.addEventListener("click", function (e) {
        e.preventDefault();

        var year = $('#inputFilterYear').val();
        var month = $('#selectFilterMonth').val();
        var fields = $('#selectFilterFields').val();
        var level = $('#selectFilterLevelProduk').val();
        var produk = $('#inputFilterProduk').val();

        loading.block();
        loadMasterData(year, month, fields, level, produk);
    });

    $('#btnFilterReset').on('click', function (e) {
        e.preventDefault();
        var dateObj = new Date();
        var year = dateObj.getUTCFullYear();
        $('#selectFilterMonth').prop('selectedIndex', data_chart.month - 1).change();
        $('#inputFilterYear').val(year);
        $('#selectFilterFields').prop('selectedIndex', 2).change();
        $('#selectFilterLevelProduk').prop('selectedIndex', 0).change();
        $('#inputFilterProduk').val('');
    });
});
