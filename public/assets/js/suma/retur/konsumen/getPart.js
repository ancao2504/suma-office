function Part(requst){
    if($('#detail_modal').find('#no_faktur').val() == ''){
        Invalid([$('#detail_modal').find('#no_faktur')], $('#detail_modal').find('#error_no_faktur'), 'No Faktur Harus diisi');
        return false;
    }

    loading.block();
    $('#part-list').find('tbody').html(`
    <tr>
        <td colspan="5" class="text-center text-primary">
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </td>
    </tr>`);
    $.get(base_url+'/part',{
        option: requst.option,
        no_faktur: $('#no_faktur').val(),
        kd_sales: old.kd_sales??$('#kd_sales').val(),
        kd_dealer: old.kd_cabang??$('#kd_dealer').val(),
        kd_part: requst.kd_part,
        page : requst.page,
        per_page : requst.per_page
    }, function (response) {
        if(response.status == '1'){
            let dataJson = response.data;
            if(requst.option[0] == 'first'){
                if (jQuery.isEmptyObject(dataJson)) {
                    $('#nm_part').val('');
                    $('#stock').val('');
                    Invalid([$('#detail_modal').find('#kd_part')], $('#detail_modal').find('#error_kd_part'), 'Part Number Tidak Ditemukan');
                } else {
                    $('#kd_part').val(dataJson.kd_part);
                    $('#nm_part').val(dataJson.nm_part);
                    $('#stock').val(dataJson.stock);
                    limit_stock = data.stock;
                    limit_jumlah = dataJson.limit_jumlah;
                    $('#kd_part').removeClass('is-invalid');
                    $('#kd_part').addClass('is-valid');
                    valid([$('#detail_modal').find('#kd_part')], $('#detail_modal').find('#error_kd_part'), '');
                }
            } else if (requst.option[0] == 'page') {
                $('#part-list').html(response.data);
                valid([$('#detail_modal').find('#kd_part')], $('#detail_modal').find('#error_kd_part'), '');
                $('#part-list').modal('show');
            } else {
                $('#dealer-list .close').trigger('click')
            }
        }
        if (response.status == '0') {
            Invalid([$('#detail_modal').find('#kd_part')], $('#detail_modal').find('#error_kd_part'), response.message);
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
        $('#part-list .close').trigger('click')
        Swal.fire({
            title: 'Error ' + jqXHR.status,
            text: 'Maaf, Terjadi Kesalahan, Silahkan coba lagi!',
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

    $(document).ajaxStop(function () {
        loading.release();
    });
}


$('#part-list').on('click','.pilih' ,function () {
    const data = JSON.parse(atob($(this).data('a')));
    $('#kd_part').val(data.kd_part);
    valid([$('#detail_modal').find('#kd_part')], $('#detail_modal').find('#error_kd_part'), '');
    $('#nm_part').val(data.nm_part);
    $('#stock').val(data.stock);
    limit_stock = data.stock;
    limit_jumlah = data.limit_jumlah;
    $('#part-list .close').trigger('click')
})

$('#kd_part').on('change', function () {
    $('#ket_part').val('');
    if($('#kd_part').val() == ''){
        $('#kd_part').removeClass('is-valid');
        return false;
    }

    Part({
        option: ['first','with_stock'],
        kd_part: $(this).val(),
        page : 1,
        per_page : 10
    });
});

$('.list-part').on('click', function () {
    Part({
        option: ['page','with_stock'],
        page : 1,
        per_page : 10
    });
});

$('#part-list').on('click', '.pagination .page-item', function () {
    Part({
        option: ['page','with_stock'],
        page : $(this).find('a').attr('href').split('page=')[1],
        per_page : $('#part-list').find('#per_page').val()
    });
});

$('#part-list').on('change','#per_page', function () {
    Part({
        option: ['page','with_stock'],
        kd_part: $('#part-list').find('#cari').val(),
        page : 1,
        per_page : $(this).val()
    });
});

$('#part-list').on('change','#cari', function () {
    $('#part-list').find('#btn_cari').trigger('click');
});
$('#part-list').on('click','#btn_cari', function () {
    Part({
        option: ['page','with_stock'],
        kd_part: $('#part-list').find('#cari').val(),
        page : 1,
        per_page : $('#part-list').find('#per_page').val()
    });
});
