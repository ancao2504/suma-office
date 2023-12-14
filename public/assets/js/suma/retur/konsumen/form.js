
let limit_jumlah = 1;
let limit_stock = 0;

function Invalid(el, el_error, text){
    el.forEach(function (e) {
        e.addClass('is-invalid');
    });
    el_error.text(text);

    $('html, body').animate({
        scrollTop: el[0].offsetTop - 100
    }, 500);
}

function valid(el, el_error, text){
    el.forEach(function (e) {
        e.removeClass('is-invalid');
    });
    el_error.text(text);
}

function listDetail(detail){
    if ((Array.isArray(detail) || typeof detail === 'object') && (detail.length == 0 || Object.keys(detail).length == 0)) {
        $('#list-retur').html(`
            <tr class="fw-bolder fs-8 border text_not_data">
                <td colspan="99" class="text-center">Tidak ada data</td>
            </tr>
        `);

        $('#kd_sales').attr('disabled', false);
        $('#jenis_konsumen').attr('disabled', false);
        $('#kd_dealer').attr('disabled', false);
        $('.list-dealer').attr('disabled', false);
        $('#kd_cabang').attr('disabled', false);

        return false;
    }

    $.each(detail, function (index, data) {
        let dta_edt = JSON.stringify({
            no_retur: data.no_dokumen,
            no_faktur: data.no_faktur,
            limit_jumlah: data.limit_jumlah,
            kd_part: data.kd_part,
            no_produksi: data.no_produksi,
            nm_part: data.nm_part,
            stock: data.stock,
            jumlah: data.qty,
            tgl_klaim: data.tgl_klaim,
            tgl_pakai: data.tgl_pakai,
            sts_stock: data.sts_stock,
            sts_klaim: data.sts_klaim,
            sts_min: data.sts_min,
            ket: data.keterangan
        });
        let dta_del = JSON.stringify({
            no_retur: data.no_dokumen,
            no_faktur: data.no_faktur,
            kd_part: data.kd_part,
            no_produksi: data.no_produksi,
        });

        $('#list-retur').append(`
            <tr class="fw-bolder fs-8 border" data-key="${String(data.no_faktur)+String(data.kd_part)+String(data.no_produksi)}">
                <td class="text-center">${index + 1}</td>
                <td>${data.no_faktur}</td>
                <td>${data.kd_part}</td>
                <td>${data.no_produksi}</td>
                <td class="text-end">${data.qty}</td>
                <td class="text-end">${moment(data.tgl_pakai).format('YYYY/MM/DD')}</td>
                <td class="text-end">${moment(data.tgl_klaim).format('YYYY/MM/DD')}</td>
                <td class="text-center">
                    ${(data.sts_stock == 1)?'<span class="badge badge-light-primary">Ganti Barang</span>':''}
                    ${(data.sts_stock == 2)?'<span class="badge badge-light-primary">Stock 0</span>':''}
                    ${(data.sts_stock == 3)?'<span class="badge badge-light-primary">Retur</span>':''}
                </td>
                <td class="text-center">
                    ${(data.sts_min == 1)?'<span class="badge badge-light-info">Minimum</span>':''}
                    ${(data.sts_min == 0)?'<span class="badge badge-light-info">Tidak</span>':''}
                </td>
                <td class="text-center">
                    ${(data.sts_klaim == 1)?'<span class="badge badge-light-warning">klaim ke Supplier</span>':''}
                    ${(data.sts_klaim == 2)?'<span class="badge badge-light-warning">Tidak Melakukan Apapun</span>':''}
                </td>
                <td>${data.keterangan??'-'}</td>
                <td>${ data.tgl_ganti?moment(data.tgl_ganti).format('YYYY/MM/DD'):'-'}</td>
                <td>${data.qty_ganti?number_format(data.qty_ganti, 0, '.', ','):'-'}</td>
                <td class="text-center">
                    <a role="button" data-bs-toggle="modal" href="#detail_modal" data-a="${btoa(dta_edt)}" class="btn_dtl_edit btn-sm btn-icon btn-warning my-1"><i class="fas fa-edit text-dark"></i></a>
                    <a role="button" data-a="${btoa(dta_del)}" class="btn_dtl_delete btn-sm btn-icon btn-danger my-1" data-bs-toggle="modal" data-bs-target="#delet-retur"><i class="fas fa-trash text-white"></i></a>
                </td>
            </tr>
        `);
    });

    if ($('#list-retur').find('tr').length > 0) {
        $('#kd_sales').attr('disabled', true);
        $('#jenis_konsumen').attr('disabled', true);
        $('#kd_dealer').attr('disabled', true);
        $('.list-dealer').attr('disabled', true);
        $('#kd_cabang').attr('disabled', true);
    }
}

function detail_clear(){
    $('.list-faktur').attr('disabled', false);
    $('#no_faktur').val('');
    $('#no_faktur').removeClass('is-valid');
    $('#no_faktur').removeClass('is-invalid');
    $('#no_faktur').attr('disabled', false);
    $('#kd_part').val('');
    $('#kd_part').removeClass('is-valid');
    $('#kd_part').removeClass('is-invalid');
    $('#kd_part').attr('disabled', false);
    $('.list-part').attr('disabled', false);
    $('#nm_part').val('');
    $('#stock').val('');
    $('#no_produksi').val('');
    $('#no_produksi').attr('disabled', false);
    $('#qty_retur').val(1);
    $("#tgl_klaim").flatpickr().setDate(moment().format('YYYY-MM-DD'));
    $("#tgl_pakai").flatpickr().setDate(moment().format('YYYY-MM-DD'));
    $('#ket').val('');
    $('#sts_stock').val('');
}

function simpan(tamp){
    loading.block();
    $.post(base_url + "/retur/konsumen/form",
        {
            _token: $('meta[name="csrf-token"]').attr('content'),
            tamp: tamp,
            no_retur: old.no_retur??$('#no_retur').val(),
            kd_sales: old.kd_sales??$('#kd_sales').val(),
            no_faktur: $('#no_faktur').val(),
            tgl_retur: $('#tgl_retur').val(),
            tgl_klaim: $('#tgl_klaim').val(),
            tgl_pakai: $('#tgl_pakai').val(),
            kd_dealer: old.kd_cabang??$('#kd_dealer').val(),
            pc: $('#jenis_konsumen').val(),
            kd_cabang: $('#kd_cabang').val(),
            no_produksi: $('#no_produksi').val(),
            kd_part: $('#kd_part').val(),
            qty_retur: $('#qty_retur').val(),
            ket: $('#ket').val(),
            sts_stock: $('#sts_stock').val(),
            sts_minimum: $('#sts_minimum').val(),
            sts_klaim: $('#sts_klaim').val(),
        },
        function (response) {
            if (response.status == '1') {
                if(tamp == 0){
                    swal.fire({
                        title: 'Perhatian!',
                        html: response.message + '<br>dengan No '+((response.data.approve==1)?'Retur':'Klaim')+' : <b>' + response.data.no_retur + '</b>'+((response.data.approve==1)?', dan <b>Berhasil di Approve</b>':''),
                        icon: 'success',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        },
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if(response.data.approve==0){
                                location.reload();
                            } else if(response.data.approve==1){
                                window.location.href = base_url + '/retur/konsumen';
                            }
                        }
                    });
                    return false;
                }
                $('#detail_modal').modal('hide');

                detail_clear();
                $('#list-retur').html('');

                listDetail(response.data.detail)

                if (response.data.warning != null) {
                    swal.fire({
                        title: 'Informasi !',
                        html: response.data.warning,
                        icon: 'info',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-secondary'
                        },
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                        }
                    });
                }
            }
            if (response.status == '0') {
                if ((Array.isArray(response.data) || typeof response.data === 'object') && (response.data.length > 0 || Object.keys(response.data).length > 0)) {
                    $('#warning_modal .modal-title').text(response.message);
                        let view = `
                        <div id="list_detail" class="table-responsive border rounded-3">
                        <table id="datatable_classporduk" class="table table-row-dashed table-row-gray-300 align-middle border">
                            <thead class="border">
                                <tr class="fs-8 fw-bolder text-muted text-center">
                                    <th rowspan="2" class="w-auto ps-3 pe-3">No</th>
                                    <th rowspan="2" class="w-auto ps-3 pe-3">No Faktur</th>
                                    <th rowspan="2" class="w-auto ps-3 pe-3">part Number</th>
                                    <th rowspan="2" class="w-auto ps-3 pe-3">No Produksi</th>
                                    <th colspan="3" class="w-auto ps-3 pe-3">Qty</th>
                                    <th rowspan="2" class="w-auto ps-3 pe-3">Keterangan</th>
                                </tr>
                                <tr class="fs-8 fw-bolder text-muted text-center">
                                    <th class="w-auto ps-3 pe-3">Jml Jual</th>
                                    <th class="w-auto ps-3 pe-3">Klaim</th>
                                    <th class="w-auto ps-3 pe-3">Stock</th>
                                </tr>
                            </thead>
                            <tbody id="list-retur">`;
                        $.each(response.data, function (index, data) {
                            view += `
                                <tr class="fw-bolder fs-8 border">
                                    <td class="text-center">${index + 1}</td>
                                    <td>${data.no_faktur}</td>
                                    <td>${data.kd_part}</td>
                                    <td>${data.no_produksi}</td>
                                    <td class="text-end">${data.jml_jual}</td>
                                    <td class="text-end">${data.qty}</td>
                                    <td class="text-end">${data.stock}</td>
                                    <td>
                                        <ul class="m-0">`;
                                    $.each(data.keterangan, function (index, data) {
                                        view += `
                                            <li class="mb-1">
                                                <span class="badge badge-light-danger">
                                                    ${data}
                                                </span>
                                            </li>
                                        `;
                                    });
                            view += `   </ul>
                                    </td>
                                </tr>`;
                        });

                    view +=`</tbody>
                        </table>
                    </div>`;

                    $('#warning_modal .modal-body').html(view);
                    $('#detail_modal').modal('hide');
                    $('#warning_modal').modal('show');
                } else {
                    swal.fire({
                        title: 'Perhatian!',
                        html: response.message,
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-secondary'
                        },
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                        }
                    });
                }
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
    }).always(function () {
        loading.release();
    }).fail(function (err) {
        swal.fire({
            title: 'Perhatian!',
            text: 'Maaf Terjadi Kesalahan!',
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
    });
}

function edit_detail(val){
    limit_stock = val.stock;
    limit_jumlah = parseInt(val.limit_jumlah);
    detail_clear();
    $('#detail_modal .modal-title').text('Edit Detail');
    $('#list-retur tr').removeClass('bg-secondary');
    $('.list-faktur').attr('disabled', true);
    $('#no_faktur').val(val.no_faktur);
    $('#no_faktur').attr('disabled', true);
    $('#kd_part').val(val.kd_part);
    $('#kd_part').attr('disabled', true);
    $('.list-part').attr('disabled', true);
    $('#no_produksi').val(val.no_produksi);
    $('#no_produksi').attr('disabled', true);
    $('#qty_retur').val(val.jumlah);
    $('#nm_part').val(val.nm_part);
    $('#stock').val(val.stock);
    $("#tgl_klaim").flatpickr().setDate(moment(val.tgl_klaim).format('YYYY-MM-DD'));
    $("#tgl_pakai").flatpickr().setDate(moment(val.tgl_pakai).format('YYYY-MM-DD'));
    $('#ket').val(val.ket);
    $('#sts_stock option[value="' + val.sts_stock + '"]').prop('selected', true).trigger('change');
    $('#sts_klaim option[value="' + val.sts_klaim + '"]').prop('selected', true);
    $('#sts_minimum option[value="' + val.sts_min + '"]').prop('selected', true);
}

function delete_detail(val){
    console.log(val);
    loading.block();
    $.post(base_url + '/retur/konsumen/delete',
        {
            _token: $('meta[name="csrf-token"]').attr('content'),
            no_retur: old.no_retur??$('#no_retur').val(),
            no_faktur: val.no_faktur,
            kd_part: val.kd_part,
            no_produksi: val.no_produksi
        },
        function (response) {
            if (response.status == '1') {
                $('#list-retur').find('tr[data-key="'+String(val.no_faktur)+String(val.kd_part)+String(val.no_produksi)+'"]').remove();
                if ($('#list-retur').find('tr').length == 0) {
                    $('#list-retur').html(`
                        <tr class="fw-bolder fs-8 border text_not_data">
                            <td colspan="99" class="text-center">Tidak ada data</td>
                        </tr>
                    `);

                    $('#kd_sales').attr('disabled', false);
                    $('#jenis_konsumen').attr('disabled', false);
                    $('#kd_dealer').attr('disabled', false);
                    $('.list-dealer').attr('disabled', false);
                    $('#kd_cabang').attr('disabled', false);
                } else {
                    $('#list-retur tr').each(function (i) {
                        $(this).find('td:eq(0)').text(i + 1);
                    });

                    $('#kd_sales').attr('disabled', true);
                    $('#jenis_konsumen').attr('disabled', true);
                    $('#kd_dealer').attr('disabled', true);
                    $('.list-dealer').attr('disabled', true);
                    $('#kd_cabang').attr('disabled', true);
                }
            }
            if (response.status == '0') {
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
                    }
                });
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
    }).always(function () {
        loading.release();
    }).fail(function (err) {
        swal.fire({
            title: 'Perhatian!',
            text: 'Terjadi Kesalahan, Silahkan Coba Lagi',
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
    });
}

function validasi_sts_stock(){
    if ($('#sts_stock').val() >= 1) {
        if (parseInt($('#qty_retur').val()) > parseInt(limit_stock)) {
            Swal.fire({
                title: 'Perhatian!',
                text: 'Stock Saat ini tidak mencukupi',
                icon: 'warning',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-secondary'
                },
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                }
            });
        }
    }
}

$(document).ready(function () {
    $("#tgl_retur").flatpickr().setDate(moment($("#tgl_retur").val()).format('YYYY-MM-DD'));

    $('#kd_cabang').val(old.kd_cabang).trigger('change');
    $('#kd_sales').val(old.kd_sales).trigger('change');

    $('#jenis_konsumen').on('change', function () {
        if ($(this).val() == '0') {
            $('#jenis_konsumen_cabang').attr('hidden', true);
            $('#jenis_konsumen_dealer').attr('hidden', false);
        } else {
            $('#jenis_konsumen_cabang').attr('hidden', false);
            $('#jenis_konsumen_dealer').attr('hidden', true);
        }
    });

    $("#list_detail").on('click','.btn_dtl_edit', function () {
        let val = JSON.parse(atob($(this).data('a')));
        edit_detail(val);
    });

    $(".btn_simpan").click(function (e) {
        swal.fire({
            title: 'Perhatian!',
            html: 'Apakah Anda Yakin Menyimpan Data Ini ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Tidak!',
            customClass: {
                confirmButton: 'btn btn-warning',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.value) {
                simpan(0);
            }
        });
    });
    $(".btn_simpan_tmp").click(function (e) {
        if($('#detail_modal').find('#no_faktur').val() == ''){
            Invalid([$('#detail_modal').find('#no_faktur')], $('#detail_modal').find('#error_no_faktur'), 'No Faktur Harus diisi');
            return false;
        } else {
            valid([$('#detail_modal').find('#no_faktur')], $('#detail_modal').find('#error_no_faktur'), '');
        }
        if($('#detail_modal').find('#kd_part').val() == ''){
            Invalid([$('#detail_modal').find('#kd_part')], $('#detail_modal').find('#error_kd_part'), 'Kode Part Harus diisi');
            return false;
        } else {
            valid([$('#detail_modal').find('#kd_part')], $('#detail_modal').find('#error_kd_part'), '');
        }
        if($('#detail_modal').find('#no_produksi').val() == ''){
            Invalid([$('#detail_modal').find('#no_produksi')], $('#detail_modal').find('#error_no_produksi'), 'No Produksi Harus diisi');
            return false;
        } else {
            valid([$('#detail_modal').find('#no_produksi')], $('#detail_modal').find('#error_no_produksi'), '');
        }
        if($('#detail_modal').find('#tgl_klaim').val() == ''){
            Invalid([$('#detail_modal').find('#tgl_klaim')], $('#detail_modal').find('#error_tgl_klaim'), 'Tanggal Klaim Harus diisi');
            return false;
        } else {
            valid([$('#detail_modal').find('#tgl_klaim')], $('#detail_modal').find('#error_tgl_klaim'), '');
        }
        if($('#detail_modal').find('#tgl_pakai').val() == ''){
            Invalid([$('#detail_modal').find('#tgl_pakai')], $('#detail_modal').find('#error_tgl_pakai'), 'Tanggal Pakai Harus diisi');
            return false;
        } else {
            valid([$('#detail_modal').find('#tgl_pakai')], $('#detail_modal').find('#error_tgl_pakai'), '');
        }
        if($('#detail_modal').find('#qty_retur').val() == 0){
            Invalid([$('#detail_modal').find('#qty_retur')], $('#detail_modal').find('#error_qty_retur'), 'Jumlah Retur Harus lebih dari 0');
            return false;
        } else {
            valid([$('#detail_modal').find('#qty_retur')], $('#detail_modal').find('#error_qty_retur'), '');
        }
        if($('#detail_modal').find('#ket').val() == ''){
            Invalid([$('#detail_modal').find('#ket')], $('#detail_modal').find('#error_ket'), 'Keterangan Harus diisi');
            return false;
        } else {
            valid([$('#detail_modal').find('#ket')], $('#detail_modal').find('#error_ket'), '');
        }
        if($('#detail_modal').find('#sts_stock').val() == ''){
            Invalid([$('#detail_modal').find('#sts_stock')], $('#detail_modal').find('#error_sts_stock'), 'Status Stock Harus diisi');
            return false;
        } else {
            valid([$('#detail_modal').find('#sts_stock')], $('#detail_modal').find('#error_sts_stock'), '');
        }
        if($('#detail_modal').find('#sts_minimum').val() == ''){
            Invalid([$('#detail_modal').find('#sts_minimum')], $('#detail_modal').find('#error_sts_minimum'), 'Status Minimum Harus diisi');
            return false;
        } else {
            valid([$('#detail_modal').find('#sts_minimum')], $('#detail_modal').find('#error_sts_minimum'), '');
        }
        if($('#detail_modal').find('#sts_klaim').val() == ''){
            Invalid([$('#detail_modal').find('#sts_klaim')], $('#detail_modal').find('#error_sts_klaim'), 'Status Klaim Harus diisi');
            return false;
        } else {
            valid([$('#detail_modal').find('#sts_klaim')], $('#detail_modal').find('#error_sts_klaim'), '');
        }

        if (parseInt($('#qty_retur').val()) > parseInt(limit_jumlah)) {
            Swal.fire({
                title: 'Perhatian!',
                html: '<div class="text-center">Jumlah Jual Pada Faktur <b>' + limit_jumlah + '</b></div>',
                icon: 'warning',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-secondary'
                },
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).val(limit_jumlah);
                }
            });
            return false;
        }

        e.preventDefault();
        simpan(1);
    });

    $("#list_detail").on('click','.btn_dtl_delete', function (e) {
        let val = JSON.parse(atob($(this).data('a')));
        Swal.fire({
            html: 'Apakah Anda Yakin Menghapus Detail Retur dengan <br>' + '<table class="table table-sm">' + '<tr><td>No Faktur</td><td>:</td><td><b>' + val.no_faktur + '</b></td></tr>' + '<tr><td>Kode Part</td><td>:</td><td><b>' + val.kd_part + '</b></td></tr>' + '<tr><td>No Produksi</td><td>:</td><td><b>' + val.no_produksi + '</b></td></tr>' + '</table>',
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Hapus!",
            cancelButtonText: "Batal!",
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-secondary"
            },
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                delete_detail(val);
            }
        });
    });

    $('#sts_stock').on('change', function () {
        if ($(this).val() == '1' || $(this).val() == '3') {
            validasi_sts_stock();
        }
    });
    $('#part-list').on('click','.pilih' ,function () {
        validasi_sts_stock();
    });

    $("#add_detail").on('click', function (e) {
        if ($('#kd_sales').val() == '') {
            Invalid([$('#kd_sales'),$('#kd_sales + span .select2-selection')], $('#error_kd_sales'), 'Kode Sales Harus diisi');
            return false;
        } else {
            valid([$('#kd_sales + span .select2-selection')], $('#error_kd_sales'), '');
        }
        if ($('#tgl_retur').val() == '') {
            Invalid([$('#tgl_retur')], $('#error_tgl_retur'), 'Tanggal Retur Harus diisi');
            return false;
        } else {
            valid([$('#tgl_retur')], $('#error_tgl_retur'), '');
        }
        if ($('#jenis_konsumen').val() == '0' && $('#kd_dealer').val() == '') {
            Invalid([$('#kd_dealer')], $('#error_kd_dealer'), 'Kode Dealer Harus diisi');
            return false;
        } else {
            valid([$('#kd_dealer')], $('#error_kd_dealer'), '');
        }
        if ($('#jenis_konsumen').val() == '1' && $('#kd_cabang').val() == '') {
            Invalid([$('#kd_cabang')], $('#error_kd_cabang'), 'Kode Cabang Harus diisi');
            return false;
        } else {
            valid([$('#kd_cabang')], $('#error_kd_cabang'), '');
        }
        $('#detail_modal .modal-title').text('Tambah Detail');
        $('#detail_modal').modal('show');
        detail_clear();
    });

    $('#warning_modal').on('click','.btn_warning_edit', function () {
        let val = JSON.parse(atob($('#list_detail').find('tr[data-key="'+$(this).data('key')+'"] td a.btn_dtl_edit').data('a')));
        edit_detail(val);
        $('#warning_modal').modal('show');
        $('#detail_modal').modal('show');
    });

    $('#detail_modal').on('change','#sts_stock', function () {
        if ($(this).val() == '') {
            $('#detail_modal  #sts_minimum').html(`<option value="">Pilih Status Minimum</option>`);
            $('#detail_modal  #sts_klaim').html(`<option value="">Pilih Status Klaim</option>`);
        } else if ($(this).val() == '1') {
            $('#detail_modal  #sts_minimum').html(`<option value="1">Minimum</option>`);
            $('#detail_modal  #sts_klaim').html(`<option value="1">klaim ke Supplier</option><option value="2">Tidak Melakukan Apapun</option>`);
        } else if ($(this).val() == '2') {
            $('#detail_modal  #sts_minimum').html(`<option value="1">Minimum</option>`);
            $('#detail_modal  #sts_klaim').html(`<option value="1">klaim ke Supplier</option>`);
        } else if ($(this).val() == '3') {
            $('#detail_modal  #sts_minimum').html(`<option value="1">Minimum</option><option value="0">Tidak</option>`);
            $('#detail_modal  #sts_klaim').html(`<option value="1">klaim ke Supplier</option><option value="2">Tidak Melakukan Apapun</option>`);
        }
    });

    // qty_retur change
    $('#detail_modal').on('change','#qty_retur', function () {
        if (parseInt($(this).val()) > parseInt(limit_jumlah)) {
            Swal.fire({
                title: 'Perhatian!',
                html: '<div class="text-center">Jumlah Jual Pada Faktur <b>' + limit_jumlah + '</b></div>',
                icon: 'warning',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-secondary'
                },
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).val(limit_jumlah);
                }
            });
        }
    });
});
