// dokumen ready faktur

$(document).ready(function () {

    $('.select2').on('select2:select', function (e) {
        $(this).focus();
    });

    // jika terdapat form submit
    $('form').submit(function () {
        loading.block();
    });
    // end jika terdapat form submit

    // ajax start
    $(document).ajaxStart(function () {
        loading.block();
    });
    // end ajax start
    // ajax stop
    $(document).ajaxStop(function () {
        loading.release();
    });
    // end ajax stop
    // change
    $('#selectPerPageForm').change(function () {
        loading.block();
    });
    // end change
    // page - item click kecuali yang ada active
    $('.page-item, td a.btn').not('.active').click(function () {
        loading.block();
    });
    // end page - item click kecuali yang ada active

    $('#btnFilterFaktur').on('click', function (e) {
        e.preventDefault();

        $('#selectFilterMonth').prop('selectedIndex', data_filter.month - 1).change();
        $('#inputFilterYear').val(data_filter.year);
        $('#inputFilterSalesman').val(data_filter.salesman);
        $('#inputFilterDealer').val(data_filter.dealer);
        $('#inputFilterNomorFaktur').val(data_filter.nomor_faktur);

        $('#modalFilter').modal('show');
    });

    $('#inputFilterSalesman').on('click', function (e) {
        e.preventDefault();
        loadDataSalesman();
        $('#searchSalesmanForm').trigger('reset');
        $('#salesmanSearchModal').modal('show');
    });

    $('#btnFilterPilihSalesman').on('click', function (e) {
        e.preventDefault();
        loadDataSalesman();
        $('#searchSalesmanForm').trigger('reset');
        $('#salesmanSearchModal').modal('show');
    });

    $('body').on('click', '#salesmanContentModal #selectSalesman', function (e) {
        e.preventDefault();
        $('#inputFilterSalesman').val($(this).data('kode_sales'));
        $('#salesmanSearchModal').modal('hide');
    });

    $('#inputFilterDealer').on('click', function (e) {
        e.preventDefault();
        loadDataDealer(1, 10, '');
        $('#searchDealerForm').trigger('reset');
        $('#dealerSearchModal').modal('show');
    });

    $('#btnFilterPilihDealer').on('click', function (e) {
        e.preventDefault();
        loadDataDealer(1, 10, '');
        $('#searchDealerForm').trigger('reset');
        $('#dealerSearchModal').modal('show');
    });

    $('body').on('click', '#dealerContentModal #selectDealer', function (e) {
        e.preventDefault();
        $('#inputFilterDealer').val($(this).data('kode_dealer'));
        $('#dealerSearchModal').modal('hide');
    });

    $('#btnFilterReset').on('click', function (e) {
        e.preventDefault();
        var dateObj = new Date();
        var month = dateObj.getUTCMonth() + 1;
        var year = dateObj.getUTCFullYear();

        $.ajax({
            url: url.setting_clossing_marketing,
            method: "get",
            success: function (response) {
                if (response.status == false) {
                    Swal.fire({
                        text: response.message,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-danger"
                        }
                    });
                } else {
                    month = response.data.bulan_aktif;
                    year = response.data.tahun_aktif;
                }
            }
        });

        $('#selectFilterMonth').prop('selectedIndex', month - 1).change();
        $('#inputFilterYear').val(year);

        input_kososng();
    });
});
