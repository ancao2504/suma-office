
$(document).ready(function () {
    // ===============================================================
    // Daftar
    // ===============================================================
    function loadMasterData(page = 1, per_page = 10, year = '', month = '', salesman = '', dealer = '', nomor_faktur = '') {
        loading.block();
        window.location.href = window.location.origin + window.location.pathname + '?year=' + year + '&month=' + month +
            '&salesman=' + salesman.trim() + '&dealer=' + dealer.trim() + '&nomor_faktur=' + nomor_faktur.trim() +
            '&per_page=' + per_page + '&page=' + page;
    }

    $('#selectPerPageMasterData').change(function() {
        var start_record = data_page.start_record;
        var per_page = $('#selectPerPageMasterData').val();
        var year = $('#inputFilterYear').val();
        var month = $('#selectFilterMonth').val();
        var salesman = $('#inputFilterSalesman').val();
        var dealer = $('#inputFilterDealer').val();
        var nomor_faktur = $('#inputFilterNomorFaktur').val();
        var page = Math.ceil(start_record / per_page);

        loadMasterData(page, per_page, year, month, salesman, dealer, nomor_faktur);
    });

    $(document).on('click', '#paginationMasterData .page-item a', function () {
        var page = $(this)[0].getAttribute('data-page');
        var per_page = $('#selectPerPageMasterData').val();
        var year = $('#inputFilterYear').val();
        var month = $('#selectFilterMonth').val();
        var salesman = $('#inputFilterSalesman').val();
        var dealer = $('#inputFilterDealer').val();
        var nomor_faktur = $('#inputFilterNomorFaktur').val();

        loadMasterData(page, per_page, year, month, salesman, dealer, nomor_faktur);
    });

    // ===============================================================
    // Filter
    // ===============================================================
    $('#btnFilterMasterData').on('click', function (e) {
        e.preventDefault();

        $('#inputFilterYear').val(data_filter.year);
        $('#selectFilterMonth').prop('selectedIndex', data_filter.month - 1).change();
        $('#inputFilterSalesman').val(data_filter.salesman);
        $('#inputFilterDealer').val(data_filter.dealer);
        $('#inputFilterNomorFaktur').val(data_filter.nomor_faktur);

        $('#modalFilter').modal('show');
    });

    $('#btnFilterProses').on('click', function (e) {
        e.preventDefault();
        var per_page = $('#selectPerPageMasterData').val();
        var year = $('#inputFilterYear').val();
        var month = $('#selectFilterMonth').val();
        var salesman = $('#inputFilterSalesman').val();
        var dealer = $('#inputFilterDealer').val();
        var nomor_faktur = $('#inputFilterNomorFaktur').val();

        loadMasterData(1, per_page, year, month, salesman, dealer, nomor_faktur);
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
            }
        });
    });

    // ===============================================================
    // Filter Salesman
    // ===============================================================
    $('#inputFilterSalesman').on('click', function (e) {
        e.preventDefault();
        if(data_user.role_id != 'D_H3' && data_user.role_id != 'MD_H3_SM') {
            loadDataOptionSalesman();
            $('#formOptionSalesman').trigger('reset');
            $('#modalOptionSalesman').modal('show');
        }
    });

    $('#btnFilterPilihSalesman').on('click', function (e) {
        e.preventDefault();
        if(data_user.role_id != 'D_H3' && data_user.role_id != 'MD_H3_SM') {
            loadDataOptionSalesman();
            $('#formOptionSalesman').trigger('reset');
            $('#modalOptionSalesman').modal('show');
        }
    });

    $('body').on('click', '#optionSalesmanContentModal #selectedOptionSalesman', function (e) {
        e.preventDefault();
        $('#inputFilterSalesman').val($(this).data('kode_sales'));
        $('#modalOptionSalesman').modal('hide');
    });

    // ===============================================================
    // Filter Dealer
    // ===============================================================
    $('#inputFilterDealer').on('click', function (e) {
        e.preventDefault();
        var kode_sales = $('#inputFilterSalesman').val();

        if(data_user.role_id != 'D_H3') {
            if(data_user.role_id == 'MD_H3_SM' || data_user.role_id == 'MD_H3_KORSM') {
                if(kode_sales == '') {
                    Swal.fire({
                        text: 'Data salesman tidak boleh kosong',
                        icon: "warning",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-warning"
                        }
                    });
                } else {
                    $('#formOptionDealerSalesman').trigger('reset');
                    loadDataOptionDealerSalesman(kode_sales.trim(), 1, 10, '');
                    $('#modalOptionDealerSalesman').modal('show');
                }
            } else {
                $('#formOptionDealer').trigger('reset');
                loadDataOptionDealer(1, 10, '');
                $('#modalOptionDealer').modal('show');
            }
        }
    });

    $('#btnFilterPilihDealer').on('click', function (e) {
        e.preventDefault();
        var kode_sales = $('#inputFilterSalesman').val();

        if(data_user.role_id != 'D_H3') {
            if(data_user.role_id == 'MD_H3_SM' || data_user.role_id == 'MD_H3_KORSM') {
                if(kode_sales == '') {
                    Swal.fire({
                        text: 'Data salesman tidak boleh kosong',
                        icon: "warning",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-warning"
                        }
                    });
                } else {
                    $('#formOptionDealerSalesman').trigger('reset');
                    loadDataOptionDealerSalesman(kode_sales.trim(), 1, 10, '');
                    $('#modalOptionDealerSalesman').modal('show');
                }
            } else {
                $('#formOptionDealer').trigger('reset');
                loadDataOptionDealer(1, 10, '');
                $('#modalOptionDealer').modal('show');
            }
        }
    });

    $('body').on('click', '#optionDealerContentModal #selectedOptionDealer', function (e) {
        e.preventDefault();
        $('#inputFilterDealer').val($(this).data('kode_dealer'));
        $('#modalOptionDealer').modal('hide');
    });

    $('body').on('click', '#optionDealerSalesmanContentModal #selectedOptionDealerSalesman', function (e) {
        e.preventDefault();
        $('#inputFilterDealer').val($(this).data('kode_dealer'));
        $('#modalOptionDealerSalesman').modal('hide');
    });

    // ===============================================================
    // Filter Nomor Faktur
    // ===============================================================
    $('#inputFilterNomorFaktur').keypress(function (e) {
        var key = e.which;
        if(key == 13)  {
            e.preventDefault();
            var per_page = $('#selectPerPageMasterData').val();
            var year = $('#inputFilterYear').val();
            var month = $('#selectFilterMonth').val();
            var salesman = $('#inputFilterSalesman').val();
            var dealer = $('#inputFilterDealer').val();
            var nomor_faktur = $('#inputFilterNomorFaktur').val();

            loadMasterData(1, per_page, year, month, salesman, dealer, nomor_faktur);
        }
    });
});
