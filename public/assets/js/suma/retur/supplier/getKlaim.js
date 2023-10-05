function Klaim(requst){
    loading.block();
    $('#klaim-list').find('tbody').html(`
    <tr>
        <td colspan="5" class="text-center text-primary">
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </td>
    </tr>`);
    $.get(base_url+'/retur',{
        option: requst.option,
        no_retur: requst.no_retur,
        page : requst.page,
        per_page : requst.per_page
    }, function (response) {
        if(response.status == '1'){
            let dataJson = response.data;
            if(requst.option[0] == 'first'){
                if (jQuery.isEmptyObject(dataJson)) {
                    toastr.warning('No Klaim Tidak Ditemukan atau sudah di tambahkan pada Retur!', "info");
                    $('#no_klaim').addClass('is-invalid');
                    $('#no_klaim').removeClass('is-valid');
                } else {
                    $('#no_klaim').val(dataJson.no_retur);
                    $('#tgl_claim').val(dataJson.tanggal);
                    $('#no_klaim').removeClass('is-invalid');
                    $('#no_klaim').addClass('is-valid');
                }
            } else if (requst.option[0] == 'page') {
                $('#klaim-list').html(response.data);
                $('#detail_modal').modal('hide');
                $('#klaim-list').modal('show');
            } else {
                $('#dealer-list .close').trigger('click')
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
        $('#klaim-list .close').trigger('click')
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

function clear_part(){
    $('#kd_part').val('');
    $('#nm_part').val('');
    $('#qty_klaim').val('');
    $('#input_no_produk').html(`
        <div class="col-2 mt-3">
            <input type="text" class="form-control" id="no_produksi1" name="no_produksi[]" placeholder="No Produksi" value="" disabled>
        </div>
    `);
    $('#ket_klaim').val('');
    $('#kd_part').removeClass('is-valid');
    $('#kd_part').removeClass('is-invalid');
}

// dokumen ready
$(document).ready(function () {

    $('#klaim-list').on('click','.pilih' ,function () {
        const data = JSON.parse(atob($(this).data('a')));
        $('#no_klaim').val(data.no_retur);
        $('#tgl_claim').val(data.tanggal);
        clear_part();
        $('#klaim-list .close').trigger('click')
    })

    $('#no_klaim').on('change', function () {
        $('#ket_part').val('');
        if($('#no_klaim').val() == ''){
            $('#no_klaim').removeClass('is-valid');
            return false;
        }

        Klaim({
            option: ['first'],
            no_retur: $(this).val(),
            page : 1,
            per_page : 10
        });

        clear_part();
    });

    $('.list-klaim').on('click', function () {
        Klaim({
            option: ['page'],
            page : 1,
            per_page : 10
        });
    });

    $('#klaim-list').on('click', '.pagination .page-item', function () {
        Klaim({
            option: ['page'],
            page : $(this).find('a').attr('href').split('page=')[1],
            per_page : $('#klaim-list').find('#per_page').val()
        });
    });

    $('#klaim-list').on('change','#per_page', function () {
        Klaim({
            option: ['page'],
            no_retur: $('#klaim-list').find('#cari').val(),
            page : 1,
            per_page : $(this).val()
        });
    });

    $('#klaim-list').on('change','#cari', function () {
        $('#klaim-list').find('#btn_cari').trigger('click');
    });
    $('#klaim-list').on('click','#btn_cari', function () {
        Klaim({
            option: ['page'],
            no_retur: $('#klaim-list').find('#cari').val(),
            page : 1,
            per_page : $('#klaim-list').find('#per_page').val()
        });
    });
});
