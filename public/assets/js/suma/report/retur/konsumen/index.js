let date = {
    tgl_klaim: {
        start: moment().startOf('month'),
        end: moment().endOf('month')
    }
}

function report(page = 1) {
    loading.block();
        $('#table_list tfoot').addClass('d-none');
        $('#table_list .card-footer').addClass('d-none');
        $('#table_list tbody').empty();
        $('#table_list tbody').html(`
            <tr>
                <td colspan="16" class="text-center text-primary">
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
                tgl_klaim: [date.tgl_klaim.start.format('YYYY-MM-DD'), date.tgl_klaim.end.format('YYYY-MM-DD')],
                kd_sales: $('#kd_sales').val(),
                kd_dealer: $('#kd_dealer').val(),
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
                                <td colspan="16" class="text-center text-danger"> Tidak ada data </td>
                            </tr>
                        `);
                        return false;
                    }

                    let no = response.data.from;
                    $.each(response.data.data, function (key, value) {
                        $('#table_list tbody').append(`
                            <tr class="fw-bolder fs-8 border">
                                <td class="text-center">${ no++}</td>
                                <td>${value.no_klaim??'-'}</td>
                                <td>${value.kd_part??'-'}</td>
                                <td>${value.tgl_klaim?moment(value.tgl_klaim).format('YYYY/MM/DD'):'-'}</td>
                                <td>${value.tgl_approve?moment(value.tgl_approve).format('YYYY/MM/DD'):'-'}</td>
                                <td>${value.tgl_retur?moment(value.tgl_retur).format('YYYY/MM/DD'):'-'}</td>
                                <td>${value.tgl_jwb?moment(value.tgl_jwb).format('YYYY/MM/DD'):'-'}</td>
                                <td>${value.kd_dealer??'-'}</td>
                                <td>${value.kd_sales??'-'}</td>
                                <td>${value.kd_supp??'-'}</td>
                                <td>${value.sts_stock??'-'}</td>
                                <td>${value.sts_min??'-'}</td>
                                <td>${value.sts_klaim??'-'}</td>
                                <td>${value.keterangan??'-'}</td>
                                <td>${value.qty_retur??'-'}</td>
                                <td>${value.qty_jwb??'-'}</td>
                            </tr>
                        `);
                    });

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
    $("#tgl_klaim").daterangepicker({
        format: 'DD/MM/YYYY',
        startDate: date.tgl_klaim.start,
        endDate: date.tgl_klaim.end,
        ranges: {
            "Hari ini": [moment(), moment()],
            "Kemarin": [moment().subtract(1, "days"), moment().subtract(1, "days")],
            "1 minggu terakhir": [moment().subtract(6, "days"), moment()],
            "Bulan ini": [moment().startOf("month"), moment().endOf("month")],
            "Bulan Kemarin": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
        }
    }, function (start, end) {
        date.tgl_klaim.start = start
        date.tgl_klaim.end = end
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