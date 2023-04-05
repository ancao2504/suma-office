var pages = 1;

function reloadDaftarHistorySaldo(page = 1, per_page = 20, start_date = '', end_date = '') {
    loading.block();
    window.location.href = window.location.origin + window.location.pathname + '?page=' + page + '&per_page=' + per_page +
            '&start_date=' + start_date + '&end_date=' + end_date;
}

function loadMoreDaftarHistorySaldo(page = 0, per_page = 20, start_date = '', end_date = '') {
    loading.block();
    $.ajax({
        url: url.daftar_history_saldo_group,
        method: "get",
        data: { page: page + 1, per_page: per_page, start_date: start_date,
                end_date: end_date },

        success: function (response) {
            loading.release();
            if (response.status == true) {
                if(response.data == '') {
                    return;
                }
                pages = parseFloat(pages) + 1;
                $('#tableHistorySaldo > tbody:last-child').append(response.data);
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
    });
}

$(document).ready(function () {
    $("#inputStartDate").flatpickr({
        clickOpens: true,
        dateFormat: 'Y-m-d',
        defaultDate: moment(data.start_date).format('YYYY-MM-DD')
    });
    $("#inputEndDate").flatpickr({
        clickOpens: true,
        dateFormat: 'Y-m-d',
        minDate: moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD'),
        maxDate: moment(new Date($("#inputStartDate").val()).fp_incr(6)).format('YYYY-MM-DD'),
        defaultDate: moment(data.end_date).format('YYYY-MM-DD')
    });

    function formatNumber(val) {
        var sign = 1;
        if (val < 0) {
            sign = -1;
            val = -val;
        }
        let num = val.toString().includes('.') ? val.toString().split('.')[0] : val.toString();
        while (/(\d+)(\d{3})/.test(num.toString())) {
            num = num.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
        }
        if (val.toString().includes('.')) {
            num = num + '.' + val.toString().split('.')[1];
        }
        return sign < 0 ? '-' + num : num;
    }

    // ===============================================================
    // Scroll
    // ===============================================================
    window.onbeforeunload = function () {
        window.scrollTo(0, 0);
    }

    $(window).scroll(function () {
        if (loading.isBlocked() === false) {
            if ($(window).scrollTop() >= $(document).height() - $(window).height() - 10) {
                var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
                var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');

                loadMoreDaftarHistorySaldo(pages, 20, start_date, end_date);
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
            maxDate: moment(new Date($("#inputStartDate").val()).fp_incr(6)).format('YYYY-MM-DD'),
            defaultDate: moment(new Date($("#inputStartDate").val()).fp_incr(6)).format('YYYY-MM-DD')
        });
    });

    $('#btnFilterMasterData').on('click', function (e) {
        e.preventDefault();
        var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
        var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');

        reloadDaftarHistorySaldo(1, 20, start_date, end_date);
    });

    $('body').on('click', '#btnDetail', function (e) {
        e.preventDefault();
        var nomor_invoice = $(this).data("nomor_invoice");
        $('#textNomorInvoice').text(nomor_invoice);

        loading.block();
        $.ajax({
            url: url.detail_history_saldo_group,
            method: "get",
            data: {
                nomor_invoice: nomor_invoice
            },
            success: function(response) {
                loading.release();

                if(response.status == true) {
                    $('#textHargaProduk').text(formatNumber(response.data.original_cost_of_goods_sold));
                    $('#textBiayaAdministrasi').text(formatNumber('-'+response.data.commission_fee));
                    $('#textBiayaLayanan').text(formatNumber('-'+response.data.service_fee));
                    $('#textBiayaPremi').text(formatNumber('-'+response.data.delivery_seller_protection_fee_premium_amount));
                    $('#textTotalBiayaAdmin').text(formatNumber('-'+response.data.admin_amount));
                    $('#textTotalPenghasilan').text(formatNumber(response.data.escrow_amount));

                    var total_faktur = 0;
                    jQuery.each(response.data.faktur, (index, item) => {
                        total_faktur = parseFloat(total_faktur) + parseFloat(item.total);
                    });

                    if(parseFloat(total_faktur) != parseFloat(response.data.original_cost_of_goods_sold)) {
                        var infoInvoice = '<div class="alert alert-danger d-flex align-items-center p-5 mb-10">'+
                                '<div class="d-flex flex-column">'+
                                    '<h4 class="mb-1 text-danger">Informasi</h4>'+
                                    '<span>Total faktur dan total penjualan produk tidak sama</span>'+
                                '</div>'+
                            '</div>';

                        $('#textInfoInvoice').html(infoInvoice);
                        $('#textHargaProduk').addClass('text-danger');
                    } else {
                        $('#textInfoInvoice').html('');
                        $('#textHargaProduk').addClass('text-success');
                    }

                    $("#tableFakturInternal tbody").empty();
                    jQuery.each(response.data.faktur, (index, item) => {
                        var view_nomor_faktur = '<td class="ps-3 pe-3 fs-7 fw-bolder" style="text-align:left;vertical-align:center;">'+item.nomor_faktur+'</td>';

                        if(parseFloat(total_faktur) != parseFloat(response.data.original_cost_of_goods_sold)) {
                            var view_total_faktur = '<td class="ps-3 pe-3 fs-7 fw-bolder text-danger" style="text-align:right;vertical-align:center;">'+formatNumber(item.total)+'</td>';
                        } else {
                            var view_total_faktur = '<td class="ps-3 pe-3 fs-7 fw-bolder text-success" style="text-align:right;vertical-align:center;">'+formatNumber(item.total)+'</td>';
                        }

                        $('#tableFakturInternal').find('tbody').append('<tr>'+view_nomor_faktur+view_total_faktur+'</tr>');
                    });

                    $('#modalDetailSaldo').modal('show');
                } else {
                    Swal.fire({
                        text: response.message,
                        icon: "warning",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-warning"
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
});
