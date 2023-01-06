// =====================================================================
// Load Data Part Number
// =====================================================================
function loadDataPartNumber(page = 1, per_page = 10, search = '') {
    loading.block();
    $.ajax({
        url: base_url + '/option/partnumber' + "?search=" + search + "&per_page=" + per_page + "&page=" + page,
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
                $('#partNumberContentModal').html(response.data);
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
    $(document).on('click', '#searchPartNumberForm #pagePartNumber .pagination .page-item a', function () {
        pages = $(this)[0].getAttribute("data-page");
        page = pages.split('?page=')[1];

        var search_part_number = $('#searchPartNumberForm #inputSearchPartNumber').val();
        var per_page_part_number = $('#searchPartNumberForm #partNumberContentModal #pagePartNumber #selectPerPagePartNumber').val();

        loadDataPartNumber(page, per_page_part_number, search_part_number);
    });

    $('body').on('change', '#searchPartNumberForm #partNumberContentModal #pagePartNumber #selectPerPagePartNumber', function (e) {
        e.preventDefault();

        var start_record_part_number = $('#searchPartNumberForm #partNumberContentModal #pagePartNumber #selectPerPagePartNumberInfo #startRecordPartNumber').html();
        var search_part_number = $('#searchPartNumberForm #inputSearchPartNumber').val();
        var per_page_part_number = $('#searchPartNumberForm #partNumberContentModal #pagePartNumber #selectPerPagePartNumber').val();

        var page = Math.ceil(start_record_part_number / per_page_part_number);

        loadDataPartNumber(page, per_page_part_number, search_part_number);
    });

    $('body').on('click', '#searchPartNumberForm #btnSearchPartNumber', function (e) {
        e.preventDefault();
        var search_part_number = $('#searchPartNumberForm #inputSearchPartNumber').val();
        var per_page_part_number = $('#searchPartNumberForm #partNumberContentModal #pagePartNumber #selectPerPagePartNumber').val();

        loadDataPartNumber(1, per_page_part_number, search_part_number);
    });

    $('#searchPartNumberForm #inputSearchPartNumber').on('change', function (e) {
        e.preventDefault();
        var search_part_number = $('#searchPartNumberForm #inputSearchPartNumber').val();
        var per_page_part_number = $('#searchPartNumberForm #partNumberContentModal #pagePartNumber #selectPerPagePartNumber').val();

        loadDataPartNumber(1, per_page_part_number, search_part_number);
    });
});
