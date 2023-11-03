function Dealer(requst) {
    if ($('#kd_sales').val() == '') {
        Invalid([$('#kd_sales')], $('#error_kd_sales'), 'Kode Sales Tidak Boleh Kosong!');
        return false;
    } else {
        valid([$('#kd_sales')], $('#error_kd_sales'), '');
    }

    loading.block();
    $.get(base_url+'/dealer',{
        option: requst.option,
        kd_sales: $('#kd_sales').val(),
        kd_dealer: requst.kd_dealer,
        page: requst.page,
        per_page: requst.per_page
    }, function (response) {
        if(response.status == '1'){
            let dataJson = response.data;
            if(requst.option == 'first'){
                if (jQuery.isEmptyObject(dataJson)) {
                    $('#kd_dealer').val('');
                    Invalid([$('#kd_dealer')], $('#error_kd_dealer'), 'Kd Dealer Tidak Ditemukan!');
                } else {
                    $('#kd_dealer').val(dataJson.kd_dealer);
                    $('#nm_dealer').val(dataJson.nm_dealer);
                    $('#alamat1').val(dataJson.alamat1);
                    $('#kotasj').val(dataJson.kotasj);
                    valid([$('#kd_dealer')], $('#error_kd_dealer'), '');
                }
            } else if (requst.option == 'page') {
                $('#dealer-list').html(response.data);
                $('#dealer-list').modal('show');
            }
        } else {
            $('#dealer-list .close').trigger('click')
        }
        if (response.status == '0') {
            $('#kd_dealer').val('');
            Invalid([$('#kd_dealer')], $('#error_kd_dealer'), response.message);
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
        $('#dealer-list .close').trigger('click')
        Swal.fire({
            title: 'Error ' + jqXHR.status,
            text: textStatus,
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-secondary'
            },
            allowOutsideClick: false
        });
    });
}

// dokumen ready
$(document).ready(function () {
    $('#dealer-list').on('click','.pilih', function () {
        $('#kd_dealer').val($(this).data('a'));
        Dealer({
            option: 'first',
            kd_dealer: $(this).data('a')
        });
        $('#dealer-list .close').trigger('click')
    });

    $('#dealer-list').on('change','#per_page', function () {
        Dealer({
            option: 'page',
            kd_dealer: $('#dealer-list').find('#cari').val(),
            page: 1,
            per_page: $(this).val(),
        });
    });

    $('#dealer-list').on('click', '.pagination .page-item', function () {
        Dealer({
            option: 'page',
            kd_dealer: $('#dealer-list').find('#cari').val(),
            page: $(this).find('a').attr('href').split('page=')[1],
            per_page: $('#dealer-list #per_page').val(),
        });
    });

    $('#dealer-list').on('change','#cari', function () {
        $('#dealer-list').find('#btn_cari').trigger('click');
    });
    $('#dealer-list').on('click','#btn_cari', function () {
        Dealer({
            option: 'page',
            kd_dealer: $('#dealer-list').find('#cari').val(),
            page: 1,
            per_page: $('#dealer-list #per_page').val(),
        });
    });

    $('#kd_dealer').on('change', function () {
        $('#nm_dealer').val('');
        if ($(this).val() == '') {
            $(this).removeClass('is-valid');
            return false;
        }

        Dealer({
            option: 'first',
            kd_dealer: $(this).val()
        });
    });

    $('.list-dealer').on('click', function () {
        Dealer({
            option: 'page',
            kd_dealer: $('#dealer-list').find('#cari').val(),
            page: 1,
            per_page: 10
        });
    });

    $('#kd_sales').on('change', function () {
        $('#kd_dealer').val('');
        $('#kd_dealer').removeClass('is-valid');
        $('#kd_dealer').removeClass('is-invalid');
        $('#nm_dealer').val('');
        $('#alamat1').val('');
        $('#kotasj').val('');
    });
});
