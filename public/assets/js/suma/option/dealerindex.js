function loadDataOptionDealerIndex(salesman = '', page = 1, per_page = 10, search = '') {
    $('#inputKodeSalesDealerIndex').val(salesman);

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
                $('#optionDealerIndexContentModal').html(response.data);
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
    $(document).on('click', '#formOptionDealerIndex #paginationOptionDealerSalesman .page-item a', function () {
        var salesman = $('#inputKodeSalesDealerIndex').val();
        var page = $(this)[0].getAttribute("data-page");
        var per_page = $('#formOptionDealerIndex #selectPerPageOptionDealer').val();
        var search = $('#formOptionDealerIndex #inputSearchOptionDealerIndex').val();

        loadDataOptionDealerIndex(salesman, page, per_page, search);
    });

    $('body').on('change', '#formOptionDealerIndex #selectPerPageOptionDealerSalesman', function (e) {
        e.preventDefault();
        var salesman = $('#inputKodeSalesDealerIndex').val();
        var per_page = $('#formOptionDealerIndex #selectPerPageOptionDealerSalesman').val();
        var start_record = $('#formOptionDealerIndex #startRecordOptionDealerSalesman').html();
        var page = Math.ceil(start_record / per_page);
        var search = $('#formOptionDealerIndex #inputSearchOptionDealerIndex').val();

        loadDataOptionDealerIndex(salesman, page, per_page, search);
    });

    $('body').on('click', '#formOptionDealerIndex #btnSearchOptionDealer', function (e) {
        e.preventDefault();
        var salesman = $('#inputKodeSalesDealerIndex').val();
        var per_page = $('#formOptionDealerIndex #selectPerPageDealerSalesman').val();
        var search = $('#formOptionDealerIndex #inputSearchOptionDealerIndex').val();

        loadDataOptionDealerIndex(salesman, 1, per_page, search);
    });

    $('#formOptionDealerIndex #inputSearchOptionDealerIndex').on('change', function (e) {
        e.preventDefault();
        var salesman = $('#inputKodeSalesDealerIndex').val();
        var per_page = $('#formOptionDealerIndex #selectPerPageDealerSalesman').val();
        var search = $('#formOptionDealerIndex #inputSearchOptionDealerIndex').val();

        loadDataOptionDealerIndex(salesman, 1, per_page, search);
    });
});
