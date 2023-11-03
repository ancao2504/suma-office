function Part(requst){
    if($('#no_klaim').val() == ''){
        Invalid([$('#no_klaim')], $('#error_no_klaim'), 'No Klaim Tidak Boleh Kosong!');
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
            kd_supp: $('#kd_supp').val(),
            no_retur: $('#detail_modal #no_klaim').val(),
            kd_part: requst.kd_part,
            page : requst.page,
            per_page : requst.per_page
        }, function (response) {
            if(response.status == '1'){
                let dataJson = response.data;
                if(requst.option[0] == 'first'){
                    if (jQuery.isEmptyObject(dataJson || null)) {
                        Invalid([$('#kd_part')], $('#error_kd_part'), 'Part Number Tidak Ditemukan!');
                    } else {
                        $('#kd_part').val(dataJson.kd_part);
                        $('#nm_part').val(dataJson.nm_part);
                        $('#qty_klaim').val(dataJson.jumlah);
                        $('#no_produksi').val(dataJson.no_produksi.join(', '));
                        $('#ket_klaim').val(dataJson.ket);
                        $('#kd_part').removeClass('is-invalid');
                        $('#kd_part').addClass('is-valid');
                    }
                } else if (requst.option[0] == 'page') {
                    $('#part-list').html(response.data);
                    $('#part-list').modal('show');
                } else {
                    $('#dealer-list .close').trigger('click');
                }
            }
            if (response.status == '0') {
                Invalid([$('#kd_part')], $('#error_kd_part'), response.message);
                $('#nm_part').val('');
                $('#qty_klaim').val('');
                $('#no_produksi').val('');
                $('#ket_klaim').val('');
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
}


$(document).ready(function () {
    $('#part-list').on('click','.pilih' ,function () {
        const data = JSON.parse(atob($(this).data('a')));
        $('#kd_part').val(data.kd_part);
        $('#kd_part').removeClass('is-invalid');
        $('#kd_part').addClass('is-valid');
        $('#nm_part').val(data.nm_part);
        $('#qty_klaim').val(data.jumlah);
        $('#input_no_produk').html('');
        $('#no_produksi').val(data.no_produksi.join(', '));
        $('#ket_klaim').val(data.ket);
        $('#part-list .close').trigger('click')
    })

    $('#kd_part').on('change', function () {
        if ($('#no_klaim').hasClass('is-invalid') || $('#no_klaim').val() == '') {
            $('#kd_part').val('');
            return false;
        }

        $('#ket_part').val('');
        if($('#kd_part').val() == ''){
            $('#kd_part').removeClass('is-valid');
            return false;
        }

        Part({
            option: ['first'],
            kd_part: $(this).val(),
            page : 1,
            per_page : 10
        });
    });

    $('.list-part').on('click', function () {
        if ($('#no_klaim').hasClass('is-invalid') || $('#no_klaim').val() == '') {
            $('#kd_part').val('');
            return false;
        }

        Part({
            option: ['page'],
            page : 1,
            per_page : 10
        });
    });

    $('#part-list').on('click', '.pagination .page-item', function () {
        Part({
            option: ['page'],
            page : $(this).find('a').attr('href').split('page=')[1],
            per_page : $('#part-list').find('#per_page').val()
        });
    });

    $('#part-list').on('change','#per_page', function () {
        Part({
            option: ['page'],
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
            option: ['page'],
            kd_part: $('#part-list').find('#cari').val(),
            page : 1,
            per_page : $('#part-list').find('#per_page').val()
        });
    });
});
