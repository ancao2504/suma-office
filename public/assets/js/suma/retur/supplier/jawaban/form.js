function form_clear(){
    $('#jml').val('');
    $('#alasan').val('RETUR').trigger('change');
    $('#ca').val('');
    $('#ket').val('');
    $('#keputusan').val('TERIMA').trigger('change');
}

function formatRibuan(input) {
    if (input != '' && input != null && input != undefined && input != 0) {
        return (input.toString().replace(/[^0-9]/g, '')).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
    } else {
        return '';
    }
}

function simpan(request){
    loading.block();

    let param = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        tamp:request.tamp,
        no_retur: request.no_retur,
    }

    if(request.tamp){
        param = {
            ...param,
            no_klaim: request.no_klaim,
            kd_part: request.kd_part,
            qty_jwb: $('#jml').val().replace(/\./g, ''),
            alasan: $('#alasan').val(),
            ca: $('#ca').val().replace(/\./g, ''),
            keputusan: $('#keputusan').val(),
            ket: $('#ket').val(),
        }
    }
    
    $.post(base_url + "/retur/supplier/jawab/form",
        param,
        function (response) {
            if (response.status == '1') {
                if(request.tamp){
                    const data_del = btoa(JSON.stringify({
                        no_retur: request.no_retur,
                        no_klaim: request.no_klaim,
                        kd_part: request.kd_part,
                        no_jwb: response.data.detail.no_jwb,
                    }));

                    $('#list-jwb .text_not_data').remove();
                    $('#list-jwb').append(`
                        <tr class="fw-bolder fs-8 border" data-i="${response.data.detail.no_jwb}">
                            <td class="text-center">${moment($('#tgl_claim').val()).format('YYYY/MM/DD HH:mm:ss')}</td>
                            <td class="text-end">${formatRibuan($('#jml').val())}</td>
                            <td class="text-center">${($('#alasan').val()== 'CA' ? 'Ganti Uang' : 'Ganti barang')}</td>
                            <td class="text-end">${($('#alasan').val()== 'CA' ?formatRibuan($('#ca').val()):'')}</td>
                            <td class="text-center">${$('#keputusan').val()}</td>
                            <td>${$('#ket').val()??''}</td>
                            <td class="text-center">
                                <a role="button" data-a="${data_del}" class="btn_jwb_hapus btn-sm btn-icon btn-danger my-1"><i class="fas fa-trash text-white"></i></a>
                            </td>
                        </tr>
                    `);
                    let view_table = $('#list_detail tr[data-key="'+request.no_klaim + request.kd_part+'"]');
                    let data = JSON.parse(atob(view_table.find('.btn_jwb').data('a')));

                    data.detail_jwb.push(response.data.detail);

                    view_table.find('td:eq(6)').html(response.data.qty);
                    view_table.find('td:eq(7)').html(response.data.ket);
                    view_table.find('.btn_jwb').data('a', btoa(JSON.stringify(data)));

                    form_clear();
                    if(parseFloat(view_table.find('td:eq(5)').text()) == parseFloat(view_table.find('td:eq(6)').text())){
                        $('#jwb_modal').modal('hide');
                    }
                    return false;
                }
                swal.fire({
                    title: 'Perhatian!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    },
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
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

$(document).ready(function () {
    $("#alasan").on('change', function (e) {
        if($(this).val() == 'CA'){
            $('#input_ca').attr('hidden', false);
        } else if ($(this).val() == 'RETUR'){
            $('#input_ca').attr('hidden', true);
        }
    });

    $('#jml , #ca').on('keyup', function () {
        $(this).val(formatRibuan($(this).val()));
    });

    $('#list-klaim .btn_jwb').on('click', function () {
        const data = JSON.parse(atob($(this).data('a')));
        $('#jwb_no_klaim').html(data.no_klaim);
        $('#jwb_kd_part').html(data.kd_part);

        $('#list-jwb').html('');

        $('#jwb_modal .btn_simpan_tmp').data('a', btoa(JSON.stringify({ tamp:data.tamp,no_retur: data.no_retur, no_klaim: data.no_klaim, kd_part: data.kd_part })));

        $('#jwb_modal #ca').val(data.harga).trigger('keyup');
        
        if (data.detail_jwb.length == 0) {
            $('#list-jwb').append(`
                <tr class="fw-bolder fs-8 border text_not_data">
                    <td colspan="8" class="text-center">Belum ada Jawaban</td>
                </tr>
            `);
            return false;
        }

        data.detail_jwb.forEach(function (item, index) {
            const data_del = btoa(JSON.stringify({
                no_retur: data.no_retur,
                no_klaim: data.no_klaim,
                kd_part: data.kd_part,
                no_jwb: item.no_jwb,
            }));
            $('#list-jwb').append(`
                <tr class="fw-bolder fs-8 border" data-i="${item.no_jwb}">
                    <td class="text-center">${moment(item.tgl_jwb).format('YYYY/MM/DD HH:mm:ss')}</td>
                    <td class="text-end">${formatRibuan(item.qty_jwb)}</td>
                    <td class="text-center">${(item.alasan == 'CA' ? 'Ganti Uang' : 'Ganti barang')}</td>
                    <td class="text-end">${formatRibuan(item.ca)}</td>
                    <td class="text-center">${item.keputusan}</td>
                    <td>${item.ket??''}</td>
                    <td class="text-center">
                        ${item.status_end != '1'?`
                        <a role="button" data-a="${data_del}" class="btn_jwb_hapus btn-sm btn-icon btn-danger my-1"><i class="fas fa-trash text-white"></i></a>
                        `:''}
                    </td>
                </tr>
            `);
        });
    });

    $('#jwb_modal #list-jwb').on('click','.btn_jwb_hapus', function(){
        loading.block();
        const request = JSON.parse(atob($(this).data('a')));
        $.post(base_url + "/retur/supplier/jawab/delete",
        {
            _token: $('meta[name="csrf-token"]').attr('content'),
            no_jwb: request.no_jwb,
            no_retur: request.no_retur,
            no_klaim: request.no_klaim,
            kd_part: request.kd_part,
        },
        function (response) {
            if (response.status == '1') {
                $('#list-jwb').find('tr[data-i="'+request.no_jwb+'"]').remove();
                
                let view_table = $('#list_detail tr[data-key="'+request.no_klaim + request.kd_part+'"]');
                let data = JSON.parse(atob(view_table.find('.btn_jwb').data('a')));
                data.detail_jwb = data.detail_jwb.filter(function( obj ) {
                    return obj.no_jwb != request.no_jwb;
                });
                view_table.find('td:eq(6)').html(response.data.qty);
                view_table.find('td:eq(7)').html(response.data.ket);
                view_table.find('.btn_jwb').data('a', btoa(JSON.stringify(data)));

                if($('#list-jwb tr').length == 0){
                    $('#list-jwb').append(`
                        <tr class="fw-bolder fs-8 border text_not_data">
                            <td colspan="7" class="text-center">Belum ada Jawaban</td>
                        </tr>
                    `);
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
    });

    $('.btn_simpan_tmp:not([disabled])').on('click', function () {
        $(this).attr('disabled', true);
        $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
        simpan(JSON.parse(atob($(this).data('a'))));
        $(this).attr('disabled', false);
        $(this).html('Simpan');
    });
    $('.btn_simpan:not([disabled])').on('click', function () {
        $(this).attr('disabled', true);
        $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
        swal.fire({
            title: 'Perhatian!',
            text: 'Apakah anda yakin ingin menyimpan semua jawaban?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak',
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-secondary'
            },
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                simpan(JSON.parse(atob($(this).data('a'))));
            }
        });
        $(this).attr('disabled', false);
        $(this).html('Simpan Semua Jawaban');
    });
});