// search di url
const params = new URLSearchParams(window.location.search)
for (const param of params) {
    let request = JSON.parse(atob(params.get('param')));
    var search = request.search;
    var per_page = request.per_page;
    var page = request.page;
}
//  end search di url

// merubah url dengan parameter yang baru + reload
function gantiUrl(page = current_page) {
    loading.block();
    window.location.href = window.location.origin + window.location.pathname + "?param=" + btoa(JSON.stringify({page: page, per_page: $('#kt_project_users_table_length > label > select').val(), search: $('#filterSearch').val()}));
    // "?page=" + page + "&per_page=" + $('#kt_project_users_table_length > label > select').val() + "&search=" + $('#filterSearch').val();
}
// end pagination,search,per_page

$(document).ready(function () {
    // jika terdapat variabel old maka data bisa langsung di submit tanpalagi menunggu validasi produk
    if (old.produk != null) {
        $('#tambah_diskon_dealer > div > div > form > div.modal-footer > button.btn.btn-primary').attr('id', 'kirim');
    }
    // end 

    // jika terdapat submit pada form
    $('form').submit(function (e) {
        loading.block();
    });
    // end form
    // ajax start loading
    $(document).ajaxStart(function () {
        loading.block();
    });
    // ajax stop loading
    $(document).ajaxStop(function () {
        loading.release();
    });
    // end ajax
    
    $('#tambah_diskon form .modal-body').find('input').on('keydown', function (e) {
        if (e.which == 13) {
            e.preventDefault();
            var index = $('#tambah_diskon form .modal-body').find('input').index(this) + 1;
            if ($('#tambah_diskon form .modal-body').find('input').eq(index).attr('readonly') || $('#tambah_diskon form .modal-body').find('input').eq(index).hasClass('bg-secondary')) {
                for (let i = index; i < $('#tambah_diskon form .modal-body').find('input').length; i++) {
                    if (!$('#tambah_diskon form .modal-body').find('input').eq(i).attr('readonly') || !$('#tambah_diskon form .modal-body').find('input').eq(i).hasClass('bg-secondary')) {
                        $('#tambah_diskon form .modal-body').find('input').eq(i).focus();
                        break;
                    }
                }
            } else {
                $('#tambah_diskon form .modal-body').find('input').eq(index).focus();
            }
        }
    });

    // validasi inputan kode produk
    $('#produk').on('change', function () {
        $.ajax({
            url: base_url + '/validasi/produk',
            type: "POST",
            data: {
                _token: $('input[name="_token"]').val(),
                kd_produk: this.value
            },
            success: function (data) {
                if (data.status == 1) {
                    $('#nama_produk').val(data.data.nama_produk);
                    $('#produk').removeClass('is-invalid');
                    $('#produk').addClass('is-valid');
                    if (this.value != '' && $('#produk').val() != '') {
                        $('#tambah_diskon_dealer > div > div > form > div.modal-footer > button.btn.btn-primary').attr('id', 'kirim');
                    }
                } else if (data.status == 0) {
                    $('#nama_produk').val('');
                    $('#produk').removeClass('is-valid');
                    $('#produk').addClass('is-invalid');
                    $('#tambah_diskon_dealer > div > div > form > div.modal-footer > button.btn.btn-primary').attr('id', '');
                }
            },
            error: function (data) {
                $('#nama_produk').val('');
                $('#produk').removeClass('is-valid');
                $('#produk').addClass('is-invalid');
                $('#tambah_diskon_dealer > div > div > form > div.modal-footer > button.btn.btn-primary').attr('id', '');
            }
        });
    });
    // end validasi inputan kode produk

    // validasi dealer
    $('#dealer').on('change', function () {
        $.ajax({
            url: base_url + '/validasi/dealer',
            type: "POST",
            data: {
                _token: $('input[name="_token"]').val(),
                kode_dealer: this.value
            },
            success: function (data) {
                if (data.status == 1) {
                    $('#nama_dealer').val(data.data.nama_dealer);
                    $('#dealer').removeClass('is-invalid');
                    $('#dealer').addClass('is-valid');

                    if (this.value != '' && $('#dealer').val() != '') {
                        $('#tambah_diskon_dealer > div > div > form > div.modal-footer > button.btn.btn-primary').attr('id', 'kirim');
                    }
                } else if (data.status == 0) {
                    $('#nama_dealer').val('');
                    $('#dealer').removeClass('is-valid');
                    $('#dealer').addClass('is-invalid');
                    $('#tambah_diskon_dealer > div > div > form > div.modal-footer > button.btn.btn-primary').attr('id', '');
                }
            },
            error: function (data) {
                $('#nama_dealer').val('');
                $('#dealer').removeClass('is-valid');
                $('#dealer').addClass('is-invalid');
                $('#tambah_diskon_dealer > div > div > form > div.modal-footer > button.btn.btn-primary').attr('id', '');
            }
        });
    });
    // end validasi dealer

    // delete data
    $('.btn-delete').on('click', function () {
        let data = $(this).data('array')
        $('#delet_model #produk').val(data.produk.trim())
        $('#delet_model #dealer').val(data.dealer.trim())
        $('#delet_model #cabang').val(data.cabang.trim())

        $('#form > div.modal-body > div > p.ms-text').html('Apakah anda yakin ingin menghapus diskon produk<br> Produk : <b>' + data.produk.trim() + '</b>, pada Dealer : <b>' + data.dealer.trim() + '</b> ?');
    });
    // end delete data

    // search
    $('#filterSearch').val(search);
    $('#filterSearch').on('change keydown', function (e) {
        if (e.keyCode == 13 || e.type == 'change') {
            gantiUrl(1);
        }
    });
    // end search


    $('#tambah_diskon_dealer form .modal-footer').find('#kirim').on('click', function (e) {
        $('#tambah_diskon_dealer form .modal-body').find('input[required], select[required]').each(function () {
            if ($(this).val() == '') {
                $(this).addClass('is-invalid');
                if (!$(this).next().hasClass('invalid-feedback')) {
                    $(this).after('<div class="invalid-feedback">Tidak boleh kosong</div>');
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).next().remove();
            }
        });

        // swal fire confirm apakah yakin akan mengirim data ambil inputan kode dealer pada carbang jika iya triger submit pada #tambah_diskon_dealer form
        if (!$('#tambah_diskon_dealer form .modal-body').find('input[required]').hasClass('is-invalid')) {
            swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah yakin akan mengirim data?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    $('#tambah_diskon_dealer form').submit();
                }
            });
        }
    });
});