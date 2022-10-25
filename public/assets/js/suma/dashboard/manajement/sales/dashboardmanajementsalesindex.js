$(document).ready(function () {
    var btnFilterProses = document.querySelector("#btnFilterProses");
    btnFilterProses.addEventListener("click", function (e) {
        e.preventDefault();
        loading.block();
        document.getElementById("formFilter").submit();
    });

    $('#btnFilterReset').on('click', function (e) {
        e.preventDefault();
        var dateObj = new Date();
        var year = dateObj.getUTCFullYear();
        $('#inputFilterYear').val(year);
        $('#selectFilterMonth').prop('selectedIndex', data_nonqty.month - 1).change();
        $('#selectFilterLevelProduk').prop('selectedIndex', 0).change();
        $('#inputFilterKodeProduk').val('');
    });

    $('#btnFilterProduk').on('click', function (e) {
        e.preventDefault();

        var selectFilterLevelProduk = $('#selectFilterLevelProduk').val();
        loadDataProduk(1, 10, '', selectFilterLevelProduk);
        $('#searchProdukForm').trigger('reset');
        $('#produkSearchModal').modal('show');
    });

    $('body').on('click', '#produkContentModal #selectProduk', function (e) {
        e.preventDefault();
        $('#inputFilterKodeProduk').val($(this).data('kode_produk'));
        $('#produkSearchModal').modal('hide');
    });

    $('#selectFilterLevelProduk').change(function () {
        $('#inputFilterKodeProduk').val('');
    });
});