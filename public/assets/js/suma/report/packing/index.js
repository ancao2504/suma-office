let date = {
    tgl_packing: {
        start: moment().startOf('month'),
        end: moment().endOf('month')
    }
}
// var formatter = new Intl.NumberFormat('id-ID', {
//     style: 'currency',
//     currency: 'IDR',
// });

function report(page = 1) {
    if($('#jenis_data').val() == '1'){
        toastr.error('Jenis Data Harus Diisi', "Error");
        return false;
    }
    if($('#jenis_data').val() == '3' && $('#group_by').val() == '1'){
        toastr.error('Group By Harus Diisi', "Error");
        return false;
    }
    loading.block();
    $('#table_list tfoot').addClass('d-none');
    $('#table_list .card-footer').addClass('d-none');
    $('#table_list #tbody-header').empty();
    $('#table_list #tbody-header').html(`
            <tr>
                <td colspan="100" class="text-center text-primary">
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
            kd_packer: $('#kd_packer').val(),
            jenis_data: $('#jenis_data').val(),
            group_by: $('#group_by').val(),
            page: page,
            per_page: $('#per_page').val(),
        }, function (response) {
            if (response.status == '1') {
                $('body').attr('data-kt-aside-minimize', 'on');
                $('#kt_aside_toggle').addClass('active');

                $('#table_list #tbody-header').empty();
                if ($.isEmptyObject(response.data.data) && response.data.data.length == 0) {
                    $('#table_list #tbody-header').html(`
                            <tr>
                                <td colspan="100" class="text-center text-danger"> Tidak ada data </td>
                            </tr>
                        `);
                    return false;
                }
                $('#title_dokumen').text(moment(date.tgl_packing.start).locale('id').format('DD MMMM YYYY') + ' s/d ' + moment(date.tgl_packing.end).locale('id').format('DD MMMM YYYY'));

                if ($('#jenis_data').val() == '2') {
                    let no = response.data.from;
                    $('#table_list thead').html(`
                            <tr class="fs-8 fw-bolder text-muted text-center">
                                <th scope="col" rowspan="2" class="w-50px ps-3 pe-3">No</th>
                                <th scope="col" rowspan="2" class="w-150px ps-3 pe-3">No Dokumen</th>
                                <th scope="col" rowspan="2" class="w-50px ps-3 pe-3">Tanggal</th>
                                <th scope="col" colspan="3" class="w-auto ps-3 pe-3">Jumlah</th>
                                <th scope="col" rowspan="2" class="w-50px ps-3 pe-3">kode Dealer</th>
                                <th scope="col" rowspan="2" class="w-auto ps-3 pe-3">Packer</th>
                                <th scope="col" rowspan="2" class="w-50px ps-3 pe-3">Meja</th>
                                <th scope="col" colspan="3" class="w-auto ps-3 pe-3">Waktu</th>
                            </tr>
                            <tr class="fs-8 fw-bolder text-muted text-center">
                                <th scope="col" colspan="1" class="w-50px ps-3 pe-3">Faktur</th>
                                <th scope="col" colspan="1" class="w-50px ps-3 pe-3">Part</th>
                                <th scope="col" colspan="1" class="w-50px ps-3 pe-3">Part (Pcs)</th>
                                <th scope="col" colspan="1" class="ps-3 pe-3">Mulai</th>
                                <th scope="col" colspan="1" class="ps-3 pe-3">Selesai</th>
                                <th scope="col" colspan="1" class="ps-3 pe-3">Proses</th>
                            </tr>
                        `);
                    $.each(response.data.data, function (key, value) {
                        $('#table_list #tbody-header').append(`
                                <tr class="fw-bolder fs-8 border ${Object.keys(value.detail).length > 0 ? 'data-header' : ''}" style="cursor: pointer;">
                                    <td class="ps-3 pe-3 text-center">${no++}</td>
                                    <td class="ps-3 pe-3 text-center">${value.no_dok ?? '-'}</td>
                                    <td class="ps-3 pe-3 text-center">${value.tanggal ? moment(value.tanggal).format('YYYY/MM/DD') : '-'}</td>
                                    <td class="ps-3 pe-3 text-end">${value.jml_faktur ?? '-'}</td>
                                    <td class="ps-3 pe-3 text-end">${value.jml_item ?? '-'}</td>
                                    <td class="ps-3 pe-3 text-end">${value.jml_pcs ?? '-'}</td>
                                    <td class="ps-3 pe-3">${value.kd_dealer ?? '-'}</td>
                                    <td class="ps-3 pe-3">${value.nm_packing ?? '-'}</td>
                                    <td class="ps-3 pe-3 text-center">${value.kd_lokpack ?? '-'}</td>
                                    <td class="ps-3 pe-3 text-center">${value.waktu_mulai ?? '-'}</td>
                                    <td class="ps-3 pe-3 text-center">${value.waktu_selesai ?? '-'}</td>
                                    <td class="ps-3 pe-3 text-center">${value.waktu_proses ?? '-'}</td>
                                </tr>
                            `);
                        if (Object.keys(value.detail).length > 0) {
                            $('#table_list #tbody-header').append(`
                                <tr class="fw-bolder fs-8 border d-none">
                                    <td colspan="100" class="p-10">
                                        <div class="table-responsive">
                                            ${Object.keys(value.detail).map((data, index) => {
                                            return `
                                                <span class="badge badge-light-primary fw-bolder mb-2">${data ?? '-'}</span>
                                                <table class="table table-row-dashed table-row-gray-300 align-middle">
                                                    <thead class="border">
                                                        <tr class="fs-8 fw-bolder text-muted text-center">
                                                            <th scope="col" class="w-50px ps-3 pe-3">No</th>
                                                            <th scope="col" class="w-auto ps-3 pe-3">Part Number</th>
                                                            <th scope="col" class="w-auto ps-3 pe-3">Nama Part</th>
                                                            <th scope="col" class="w-auto ps-3 pe-3">Jumlah</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="border">
                                                    ${value.detail[data].map((value, index) => {
                                                        return `
                                                            <tr class="fw-bolder fs-8 border">
                                                                <td class="ps-3 pe-3 text-center">${index + 1}</td>
                                                                <td class="ps-3 pe-3">${value.kd_part ?? '-'}</td>
                                                                <td class="ps-3 pe-3">${value.nm_part ?? '-'}</td>
                                                                <td class="ps-3 pe-3 text-end">${value.jml_part.toLocaleString('id-ID') ?? '-'}</td>
                                                            </tr>
                                                        `;
                                                    }).join('')}
                                                    </tbody>
                                                </table>
                                            `;
                                        }).join('')}
                                        </div>
                                    </td>
                                </tr>
                            `);
                        }
                    });

                    $('#table_list .data-header').on('click', function () {
                        $(this).toggleClass('bg-secondary');
                        $(this).next().toggleClass('d-none');
                    });

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
                } else if ($('#jenis_data').val() == '3') {

                    let no = response.data.from;
                    $('#table_list thead').html(`
                            <tr class="fs-8 fw-bolder text-muted text-center">
                                <th scope="col" rowspan="2" class="w-50px ps-3 pe-3">No</th>
                                <th scope="col" rowspan="2" class="w-50px ps-3 pe-3">Tanggal</th>
                                <th scope="col" colspan="5" class="w-150px ps-3 pe-3">Jumlah</th>
                                <th scope="col" rowspan="2" class="w-auto ps-3 pe-3">Packer</th>
                                <th scope="col" rowspan="2" class="w-50px ps-3 pe-3">Meja</th>
                                <th scope="col" rowspan="2" class="w-auto ps-3 pe-3">Rata-rata Waktu Proses</th>
                            </tr>
                            <tr class="fs-8 fw-bolder text-muted text-center">
                                <th scope="col" colspan="1" class="w-50px ps-3 pe-3">Dokumen</th>
                                <th scope="col" colspan="1" class="w-50px ps-3 pe-3">Faktur</th>
                                <th scope="col" colspan="1" class="w-50px ps-3 pe-3">Dealer</th>
                                <th scope="col" colspan="1" class="w-50px ps-3 pe-3">Part</th>
                                <th scope="col" colspan="1" class="w-50px ps-3 pe-3">Part (Pcs)</th>
                            </tr>
                        `);
                    $.each(response.data.data, function (key, value) {
                        $('#table_list #tbody-header').append(`
                                <tr class="fw-bolder fs-8 border">
                                    <td class="ps-3 pe-3 text-center">${no++}</td>
                                    <td class="ps-3 pe-3 text-center">${value.tanggal ? moment(value.tanggal).format('YYYY/MM/DD') : '-'}</td>
                                    <td class="ps-3 pe-3 text-end">${value.jml_dok ?? '-'}</td>
                                    <td class="ps-3 pe-3 text-end">${value.jml_faktur ?? '-'}</td>
                                    <td class="ps-3 pe-3 text-end">${value.jml_dealer ?? '-'}</td>
                                    <td class="ps-3 pe-3 text-end">${value.jml_item ?? '-'}</td>
                                    <td class="ps-3 pe-3 text-end">${value.jml_pcs.toLocaleString('id-ID') ?? '-'}</td>
                                    <td class="ps-3 pe-3">${value.nm_pack ?? '-'}</td>
                                    <td class="ps-3 pe-3 text-center">${value.kd_lokpack ?? '-'}</td>
                                    <td class="ps-3 pe-3 text-center">${value.rata2_waktu_proses ?? '-'}</td>
                                </tr>
                            `);
                    });

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
            }
            if (response.status == '0') {
                toastr.error(response.message, "Error");
                $('#table_list #tbody-header').html(`
                    <tr>
                        <td colspan="10" class="text-center text-danger">
                            ${response.message}
                        </td>
                    </tr>`);
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
    $("#tgl_packing").flatpickr({
        mode: "range",
        dateFormat: "d/m/Y",
        defaultDate: [date.tgl_packing.start, date.tgl_packing.end],
        onChange: function (selectedDates, dateStr, instance) {
            date.tgl_packing.start = moment(selectedDates[0]);
            date.tgl_packing.end = moment(selectedDates[1]);
        }
    });

    $('#jenis_data').on('change', function () {
        if ($(this).val() == '2') {
            $('#group_by').parents('.col-lg-6').attr('hidden', true);
            $('#group_by').val('1').trigger('change');
            $('#no_meja').parents('.col-lg-6').attr('hidden', false);
            $('#kd_packer').parents('.col-lg-6').attr('hidden', false);
        } else if ($(this).val() == '3') {
            $('#group_by').parents('.col-lg-6').attr('hidden', false);
            $('#no_meja').parents('.col-lg-6').attr('hidden', true);
            $('#no_meja').val('').trigger('change');
            $('#kd_packer').parents('.col-lg-6').attr('hidden', true);
            $('#kd_packer').val('').trigger('change');
        } else {
            $('#group_by').parents('.col-lg-6').attr('hidden', true);
            $('#group_by').val('1').trigger('change');
            $('#no_meja').parents('.col-lg-6').attr('hidden', true);
            $('#no_meja').val('').trigger('change');
            $('#kd_packer').parents('.col-lg-6').attr('hidden', true);
            $('#kd_packer').val('').trigger('change');
        }
    });

    $('#group_by').on('change', function () {
        if ($(this).val() == '2') {
            $('#kd_packer').parents('.col-lg-6').attr('hidden', true);
            $('#kd_packer').val('').trigger('change');
            $('#no_meja').parents('.col-lg-6').attr('hidden', false);
        } else if ($(this).val() == '3') {
            $('#kd_packer').parents('.col-lg-6').attr('hidden', false);
            $('#no_meja').parents('.col-lg-6').attr('hidden', true);
            $('#no_meja').val('').trigger('change');
        } else if ($(this).val() == '4') {
            $('#kd_packer').parents('.col-lg-6').attr('hidden', false);
            $('#no_meja').parents('.col-lg-6').attr('hidden', false);
        } else {
            $('#kd_packer').parents('.col-lg-6').attr('hidden', true);
            $('#kd_packer').val('').trigger('change');
            $('#no_meja').parents('.col-lg-6').attr('hidden', true);
            $('#no_meja').val('').trigger('change');
        }
    });

    $('.btn-smt').on('click', function (e) {
        e.preventDefault();
        report(1);
    });

    $('#export_exel').on('click', function () {
        if($('#jenis_data').val() == '1'){
            toastr.error('Jenis Data Harus Diisi', "Error");
            return false;
        }
        if($('#jenis_data').val() == '3' && $('#group_by').val() == '1'){
            toastr.error('Group By Harus Diisi', "Error");
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
                tanggal: [date.tgl_packing.start.format('YYYY-MM-DD'), date.tgl_packing.end.format('YYYY-MM-DD')],
                no_meja: $('#no_meja').val(),
                kd_packer: $('#kd_packer').val(),
                jenis_data: $('#jenis_data').val(),
                group_by: $('#group_by').val()
            }
        }).done(function (response) {
            if (response.status == '0') {
                toastr.error(response.message, "Error");
                $('#table_list #tbody-header').html(`
                <tr>
                    <td colspan="10" class="text-center text-danger">
                        ${response.message}
                    </td>
                </tr>`);
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

            link.download = 'Packing_' + ($('#tgl_packing').val() != '' ? ' Tanggal =' + date.tgl_packing.start.format('DD-MM-YYYY') + ' s/d ' + date.tgl_packing.end.format('DD-MM-YYYY') : '');
            switch ($('#jenis_data').val()) {
                case '2':
                    link.download = link.download+
                    ($('#jenis_data').val() == '2' ? ' Data Per Dokumen Where '
                            + ($('#no_meja').val() != '' ? ' No Meja =' + $('#no_meja').val() : ' Semua No Meja')
                            + ($('#kd_packer').val() != '' ? ' Packer =' + $('#kd_packer').val() : ' Semua Packer') : '') + '.xlsx';
                    break;
                case '3':
                    link.download = link.download+
                        ($('#jenis_data').val() == '3' ? ' Data Group by' : '') +
                        ($('#group_by').val() == '2' ? ' No Meja Where ' +
                            ($('#no_meja').val() != '' ? $('#no_meja').val() : ' Semua No Meja') : '') +
                        ($('#group_by').val() == '3' ? ' Packer Where ' + ($('#kd_packer').val() != '' ? $('#kd_packer').val() : ' Semua Packer') : '') +
                        ($('#group_by').val() == '4' ? ' No Meja & Packer Where ' +
                            ($('#no_meja').val() != '' ? $('#no_meja').val() : ' Semua No Meja') +
                            ($('#kd_packer').val() != '' ? $('#kd_packer').val() : ' Semua Packer') : '') + '.xlsx';
                    break;
                default:
                    break;
            }

            link.click();
            link.remove();
        }).fail(function (jqXHR, textStatus, error) {
            Swal.fire({
                title: 'Perhatian!',
                text: 'Terjadi kesalahan, silahkan coba beberapa saat lagi, Jika masih terjadi kesalahan Mohon filter ulang data lebih spesifik',
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