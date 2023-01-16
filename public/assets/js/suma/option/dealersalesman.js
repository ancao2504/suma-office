function loadDataOptionDealerSalesman(salesman = '', page = 1, per_page = 10, search = '') {
    $('#formOptionDealerSalesman #inputKodeSalesDealerSalesman').val(salesman);

    loading.block();
    $.ajax({
        url: base_url + '/option/dealersalesman' + "?salesman=" + salesman + "&search=" + search + "&per_page=" + per_page + "&page=" + page,
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
                $('#optionDealerSalesmanContentModal').html(response.data);
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
}

$(document).ready(function () {
    $(document).on('click', '#formOptionDealerSalesman #paginationOptionDealerSalesman .page-item a', function () {
        var salesman = $('#formOptionDealerSalesman #inputKodeSalesDealerSalesman').val();
        var page = $(this)[0].getAttribute("data-page");
        var per_page = $('#formOptionDealerSalesman #selectPerPageOptionDealerSalesman').val();
        var search = $('#formOptionDealerSalesman #inputSearchOptionDealerSalesman').val();

        loadDataOptionDealerSalesman(salesman, page, per_page, search);
    });

    $('body').on('change', '#formOptionDealerSalesman #selectPerPageOptionDealerSalesman', function (e) {
        e.preventDefault();
        var salesman = $('#formOptionDealerSalesman #inputKodeSalesDealerSalesman').val();
        var per_page = $('#formOptionDealerSalesman #selectPerPageOptionDealerSalesman').val();
        var start_record = $('#formOptionDealerSalesman #startRecordOptionDealerSalesman').html();
        var page = Math.ceil(start_record / per_page);
        var search = $('#formOptionDealerSalesman #inputSearchOptionDealerSalesman').val();

        loadDataOptionDealerSalesman(salesman, page, per_page, search);
    });

    $('body').on('click', '#formOptionDealerSalesman #btnSearchOptionDealerSalesman', function (e) {
        e.preventDefault();
        var salesman = $('#formOptionDealerSalesman #inputKodeSalesDealerSalesman').val();
        var per_page = $('#formOptionDealerSalesman #selectPerPageDealerSalesman').val();
        var search = $('#formOptionDealerSalesman #inputSearchOptionDealerSalesman').val();

        loadDataOptionDealerSalesman(salesman, 1, per_page, search);
    });

    $('#formOptionDealerSalesman #inputSearchOptionDealerSalesman').on('change', function (e) {
        e.preventDefault();
        var salesman = $('#formOptionDealerSalesman #inputKodeSalesDealerSalesman').val();
        var per_page = $('#formOptionDealerSalesman #selectPerPageDealerSalesman').val();
        var search = $('#formOptionDealerSalesman #inputSearchOptionDealerSalesman').val();

        loadDataOptionDealerSalesman(salesman, 1, per_page, search);
    });

    $('#modalOptionDealerSalesman').on('click', '#selectedOptionDealerSalesman', function () {
        $('#inputDealer').val($(this).data('kode_dealer'));
        $('#modalOptionDealerSalesman').modal('hide');
    });
});
