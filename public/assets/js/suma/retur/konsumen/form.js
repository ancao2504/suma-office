function validasi_sts_stock(){
    if ($('#sts_stock').val() == 1) {
        if (parseInt($('#qty_retur').val()) > parseInt($('#stock').val())) {
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

function detail_clear(){
    $('#no_produksi').val('');
    $('#kd_part').val('');
    $('#kd_part').removeClass('is-valid');
    $('#kd_part').removeClass('is-invalid');

    $('#nm_part').val('');
    $('#stock').val('');
    $('#qty_retur').val(1);
    $('#ket').val('');
    $('#sts_stock').val('').trigger('change');
}

function simpan(tamp){
    loading.block();
    $.post(base_url + "/retur/konsumen/form",
        {
            _token: $('meta[name="csrf-token"]').attr('content'),
            tamp: tamp,

            no_retur: $('#no_retur').val(),
            kd_sales: $('#kd_sales').val(),
            tgl_retur: $('#tgl_retur').val(),
            kd_dealer: $('#kd_dealer').val(),
            pc:$('#jenis_konsumen').val(),
            kd_dealer: $('#kd_dealer').val(),
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
                if(tamp == false){
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

                let dta_edt = JSON.stringify({
                    no_retur: $('#no_retur').val(),
                    no_produksi: $('#no_produksi').val(),
                    kd_part: $('#kd_part').val(),
                    nm_part: $('#nm_part').val(),
                    stock: $('#stock').val(),
                    jumlah: $('#qty_retur').val(),
                    sts_stock: $('#sts_stock').val(),
                    sts_klaim: $('#sts_klaim').val(),
                    sts_min: $('#sts_minimum').val(),
                    ket: $('#ket').val()
                });
                let dta_del = JSON.stringify({
                    no_retur: $('#no_retur').val(),
                    kd_part: $('#kd_part').val()
                });

                if($('#kd_part').val() != '' && $('#list-retur tr[data-key="'+$('#kd_part').val()+'"]').length == 0){

                    $('#list-retur .text_not_data').remove();

                    $('#list-retur').append(`
                        <tr class="fw-bolder fs-8 border" data-key="${$('#kd_part').val()}">
                            <td class="text-center">${$('#list-retur tr').length + 1}</td>
                            <td>${$('#kd_part').val()}</td>
                            <td class="text-end">${$('#qty_retur').val()}</td>
                            <td>${$('#no_produksi').val()}</td>
                            <td>-</td>
                            <td>-</td>
                            <td class="text-center">
                                ${
                                    ($('#sts_stock').val() == 1) ? '<span class="badge badge-light-primary">Ganti Barang</span>' : 
                                    ($('#sts_stock').val() == 2) ? '<span class="badge badge-light-primary">Stock 0</span>' : 
                                    ( ($('#sts_stock').val() == 3) ? '<span class="badge badge-light-primary">Retur</span>' : '<span class="badge badge-light-info">Belum di atur</span>')
                                }
                            </td>
                            <td class="text-center">
                                ${
                                    
                                    ($('#sts_minimum').val() == 1) ? '<span class="badge badge-light-info">Minimum</span>' : 
                                    ($('#sts_minimum').val() == 0) ? '<span class="badge badge-light-info">Tidak</span>' : '<span class="badge badge-light-info">Belum di atur</span>'
                                }
                            </td>
                            <td class="text-center">
                                ${
                                    ($('#sts_klaim').val() == 1) ? '<span class="badge badge-light-warning">klaim ke Supplier</span>' : 
                                    ($('#sts_klaim').val() == 2) ? '<span class="badge badge-light-warning">Tidak Melakukan Apapun</span>' : '<span class="badge badge-light-info">Belum di atur</span>'
                                }
                            </td>
                            <td>${($('#ket').val()??'-')}</td>
                            <td class="text-center">
                                <a role="button" data-bs-toggle="modal" href="#detail_modal" data-a='${btoa(dta_edt)}' class="btn_dtl_edit btn-sm btn-icon btn-warning my-1"><i class="fas fa-edit text-dark"></i></a>
                                <a role="button" data-a='${btoa(dta_del)}' class="btn_dtl_delete btn-sm btn-icon btn-danger my-1" data-bs-toggle="modal" data-bs-target="#delet-retur"><i class="fas fa-trash text-white"></i></a>
                            </td>
                        </tr>
                    `);
                } else {

                    $('#list-retur tr[data-key="'+$('#kd_part').val()+'"]').find('td:eq(2)').text($('#qty_retur').val());
                    $('#list-retur tr[data-key="'+$('#kd_part').val()+'"]').find('td:eq(3)').text($('#no_produksi').val());
                    $('#list-retur tr[data-key="'+$('#kd_part').val()+'"]').find('td:eq(4)').text('-');
                    $('#list-retur tr[data-key="'+$('#kd_part').val()+'"]').find('td:eq(5)').text('-');
                    $('#list-retur tr[data-key="'+$('#kd_part').val()+'"]').find('td:eq(6)').html(
                        ($('#sts_stock').val() == 1) ? '<span class="badge badge-light-primary">Ganti Barang</span>' : 
                        ($('#sts_stock').val() == 2) ? '<span class="badge badge-light-primary">Stock 0</span>' : 
                        ( ($('#sts_stock').val() == 3) ? '<span class="badge badge-light-primary">Retur</span>' : '<span class="badge badge-light-info">Belum di atur</span>'));

                    $('#list-retur tr[data-key="'+$('#kd_part').val()+'"]').find('td:eq(7)').html(
                        ($('#sts_minimum').val() == 1) ? '<span class="badge badge-light-info">Minimum</span>' : 
                        ($('#sts_minimum').val() == 0) ? '<span class="badge badge-light-info">Tidak</span>' : '<span class="badge badge-light-info">Belum di atur</span>');

                    $('#list-retur tr[data-key="'+$('#kd_part').val()+'"]').find('td:eq(8)').html(
                        ($('#sts_klaim').val() == 1) ? '<span class="badge badge-light-warning">klaim ke Supplier</span>' : 
                        ($('#sts_klaim').val() == 2) ? '<span class="badge badge-light-warning">Tidak Melakukan Apapun</span>' : '<span class="badge badge-light-info">Belum di atur</span>');

                    $('#list-retur tr[data-key="'+$('#kd_part').val()+'"]').find('td:eq(9)').text($('#ket').val());
                    $('#list-retur tr[data-key="'+$('#kd_part').val()+'"]').find('td:eq(10)').html(`
                        <a role="button" data-bs-toggle="modal" href="#detail_modal" data-a='${btoa(dta_edt)}' class="btn_dtl_edit btn-sm btn-icon btn-warning my-1"><i class="fas fa-edit text-dark"></i></a>
                        <a role="button" data-a='${btoa(dta_del)}' class="btn_dtl_delete btn-sm btn-icon btn-danger my-1" data-bs-toggle="modal" data-bs-target="#delet-retur"><i class="fas fa-trash text-white"></i></a>
                    `);
                }

                detail_clear();
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
                                    <th rowspan="2" class="w-auto ps-3 pe-3">part Number</th>
                                    <th colspan="2" class="w-auto ps-3 pe-3">Qty</th>
                                    <th rowspan="2" class="w-auto ps-3 pe-3">Keterangan</th>
                                    <th rowspan="2" class="w-auto ps-3 pe-3">Action</th>
                                </tr>
                                <tr class="fs-8 fw-bolder text-muted text-center">
                                    <th class="w-auto ps-3 pe-3">Retur</th>
                                    <th class="w-auto ps-3 pe-3">Stock</th>
                                </tr>
                            </thead>
                            <tbody id="list-retur">`
                        $.each(response.data, function (index, data) {
                            view += `
                                <tr class="fw-bolder fs-8 border">
                                    <td class="text-center">${index + 1}</td>
                                    <td>${data.kd_part}</td>
                                    <td class="text-end">${data.qty}</td>
                                    <td class="text-end">${data.stock}</td>
                                    <td class="text-center"><span class="badge badge-light-danger">${data.keterangan}</span></td>
                                    <td class="text-center">
                                        <a role="button" data-key="${data.kd_part}" class="btn_warning_edit btn-sm btn-icon btn-warning my-1"><i class="fas fa-edit text-dark"></i></a>
                                    </td>
                                </tr>
                            `;
                    });

                    view +=`</tbody>
                        </table>
                    </div>`;

                    $('#warning_modal .modal-body').html(view);
                    $('#detail_modal').modal('hide');
                    $('#warning_modal').modal('show');
                } else {
                    toastr.error(response.message, "Error");
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
    loading.release();
}

function edit_detail(val){
    $('#list-retur tr').removeClass('bg-secondary');
    $('#no_produksi').val(val.no_produksi);
    $('#kd_part').val(val.kd_part);
    $('#nm_part').val(val.nm_part);
    $('#stock').val(val.stock);
    $('#qty_retur').val(val.jumlah);
    $('#ket').val(val.ket);
    $('#sts_stock option[value="' + val.sts_stock + '"]').prop('selected', true).trigger('change');
    $('#sts_klaim option[value="' + val.sts_klaim + '"]').prop('selected', true);
    $('#sts_minimum option[value="' + val.sts_min + '"]').prop('selected', true);
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
            html: 'Apakah Anda Yakin Ingin Menyimpan Data Ini, <b>Data yang diSimpan tidak bisa di Ubah </b>?',
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
                simpan(false);
            }
        });
    });
    $(".btn_simpan_tmp").click(function (e) {
        e.preventDefault();
        simpan(true);
    });
    
    $("#list_detail").on('click','.btn_dtl_delete', function (e) {
        let val = JSON.parse(atob($(this).data('a')));
        Swal.fire({
            html: 'Apakah Anda Yakin Menghapus Detail Retur dengan <b>Kd Part ' + val.kd_part +'</b>',
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
                loading.block();
                $.post(base_url + '/retur/konsumen/delete',
                    {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        no_retur: $('#no_retur').val(),
                        kd_part: val.kd_part,
                    },
                    function (response) {
                        if (response.status == '1') {
                            $('#list-retur').find('tr[data-key="'+val.kd_part+'"]').remove();
                            if ($('#list-retur').find('tr').length == 0) {
                                $('#list-retur').html(`
                                    <tr class="fw-bolder fs-8 border text_not_data">
                                        <td colspan="13" class="text-center">Tidak ada data</td>
                                    </tr>
                                `);
                            } else {
                                $('#list-retur tr').each(function (i) {
                                    $(this).find('td:eq(0)').text(i + 1);
                                });
                            }
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
        });

    });
    
    $('#sts_stock, #qty_retur').on('change', function () {
        validasi_sts_stock();
    });
    $('#part-list').on('click','.pilih' ,function () {
        validasi_sts_stock();
    });
    $("#add_detail").on('click', function (e) {
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
});