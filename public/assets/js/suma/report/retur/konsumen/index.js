let date = {
    tgl_claim: {
        start: moment().subtract(0, "days"),
        end: moment()
    },
    tgl_terima: {
        start: moment().subtract(0, "days"),
        end: moment()
    }
}

function report(page = 1) {
    loading.block();
        $('#table_list tfoot').addClass('d-none');
        $('#table_list .card-footer').addClass('d-none');
        $('#table_list tbody').empty();
        $('#table_list tbody').html(`
            <tr>
                <td colspan="8" class="text-center text-primary">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </td>
            </tr>`);

        $.post(window.location.href,
            {
                _token: $('meta[name="csrf-token"]').attr('content'),
                tgl_claim: $('#tgl_claim').val()==''?'':[date.tgl_claim.start.format('YYYY-MM-DD'), date.tgl_claim.end.format('YYYY-MM-DD')],
                tgl_terima: $('#tgl_terima').val()==''?'':[date.tgl_terima.start.format('YYYY-MM-DD'), date.tgl_terima.end.format('YYYY-MM-DD')],
                kd_sales: $('#kd_sales').val(),
                kd_dealer: $('#kd_dealer').val(),
                no_faktur: $('#no_faktur').val(),
                kd_part: $('#kd_part').val(),
                sts: $('#sts').val(),
                page: page,
                per_page: $('#per_page').val(),
            }, function (response) {
                if (response.status == '1') {
                    $('body').attr('data-kt-aside-minimize', 'on');
                    $('#kt_aside_toggle').addClass('active');

                    $('#table_list tbody').empty();
                    if($.isEmptyObject(response.data.data) && response.data.data.length == 0){
                        $('#table_list tbody').html(`
                            <tr>
                                <td colspan="8" class="text-center text-danger"> Tidak ada data </td>
                            </tr>
                        `);
                        return false;
                    }

                    let no = response.data.from;
                    let total_qty_claim = 0;
                    let total_qty_dikirim = 0;
                    $.each(response.data.data, function (key, value) {
                        $('#table_list tbody').append(`
                            <tr class="fw-bolder fs-8 border">
                                <td class="text-center">${ no++}</td>
                                <td>${value.no_retur?value.no_retur:'-'}</td>
                                <td>${value.no_faktur?value.no_faktur:'-'}</td>
                                <td>${value.kd_part?value.kd_part:'-'}</td>
                                <td class="text-end">${value.qty_claim?value.qty_claim:'-'}</td>
                                <td class="text-end">${value.qty_dikirim?value.qty_dikirim:'-'}</td>
                                <td>${value.ket?value.ket:'-'}</td>
                                <td>${value.status?value.status:'-'}</td>
                            </tr>
                        `);
                        total_qty_claim += parseInt(value.qty_claim);
                        total_qty_dikirim += parseInt(value.qty_dikirim);
                    });
                    $('#table_list tbody').append(`
                        <tr class="fw-bolder fs-8 border bg-secondary">
                            <td colspan="4" class="text-center"> Total : </td>
                            <td class="text-end">${total_qty_claim?total_qty_claim:'-'}</td>
                            <td class="text-end">${total_qty_dikirim?total_qty_dikirim:'-'}</td>
                            <td colspan="2"></td>
                        </tr>
                    `);

                    // pagination
                    $('#table_list .pagination').empty();
                    response.data.links.map(data => {
                        $('#table_list .pagination').append(`
                            <li class="page-item ${(data.active) ? 'active' : ''} ${(data.url) ? '' : 'disabled'}">
                                <a class="page-link" data-page="${(data.url) ? data.url.split('?page=')[1] : ''}" href="#">${data.label.replace('pagination.previous', '<i class="fas fa-angle-double-left"></i>').replace('pagination.next', '<i class="fas fa-angle-double-right"></i>')}</a>
                            </li >
                        `);
                    });
                    $('#table_list .jmldta').text('Jumlah data : ' + response.data.total);
                    $('#table_list .card-footer').removeClass('d-none');
                }
                if (response.status == '0') {
                    toastr.error(response.message, "Error");
                }
                if (response.status == '2') {
                    swal.fire({
                        title: 'Perhatian!',
                        text: response.message,
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-secondary'
                        },
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                }
        }).fail(function (jqXHR, textStatus, error) {
            $('#dealer-list .close').trigger('click')
            Swal.fire({
                title: 'Error ' + jqXHR.status,
                text: 'Terjadi kesalahan, silahkan coba beberapa saat lagi',
                icon: 'error',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-secondary'
                },
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });
        }).always(function () {
            loading.release();
        });
}

$(document).ready(function () {
    $("#tgl_claim").on('focus', function () {
        $("#tgl_claim").daterangepicker({
            format: 'DD/MM/YYYY',
            startDate: date.tgl_claim.start,
            endDate: date.tgl_claim.end,
            ranges: {
                "Hari ini": [moment(), moment()],
                "Kemarin": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                "1 minggu terakhir": [moment().subtract(6, "days"), moment()],
                "Bulan ini": [moment().startOf("month"), moment().endOf("month")],
                "Bulan Kemarin": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
            }
        }, function (start, end) {
            date.tgl_claim.start = start
            date.tgl_claim.end = end
        });
    });

    $("#tgl_terima").on('focus', function () {
        $("#tgl_terima").daterangepicker({
            startDate: date.tgl_terima.start,
            endDate: date.tgl_terima.end,
            ranges: {
                "Hari ini": [moment(), moment()],
                "Kemarin": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                "1 minggu terakhir": [moment().subtract(6, "days"), moment()],
                "Bulan ini": [moment().startOf("month"), moment().endOf("month")],
                "Bulan Kemarin": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
            }
        }, function (start, end) {
            date.tgl_terima.start = start
            date.tgl_terima.end = end
        });
    });

    $('.btn-smt').on('click', function (e) {
        e.preventDefault();
        report(1);
    });

    $('#export_exel').on('click', function () {
        if ($('#tgl_claim').val() == '' && $('#tgl_terima').val() == '' && $('#kd_sales').val() == '' && $('#kd_dealer').val() == '' && $('#no_faktur').val() == '' && $('#kd_part').val() == '' && $('#sts').val() == '') {
            toastr.warning('Anda belum mengatur filter apapun', "Warning");
            return false;
        }

        $.ajax({
            url: window.location.origin + window.location.pathname + '/export',
            method: 'POST',
            xhrFields: {
                responseType: 'blob'
            },
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                tgl_claim: $('#tgl_claim').val() == '' ? '' : [date.tgl_claim.start.format('YYYY-MM-DD'), date.tgl_claim.end.format('YYYY-MM-DD')],
                tgl_terima: $('#tgl_terima').val() == '' ? '' : [date.tgl_terima.start.format('YYYY-MM-DD'), date.tgl_terima.end.format('YYYY-MM-DD')],
                kd_sales: $('#kd_sales').val(),
                kd_dealer: $('#kd_dealer').val(),
                no_faktur: $('#no_faktur').val(),
                kd_part: $('#kd_part').val(),
                sts: $('#sts').val(),
            },
            beforeSend: function () {
                loading.block();
            }
        }).done(function (response) {
            if (response.status == '0') {
                toastr.error(response.message, "Error");
                return false;
            }
            if (response.status == '2') {
                swal.fire({
                    title: 'Perhatian!',
                    text: response.message,
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-secondary'
                    },
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
                return false;
            }
            
            var blob = new Blob([response], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            });
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = 'Retur Konsumen_' + ($('#tgl_claim').val() != ''? ' Tanggal Claim =' + date.tgl_claim.start.format('DD-MM-YYYY') + ' s/d ' + date.tgl_claim.end.format('DD-MM-YYYY') : '') + ($('#tgl_terima').val() != ''? ' Tanggal Terima =' + date.tgl_terima.start.format('DD-MM-YYYY') + ' s/d ' + date.tgl_terima.end.format('DD-MM-YYYY') : '') + ($('#kd_sales').val() != ''? ' Sales =' + $('#kd_sales').val() : '') + ($('#kd_dealer').val() != ''? ' Dealer =' + $('#kd_dealer').val() : '') + ($('#no_faktur').val() != ''? ' No Faktur =' + $('#no_faktur').val() : '') + ($('#kd_part').val() != ''? ' Kode Part =' + $('#kd_part').val() : '') + ($('#sts').val() != ''? ' Status =' + $('#sts').val() : '') + '.xlsx';
            link.click();
            link.remove();
        }).fail(function (jqXHR, textStatus, error) {
            Swal.fire({
                title: 'Perhatian!',
                text: 'Data Terlalu Banyak, Silahkan Filter Data Lebih Spesifik',
                icon: 'error',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-secondary'
                },
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });
        }).always(function () {
            loading.release();
        });


        
        // window.open(baseurl + `/report/retur/konsumen/export?tgl_claim=${$('#tgl_claim').val() == '' ? '' : [date.tgl_claim.start.format('YYYY-MM-DD'), date.tgl_claim.end.format('YYYY-MM-DD')]}&tgl_terima=${$('#tgl_terima').val() == '' ? '' : [date.tgl_terima.start.format('YYYY-MM-DD'), date.tgl_terima.end.format('YYYY-MM-DD')]}&kd_sales=${$('#kd_sales').val()}&kd_dealer=${$('#kd_dealer').val()}&no_faktur=${$('#no_faktur').val()}&kd_part=${$('#kd_part').val()}&sts=${$('#sts').val()}`, '_blank');
    });

    $('#table_list .pagination').on('click', '.page-item:not(.disabled)', function () {
        report($(this).find('a').data('page'));
    });

    $('#table_list #per_page').on('change', function () {
        report(1);
    });
});