// =====================================================================
// Load Data Tipe Motor
// =====================================================================
function loadDataOptionTipeMotor(page = 1, per_page = 10, search = '') {
    loading.block();
    $.ajax({
        url: base_url + '/option/tipemotor' + "?search=" + search +
                "&per_page=" + per_page + "&page=" + page,
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
                $('#optionTipeMotorContentModal').html(response.data);
            }
        },
        error: function () {
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
    $(document).on('click', '#formOptionTipeMotor #paginationOptionTipeMotor .page-item a', function () {
        var page = $(this)[0].getAttribute("data-page");
        var per_page = $('#formOptionTipeMotor #selectPerPageOptionTipeMotor').val();
        var search = $('#formOptionTipeMotor #inputSearchOptionTipeMotor').val();

        loadDataOptionTipeMotor(page, per_page, search);
    });

    $('body').on('change', '#formOptionTipeMotor #selectPerPageOptionTipeMotor', function (e) {
        e.preventDefault();
        var per_page = $('#formOptionTipeMotor #selectPerPageOptionTipeMotor').val();
        var page = Math.ceil(start_record_tipeMotor / per_page);
        var start_record_tipeMotor = $('#formOptionTipeMotor #startRecordOptionTipeMotor').html();
        var search = $('#formOptionTipeMotor #inputSearchOptionTipeMotor').val();

        loadDataOptionTipeMotor(page, per_page, search);
    });

    $('body').on('click', '#formOptionTipeMotor #btnSearchOptionTipeMotor', function (e) {
        e.preventDefault();
        var per_page = $('#formOptionTipeMotor #selectPerPageTipeMotor').val();
        var search = $('#formOptionTipeMotor #inputSearchOptionTipeMotor').val();

        loadDataOptionTipeMotor(1, per_page, search);
    });

    $('#searchTipeMotorForm #inputSearchOptionTipeMotor').on('change', function (e) {
        e.preventDefault();
        var per_page = $('#formOptionTipeMotor #selectPerPageTipeMotor').val();
        var search = $('#formOptionTipeMotor #inputSearchOptionTipeMotor').val();

        loadDataOptionTipeMotor(1, per_page, search);
    });
});
