function Faktur(requst) {
    $.get(base_url+'/faktur/klaim',{
        option: requst.option,
        kd_sales: $('#kd_sales').val(),
        kd_dealer: $('#kd_dealer').val(),
        no_faktur: requst.no_faktur,
    }, function (response) {
        if(response.status == '1'){
            let dataJson = response.data;
            if(requst.option == 'first'){
                if (jQuery.isEmptyObject(dataJson)) {
                    Invalid([$('#detail_modal').find('#no_faktur')], $('#detail_modal').find('#error_no_faktur'), 'No Faktur tidak ditemukan');
                } else {
                    $('#no_faktur').val(dataJson.no_faktur);
                    $("#tgl_pakai").flatpickr().setDate(moment(dataJson.tgl_faktur).format('YYYY-MM-DD'));
                    $('#no_faktur').addClass('is-valid');
                    $('#no_faktur').removeClass('is-invalid');
                    valid([$('#detail_modal').find('#no_faktur')], $('#detail_modal').find('#error_no_faktur'), '');
                    $('.list-part').trigger('click');
                }
            }
        }
        if (response.status == '0') {
            Invalid([$('#detail_modal').find('#no_faktur')], $('#detail_modal').find('#error_no_faktur'), response.message);
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
    }).fail(function (jqXHR, textStatus, error) {
        Swal.fire({
            title: 'Error ' + jqXHR.status,
            text: textStatus,
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
    });
}

$('#no_faktur').on('change', function () {
    if($('#no_faktur').val() == ''){
        $('#no_faktur').removeClass('is-valid');
        $('#kd_part').val('');
        $('#kd_part').removeClass('is-valid');
        $('#nm_part').val('');
        $('#stock').val('');
        return false;
    }

    Faktur({
        option: 'first',
        no_faktur: $('#no_faktur').val()
    });
});
