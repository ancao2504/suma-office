function loadDataOptionDealer(page = 1, per_page = 10, search = '') {
    loading.block();
    $.ajax({
        url: base_url + '/option/dealer' + "?search=" + search + "&per_page=" + per_page + "&page=" + page,
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
                $('#optionDealerContentModal').html(response.data);
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
    $(document).on('click', '#formOptionDealer #paginationOptionDealer .page-item a', function () {
        var page = $(this)[0].getAttribute("data-page");
        var per_page = $('#formOptionDealer #selectPerPageOptionDealer').val();
        var search = $('#formOptionDealer #inputSearchOptionDealer').val();

        loadDataOptionDealer(page, per_page, search);
    });

    $('body').on('change', '#formOptionDealer #selectPerPageOptionDealer', function (e) {
        e.preventDefault();
        var per_page = $('#formOptionDealer #selectPerPageOptionDealer').val();
        var start_record = $('#formOptionDealer #startRecordOptionDealer').html();
        var page = Math.ceil(start_record / per_page);
        var search = $('#formOptionDealer #inputSearchOptionDealer').val();

        loadDataOptionDealer(page, per_page, search);
    });

    $('body').on('click', '#formOptionDealer #btnSearchOptionDealer', function (e) {
        e.preventDefault();
        var per_page = $('#formOptionDealer #selectPerPageDealer').val();
        var search = $('#formOptionDealer #inputSearchOptionDealer').val();

        loadDataOptionDealer(1, per_page, search);
    });

    $('#formOptionDealer #inputSearchOptionDealer').on('change', function (e) {
        e.preventDefault();
        var per_page = $('#formOptionDealer #selectPerPageDealer').val();
        var search = $('#formOptionDealer #inputSearchOptionDealer').val();

        loadDataOptionDealer(1, per_page, search);
    });
});
