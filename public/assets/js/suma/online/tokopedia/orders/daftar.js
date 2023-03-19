function reloadDaftarOrders(page = 1, per_page = 10, start_date = '', end_date = '', status = '') {
    loading.block();
    window.location.href = window.location.origin + window.location.pathname + '?page=' + page + '&per_page=' + per_page +
        '&start_date=' + start_date + '&end_date=' + end_date + '&status=' + status;
}

function loadDaftarOrders(page = 1, per_page = 10, start_date = '', end_date = '', status = '') {
    loading.block();
    $.ajax({
        url: url.daftar_order,
        method: "get",
        data: { page: page, per_page: per_page, start_date: start_date,
                end_date: end_date, status: status },

        success: function (response) {
            loading.release();

            if (response.status == true) {
                if(response.data == '') {
                    return;
                }
                $('#postOrder').append(response.data);
            } else {
                Swal.fire({
                    text: response.message,
                    icon: "warning",
                    buttonsStyling: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-warning"
                    }
                });
            }
        },
        error: function () {
            loading.release();
            Swal.fire({
                text: 'Server not responding',
                icon: "error",
                buttonsStyling: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-danger"
                }
            });
        }
    })
}

$(document).ready(function () {
    $("#inputStartDate").flatpickr({
        clickOpens: true,
        dateFormat: 'Y-m-d'
    });
    $("#inputEndDate").flatpickr({
        clickOpens: true,
        dateFormat: 'Y-m-d',
        minDate: moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD'),
        maxDate: moment(new Date($("#inputStartDate").val()).fp_incr(2)).format('YYYY-MM-DD'),
        defaultDate: moment(new Date($("#inputStartDate").val()).fp_incr(2)).format('YYYY-MM-DD')
    });

    // ===============================================================
    // Scroll
    // ===============================================================
    var pages = 1;

    window.onbeforeunload = function () {
        window.scrollTo(0, 0);
    }

    $(window).scroll(function () {
        if (loading.isBlocked() === false) {
            if ($(window).scrollTop() >= $(document).height() - $(window).height() - 10) {
                var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
                var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');
                var status = $('#selectStatus').find(":selected").val();

                pages++;
                loadDaftarOrders(pages, 10, start_date, end_date, status);
            }
        }
    });

    // ===============================================================
    // Filter
    // ===============================================================
    $('#inputStartDate').on('change', function (e) {
        e.preventDefault();
        $("#inputEndDate").flatpickr({
            clickOpens: true,
            dateFormat: 'Y-m-d',
            minDate: moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD'),
            maxDate: moment(new Date($("#inputStartDate").val()).fp_incr(2)).format('YYYY-MM-DD'),
            defaultDate: moment(new Date($("#inputStartDate").val()).fp_incr(2)).format('YYYY-MM-DD')
        });
    });

    $('#btnFilterMasterData').on('click', function (e) {
        e.preventDefault();
        var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
        var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');
        var status = $('#selectStatus').find(":selected").val();
        reloadDaftarOrders(1, 10, start_date, end_date, status);
    });

    $('#selectStatus').on('change', function (e) {
        e.preventDefault();
        var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
        var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');
        var status = $('#selectStatus').find(":selected").val();
        reloadDaftarOrders(1, 10, start_date, end_date, status);
    });

    $('#navSemuaProses').on('click', function (e) {
        e.preventDefault();
        var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
        var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');
        reloadDaftarOrders(1, 10, start_date, end_date, '');
    });

    $('#navBelumProses').on('click', function (e) {
        e.preventDefault();
        var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
        var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');
        reloadDaftarOrders(1, 10, start_date, end_date, '220');
    });

    $('#navRequestPickup').on('click', function (e) {
        e.preventDefault();
        var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
        var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');
        reloadDaftarOrders(1, 10, start_date, end_date, '400');
    });

    $('body').on('click', '#btnDetailInvoice', function () {
        loading.block();
    });

    $('body').on('click', '#btnCetakLabel', function (e) {
        e.preventDefault();
        var nomor_invoice = $(this).data("nomor_invoice");
        var _token = $('input[name="_token"]').val();

        loading.block();
        $.ajax({
            url: url.proses_cetak_label,
            method: "POST",
            data: { nomor_invoice: nomor_invoice, _token: _token },

            success: function (response) {
                loading.release();

                if (response.status == true) {
                    var newWindow = window.open('url', '_blank');
                    newWindow.document.open();
                    newWindow.document.write(response.data);
                    newWindow.document.close();
                } else {
                    Swal.fire({
                        html: response.message,
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
        });
    });

    $('body').on('click', '#btnRequestPickup', function (e) {
        e.preventDefault();
        var nomor_invoice = $(this).data("nomor_invoice");
        var _token = $('input[name="_token"]').val();

        Swal.fire({
            html: `Apakah anda yakin akan memproses request pickup nomor invoice
                    <strong>`+ nomor_invoice + `</strong> ?`,
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
                    url: url.proses_request_pickup_tokopedia,
                    method: "post",
                    data: {
                        nomor_invoice: nomor_invoice, _token: _token
                    },
                    success: function(response) {
                        loading.release();

                        if (response.status == true) {
                            Swal.fire({
                                text: response.message,
                                icon: 'success',
                                buttonsStyling: false,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                confirmButtonText: 'Ok, got it!',
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
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
        });
    });
});
