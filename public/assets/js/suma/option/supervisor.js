function loadDataOptionSupervisor(page = 1, per_page = 10, search = '') {
    loading.block();
    $.ajax({
        url: base_url + '/option/supervisor' + "?search=" + search + "&per_page=" + per_page + "&page=" + page,
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
                $('#optionSupervisorContentModal').html(response.data);
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
    $(document).on('click', '#formOptionSupervisor #paginationOptionSupervisor .page-item a', function () {
        var page = $(this)[0].getAttribute("data-page");
        var per_page = $('#formOptionSupervisor #selectPerPageOptionSupervisor').val();
        var search = $('#formOptionSupervisor #inputSearchOptionSupervisor').val();

        loadDataOptionSupervisor(page, per_page, search);
    });

    $('body').on('change', '#formOptionSupervisor #selectPerPageOptionSupervisor', function (e) {
        e.preventDefault();
        var per_page = $('#formOptionSupervisor #selectPerPageOptionSupervisor').val();
        var start_record = $('#formOptionSupervisor #startRecordOptionSupervisor').html();
        var page = Math.ceil(start_record / per_page);
        var search = $('#formOptionSupervisor #inputSearchOptionSupervisor').val();

        loadDataOptionSupervisor(page, per_page, search);
    });

    $('body').on('click', '#formOptionSupervisor #btnSearchOptionSupervisor', function (e) {
        e.preventDefault();
        var per_page = $('#formOptionSupervisor #selectPerPageSupervisor').val();
        var search = $('#formOptionSupervisor #inputSearchOptionSupervisor').val();

        loadDataOptionSupervisor(1, per_page, search);
    });

    $('#formOptionSupervisor #inputSearchOptionSupervisor').on('change', function (e) {
        e.preventDefault();
        var per_page = $('#formOptionSupervisor #selectPerPageSupervisor').val();
        var search = $('#formOptionSupervisor #inputSearchOptionSupervisor').val();

        loadDataOptionSupervisor(1, per_page, search);
    });
});
