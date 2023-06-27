function Part(requst){
    loading.block();
    $.get(base_url+'/part',{
        option: requst.option,
        no_faktur: $('#no_faktur').val(),
        kd_sales: $('#kd_sales').val(),
        kd_part: requst.kd_part,
        page : requst.page,
        per_page : requst.per_page
    }, function (response) {
        if(response.status == '1'){
            let dataJson = response.data;
            if(requst.option == 'first'){
                if (jQuery.isEmptyObject(dataJson)) {
                    toastr.error('Part Number Tidak Ditemukan!', "info");
                    $('#kd_part').addClass('is-invalid');
                    $('#kd_part').removeClass('is-valid');
                } else {
                    $('#kd_part').val(dataJson.kd_part);
                    $('#ket_part').val(dataJson.nm_part);
                    $('#kd_part').removeClass('is-invalid');
                    $('#kd_part').addClass('is-valid');
                }
            } else if (requst.option == 'page') {
                $('#part-list').html(response.data);
                $('#part-list').modal('show');
            } else {
                $('#dealer-list .close').trigger('click')
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
    }).fail(function (jqXHR, textStatus, error) {
        $('#part-list .close').trigger('click')
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


$('#part-list').on('click','.pilih' ,function () {
    $('#kd_part').val($(this).data('a'));
    Part({
        option: 'first',
        kd_part: $(this).data('a'),
        page : 1,
        per_page : 10
    });
    $('#part-list .close').trigger('click')
})

$('#kd_part').on('change', function () {
    $('#ket_part').val('');
    if($('#kd_part').val() == ''){
        $('#kd_part').removeClass('is-valid');
        return false;
    }

    Part({
        option: 'first',
        kd_part: $(this).val(),
        page : 1,
        per_page : 10
    });
});

$('.list-part').on('click', function () {
    Part({
        option: 'page',
        page : 1,
        per_page : 10
    });
});

$('#part-list').on('click', '.pagination .page-item', function () {
    Part({
        option: 'page',
        page : $(this).find('a').attr('href').split('page=')[1],
        per_page : $('#part-list').find('#per_page').val()
    });
});

$('#part-list').on('change','#per_page', function () {
    Part({
        option: 'page',
        kd_part: $('#part-list').find('#cari').val(),
        page : 1,
        per_page : $(this).val()
    });
});

$('#part-list').on('click','#btn_cari', function () {
    Part({
        option: 'page',
        kd_part: $('#part-list').find('#cari').val(),
        page : 1,
        per_page : $('#part-list').find('#per_page').val()
    });
});
