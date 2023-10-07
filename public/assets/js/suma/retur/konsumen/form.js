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

function listDetail(detail){
    if ((Array.isArray(detail) || typeof detail === 'object') && (detail.length == 0 || Object.keys(detail).length == 0)) {
        $('#list-retur').html(`
            <tr class="fw-bolder fs-8 border text_not_data">
                <td colspan="11" class="text-center">Tidak ada data</td>
            </tr>
        `);
        return false;
    }
    $.each(detail, function (index, data) {
        let dta_edt = JSON.stringify({
            no_retur: data.no_dokumen,
            no_produksi: data.no_produksi,
            kd_part: data.kd_part,
            nm_part: data.nm_part,
            stock: data.stock,
            jumlah: data.qty,
            sts_stock: data.sts_stock,
            sts_klaim: data.sts_klaim,
            sts_min: data.sts_min,
            ket: data.keterangan
        });
        let dta_del = JSON.stringify({
            no_retur: data.no_dokumen,
            kd_part: data.kd_part,
            no_produksi: data.no_produksi
        });

        $('#list-retur').append(`
            <tr class="fw-bolder fs-8 border" data-key="${String(data.kd_part)+String(data.no_produksi)}">
                <td class="text-center">${index + 1}</td>
                <td>${data.no_produksi}</td>
                <td>${data.kd_part}</td>
                <td class="text-end">${1}</td>
                <td>${data.tgl_ganti??'-'}</td>
                <td>${data.qty_ganti?number_format(data.qty_ganti, 0, '.', ','):'-'}</td>
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
                <td class="text-center">
                    <a role="button" data-bs-toggle="modal" href="#detail_modal" data-a="${btoa(dta_edt)}" class="btn_dtl_edit btn-sm btn-icon btn-warning my-1"><i class="fas fa-edit text-dark"></i></a>
                    <a role="button" data-a="${btoa(dta_del)}" class="btn_dtl_delete btn-sm btn-icon btn-danger my-1" data-bs-toggle="modal" data-bs-target="#delet-retur"><i class="fas fa-trash text-white"></i></a>
                </td>
            </tr>
        `);
    });
}

function detail_clear(){
    $('#input_no_produk').html(`
        <div class="col-2 mt-3">
            <input type="text" class="form-control" id="no_produksi1" name="no_produksi[]" placeholder="No Produksi" value="" required>
        </div>
    `);

    $('#kd_part').val('');
    $('#kd_part').removeClass('is-valid');
    $('#kd_part').removeClass('is-invalid');
    $('#kd_part').attr('disabled', false);
    $('.list-part').attr('disabled', false);
    $('#nm_part').val('');
    $('#stock').val('');
    $('#qty_retur').val(1);
    $('#qty_retur').attr('disabled', false);
    $('#ket').val('');
    $('#sts_stock').val('').trigger('change');
}

function simpan(tamp){
    loading.block();
    let no_produksi = [];
    for (let x = 1; x <= parseInt($('#qty_retur').val()); x++) {
        no_produksi.push($('#no_produksi'+x).val());
    }
    
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
            no_produksi: no_produksi,
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

                detail_clear();
                $('#list-retur').html('');
                
                listDetail(response.data.detail)
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
                                    <th rowspan="2" class="w-auto ps-3 pe-3">No Produksi</th>
                                    <th rowspan="2" class="w-auto ps-3 pe-3">part Number</th>
                                    <th colspan="2" class="w-auto ps-3 pe-3">Qty</th>
                                    <th rowspan="2" class="w-auto ps-3 pe-3">Keterangan</th>
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
                                    <td>${data.no_produksi}</td>
                                    <td>${data.kd_part}</td>
                                    <td class="text-end">${data.qty}</td>
                                    <td class="text-end">${data.stock}</td>
                                    <td class="text-center"><span class="badge badge-light-danger">${data.keterangan}</span></td>
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
                    toastr.warning(response.message, "Peringatan");
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
    detail_clear()
    $('#list-retur tr').removeClass('bg-secondary');

    $('#kd_part').val(val.kd_part);
    $('#kd_part').attr('disabled', true);
    $('.list-part').attr('disabled', true);
    $('#no_produksi1').val(val.no_produksi);
    $('#no_produksi1').attr('disabled', true);
    $('#qty_retur').val(val.jumlah);
    $('#qty_retur').attr('disabled', true);
    $('#nm_part').val(val.nm_part);
    $('#stock').val(val.stock);
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
        if($('#detail_modal').find('#kd_part').val() == ''){
            toastr.warning('Kode Part Harus diisi', "Peringatan");
            $('#detail_modal').find('#kd_part').focus();
            return false;
        }
        if($('#detail_modal').find('#qty_retur').val() == 0){
            toastr.warning('Qty Retur Harus lebih dari 0', "Peringatan");
            $('#detail_modal').find('#qty_retur').focus();
            return false;
        }
        if($('#input_no_produk').find('.col-2').length > 0){
            let emptyInput = $('#input_no_produk .col-2 input').filter(function () {
                return this.value == '';
            });
            if (emptyInput.length > 0) {
                toastr.warning('No Produksi Harus diisi semua', "Peringatan");
                emptyInput.focus();
                return false;
            }
        }
        if($('#detail_modal').find('#sts_stock').val() == ''){
            toastr.warning('Status Stock Harus diisi', "Peringatan");
            $('#detail_modal').find('#sts_stock').focus();
            return false;
        }
        if($('#detail_modal').find('#sts_minimum').val() == ''){
            toastr.warning('Status Minimum Harus diisi', "Peringatan");
            $('#detail_modal').find('#sts_minimum').focus();
            return false;
        }
        if($('#detail_modal').find('#sts_klaim').val() == ''){
            toastr.warning('Status Klaim Harus diisi', "Peringatan");
            $('#detail_modal').find('#sts_klaim').focus();
            return false;
        }

        e.preventDefault();
        simpan(true);
    });
    
    $("#list_detail").on('click','.btn_dtl_delete', function (e) {
        let val = JSON.parse(atob($(this).data('a')));
        Swal.fire({
            html: 'Apakah Anda Yakin Menghapus Detail Retur dengan <b>Kode Part : ' + val.kd_part +'</b> dengan <b>No Produksi : '+val.no_produksi+'</b>',
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
                        no_produksi: val.no_produksi
                    },
                    function (response) {
                        if (response.status == '1') {
                            $('#list-retur').find('tr[data-key="'+String(val.kd_part)+String(val.no_produksi)+'"]').remove();
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
                            toastr.warning(response.message, "Peringatan");
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
    $('#qty_retur').on('change', function () {
        if($(this).val() == 0 || $(this).val() == ''){
            $(this).val(1);
        }else if(parseInt($(this).val()) > 100){
            $('#qty_retur').val(100);
        }

        validasi_sts_stock();

        if(parseInt($('#input_no_produk .col-2').length) < parseInt($(this).val())){
            for (let x = parseInt($('#input_no_produk .col-2').length)+1; x <= parseInt($(this).val()); x++) {
                $('#input_no_produk').append(`
                    <div class="col-2 mt-3">
                        <input type="text" class="form-control" id="no_produksi${x}" name="no_produksi[]" placeholder="No Produksi" value="" style="text-transform: uppercase;" required>
                    </div>
                `);
            }
        } else {
            for (; parseInt($('#input_no_produk .col-2').length) > parseInt($(this).val());) {
                let emptyInput = $('#input_no_produk .col-2 input').filter(function () {
                    return this.value == '';
                });
                console.log(emptyInput);
                if (emptyInput.length > 0) {
                    emptyInput.parent('.col-2').last().remove();
                } else {
                    $('#input_no_produk .col-2').last().remove();
                }
            }
        }

        enter_input();
    });
    $('#sts_stock').on('change', function () {
        validasi_sts_stock();
    });
    $('#part-list').on('click','.pilih' ,function () {
        validasi_sts_stock();
    });
    $("#add_detail").on('click', function (e) {
        switch (true) {
            case ($('#kd_sales').val() == ''):
                toastr.warning('Kode Sales Harus diisi', "Peringatan");
                $('#kd_sales').focus();
                return false;
            case ($('#tgl_retur').val() == ''):
                toastr.warning('Tanggal Retur Harus diisi', "Peringatan");
                $('#tgl_retur').focus();
                return false;
            case ($('#jenis_konsumen').val() == '0' && $('#kd_dealer').val() == ''):
                toastr.warning('Kode Dealer Harus diisi', "Peringatan");
                $('#kd_dealer').focus();
                return false;
            case ($('#jenis_konsumen').val() == '1' && $('#kd_cabang').val() == ''):
                toastr.warning('Kode Cabang Harus diisi', "Peringatan");
                $('#kd_cabang').focus();
                return false;
        }
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
});