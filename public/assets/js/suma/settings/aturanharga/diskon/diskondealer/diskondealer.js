// search di url
const params = new URLSearchParams(window.location.search)
for (const param of params) {
    let request = JSON.parse(atob(params.get('param')));
    var search = request.search;
    var per_page = request.per_page;
    var page = request.page;
}
//  end search di url

function editData(data) {
    $('#dealer').val(data.kode_dealer);
    $('#dealer').attr('readonly', true);
    $('#dealer').addClass('bg-secondary');
    $('#dealer').trigger('change');

    // $('#nama_dealer').val();
    $('#disc_default').val(data.disc_default == '.00' ? 0 : data.disc_default);
    $('#disc_plus').val(data.disc_plus == '.00' ? 0 : data.disc_plus);
    $('#umur_faktur').val(data.umur_faktur == '.00' ? 0 : data.umur_faktur);

    // hapus is-valid dan invalid-feedback pada semua input kecuali kode dealer
    $('#tambah_diskon form .modal-body').find('input[required]').each(function () {
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

    $('#tambah_diskon').modal('show');
    $('#tambah_diskon > div > div > form > div.modal-footer > button.btn.btn-primary').attr('id', 'kirim');
}
// end edit data

// merubah url dengan parameter yang baru + reload
function gantiUrl(page = current_page) {
    loading.block();
    window.location.href = window.location.origin + window.location.pathname + "?param=" + btoa(JSON.stringify({page: page, per_page: $('#kt_project_users_table_length > label > select').val(), search: $('#filterSearch').val()}));
    // "?page=" + page + "&per_page=" + $('#kt_project_users_table_length > label > select').val() + "&search=" + $('#filterSearch').val();
}
// end pagination,search,per_page


$(document).ready(function () {
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

    $('#tambah_diskon form .modal-footer #kirim').on('click', function (e) {
        $('#tambah_diskon form .modal-body').find('input[required]').each(function () {
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

        // swal fire confirm apakah yakin akan mengirim data ambil inputan kode dealer pada carbang jika iya triger submit pada #tambah_diskon form
        if (!$('#tambah_diskon form .modal-body').find('input[required]').hasClass('is-invalid')) {
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
                    $('#tambah_diskon form').submit();
                }
            });
        }
    });

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
                        $('#tambah_diskon > div > div > form > div.modal-footer > button.btn.btn-primary').attr('id', 'kirim');
                    }
                } else if (data.status == 0) {
                    $('#nama_dealer').val('');
                    $('#dealer').removeClass('is-valid');
                    $('#dealer').addClass('is-invalid');
                    $('#tambah_diskon > div > div > form > div.modal-footer > button.btn.btn-primary').attr('id', 'kirim');
                }
            },
            error: function (data) {
                $('#nama_dealer').val('');
                $('#dealer').removeClass('is-valid');
                $('#dealer').addClass('is-invalid');
                $('#tambah_diskon > div > div > form > div.modal-footer > button.btn.btn-primary').attr('id', 'kirim');
            }
        });
    });
    // end validasi dealer

    // search
    $('#filterSearch').val(search);
    $('#filterSearch').on('change keydown', function (e) {
        if (e.keyCode == 13 || e.type == 'change') {
            gantiUrl(1);
        }
    });
    // end search


    // delete data
    $('.btn-delete').on('click', function () {
        let data = $(this).data('array')
        $('#delet_model #dealer').val(data.kode_dealer.trim())

        $('#form > div.modal-body > div > p.ms-text').html('Apakah anda yakin ingin menghapus diskon pada <br>Dealer : <b>' + data.kode_dealer.trim() + '</b> ?');
    });
    // end delete data


    //  add data hanya menganti label di modal dan mengosongkan inputan
    $('#btn-adddiskonproduk').on('click', function () {
        $('#tambah_diskonLabel').html('Tambah Diskon Dealer');
        $('#tambah_diskon > div > div > form').trigger('reset');
        $('#dealer').removeAttr('readonly');
        $('#dealer').removeClass('bg-secondary');
        $('#dealer').removeClass('is-valid');
        $('#dealer').removeClass('is-invalid');
    });
    // end add data

    // edit data
    $('.btn-edit').on('click', function () {
        $('#tambah_diskonLabel').html('Edit Diskon Dealer');
        var data = $(this).data('array');
        editData(data);
    });
});