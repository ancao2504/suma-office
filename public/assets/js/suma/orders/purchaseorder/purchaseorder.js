
$(document).ready(function () {

    // jika terdapat form submit
    $('form').submit(function () {
        loading.block();
    });
    // end jika terdapat form submit
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

    $('#btnFilterPof').on('click', function (e) {
        e.preventDefault();

        $('#selectFilterMonth').prop('selectedIndex', data_filter.month - 1).change();
        $('#inputFilterYear').val(data_filter.year);
        $('#inputFilterSalesman').val(data_filter.salesman);
        $('#inputFilterDealer').val(data_filter.dealer);

        $('#modalFilter').modal('show');
    });

    $('#modalFilter #btnFilterPilihSalesman').on('click', function (e) {
        e.preventDefault();
        loadDataSalesman();
        $('#searchSalesmanForm').trigger('reset');
        $('#salesmanSearchModal').modal('show');
    });

    $('#modalFilter #inputFilterSalesman').on('click', function (e) {
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

    $('#btnFilterPilihDealer').on('click', function (e) {
        e.preventDefault();
        loadDataDealer(1, 10, '');
        $('#searchDealerForm').trigger('reset');
        $('#dealerSearchModal').modal('show');
    });

    $('#inputFilterDealer').on('click', function (e) {
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
