function Faktur(requst) {
    $.get(base_url+'/faktur',{
        option: requst.option,
        kd_sales: $('#kd_sales').val(),
        kd_dealer: $('#kd_dealer').val(),
        no_faktur: requst.no_faktur,
    }, function (response) {
        if(response.status == '1'){
            let dataJson = response.data;
            if(requst.option == 'first'){
                if (jQuery.isEmptyObject(dataJson)) {
                    toastr.warning('No Faktur Tidak Ditemukan!', "info");
                    $('#no_faktur').addClass('is-invalid');
                    $('#no_faktur').removeClass('is-valid');
                } else {
                    $('#no_faktur').val(dataJson.no_faktur);
                    $('#tgl_faktur').val(dataJson.tgl_faktur);
                    disc2 = dataJson.disc2;
                    $('#no_faktur').addClass('is-valid');
                    $('#no_faktur').removeClass('is-invalid');
                }
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
    $('#kd_part').val('');
    $('#ket_part').val('');
    if($('#no_faktur').val() == ''){
        $('#no_faktur').removeClass('is-valid');
        return false;
    }

    Faktur({
        option: 'first',
        no_faktur: $('#no_faktur').val()
    });
});
