const params = new URLSearchParams(window.location.search)
for (const param of params) {
    let request = JSON.parse(atob(params.get('param')));
    var search = request.search;
    var per_page = request.per_page;
    var page = request.page;
}

// merubah url dengan parameter yang baru + reload
function gantiUrl(page = current_page) {
    loading.block();
    window.location.href = window.location.origin + window.location.pathname + "?param=" + btoa(JSON.stringify({page: page,per_page: $('#kt_project_users_table_length > label > select').val(),search: $('#filterSearch').val()}));
    // "?page=" + page + "&per_page=" + $('#kt_project_users_table_length > label > select').val() + "&search=" + $('#filterSearch').val();
    // encript  base64
    
}
// end pagination,search,per_page

/* Fungsi */
function formatRupiah(angka, prefix) {
    var number_string = angka.replace(/[^,\d]/g, '').toString(),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
}

function editData(data) {
    // console.log(data);
    $('#part_number').val(data.part_number.trim());
    $('#part_number').attr('readonly', true);
    $('#part_number').addClass('bg-secondary');
    $('#part_number').trigger('change');

    $('#dealer').val(data.kode_dealer.trim());
    $('#dealer').attr('readonly', true);
    $('#dealer').addClass('bg-secondary');
    $('#dealer').trigger('change');

    $('#harga').val(data.harga_jual.trim());

    $('#keterangan').val(data.keterangan);
    $('#staticBackdrop form .modal-body').find('input[required], select[required]').each(function () {
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
    $('#staticBackdrop').modal('show');
    $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('id','kirim');
}

$(document).ready(function () {
    //  jika terdapat kesalahan dan kembali ke halaman maka otomatis terdapat variabel old agar button pada model dapat bisa di submit tanpa lagi menunggu verivikasi part dan dealer
    if (old.part_number != null) {
        $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('id', 'kirim');
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

    // search
    $('#filterSearch').val(search);
    $('#filterSearch').on('change keydown', function (e) {
        if (e.keyCode == 13 || e.type == 'change') {
            gantiUrl(1);
        }
    });
    // end search


    // validasi inputan kode produk
    $('#part_number').on('change', function () {
        $.ajax({
            url: base_url + '/validasi/partnumber',
            type: "POST",
            data: {
                _token: $('input[name="_token"]').val(),
                part_number: this.value
            },
            success: function (data) {
                // console.log(data);
                if (data.status == 1) {
                    $('#nama_part').val(data.data.description);
                    $('#part_number').removeClass('is-invalid');
                    $('#part_number').addClass('is-valid');
                    $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('id', 'kirim');
                } else if (data.status == 0) {
                    $('#nama_part').val('');
                    $('#part_number').removeClass('is-valid');
                    $('#part_number').addClass('is-invalid');
                    $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('id', '');
                }
            },
            error: function (data) {
                $('#nama_part').val('');
                $('#part_number').removeClass('is-valid');
                $('#part_number').addClass('is-invalid');
                $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('id', '');
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
                        $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('id', 'kirim');
                    }
                } else if (data.status == 0) {
                    $('#nama_dealer').val('');
                    $('#dealer').removeClass('is-valid');
                    $('#dealer').addClass('is-invalid');
                    $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('id', '');
                }
            },
            error: function (data) {
                $('#nama_dealer').val('');
                $('#dealer').removeClass('is-valid');
                $('#dealer').addClass('is-invalid');
                $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('id', '');
            }
        });
    });
    // end validasi dealer

    // delete data
    $('.btn-delete').on('click', function () {
        var data = $(this).data('array');
        $('#delet_model #part_number').val(data.part_number);
        $('#delet_model #dealer').val(data.kode_dealer);

        $('#form > div.modal-body > div > p.ms-text').html('Apakah anda yakin ingin menghapus harga netto <br> Part Number : <b>' + data.part_number + '</b> pada Dealer : <b>' + data.kode_dealer + '</b> ?');
    });
    // end delete data

    // edit data
    $('.btn-edit').on('click', function () {
        $('#staticBackdropLabel').html('Edit Diskon Produk');
        var data = $(this).data('array');
        editData(data);
    });
    // end edit data

    //  add data hanya menganti label di modal dan mengosongkan inputan
    $('#btn-adddiskonproduk').on('click', function () {
        $('#staticBackdropLabel').html('Tambah Part Netto Dealer');
        $('#staticBackdrop > div > div > form').trigger('reset');
        $('#part_number').removeAttr('readonly');
        $('#part_number').removeClass('bg-secondary');
        $('#part_number').removeClass('is-valid');
        $('#part_number').removeClass('is-invalid');

        $('#dealer').removeAttr('readonly');
        $('#dealer').removeClass('bg-secondary');
        $('#dealer').removeClass('is-valid');
        $('#dealer').removeClass('is-invalid');
    });
    // end add data

    // saat tambah diskon di klik focus ke pruduk dan merubah tombol enter menjadi tab
    $('#staticBackdrop form .modal-body').find('input').on('keydown', function (e) {
        if (e.which == 13) {
            e.preventDefault();
            var index = $('#staticBackdrop form .modal-body').find('input').index(this) + 1;
            if ($('#staticBackdrop form .modal-body').find('input').eq(index).attr('readonly') || $('#staticBackdrop form .modal-body').find('input').eq(index).hasClass('bg-secondary')) {
                for (let i = index; i < $('#staticBackdrop form .modal-body').find('input').length; i++) {
                    if (!$('#staticBackdrop form .modal-body').find('input').eq(i).attr('readonly') || !$('#staticBackdrop form .modal-body').find('input').eq(i).hasClass('bg-secondary')) {
                        $('#staticBackdrop form .modal-body').find('input').eq(i).focus();
                        break;
                    }
                }
            } else {
                $('#staticBackdrop form .modal-body').find('input').eq(index).focus();
            }
        }
    });
    // end saat tambah diskon

    /* Tanpa Rupiah */
    var tanpa_rupiah = document.getElementById('harga');
    tanpa_rupiah.addEventListener('keyup', function (e) {
        // console.log(e.target.value);
        tanpa_rupiah.value = formatRupiah(this.value);
    });

    $('#staticBackdrop form .modal-footer').find('#kirim').on('click', function (e) {
        $('#staticBackdrop form .modal-body').find('input[required], select[required]').each(function () {
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

        // swal fire confirm apakah yakin akan mengirim data ambil inputan kode dealer pada carbang jika iya triger submit pada #staticBackdrop form
        if (!$('#staticBackdrop form .modal-body').find('input[required], select[required]').hasClass('is-invalid')) {
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
                    $('#staticBackdrop form').submit();
                }
            });
        }
    });
});
