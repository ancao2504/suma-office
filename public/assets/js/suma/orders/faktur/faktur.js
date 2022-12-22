$(document).ready(function () {
// ===============================================================
    // Daftar
    // ===============================================================
    function loadMasterData(page = 1, per_page = 10, year = '', month = '', salesman = '', dealer = '', nomor_faktur = '') {
        loading.block();
        window.location.href = window.location.origin + window.location.pathname + '?year=' + year + '&month=' + month +
            '&salesman=' + salesman + '&dealer=' + dealer + '&nomor_faktur=' + nomor_faktur + '&per_page=' + per_page +
            '&page=' + page;
    }

    $('#inputFilterNomorFaktur').on('change keydown', function (e) {
        if (e.keyCode == 13 || e.type == 'change') {
            var per_page = $('#selectPerPage').val();
            var year = $('#inputFilterYear').val();
            var month = $('#selectFilterMonth').val();
            var salesman = $('#inputFilterSalesman').val();
            var dealer = $('#selectFilterDealer').val();
            var nomor_faktur = $('#inputFilterNomorFaktur').val();

            loadMasterData(1, per_page, year, month, salesman, dealer, nomor_faktur);
        }
    });

    $('#selectPerPage').change(function() {
        var start_record = data_page.start_record;
        var per_page = $('#selectPerPage').val();
        var year = $('#inputFilterYear').val();
        var month = $('#selectFilterMonth').val();
        var salesman = $('#inputFilterSalesman').val();
        var dealer = $('#selectFilterDealer').val();
        var nomor_faktur = $('#inputFilterNomorFaktur').val();
        var page = Math.ceil(start_record / per_page);

        loadMasterData(page, per_page, year, month, salesman, dealer, nomor_faktur);
    });

    $(document).on('click', '.page-item a', function () {
        var page_link = $(this)[0].getAttribute('data-page');
        var page = page_link.split('?page=')[1];

        var per_page = $('#selectPerPage').val();
        var year = $('#inputFilterYear').val();
        var month = $('#selectFilterMonth').val();
        var salesman = $('#inputFilterSalesman').val();
        var dealer = $('#selectFilterDealer').val();
        var nomor_faktur = $('#inputFilterNomorFaktur').val();

        loadMasterData(page, per_page, year, month, salesman, dealer, nomor_faktur);
    });

    // ===============================================================
    // Filter
    // ===============================================================
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
        $('#inputFilterSalesman').val('');
        $('#selectFilterDealer').val('');
        $('#inputFilterNomorFaktur').val('');

        var per_page = $('#selectPerPage').val();
        var year = $('#inputFilterYear').val();
        var month = $('#selectFilterMonth').val();
        var salesman = $('#inputFilterSalesman').val();
        var dealer = $('#selectFilterDealer').val();
        var nomor_faktur = $('#inputFilterNomorFaktur').val();

        loadMasterData(1, per_page, year, month, salesman, dealer, nomor_faktur);
    });
});
