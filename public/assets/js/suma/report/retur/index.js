let date = {
    tgl_klaim: {
        start: moment().startOf('month').format('YYYY-MM-DD'),
        end: moment().endOf('month').format('YYYY-MM-DD'),
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
                tanggal: [date.tgl_klaim.start, date.tgl_klaim.end],
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
                            <tr class="fw-bolder fs-8 border">
                                <td class="ps-3 pe-3 text-center">${no++}</td>
                                <td class="ps-3 pe-3">${value.kd_dealer??'-'}</td>
                                <td class="ps-3 pe-3">${value.nm_dealer??'-'}</td>
                                <td class="ps-3 pe-3">${value.kd_part??'-'}</td>
                                <td class="ps-3 pe-3 text-end">${value.qty_klaim??'-'}</td>
                                <td class="ps-3 pe-3 text-center">${value.tgl_pakai??'-'}</td>
                                <td class="ps-3 pe-3 text-center">${value.tgl_klaim??'-'}</td>
                                <td class="ps-3 pe-3 text-end">${value.pemakaian??'-'}</td>
                                <td class="ps-3 pe-3">${value.ket??'-'}</td>
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
    // $("#filter-report #tgl_klaim").daterangepicker({
    //     format: 'DD/MM/YYYY',
    //     startDate: date.tgl_klaim.start,
    //     endDate: date.tgl_klaim.end,
    //     ranges: {
    //         "Hari ini": [moment(), moment()],
    //         "Kemarin": [moment().subtract(1, "days"), moment().subtract(1, "days")],
    //         "1 minggu terakhir": [moment().subtract(6, "days"), moment()],
    //         "Bulan ini": [moment().startOf("month"), moment().endOf("month")],
    //         "Bulan Kemarin": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
    //     }
    // }, function (start, end) {
    //     date.tgl_klaim.start = start
    //     date.tgl_klaim.end = end
    // });
    $("#filter-report #tgl_klaim").flatpickr({
        altInput: true,
        altFormat: "d/m/Y",
        dateFormat: "Y-m-d",
        mode: "range",
        defaultDate: [date.tgl_klaim.start, date.tgl_klaim.end],
        onChange: function (selectedDates, dateStr, instance) {
            date.tgl_klaim.start = moment(selectedDates[0]).format('YYYY-MM-DD');
            date.tgl_klaim.end = moment(selectedDates[1]).format('YYYY-MM-DD');
        }
    })

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
                tanggal: [date.tgl_klaim.start, date.tgl_klaim.end],
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
            link.download = 'Retur Konsumen_' + ($('#tgl_claim').val() != ''? ' Tanggal Claim =' + moment(date.tgl_klaim.start).format('DD-MM-YYYY') + ' s/d ' + moment(date.tgl_klaim.end).format('DD-MM-YYYY') : '') + ($('#kd_dealer').val() != ''? ' Dealer =' + $('#kd_dealer').val() : '') + '.xlsx';
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
