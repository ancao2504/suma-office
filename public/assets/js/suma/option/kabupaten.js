function loadDataOptionKabupaten(page = 1, per_page = 10, search = '') {
    loading.block();
    $.ajax({
        url: base_url + '/option/kabupaten' + "?search=" + search + "&per_page=" + per_page + "&page=" + page,
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
                $('#optionKabupatenContentModal').html(response.data);
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
    $(document).on('click', '#formOptionKabupaten #paginationOptionKabupaten .page-item a', function () {
        var page = $(this)[0].getAttribute("data-page");
        var per_page = $('#formOptionKabupaten #selectPerPageOptionKabupaten').val();
        var search = $('#formOptionKabupaten #inputSearchOptionKabupaten').val();

        loadDataOptionKabupaten(page, per_page, search);
    });

    $('body').on('change', '#formOptionKabupaten #selectPerPageOptionKabupaten', function (e) {
        e.preventDefault();
        var per_page = $('#formOptionKabupaten #selectPerPageOptionKabupaten').val();
        var start_record = $('#formOptionKabupaten #startRecordOptionKabupaten').html();
        var page = Math.ceil(start_record / per_page);
        var search = $('#formOptionKabupaten #inputSearchOptionKabupaten').val();

        loadDataOptionKabupaten(page, per_page, search);
    });

    $('body').on('click', '#formOptionKabupaten #btnSearchOptionKabupaten', function (e) {
        e.preventDefault();
        var per_page = $('#formOptionKabupaten #selectPerPageKabupaten').val();
        var search = $('#formOptionKabupaten #inputSearchOptionKabupaten').val();

        loadDataOptionKabupaten(1, per_page, search);
    });

    $('#formOptionKabupaten #inputSearchOptionKabupaten').on('change', function (e) {
        e.preventDefault();
        var per_page = $('#formOptionKabupaten #selectPerPageKabupaten').val();
        var search = $('#formOptionKabupaten #inputSearchOptionKabupaten').val();

        loadDataOptionKabupaten(1, per_page, search);
    });
});
