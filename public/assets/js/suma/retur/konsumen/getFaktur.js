function Faktur(requst) {
    loading.block();
    $.get(base_url+'/faktur/klaim',{
        option: requst.option,
        kd_sales: $('#kd_sales').val(),
        kd_dealer: $('#kd_dealer').val(),
        no_faktur: requst.no_faktur??$('#faktur-list #no_faktur').val(),
        no_retur: $('#no_retur').val(),
        page : requst.page,
        per_page : requst.per_page,
    }, function (response) {
        if(response.status == '1'){
            let dataJson = response.data;
            if(requst.option == 'first'){
                if (jQuery.isEmptyObject(dataJson)) {
                    Invalid([$('#detail_modal').find('#no_faktur')], $('#detail_modal').find('#error_no_faktur'), 'No Faktur tidak ditemukan');
                } else {
                    $('#detail_modal #no_faktur').val(dataJson.no_faktur);
                    $("#detail_modal #tgl_pakai").flatpickr().setDate(moment(dataJson.tgl_faktur).format('YYYY-MM-DD'));
                    $('#detail_modal #no_faktur').addClass('is-valid');
                    $('#detail_modal #no_faktur').removeClass('is-invalid');
                    valid([$('#detail_modal').find('#no_faktur')], $('#detail_modal').find('#error_no_faktur'), '');
                    $('.list-part').trigger('click');
                }
            }else if (requst.option == 'page') {
                $('#faktur-list').html(dataJson);
                valid([$('#detail_modal').find('#no_faktur')], $('#detail_modal').find('#error_no_faktur'), '');
                $('#detail_modal').modal('hide');
                $('#faktur-list').modal('show');

                $('#kd_part').val('');
                $('#kd_part').removeClass('is-valid');
                $('#nm_part').val('');
                $('#stock').val('');
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
    $(document).ajaxStop(function () {
        loading.release();
    });
}

// document ready
$(document).ready(function () {
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
            no_faktur: $('#detail_modal #no_faktur').val(),
            page : 1,
            per_page : 10

        });
    });

    $('#faktur-list').on('click','.pilih' ,function () {
        const a =  JSON.parse(atob($(this).data('a')));
        $('#detail_modal #no_faktur').val(a.no_faktur);
        $("#detail_modal #tgl_pakai").flatpickr().setDate(moment(a.tgl_faktur).format('YYYY-MM-DD'));
        $('#faktur-list').modal('hide');
        $('#detail_modal').modal('show');
        valid([$('#detail_modal').find('#no_faktur')], $('#detail_modal').find('#error_no_faktur'), '');
        $('#detail_modal .list-part').trigger('click');
    })

    $('.list-faktur').on('click', function () {
        Faktur({
            option: 'page',
            page : 1,
            per_page : 10
        });
    });

    $('#faktur-list').on('click', '.pagination .page-item', function () {
        Faktur({
            option: 'page',
            page : $(this).find('a').attr('href').split('page=')[1],
            per_page : $('#faktur-list').find('#per_page').val()
        });
    });

    $('#faktur-list').on('change','#per_page', function () {
        Faktur({
            option: 'page',
            page : 1,
            per_page : $(this).val()
        });
    });

    $('#faktur-list').on('change','#cari', function () {
        $('#faktur-list').find('#btn_cari').trigger('click');
    });

    $('#faktur-list').on('click','#btn_cari', function () {
        Faktur({
            option: 'page',
            page : 1,
            per_page : $('#faktur-list').find('#per_page').val()
        });
    });
});
