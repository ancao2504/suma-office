function detail_clear(){
    $('#no_ps').val('');
    // $('#no_dus').val('');
    $('#no_klaim').val('');
    $('#no_klaim').removeClass('is-valid');
    $('#no_klaim').removeClass('is-invalid');
    $('#no_klaim').attr('disabled', false);
    $('.list-klaim').attr('disabled', false);
    $('#kd_part').val('');
    $('#ket_klaim').val('');
    $('#kd_part').removeClass('is-valid');
    $('#kd_part').removeClass('is-invalid');
    $('#kd_part').attr('disabled', false);
    $('.list-part').attr('disabled', false);
    $('#nm_part').val('');
    $('#qty_klaim').val('');
    $('#input_no_produk').html(`
        <div class="col-2 mt-3">
            <input type="text" class="form-control" id="no_produksi1" name="no_produksi[]" placeholder="No Produksi" value="" disabled>
        </div>
    `);
    $('#kode_claim_kualitas').val('').trigger('change');
    $('#kode_claim_non_kualitas').val('').trigger('change');
    $('#kode_claim').val('');
    $('#ket').val('');
    $('#diterima').val('');
}

function Invalid(el, el_error, text){
    el.forEach(function (e) {
        e.addClass('is-invalid');
    });
    el_error.text(text);

    $('html, body').animate({
        scrollTop: (el[0].offsetTop - 100)
    }, 500);
}

function valid(el, el_error, text){
    el.forEach(function (e) {
        e.removeClass('is-invalid');
    });
    el_error.text(text);
}

function simpan(tamp){
    if ($('#tgl_retur').val() == '') {
        Invalid([$('#tgl_retur')], $('#error_tgl_retur'), 'Tanggal Retur Harus diisi');
        return false;
    } else {
        valid([$('#tgl_retur')], $('#error_tgl_retur'), '');
    }
    if ($('#kd_supp').val() == '') {
        Invalid([$('#kd_supp'),$('#kd_supp + span .select2-selection')], $('#error_kd_supp'), 'Kode Supplier Harus diisi');
        return false;
    } else {
        valid([$('#kd_supp'),$('#kd_supp + span .select2-selection')], $('#error_kd_supp'), '');
    }

    if (tamp == 1){
        if ($('#no_klaim').val() == '') {
            Invalid([$('#no_klaim')], $('#error_no_klaim'), 'No Klaim Harus diisi');
            return false;
        } else {
            valid([$('#no_klaim')], $('#error_no_klaim'), '');
        }
        if ($('#kd_part').val() == '') {
            Invalid([$('#kd_part')], $('#error_kd_part'), 'Kode Part Harus diisi');
            return false;
        } else {
            valid([$('#kd_part')], $('#error_kd_part'), '');
        }
    }

    loading.block();
    let ket = '';
    if ($('#kode_claim_kualitas').val().split('|')[0] == 'I'){
        ket = $('#kode_claim_kualitas').val()+$('#ket').val();
    } else if ($('#kode_claim_non_kualitas').val().split('|')[0] == 'Q'){
        ket = $('#kode_claim_non_kualitas').val()+$('#ket').val();
    } else if ($('#kode_claim_kualitas').val() != '' && $('#kode_claim_non_kualitas').val() == ''){
        ket = $('#kode_claim_kualitas').val();
    } else if ($('#kode_claim_kualitas').val() == '' && $('#kode_claim_non_kualitas').val() != ''){
        ket = $('#kode_claim_non_kualitas').val();
    }

    $.post(base_url + "/retur/supplier/form",
        {
            _token: $('meta[name="csrf-token"]').attr('content'),
            no_retur: ((tamp==1)?$('#no_retur').val():Math.floor(Math.random() * 1000000000) + 1),
            kd_supp: old.kd_supp??$('#kd_supp').val(),
            tgl_retur: $('#tgl_retur').val(),

            no_ps: $('#no_ps').val(),
            // no_dus: $('#no_dus').val(),
            no_klaim: $('#no_klaim').val(),
            kd_part: $('#kd_part').val(),
            qty_klaim: $('#qty_klaim').val(),
            no_produksi: $('#no_produksi').val(),
            ket: ket,
            diterima: $('#diterima').val(),
        },
        function (response) {
            if (response.status == '1') {
                if(tamp == 0){
                    swal.fire({
                        title: 'Perhatian!',
                        html: response.message + '<br>dengan No Retur : <b>' + response.data + '</b>',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        },
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = base_url + '/retur/supplier/jawab/ca';
                        }
                    });
                    return false;
                }
                $('#detail_modal').modal('hide');
                let dta_edt = JSON.stringify({
                    no_ps : $('#no_ps').val(),
                    // no_dus : $('#no_dus').val(),
                    no_klaim : $('#no_klaim').val(),
                    no_produksi : $('#no_produksi').val(),
                    kd_part : $('#kd_part').val(),
                    nm_part : $('#nm_part').val(),
                    ket : (
                        ($('#kode_claim_kualitas').val().split('|')[0] == 'I')? $('#kode_claim_kualitas').val()+$('#ket').val() :
                        ($('#kode_claim_non_kualitas').val().split('|')[0] == 'Q')? $('#kode_claim_non_kualitas').val()+$('#ket').val() :
                        ($('#kode_claim_kualitas').val() != '' && $('#kode_claim_non_kualitas').val() == '')? $('#kode_claim_kualitas').val() :
                        ($('#kode_claim_kualitas').val() == '' && $('#kode_claim_non_kualitas').val() != '')? $('#kode_claim_non_kualitas').val() : ''
                    ),
                    jumlah : $('#qty_klaim').val(),
                    diterima : $('#diterima').val(),
                    ket_klaim : $('#ket_klaim').val(),
                });
                let dta_del = JSON.stringify({
                    no_klaim: $('#no_klaim').val(),
                    kd_part: $('#kd_part').val()
                });
                if($('#no_klaim').val() != '' && $('#list-retur tr[data-key="'+($('#no_klaim').val()+$('#kd_part').val())+'"]').length == 0){

                    $('#list-retur .text_not_data').remove();

                    $('#list-retur').append(`
                        <tr class="fw-bolder fs-8 border" data-key="${($('#no_klaim').val()+$('#kd_part').val())}">
                            <td class="text-center">${$('#list-retur tr').length + 1}</td>
                            <td>${$('#no_klaim').val()}</td>
                            <td>${$('#kd_part').val()}</td>
                            <td>${$('#nm_part').val()}</td>
                            <td class="text-end">${$('#qty_klaim').val()}</td>
                            <td>${(
                                ($('#kode_claim_kualitas').val().split('|')[0] == 'I' || $('#kode_claim_non_kualitas').val().split('|')[0] == 'Q')? $('#ket').val() :

                                ($('#kode_claim_kualitas').val() != '' && $('#kode_claim_non_kualitas').val() == '')? $('#kode_claim_kualitas').val().split('|')[1] :

                                ($('#kode_claim_kualitas').val() == '' && $('#kode_claim_non_kualitas').val() != '')? $('#kode_claim_non_kualitas').val().split('|')[1] : ''
                            )}</td>
                            <td class="text-center">
                                <a role="button" data-bs-toggle="modal" href="#detail_modal" data-a='${btoa(dta_edt)}' class="btn_dtl_edit btn-sm btn-icon btn-warning my-1"><i class="fas fa-edit text-dark"></i></a>
                                <a role="button" data-a='${btoa(dta_del)}' class="btn_dtl_delete btn-sm btn-icon btn-danger my-1" data-bs-toggle="modal" data-bs-target="#delet-retur"><i class="fas fa-trash text-white"></i></a>
                            </td>
                        </tr>
                    `);
                } else {
                    $('#list-retur tr[data-key="'+($('#no_klaim').val()+$('#kd_part').val())+'"]').find('td:eq(1)').text($('#no_klaim').val());
                    $('#list-retur tr[data-key="'+($('#no_klaim').val()+$('#kd_part').val())+'"]').find('td:eq(2)').text($('#kd_part').val());
                    $('#list-retur tr[data-key="'+($('#no_klaim').val()+$('#kd_part').val())+'"]').find('td:eq(3)').text($('#nm_part').val());
                    $('#list-retur tr[data-key="'+($('#no_klaim').val()+$('#kd_part').val())+'"]').find('td:eq(4)').text($('#qty_klaim').val());
                    $('#list-retur tr[data-key="'+($('#no_klaim').val()+$('#kd_part').val())+'"]').find('td:eq(5)').text((
                        ($('#kode_claim_kualitas').val().split('|')[0] == 'I' || $('#kode_claim_non_kualitas').val().split('|')[0] == 'Q')? $('#ket').val() :
                        ($('#kode_claim_kualitas').val() != '' && $('#kode_claim_non_kualitas').val() == '')? $('#kode_claim_kualitas').val().split('|')[1] :
                        ($('#kode_claim_kualitas').val() == '' && $('#kode_claim_non_kualitas').val() != '')? $('#kode_claim_non_kualitas').val().split('|')[1] : ''
                    ));
                    $('#list-retur tr[data-key="'+($('#no_klaim').val()+$('#kd_part').val())+'"]').find('td:eq(6)').html(`
                        <a role="button" data-bs-toggle="modal" href="#detail_modal" data-a='${btoa(dta_edt)}' class="btn_dtl_edit btn-sm btn-icon btn-warning my-1"><i class="fas fa-edit text-dark"></i></a>
                        <a role="button" data-a='${btoa(dta_del)}' class="btn_dtl_delete btn-sm btn-icon btn-danger my-1" data-bs-toggle="modal" data-bs-target="#delet-retur"><i class="fas fa-trash text-white"></i></a>
                    `);
                }

                detail_clear();

                $('#kd_supp').attr('disabled', true);
                $('#tgl_retur').attr('disabled', true);
            }
            if (response.status == '0') {
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
    $('#no_ps').val(val.no_ps);
    // $('#no_dus').val(val.no_dus);
    $('#no_klaim').val(val.no_klaim);
    $('#no_klaim').addClass('is-valid');
    $('#no_klaim').attr('disabled', true);
    $('.list-klaim').attr('disabled', true);
    $('#kd_part').val(val.kd_part);
    $('#kd_part').addClass('is-valid');
    $('#kd_part').attr('disabled', true);
    $('.list-part').attr('disabled', true);
    $('#nm_part').val(val.nm_part);
    $('#qty_klaim').val(val.jumlah);
    $('#no_produksi').val(val.no_produksi);
    $('#ket_klaim').val(val.ket_klaim);

    if(val.ket.split('|')[0] == 'I' || val.ket.split('|')[0] == 'Q'){
        $('#kode_claim_kualitas option[value="'+val.ket.split('|')[0]+'|"]').prop('selected', true).trigger('change');
        $('#kode_claim_non_kualitas option[value="'+val.ket.split('|')[0]+'|"]').prop('selected', true).trigger('change');
        $('#ket').val(val.ket.split('|')[1]);
    } else {
        $('#kode_claim_kualitas option[value="'+val.ket+'"]').prop('selected', true).trigger('change');
        $('#kode_claim_non_kualitas option[value="'+val.ket+'"]').prop('selected', true).trigger('change');
        $('#ket').val('');
    }
    $('#diterima').val(val.diterima);
}

function delete_detail(val){
    loading.block();
    $.post(base_url + '/retur/supplier/delete',
        {
            _token: $('meta[name="csrf-token"]').attr('content'),
            no_retur: $('#no_retur').val(),
            no_klaim: val.no_klaim,
            kd_part: val.kd_part,
        },
        function (response) {
            if (response.status == '1') {
                $('#list-retur').find('tr[data-key="'+(val.no_klaim+val.kd_part)+'"]').remove();
                if ($('#list-retur').find('tr').length == 0) {
                    $('#list-retur').html(`
                        <tr class="fw-bolder fs-8 border text_not_data">
                            <td colspan="13" class="text-center">Tidak ada data</td>
                        </tr>
                    `);

                    $('#kd_supp').attr('disabled', false);
                    $('#kd_supp').val('').trigger('change');
                    $('#tgl_retur').attr('disabled', false);

                    old.kd_supp = null;
                } else {
                    $('#list-retur tr').each(function (i) {
                        $(this).find('td:eq(0)').text(i + 1);
                    });
                    $('#kd_supp').attr('disabled', true);
                    $('#tgl_retur').attr('disabled', true);
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

$(document).ready(function () {
    $("#tgl_retur").flatpickr().setDate(moment($("#tgl_retur").val()).format('YYYY-MM-DD'));
    $('#kd_supp').val(old.kd_supp).trigger('change');

    $("#list_detail").on('click','.btn_dtl_edit', function () {
        let val = JSON.parse(atob($(this).data('a')));
        edit_detail(val);
    });

    $(".btn_simpan").click(function (e) {
        swal.fire({
            title: 'Perhatian!',
            html: 'Apakah Anda Yakin Menyimpan Data Retur?',
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
        e.preventDefault();
        if ($('#no_klaim').hasClass('is-invalid')){
            return false;
        }
        if ($('#kd_part').hasClass('is-invalid')){
            return false;
        }
        if($('#no_klaim').val() == ''){
            Invalid([$('#no_klaim')], $('#error_no_klaim'), 'No Klaim Harus diisi');
            return false;
        } else {
            valid([$('#no_klaim')], $('#error_no_klaim'), '');
        }
        if($('#kd_part').val() == ''){
            Invalid([$('#kd_part'),$('#kd_part + span .select2-selection')], $('#error_kd_part'), 'Kode Part Harus diisi');
            return false;
        } else {
            valid([$('#kd_part'),$('#kd_part + span .select2-selection')], $('#error_kd_part'), '');
        }
        if($('#kode_claim_kualitas').val() == '' && $('#kode_claim_non_kualitas').val() == ''){
            Invalid([$('#kode_claim_kualitas'),$('#kode_claim_kualitas + span .select2-selection'),$('#kode_claim_non_kualitas'),$('#kode_claim_non_kualitas + span .select2-selection')], $('#error_kode_claim'), 'Kode Claim Kualitas atau Non Kualitas Harus diisi');
            return false;
        } else {
            valid([$('#kode_claim_kualitas'),$('#kode_claim_kualitas + span .select2-selection'),$('#kode_claim_non_kualitas'),$('#kode_claim_non_kualitas + span .select2-selection')], $('#error_kode_claim'), '');
        }
        if($('#diterima').val() == ''){
            Invalid([$('#diterima')], $('#error_diterima'), 'Diterima Harus diisi');
            return false;
        } else {
            valid([$('#diterima')], $('#error_diterima'), '');
        }
        simpan(1);
    });
    $("#list_detail").on('click','.btn_dtl_delete', function (e) {
        let val = JSON.parse(atob($(this).data('a')));
        Swal.fire({
            html: `
                <div class="text-start">
                    Apakah Anda Yakin Menghapus Detail Retur dengan
                </div>
                    <table class="table table-row-dashed table-row-gray-300 align-middle">
                        <tbody class="fw-bolder fs-8 border">
                            <tr>
                                <td>No Klaim</td>
                                <td>${val.no_klaim}</td>
                            </tr>
                            <tr>
                                <td>Kode Part</td>
                                <td>${val.kd_part}</td>
                            </tr>
                        </tbody>
                    </table>
            `,
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

    $("#add_detail").on('click', function (e) {
        if ($('#tgl_retur').val() == '') {
            Invalid([$('#tgl_retur')], $('#error_tgl_retur'), 'Tanggal Retur Harus diisi');
            return false;
        } else {
            valid([$('#tgl_retur')], $('#error_tgl_retur'), '');
        }
        if ($('#kd_supp').val() == '') {
            Invalid([$('#kd_supp'),$('#kd_supp + span .select2-selection')], $('#error_kd_supp'), 'Kode Supplier Harus diisi');
            return false;
        } else {
            valid([$('#kd_supp'),$('#kd_supp + span .select2-selection')], $('#error_kd_supp'), '');
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



    $('#detail_modal').on('change','#kode_claim_kualitas', function () {
        $('#detail_modal  #kode_claim_non_kualitas option[value=""]').prop('selected', true);
    });
    $('#detail_modal').on('change','#kode_claim_non_kualitas', function () {
        $('#detail_modal  #kode_claim_kualitas option[value=""]').prop('selected', true);
    });

    $('#detail_modal').on('change','#kode_claim_kualitas, #kode_claim_non_kualitas', function () {
        if($(this).val().split('|')[0] == 'I' || $(this).val().split('|')[0] == 'Q'){
            $('#detail_modal  #ket').attr('hidden', false);
        }else{
            $('#detail_modal  #ket').attr('hidden', true);
        }
    });

    $('#klaim-list').on('click', '.close', function () {
        $('#detail_modal').modal('show');
    });
});
