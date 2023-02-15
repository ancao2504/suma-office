// =====================================================================
// Load Data Tipe Motor
// =====================================================================
function loadDataOptionUpdateHarga(lokasi = '', page = 1, per_page = 10, search = '') {
    loading.block();

    $('#formOptionUpdateHarga #inputKodeLokasi').html(lokasi);

    $.ajax({
        url: base_url + '/option/updateharga' + "?lokasi="+ lokasi +"&search=" + search +
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
                $('#optionUpdateHargaContentModal').html(response.data);
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
    $(document).on('click', '#formOptionUpdateHarga #paginationOptionUpdateHarga .page-item a', function () {
        var lokasi = $('#formOptionUpdateHarga #inputKodeLokasi').html();
        var page = $(this)[0].getAttribute("data-page");
        var per_page = $('#formOptionUpdateHarga #selectPerPageOptionUpdateHarga').val();
        var search = $('#formOptionUpdateHarga #inputSearchOptionUpdateHarga').val();

        loadDataOptionUpdateHarga(lokasi, page, per_page, search);
    });

    $('body').on('change', '#formOptionUpdateHarga #selectPerPageOptionUpdateHarga', function (e) {
        e.preventDefault();
        var lokasi = $('#formOptionUpdateHarga #inputKodeLokasi').html();
        var per_page = $('#formOptionUpdateHarga #selectPerPageOptionUpdateHarga').val();
        var page = Math.ceil(start_record_tipeMotor / per_page);
        var start_record_tipeMotor = $('#formOptionUpdateHarga #startRecordOptionUpdateHarga').html();
        var search = $('#formOptionUpdateHarga #inputSearchOptionUpdateHarga').val();

        loadDataOptionUpdateHarga(lokasi, page, per_page, search);
    });

    $('body').on('click', '#formOptionUpdateHarga #btnSearchOptionUpdateHarga', function (e) {
        e.preventDefault();
        var lokasi = $('#formOptionUpdateHarga #inputKodeLokasi').html();
        var per_page = $('#formOptionUpdateHarga #selectPerPageUpdateHarga').val();
        var search = $('#formOptionUpdateHarga #inputSearchOptionUpdateHarga').val();

        loadDataOptionUpdateHarga(lokasi, 1, per_page, search);
    });

    $('#searchUpdateHargaForm #inputSearchOptionUpdateHarga').on('change', function (e) {
        e.preventDefault();

        var lokasi = $('#formOptionUpdateHarga #inputKodeLokasi').html();
        var per_page = $('#formOptionUpdateHarga #selectPerPageUpdateHarga').val();
        var search = $('#formOptionUpdateHarga #inputSearchOptionUpdateHarga').val();

        loadDataOptionUpdateHarga(lokasi, 1, per_page, search);
    });
});
