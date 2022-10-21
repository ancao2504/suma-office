
$(document).ready(function () {

    var pages = 1;

    $(window).scroll(function () {
        if (loading.isBlocked() === false) {
            if ($(window).scrollTop() >= $(document).height() - $(window).height() - 10) {
                const params = new URLSearchParams(window.location.search)
                for (const param of params) {
                    var year = params.get('year');
                    var month = params.get('month');
                    var salesman = params.get('salesman');
                    var dealer = params.get('dealer');
                    var nomor_faktur = params.get('nomor_faktur');
                }
                pages++;
                loadMoreData(year, month, salesman, dealer, nomor_faktur, pages);
            }
        }
    });

    window.onbeforeunload = function () {
        window.scrollTo(0, 0);
    }

    async function loadMoreData(year, month, salesman, dealer, nomor_faktur, pages) {
        loading.block();

        $.ajax({
            url: url_terbayar.pembayaran_faktur_terbayar,
            type: "get",
            data: {
                year: year, month: month, page: pages, salesman: salesman,
                dealer: dealer, nomor_faktur: nomor_faktur
            },
            success: function (response) {
                if (response.html == '') {
                    $('#dataLoadPembayaran').html('<center><div class="fw-bolder fs-3 text-gray-600 text-hover-primary mt-10 mb-10">- No more record found -</div><center>');
                    loading.release();
                    return;
                }
                $("#dataPembayaran").append(response.html);
                loading.release();
            },
            error: function () {
                loading.release();
                pages = pages - 1;

                Swal.fire({
                    text: "Gagal mengambil data ke dalam server, Coba lagi",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-danger"
                    }
                });
            }
        });
    }
    $('#btnFilterPembayaran').on('click', function (e) {
        e.preventDefault();

        $('#selectFilterMonth').prop('selectedIndex', data_filter_terbayar.month - 1).change();
        $('#inputFilterYear').val(data_filter_terbayar.year);
        $('#inputFilterSalesman').val(data_filter_terbayar.kode_sales);
        $('#inputFilterDealer').val(data_filter_terbayar.kode_dealer);
        $('#inputFilterNomorFaktur').val(data_filter_terbayar.nomor_faktur);

        $('#modalFilter').modal('show');
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
            url: url_terbayar.setting_clossing_marketing,
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