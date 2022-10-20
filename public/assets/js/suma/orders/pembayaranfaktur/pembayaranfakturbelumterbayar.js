
$(document).ready(function () {

    var pages = 1;

    $(window).scroll(function () {
        if (loading.isBlocked() === false) {
            if ($(window).scrollTop() >= $(document).height() - $(window).height() - 10) {
                const params = new URLSearchParams(window.location.search)
                for (const param of params) {
                    var start_date = params.get('start_date');
                    var end_date = params.get('month');
                    var salesman = params.get('salesman');
                    var dealer = params.get('dealer');
                    var nomor_faktur = params.get('nomor_faktur');
                }
                pages++;
                loadMoreData(start_date, end_date, salesman, dealer, nomor_faktur, pages);
            }
        }
    });

    window.onbeforeunload = function () {
        window.scrollTo(0, 0);
    }

    async function loadMoreData(start_date, end_date, salesman, dealer, nomor_faktur, pages) {
        loading.block();

        $.ajax({
            url: url_belumterbayar.ayaran_faktur_belum_terbayar,
            type: "get",
            data: {
                start_date: start_date, end_date: end_date, page: pages,
                salesman: salesman, dealer: dealer, nomor_faktur: nomor_faktur
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

        $('#inputFilterSalesman').val(data_filter_belumterbayar.kode_sales);
        $('#inputFilterDealer').val(data_filter_belumterbayar.kode_dealer);
        $('#inputFilterNomorFaktur').val(data_filter_belumterbayar.nomor_faktur);

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
        input_kososng();
    });
});