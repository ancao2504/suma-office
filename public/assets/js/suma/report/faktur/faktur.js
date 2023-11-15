let date = {
    priode_date : {
        start : moment().subtract(0, "days"),
        end : moment()
    }
}

function report(page = 1) {
    $('body').attr('data-kt-aside-minimize', 'on');
    $('#kt_aside_toggle').addClass('active');

    $('.tbody').empty();
    $('.tbody').html(`
        <tr>
            <td colspan="14" class="text-center text-primary">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </td>
        </tr>`);

    $.post(window.location.href,
    {
        tgl_faktur: [date.priode_date.start.format('YYYY-MM-DD'), date.priode_date.end.format('YYYY-MM-DD')],
        kd_sales: $('#sales').val(),
        kd_produk: $('#produk').val(),
        page: page,
        per_page: $('#p-faktur #per_page').val(),
        _token: $('meta[name=csrf-token]').attr('content')
    }, function (response)
    {
        if (response.status == '1') {
            let json = response.data;
            $('#p-faktur #per_page option[value="' + json.per_page + '"]').prop('selected', true);

            var formatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
            });

            $('.tbody').empty();
            if (json.data.length == 0) {
                $('.tbody').html(`<tr>
                    <td colspan="14" class="text-center text-secondary">Data Tidak Ditemukan</td>
                </tr>`);
            } else {
                let no = json.from;
                json.data.map(data => {
                    $('.tbody').append(`
                        <tr class="fw-bolder fs-8 border">
                            <td class="text-center">${ no++}</td>
                            <td>${data.kd_dealer}</td>
                            <td>${data.nm_dealer}</td>
                            <td>${data.kd_sales}</td>
                            <td>${data.no_faktur}</td>
                            <td>${data.tgl_faktur.split('-').join('/')}</td>
                            <td>${data.kota}</td>
                            <td>${data.kd_produk}</td>
                            <td>${data.kd_part}</td>
                            <td>${data.kd_sub}</td>
                            <td class="text-end">${data.jml_order}</td>
                            <td class="text-end">${data.jml_jual}</td>
                            <td class="text-end pe-3">${formatter.format(data.total)}</td>
                        </tr>
                    `);
                });
            }

            $('#p-faktur').attr('hidden', false);
            // pagination
            $('.pagination').empty();
            json.links.map(data => {
                $('.pagination').append(`
                    <li class="page-item ${(data.active) ? 'active' : ''} ${(data.url) ? '' : 'disabled'}">
                        <a class="page-link" data-page="${(data.url) ? data.url.split('?page=')[1] : ''}" href="#">${data.label.replace('pagination.previous', '<i class="fas fa-angle-double-left"></i>').replace('pagination.next', '<i class="fas fa-angle-double-right"></i>')}</a>
                    </li >
                `);
            });
            $('.jmldta').text('Jumlah data : ' + json.total);
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
    });
}

// dokumen ready
$(document).ready(function () {
    $("#priode_date").daterangepicker({
        startDate: date.priode_date.start,
        endDate: date.priode_date.end,
        ranges: {
            "Hari ini": [moment(), moment()],
            "Kemarin": [moment().subtract(1, "days"), moment().subtract(1, "days")],
            "1 minggu terakhir": [moment().subtract(6, "days"), moment()],
            "Bulan ini": [moment().startOf("month"), moment().endOf("month")],
            "Bulan Kemarin": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
        }
    }, function (start, end) {
        date.priode_date.start = start;
        date.priode_date.end = end;
    });

    $('#btn-smt').on('click', function () {
        report(1);
        $('.modal').modal('hide');
    });

    $('#btn_export').on('click', function () {
        $.ajax({
            url: window.location.origin + window.location.pathname + '/export',
            method: 'POST',
            xhrFields: {
                responseType: 'blob'
            },
            data: {
                tgl_faktur: [date.priode_date.start.format('YYYY-MM-DD'), date.priode_date.end.format('YYYY-MM-DD')],
                kd_sales: $('#sales').val(),
                kd_produk: $('#produk').val(),
                _token: $('meta[name=csrf-token]').attr('content')
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
            link.download = 'Faktur_' + ($('#priode_date').val() != '' ? ' Periode =' + date.priode_date.start.format('DD-MM-YYYY') + ' s/d ' + date.priode_date.end.format('DD-MM-YYYY') : '') + ($('#company').val() != '' ? ' Company =' + $('#company').val() : '') + ($('#sales').val() != '' ? ' Sales =' + $('#sales').val() : '') + ($('#produk').val() != '' ? ' Produk =' + $('#produk').val() : '') + '.xlsx';
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

    $('.pagination').on('click', '.page-item:not(.disabled)', function () {
        report($(this).find('a').data('page'));
    });

    $('#p-faktur #per_page').on('change', function () {
        report(1);
    });
});
