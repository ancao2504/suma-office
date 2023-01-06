// =====================================================================
// Load Data Produk
// =====================================================================
function loadDataOptionProduk(page = 1, per_page = 10, level = '', search = '') {
    $('#searchProdukForm #inputFilterLevelProduk').html(level);

    loading.block();
    $.ajax({
        url: base_url + '/option/groupproduk' + "?level=" + level + "&search=" + search +
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
                $('#optionProdukContentModal').html(response.data);
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
    $(document).on('click', '#formOptionGroupProduk #paginationOptionGroupProduk .page-item a', function () {
        var page = $(this)[0].getAttribute("data-page");
        var per_page = $('#formOptionGroupProduk #selectPerPageOptionProduk').val();
        var level = $('#formOptionGroupProduk #inputOptionLevelProduk').html();
        var search = $('#formOptionGroupProduk #inputSearchOptionProduk').val();

        loadDataOptionProduk(page, per_page, level, search);
    });

    $('body').on('change', '#formOptionGroupProduk #selectPerPageOptionProduk', function (e) {
        e.preventDefault();
        var per_page = $('#formOptionGroupProduk #selectPerPageOptionProduk').val();
        var page = Math.ceil(start_record_produk / per_page);
        var start_record_produk = $('#formOptionGroupProduk #startRecordOptionProduk').html();
        var level = $('#formOptionGroupProduk #inputFilterLevelProduk').html();
        var search = $('#formOptionGroupProduk #inputSearchOptionProduk').val();

        loadDataOptionProduk(page, per_page, level, search);
    });

    $('body').on('click', '#formOptionGroupProduk #btnSearchOptionProduk', function (e) {
        e.preventDefault();
        var per_page = $('#formOptionGroupProduk #selectPerPageProduk').val();
        var level = $('#formOptionGroupProduk #inputFilterLevelProduk').html();
        var search = $('#formOptionGroupProduk #inputSearchOptionProduk').val();

        loadDataOptionProduk(1, per_page, level, search);
    });

    $('#searchProdukForm #inputSearchOptionProduk').on('change', function (e) {
        e.preventDefault();
        var per_page = $('#formOptionGroupProduk #selectPerPageProduk').val();
        var level = $('#formOptionGroupProduk #inputFilterLevelProduk').html();
        var search = $('#formOptionGroupProduk #inputSearchOptionProduk').val();

        loadDataOptionProduk(1, per_page, level, search);
    });
});
