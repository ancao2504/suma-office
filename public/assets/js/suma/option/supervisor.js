function loadDataSupervisor(page = 1, per_page = 10, search = '') {
    loading.block();
    $.ajax({
        url: base_url + '/option/supervisor' + "?search=" + search + "&per_page=" + per_page + "&page=" + page,
        method: "get",
        success: function (response) {
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
                $('#supervisorContentModal').html(response.data);
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
    $(document).on('click', '#searchSupervisorForm #pageSupervisor .pagination .page-item a', function () {
        pages = $(this)[0].getAttribute("data-page");
        page = pages.split('?page=')[1];

        var search_spv = $('#searchSupervisorForm #inputSearchSupervisor').val();
        var per_page_spv = $('#searchSupervisorForm #supervisorContentModal #pageSupervisor #selectPerPageSupervisor').val();

        loadDataSupervisor(page, per_page_spv, search_spv);
    });

    $('body').on('change', '#searchSupervisorForm #supervisorContentModal #pageSupervisor #selectPerPageSupervisor', function (e) {
        e.preventDefault();

        var start_record_spv = $('#searchSupervisorForm #supervisorContentModal #pageSupervisor #selectPerPageSupervisorInfo #startRecordSupervisor').html();
        var search_spv = $('#searchSupervisorForm #inputSearchSupervisor').val();
        var per_page_spv = $('#searchSupervisorForm #supervisorContentModal #pageSupervisor #selectPerPageSupervisor').val();

        var page = Math.ceil(start_record_spv / per_page_spv);

        loadDataSupervisor(page, per_page_spv, search_spv);
    });

    $('body').on('click', '#searchSupervisorForm #btnSearchSupervisor', function (e) {
        e.preventDefault();
        var search_spv = $('#searchSupervisorForm #inputSearchSupervisor').val();
        var per_page_spv = $('#searchSupervisorForm #supervisorContentModal #pageSupervisor #selectPerPageSupervisor').val();

        loadDataSupervisor(1, per_page_spv, search_spv);
    });

    $('#searchSupervisorForm #inputSearchSupervisor').on('change', function (e) {
        e.preventDefault();
        var search_spv = $('#searchSupervisorForm #inputSearchSupervisor').val();
        var per_page_spv = $('#searchSupervisorForm #supervisorContentModal #pageSupervisor #selectPerPageSupervisor').val();

        loadDataSupervisor(1, per_page_spv, search_spv);
    });
});
