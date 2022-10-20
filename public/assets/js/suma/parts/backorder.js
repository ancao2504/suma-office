// dokumen rady
$(document).ready(function () {

    // jika terdapat form submit
    $('form').submit(function () {
        loading.block();
    });
    // end jika terdapat form submit

    // pageination
    var pages = 1;

    $(window).scroll(function () {
        if (loading.isBlocked() === false) {
            if ($(window).scrollTop() >= $(document).height() - $(window).height() - 10) {
                const params = new URLSearchParams(window.location.search)
                for (const param of params) {
                    var salesman = params.get('salesman');
                    var dealer = params.get('dealer');
                    var part_number = params.get('part_number');
                }
                pages++;
                loadMoreData(salesman, dealer, part_number, pages);
            }
        }
    });

    window.onbeforeunload = function () {
        window.scrollTo(0, 0);
    }
    // end pageination

    // data more
    async function loadMoreData(salesman, dealer, part_number, pages) {
        loading.block();

        $.ajax({
            url: url.back_order,
            type: "get",
            data: { salesman: salesman, dealer: dealer, part_number: part_number, page: pages },

            success: function (response) {
                if (response.html == '') {
                    $('#dataLoadBackOrder').html('<center><div class="fw-bolder fs-3 text-gray-600 text-hover-primary mt-10 mb-10">- No more record found -</div><center>');
                    loading.release();
                    return;
                }
                $("#dataBackOrder").append(response.html);
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
    // end data more

    $('#btnFilterBackOrder').on('click', function (e) {
        e.preventDefault();

        $('#inputFilterSalesman').val(data_filter.kode_sales);
        $('#inputFilterDealer').val(data_filter.kode_dealer);
        $('#inputFilterPartNumber').val(data_filter.part_number);

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
});