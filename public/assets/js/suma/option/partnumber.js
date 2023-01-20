
    // jika terdapat ajax maka loading
    $(document).ajaxStart(function () {
        loading.block();
    });
    // ajax selesai maka loading di release
    $(document).ajaxStop(function () {
        loading.release();
    });

// =====================================================================
// Load Data Part Number
// =====================================================================
function loadDataPartNumber(page = 1, per_page = 10, search = '') {
    $.ajax({
        url: base_url + '/option/partnumber' + "?search=" + search + "&per_page=" + per_page + "&page=" + page,
        method: "get",
        success: function (response) {
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
                $('#optionPartNumberContentModal').html(response.data);
            }
        },
        error: function() {
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

function defaultImage(img) {
    img.src = base_url + '/assets/images/background/part_image_not_found.png';
}

$(document).ready(function () {

    $('#modalOptionPartNumber').on('change', '#selectPerPageOptionPartNumber', function (e) {
        e.preventDefault();

        var start_record_part_number = $('#selectedOptionPartNumber #optionPartNumberContentModal #pagePartNumber #selectPerPagePartNumberInfo #startRecordPartNumber').html();
        var search_part_number = $('#modalOptionPartNumber #inputSearchOptionPartNumber').val();
        var per_page_part_number = $('#modalOptionPartNumber #selectPerPageOptionPartNumber').val();

        var page = Math.ceil(start_record_part_number / per_page_part_number);

        loadDataPartNumber(page, per_page_part_number, search_part_number);
    });

    // $('body').on('click', '#selectedOptionPartNumber #btnSearchOptionPartNumber', function (e) {
    $('#modalOptionPartNumber #btnSearchOptionPartNumber').on('click', function (e) {
        e.preventDefault();
        var search_part_number = $('#modalOptionPartNumber #inputSearchOptionPartNumber').val();
        var per_page_part_number = $('#modalOptionPartNumber #selectPerPageOptionPartNumber').val();

        loadDataPartNumber(1, per_page_part_number, search_part_number);
    });

    $('#modalOptionPartNumber #inputSearchOptionPartNumber').on('change', function (e) {
        e.preventDefault();
        var search_part_number = $('#modalOptionPartNumber #inputSearchOptionPartNumber').val();
        var per_page_part_number = $('#modalOptionPartNumber #selectPerPageOptionPartNumber').val();

        loadDataPartNumber(1, per_page_part_number, search_part_number);
    });

    $('#modalOptionPartNumber').on('click', '.pagination a.page-link',function () {
        // pages = $(this)[0].getAttribute("data-page");
        // page = pages.split('?page=')[1];
        page = $(this).data('page');
    
        var search_part_number = $('#modalOptionPartNumber #inputSearchOptionPartNumber').val();
        var per_page_part_number = $('#modalOptionPartNumber #selectPerPageOptionPartNumber').val();
    
        loadDataPartNumber(page, per_page_part_number, search_part_number);
    });
});
