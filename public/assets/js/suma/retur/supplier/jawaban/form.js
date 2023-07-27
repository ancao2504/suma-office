function form_clear(){
    $('#jml').val('');
    $('#alasan').val('RETUR').trigger('change');
    $('#ca').val('');
    $('#ket').val('');
    $('#keputusan').val('TERIMA').trigger('change');
}

function formatRibuan(input) {
    if (input != '' && input != null && input != undefined && input != 0) {
        return (input.replace(/[^0-9]/g, '')).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
    } else {
        return '';
    }
}

function simpan(request){
    loading.block();
    $.post(base_url + "/retur/supplier/jawab/form",
        {
            _token: $('meta[name="csrf-token"]').attr('content'),
            no_retur: request.no_retur,
            no_klaim: request.no_klaim,
            kd_part: request.kd_part,
            qty_jwb: $('#jml').val().replace(/\./g, ''),
            alasan: $('#alasan').val(),
            ca: $('#ca').val().replace(/\./g, ''),
            keputusan: $('#keputusan').val(),
            ket: $('#ket').val(),
        },
        function (response) {
            if (response.status == '1') {
                $('#list-jwb .text_not_data').remove();
                $('#list-jwb').append(`
                    <tr class="fw-bolder fs-8 border">
                        <td class="text-center">${$('#list-jwb tr').length + 1}</td>
                        <td class="text-center">${moment($('#tgl_claim').val()).format('YYYY/MM/DD HH:mm:ss')}</td>
                        <td class="text-end">${formatRibuan($('#jml').val())}</td>
                        <td class="text-center">${($('#alasan').val()== 'CA' ? 'Ganti Uang' : 'Ganti barang')}</td>
                        <td class="text-end">${formatRibuan($('#ca').val())}</td>
                        <td class="text-center">${$('#keputusan').val()}</td>
                        <td>${$('#ket').val()??''}</td>
                    </tr>
                `);

                let view_table = $('#list_detail tr[data-key="'+request.no_klaim + request.kd_part+'"]');
                let data = JSON.parse(atob(view_table.find('.btn_jwb').data('a')));

                data.detail_jwb.push({
                    CompanyId: '',
                    alasan: $('#alasan').val(),
                    ca: $('#ca').val().replace(/\./g, ''),
                    kd_part: request.kd_part,
                    keputusan: $('#keputusan').val(),
                    ket: $('#ket').val(),
                    no_jwb: '',
                    no_klaim: request.no_klaim,
                    no_retur: request.no_retur,
                    qty_jwb: $('#jml').val().replace(/\./g, ''),
                    tgl_jwb: moment($('#tgl_claim').val()).format('YYYY/MM/DD HH:mm:ss'),
                    usertime: '',
                });

                view_table.find('td:eq(6)').html(response.data.qty_jwb);
                view_table.find('td:eq(7)').html(response.data.ket_jwb);
                view_table.find('.btn_jwb').data('a', btoa(JSON.stringify(data)));

                form_clear();
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

        $('#jwb_modal a.btn_simpan').data('a', btoa(JSON.stringify({ no_retur: data.no_retur, no_klaim: data.no_klaim, kd_part: data.kd_part })));

        if (data.detail_jwb.length == 0) {
            $('#list-jwb').append(`
                <tr class="fw-bolder fs-8 border text_not_data">
                    <td colspan="7" class="text-center">Belum ada Jawaban</td>
                </tr>
            `);
            return false;
        }

        data.detail_jwb.forEach(function (item, index) {
            $('#list-jwb').append(`
                <tr class="fw-bolder fs-8 border">
                    <td class="text-center">${index + 1}</td>
                    <td class="text-center">${moment(item.tgl_jwb).format('YYYY/MM/DD HH:mm:ss')}</td>
                    <td class="text-end">${formatRibuan(item.qty_jwb)}</td>
                    <td class="text-center">${(item.alasan == 'CA' ? 'Ganti Uang' : 'Ganti barang')}</td>
                    <td class="text-end">${formatRibuan(item.ca)}</td>
                    <td class="text-center">${item.keputusan}</td>
                    <td>${item.ket??''}</td>
                </tr>
            `);
        });
    });

    $('.btn_simpan').on('click', function () {
        simpan(JSON.parse(atob($(this).data('a'))));
    });
});