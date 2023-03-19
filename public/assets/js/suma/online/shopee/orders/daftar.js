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
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        contentType : 'application/json;application/pdf;charset=utf-8',
                        type: "POST",
                        url: response.data.url,
                        dataType: "blob",
                        data: JSON.stringify(response.data.parameter),
                        xhrFields: {
                            responseType: 'blob'
                        },
                    }).done(function (response) {
                        console.log(response);
                    }).fail(function(xhr, ajaxOps, error) {
                        console.log('Failed: ' + error + xhr + ajaxOps);
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

    $('body').on('click', '#btnAturPengiriman', function (e) {
        e.preventDefault();

        var nomor_invoice =  $(this).data("nomor_invoice");
        var _token = $('input[name="_token"]').val();

        loading.block();
        $.ajax({
            url: url.data_request_pickup_shopee,
            method: "post",
            data: {
                nomor_invoice: nomor_invoice, _token: _token
            },
            success: function(response) {
                loading.release();

                if (response.status == true) {
                    $('#inputNomorInvoice').val(nomor_invoice);
                    $('#selectTanggalJamPickup').find('option').remove().end().append('<option value="">Pilih tanggal & jam pickup</option>').val();
                    $('#inputKeteranganPickup').val();

                    var datetimePickup = response.data.pickup.address_list[0].time_slot_list;
                    var infoSeller = response.data.pickup.address_list[0];

                    $.each(datetimePickup, function(key, value) {
                        moment.locale('id');
                        var tanggal = moment.unix(value.date).format("dddd, DD MMMM YYYY");
                        var jam = value.time_text;
                        var newoption = new Option(tanggal.toString()+ ' = '+jam, value.pickup_time_id);

                        $("#selectTanggalJamPickup").append(newoption);
                    });

                    $('#inputIdAlamatSeller').text(infoSeller.address_id);
                    $('#inputAlamatSeller').text(infoSeller.address);
                    $('#inputKotaSeller').text(infoSeller.district+', '+infoSeller.city);
                    $('#inputProvinsiSeller').text(infoSeller.state);
                    $('#inputKodePosSeller').text(infoSeller.zipcode);

                    $('#modalRequestPickupShopee').modal('show');
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
    });

    $('#btnSimpanRequestPickupShopee').on('click', function (e) {
        e.preventDefault();
        var nomor_invoice = $('#inputNomorInvoice').val();
        var pickup_time_id = $('#selectTanggalJamPickup').find(":selected").val();
        var address_id = $('#inputIdAlamatSeller').text();
        var _token = $('input[name="_token"]').val();

        if(address_id == '' || nomor_invoice == '' || pickup_time_id == '') {
            Swal.fire({
                text: 'Isi data secara lengkap',
                icon: "warning",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-warning"
                }
            });
        } else {
            loading.block();
            $.ajax({
                url: url.proses_request_pickup_shopee,
                method: "post",
                data: {
                    nomor_invoice: nomor_invoice,
                    address_id: address_id, pickup_time_id: pickup_time_id,
                    _token: _token
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
