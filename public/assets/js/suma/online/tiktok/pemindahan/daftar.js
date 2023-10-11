
$(document).ready(function () {
    $("#inputStartDate").flatpickr({
        clickOpens: true,
        dateFormat: 'Y-m-d'
    });
    $("#inputEndDate").flatpickr({
        clickOpens: true,
        dateFormat: 'Y-m-d',
        minDate: moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD')
    });
    // ===============================================================
    // Daftar
    // ===============================================================
    function loadMasterData(page = 1, per_page = 10, start_date = '', end_date = '', search = '') {
        loading.block();
        window.location.href = window.location.origin + window.location.pathname + '?start_date=' + start_date.trim() + '&end_date=' + end_date.trim() +
            '&search=' + search.trim() + '&per_page=' + per_page + '&page=' + page;
    }

    $('#selectPerPageMasterData').change(function() {
        var start_record = data_page.start_record;
        var per_page = $('#selectPerPageMasterData').val();
        var start_date = $('#inputStartDate').val();
        var end_date = $('#inputEndDate').val();
        var search = $('#inputSearch').val();
        var page = Math.ceil(start_record / per_page);

        loadMasterData(page, per_page, start_date, end_date, search);
    });

    $(document).on('click', '#paginationMasterData .page-item a', function () {
        var page = $(this)[0].getAttribute('data-page');
        var per_page = $('#selectPerPageMasterData').val();
        var start_date = $('#inputStartDate').val();
        var end_date = $('#inputEndDate').val();
        var search = $('#inputSearch').val();

        loadMasterData(page, per_page, start_date, end_date, search);
    });

    // ===============================================================
    // Filter
    // ===============================================================
    $('#inputStartDate').change(function() {
        $("#inputEndDate").flatpickr({
            clickOpens: true,
            dateFormat: 'Y-m-d',
            minDate: moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD')
        });
    });

    $('#inputSearch').on('change', function(){
        var per_page = $('#selectPerPageMasterData').val();
        var start_date = $('#inputStartDate').val();
        var end_date = $('#inputEndDate').val();
        var search = $('#inputSearch').val();

        loadMasterData(1, per_page, start_date, end_date, search);
    });

    $('#btnFilterMasterData').on('click', function (e) {
        e.preventDefault();

        var per_page = $('#selectPerPageMasterData').val();
        var start_date = $('#inputStartDate').val();
        var end_date = $('#inputEndDate').val();
        var search = $('#inputSearch').val();

        loadMasterData(1, per_page, start_date, end_date, search);
    });

    // ===============================================================
    // Modal Update
    // ===============================================================
    $('body').on('click', '#btnUpdateStockAll', function (e) {
        e.preventDefault();
        var nomor_dokumen = $(this).data('nomor_dokumen');
        var _token = $('input[name="_token"]').val();

        Swal.fire({
            html: `Apakah anda yakin akan mengupdate stock nomor dokumen
                    <strong>`+ nomor_dokumen + `</strong> ?`,
            icon: "info",
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: 'No',
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: 'btn btn-primary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                loading.block();
                $.ajax({
                    url: url.update_per_dokumen,
                    method: "POST",
                    data: { nomor_dokumen: nomor_dokumen, _token: _token },

                    success: function (response) {
                        loading.release();

                        if (response.status == true) {
                            $('#resultUpdateStock').html(response.data.update_stock);
                            $('#resultUpdateStatus').html(response.data.update_status);
                            $('#modalResultPindahLokasi').modal('show');
                        } else {
                            Swal.fire({
                                text: response.message,
                                icon: 'warning',
                                buttonsStyling: false,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                confirmButtonText: 'Ok, got it!',
                                customClass: {
                                    confirmButton: 'btn btn-warning'
                                }
                            });
                        }
                    },
                    error: function () {
                        loading.release();
                        Swal.fire({
                            text: 'Server Not Responding',
                            icon: 'error',
                            buttonsStyling: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            confirmButtonText: 'Ok, got it!',
                            customClass: {
                                confirmButton: 'btn btn-danger'
                            }
                        });
                    }
                })
            }
        });
    });

    $('#modalResultPindahLokasi').on('hidden.bs.modal', function () {
        location.reload();
    });
});
