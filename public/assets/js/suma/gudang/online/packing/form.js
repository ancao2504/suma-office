function focusNextElement(event, currentElement) {
    if (event.keyCode === 13) {
        event.preventDefault();
        const form = document.querySelector('form');

        console.log(form.querySelectorAll('input, select, textarea'));
        // var elements = document.querySelectorAll('input, select, textarea');
        // var found = false;

        // for (var i = 0; i < elements.length; i++) {
        //     if (found) {
        //         if (!elements[i].disabled && !elements[i].readOnly && !elements[i].hidden) {
        //         elements[i].focus();
        //         break;
        //         }
        //     }
        //     else if (elements[i] === currentElement) {
        //         found = true;
        //     }
        //     console.log(i, elements.length);
        // }
    }
}

function validasi_input(target, massage) {
            
    if($(target).val() == ''){
        $(target).addClass('is-invalid');
        if($(target).next().is('div.invalid-feedback')){
            $(target).next().html(massage);
        }else{
            $(target).after('<div class="invalid-feedback">'+massage+'</div>');
        }
    } else {
        $(target).removeClass('is-invalid');
        $(target).next().remove();
        return true;
    }
}

function validasi_input_group(target, massage){
    if($(target).val() == ''){
        $(target).addClass('is-invalid');
        if($(target).closest('div.input-group').find('div.invalid-feedback').length > 0){
            $(target).closest('div.input-group').find('div.invalid-feedback').html(massage);
        }else{
            $(target).closest('div.input-group').append('<div class="invalid-feedback">'+massage+'</div>');
        }
    } else {
        $(target).removeClass('is-invalid');
        $(target).closest('div.input-group').find('div.invalid-feedback').remove();
        return true;
    }
}

function wh(requst) {
    loading.block();
        $('#modal_nowh').find('tbody').html(`
        <tr>
            <td colspan="11" class="text-center text-primary">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </td>
        </tr>`);

    $.get(base_url + '/wh',{
        option: requst.option,
        no_wh: requst.no_wh,
        page: requst.page,
        per_page: requst.per_page
    }, function (response) {
        if(response.status == '1'){
            let dataJson = response.data;
            if(requst.option == 'first'){
                if (jQuery.isEmptyObject(dataJson)) {
                    toastr.error('Nomor WH Tidak Ditemukan!', "info");
                    $('#no_dok').val('');
                    $('#no_dok').addClass('is-invalid');
                    $('#no_dok').removeClass('is-valid');
                } else {
                    toastr.success('Nomor WH Ditemukan!', "success");
                    $('#no_dok').val(dataJson.no_dok);
                    $('#no_dok').addClass('is-valid');
                    $('#no_dok').removeClass('is-invalid');

                    $('#keterangan_info').text(dataJson.ket??'-');
                    $('#Expedisi_info').text(dataJson.kd_ekspedisi??'-');
                    $('#Nama_info').text(dataJson.nm_dealer??'-');
                }
            } else if (requst.option == 'page') {
                $('#modal_nowh').html(dataJson);
                $('#modal_nowh').modal('show');
            }
        } else {
            $('#modal_nowh').modal('hide');
        }
        if (response.status == '0') {
            toastr.error(response.message, "Error");
            $('#no_dok').addClass('is-invalid');
            $('#no_dok').removeClass('is-valid');
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
        $('#modal_nowh .close').trigger('click')
        Swal.fire({
            title: 'Error ' + jqXHR.status,
            text: 'Maaf terjadi kesalahan, mohon coba kembali!',
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-secondary'
            },
            allowOutsideClick: false
        });
    });
    loading.release();
}
$(document).ready(function() {
    setInterval(() => {
        $('#time_packer').text(moment().format('DD/MM/YYYY HH:mm:ss'));
    }, 1000);

    $('#no_meja').val(old.no_meja);

    $('#no_dok').on('change', function () {
        if ($(this).val() != '') {
            wh({
                option: 'first',
                no_wh: $(this).val(),
            });
        }
    })
    $('#btn_nowh').on('click', function () {
        wh({
            option: 'page',
            no_wh: $('#modal_nowh #cari').val(),
            page: 1,
            per_page: 10
        });
    })
    $('#modal_nowh').on('change','#per_page', function () {
        wh({
            option: 'page',
            no_wh: $('#modal_nowh #cari').val(),
            page: 1,
            per_page: $(this).val()
        });
    })
    $('#modal_nowh').on('click', '.pagination a.page-link', function () {
        wh({
            option: 'page',
            no_wh: $('#modal_nowh #cari').val(),
            page: $(this).attr('href').split('?page=')[1],
            per_page: $('#modal_nowh #per_page').val(),
        });
    });
    $('#modal_nowh').on('click','#btn_cari', function () {
        wh({
            'option': 'page',
            'no_wh': $('#modal_nowh #cari').val(),
            'page': 1,
            'per_page': 10,
        });
    });

    $('#modal_nowh').on('click','.pilih', function () {
        const data_complate = JSON.parse(atob($(this).data('kd')));
        $('#no_dok').val(data_complate.no_dok);
        $('#no_dok').addClass('is-valid');
        $('#no_dok').removeClass('is-invalid');

        $('#keterangan_info').text(data_complate.ket??'-');
        $('#Expedisi_info').text(data_complate.kd_ekspedisi??'-');
        $('#Nama_info').text(data_complate.nm_dealer??'-');
        $('#modal_nowh').modal('hide');
    });

    $('#btn_submit').on('click', function(){
        let valid = {
            no_meja: validasi_input('#no_meja', 'Nomor Meja tidak boleh kosong!'),
            kd_packer: validasi_input('#kd_packer', 'Packer tidak boleh kosong!'),
            no_dok: validasi_input_group('#no_dok', 'Nomor WH tidak boleh kosong!'),
        };

        if(valid.no_meja && valid.kd_packer && valid.no_dok){
            $('#btn_submit').closest('form').submit();
        }
    });
});