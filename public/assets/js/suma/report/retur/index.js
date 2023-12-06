let date = {
    tgl_klaim: {
        start: moment().startOf('month'),
        end: moment().endOf('month')
    }
}
var formatter = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
});

function report(page = 1) {
    loading.block();
        $('#table_list tfoot').addClass('d-none');
        $('#table_list .card-footer').addClass('d-none');
        $('#table_list tbody').empty();
        $('#table_list tbody').html(`
            <tr>
                <td colspan="24" class="text-center text-primary">
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
                tanggal: [date.tgl_klaim.start.format('YYYY-MM-DD'), date.tgl_klaim.end.format('YYYY-MM-DD')],
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
                                <td colspan="24" class="text-center text-danger"> Tidak ada data </td>
                            </tr>
                        `);
                        return false;
                    }
                    $('#title_dokumen').text(moment(date.tgl_klaim.start).locale('id').format('DD MMMM YYYY') + ' s/d ' + moment(date.tgl_klaim.end).locale('id').format('DD MMMM YYYY'));

                    let no = response.data.from;
                    $.each(response.data.data, function (key, value) {
                        $('#table_list tbody').append(`
                            <tr class="fw-bolder fs-8">
                                <td class="ps-3 pe-3 text-center">${ no++}</td>
                                <td class="ps-3 pe-3">${value.kd_dealer??'-'}</td>
                                <td class="ps-3 pe-3 min-w-150px">${value.nm_dealer??'-'}</td>
                                <td class="ps-3 pe-3 text-center">${value.kd_sales??'-'}</td>
                                <td class="ps-3 pe-3 min-w-80px">${value.kd_part??'-'}</td>
                                <td class="ps-3 pe-3 min-w-50px text-center">${value.no_klaim??'-'}</td>
                                <td class="ps-3 pe-3 text-end">${value.qty_klaim??'-'}</td>
                                <td class="ps-3 pe-3 text-end">${(value.qty_jwb)??'-'}</td>
                                <td class="ps-3 pe-3 text-center">${value.tgl_pakai??'-'}</td>
                                <td class="ps-3 pe-3 text-center">${value.tgl_klaim??'-'}</td>
                                <td class="ps-3 pe-3 text-end">${value.pemakaian} Hari</td>
                                <td class="ps-3 pe-3 min-w-150px">${value.keterangan??'-'}</td>
                                <td class="ps-3 pe-3 text-center min-w-100px">${value.sts_stock??'-'}</td>
                                <td class="ps-3 pe-3 text-center">${value.sts_min??'-'}</td>
                                <td class="ps-3 pe-3 text-center">${value.sts_klaim??'-'}</td>
                                <td class="ps-3 pe-3 text-center">${(parseInt(value.sts_approve)==1)?'<i class="bi bi-check-lg"></i>':'<i class="bi bi-dash"></i>'}</td>
                                <td class="ps-3 pe-3 text-center">${(parseInt(value.sts_selesai)==1)?'<i class="bi bi-check-lg"></i>':'<i class="bi bi-dash"></i>'}</td>
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
        if($('#table_list tbody tr:first td:first').hasClass('text-danger')){
            swal.fire({
                title: 'Perhatian!',
                text: 'Tidak ada data yang akan di export, silahkan filter data terlebih dahulu',
                icon: 'warning',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-secondary'
                },
                allowOutsideClick: false
            });
            return false;
        }
        loading.block();
        $.ajax({
            url: window.location.origin + window.location.pathname + '/export',
            method: 'POST',
            xhrFields: {
                responseType: 'blob'
            },
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                tanggal: [date.tgl_klaim.start.format('YYYY-MM-DD'), date.tgl_klaim.end.format('YYYY-MM-DD')],
                kd_sales: $('#kd_sales').val(),
                kd_dealer: $('#kd_dealer').val()
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
            link.download = 'Retur Konsumen_' + ($('#tgl_claim').val() != ''? ' Tanggal Claim =' + date.tgl_klaim.start.format('DD-MM-YYYY') + ' s/d ' + date.tgl_klaim.end.format('DD-MM-YYYY') : '') + ($('#kd_sales').val() != ''? ' Sales =' + $('#kd_sales').val() : '') + ($('#kd_dealer').val() != ''? ' Dealer =' + $('#kd_dealer').val() : '') + '.xlsx';
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
    });

    $('#table_list .pagination').on('click', '.page-item:not(.disabled)', function () {
        report($(this).find('a').data('page'));
    });

    $('#table_list #per_page').on('change', function () {
        report(1);
    });
});
