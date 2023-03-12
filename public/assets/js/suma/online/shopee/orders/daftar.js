function reloadDaftarOrders(cursor = 0, page_size = 10, fields = 'create_time', start_date = '', end_date = '', status = '') {
    loading.block();
    window.location.href = window.location.origin + window.location.pathname + '?fields=' + fields +
        '&start_date=' + start_date + '&end_date=' + end_date + '&status=' + status +
        '&page_size=' + page_size + '&cursor=' + cursor;

}

function loadDaftarOrders(cursor = 0, page_size = 10, fields = 'create_time', start_date = '', end_date = '', status = '') {
    loading.block();
    $.ajax({
        url: url.daftar_order,
        method: "get",
        data: { cursor: cursor, page_size: page_size, fields: fields,
                start_date: start_date, end_date: end_date,
                status: status },

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
        maxDate: moment(new Date($("#inputStartDate").val()).fp_incr(14)).format('YYYY-MM-DD'),
        defaultDate: moment(new Date($("#inputStartDate").val()).fp_incr(14)).format('YYYY-MM-DD')
    });

    // ===============================================================
    // Scroll
    // ===============================================================
    var pages = 0;

    window.onbeforeunload = function () {
        window.scrollTo(0, 0);
    }

    $(window).scroll(function () {
        if (loading.isBlocked() === false) {
            if ($(window).scrollTop() >= $(document).height() - $(window).height() - 10) {
                var fields = $('#selectFields').val();
                var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
                var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');
                var status = $('#selectStatus').val();

                pages++;
                loadDaftarOrders(pages, 10, fields, start_date, end_date, status);
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
            maxDate: moment(new Date($("#inputStartDate").val()).fp_incr(14)).format('YYYY-MM-DD'),
            defaultDate: moment(new Date($("#inputStartDate").val()).fp_incr(14)).format('YYYY-MM-DD')
        });
    });

    $('#btnFilterMasterData').on('click', function (e) {
        e.preventDefault();
        var fields = $('#selectFields').val();
        var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
        var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');
        var status = $('#selectStatus').val();
        reloadDaftarOrders(0, 10, fields, start_date, end_date, status);
    });

    $('#selectStatus').on('change', function (e) {
        e.preventDefault();
        var fields = $('#selectFields').val();
        var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
        var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');
        var status = $('#selectStatus').val();
        reloadDaftarOrders(0, 10, fields, start_date, end_date, status);
    });

    $('#navSemuaProses').on('click', function (e) {
        e.preventDefault();
        var fields = $('#selectFields').val();
        var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
        var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');
        var status = $('#selectStatus').val();
        reloadDaftarOrders(0, 10, fields, start_date, end_date, status);
    });

    $('#navBelumProses').on('click', function (e) {
        e.preventDefault();
        var fields = $('#selectFields').val();
        var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
        var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');
        reloadDaftarOrders(0, 10, fields, start_date, end_date, 'READY_TO_SHIP');
    });

    $('body').on('click', '#btnDetailInvoice', function (e) {
        loading.block();
    });

    $('body').on('click', '#btnRequestPickup', function (e) {
        e.preventDefault();
        var nomor_invoice = $(this).data("nomor_invoice");
        var _token = $('input[name="_token"]').val();

        Swal.fire({
            html: `Apakah anda yakin akan memproses pickup nomor invoice
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
                    url: url.proses_pickup,
                    method: "POST",
                    data: { nomor_invoice: nomor_invoice, _token: _token },

                    success: function (response) {
                        loading.release();

                        if (response.status == true) {
                            Swal.fire({
                                html: response.message,
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
                                    var fields = $('#selectFields').val();
                                    var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
                                    var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');
                                    var status = $('#selectStatus').val();
                                    reloadDaftarOrders(0, 10, fields, start_date, end_date, status);
                                }
                            });
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
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    var fields = $('#selectFields').val();
                                    var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
                                    var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');
                                    var status = $('#selectStatus').val();
                                    reloadDaftarOrders(0, 10, fields, start_date, end_date, status);
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

                if(response.status == true) {
                    console.log(response.data.url+response.data.parameter);

                    $.ajax({
                        url: response.data.url,
                        method: "POST",
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        data: JSON.stringify(response.data.parameter),

                        success: function (response) {
                            if(response.error != '') {
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
                        error: function() {
                            Swal.fire({
                                html: 'Server shopee tidak merespon, coba lagi',
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
});
