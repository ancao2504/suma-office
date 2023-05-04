function loadDataOptionCompany(page = 1, per_page = 10, search = '') {
    loading.block();
    $.ajax({
        url: base_url + '/option/company' + "?search=" + search + "&per_page=" + per_page + "&page=" + page,
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
                $('#optionCompanyContentModal').html(response.data);
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
    $(document).on('click', '#formOptionCompany #paginationOptionCompany .page-item a', function () {
        var page = $(this)[0].getAttribute("data-page");
        var per_page = $('#formOptionCompany #selectPerPageOptionCompany').val();
        var search = $('#formOptionCompany #inputSearchOptionCompany').val();

        loadDataOptionCompany(page, per_page, search);
    });

    $('body').on('change', '#formOptionCompany #selectPerPageOptionCompany', function (e) {
        e.preventDefault();
        var per_page = $('#formOptionCompany #selectPerPageOptionCompany').val();
        var start_record = $('#formOptionCompany #startRecordOptionCompany').html();
        var page = Math.ceil(start_record / per_page);
        var search = $('#formOptionCompany #inputSearchOptionCompany').val();

        loadDataOptionCompany(page, per_page, search);
    });

    $('body').on('click', '#formOptionCompany #btnSearchOptionCompany', function (e) {
        e.preventDefault();
        var per_page = $('#formOptionCompany #selectPerPageCompany').val();
        var search = $('#formOptionCompany #inputSearchOptionCompany').val();

        loadDataOptionCompany(1, per_page, search);
    });

    $('#formOptionCompany #inputSearchOptionCompany').on('change', function (e) {
        e.preventDefault();
        var per_page = $('#formOptionCompany #selectPerPageCompany').val();
        var search = $('#formOptionCompany #inputSearchOptionCompany').val();

        loadDataOptionCompany(1, per_page, search);
    });
});
