let date = {
    tgl_packing: {
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
                <td colspan="10" class="text-center text-primary">
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
                tanggal: [date.tgl_packing.start.format('YYYY-MM-DD'), date.tgl_packing.end.format('YYYY-MM-DD')],
                no_meja: $('#no_meja').val(),
                kd_packing: $('#kd_packing').val(),
                page: page,
                per_page: $('#per_page').val(),
            }, function (response) {
                if (response.status == '1') {
                    console.log(response.data);
                    $('body').attr('data-kt-aside-minimize', 'on');
                    $('#kt_aside_toggle').addClass('active');

                    $('#table_list tbody').empty();
                    if($.isEmptyObject(response.data.data) && response.data.data.length == 0){
                        $('#table_list tbody').html(`
                            <tr>
                                <td colspan="10" class="text-center text-danger"> Tidak ada data </td>
                            </tr>
                        `);
                        return false;
                    }
                    $('#title_dokumen').text(moment(date.tgl_packing.start).locale('id').format('DD MMMM YYYY') + ' s/d ' + moment(date.tgl_packing.end).locale('id').format('DD MMMM YYYY'));

                    let no = response.data.from;
                    $.each(response.data.data, function (key, value) {
                        $('#table_list tbody').append(`
                            <tr class="fw-bolder fs-8 border">
                                <td class="ps-3 pe-3 text-center">${ no++}</td>
                                <td class="ps-3 pe-3 text-center">${value.no_dok??'-'}</td>
                                <td class="ps-3 pe-3 text-center">${value.jumlah_faktur??'-'}</td>
                                <td class="ps-3 pe-3">${value.kd_dealer??'-'}</td>
                                <td class="ps-3 pe-3 text-center">${value.tanggal?moment(value.tanggal).format('YYYY/MM/DD'):'-'}</td>
                                <td class="ps-3 pe-3">${value.kd_pack??'-'}</td>
                                <td class="ps-3 pe-3 text-center">${value.kd_lokpack??'-'}</td>
                                <td class="ps-3 pe-3 text-center">${value.waktu_mulai??'-'}</td>
                                <td class="ps-3 pe-3 text-center">${value.waktu_selesai??'-'}</td>
                                <td class="ps-3 pe-3 text-center">${value.waktu_proses??'-'}</td>
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
    $("#tgl_packing").daterangepicker({
        format: 'DD/MM/YYYY',
        startDate: date.tgl_packing.start,
        endDate: date.tgl_packing.end,
        ranges: {
            "Hari ini": [moment(), moment()],
            "Kemarin": [moment().subtract(1, "days"), moment().subtract(1, "days")],
            "1 minggu terakhir": [moment().subtract(6, "days"), moment()],
            "Bulan ini": [moment().startOf("month"), moment().endOf("month")],
            "Bulan Kemarin": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
        }
    }, function (start, end) {
        date.tgl_packing.start = start
        date.tgl_packing.end = end
    });

    $('.btn-smt').on('click', function (e) {
        e.preventDefault();
        report(1);
    });

    $('#export_exel').on('click', function () {
        loading.block();
        $.ajax({
            url: window.location.origin + window.location.pathname + '/export',
            method: 'POST',
            xhrFields: {
                responseType: 'blob'
            },
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                tanggal: [date.tgl_packing.start.format('YYYY-MM-DD'), date.tgl_packing.end.format('YYYY-MM-DD')],
                no_meja: $('#no_meja').val(),
                kd_packer: $('#kd_packing').val()
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
            link.download = 'Packing_' + ($('#tgl_packing').val() != ''? ' Tanggal Packing =' + date.tgl_packing.start.format('DD-MM-YYYY') + ' s/d ' + date.tgl_packing.end.format('DD-MM-YYYY') : '') + ($('#no_meja').val() != ''? ' No Meja =' + $('#no_meja').val() : '') + ($('#kd_packing').val() != ''? ' Packing =' + $('#kd_packing').val() : '') + '.xlsx';
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