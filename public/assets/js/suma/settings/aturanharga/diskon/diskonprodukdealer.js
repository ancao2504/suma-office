$(document).ready(function () {
    // jika terdapat variabel old maka data bisa langsung di submit tanpalagi menunggu validasi produk
    if (old.produk != null) {
        $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('type', 'submit');
    }
    // end 

    // search di url
    const params = new URLSearchParams(window.location.search)
    for (const param of params) {
        var search = params.get('search');
        var per_page = params.get('per_page');
        var page = params.get('page');
        var menuview = params.get('data');
    }
    //  end search di url

    // jika terdapat submit pada form
    $('form').submit(function (e) {
        loading.block();
    });
    // end form

    // validasi inputan kode produk
    $('#produk').on('change', function () {
        loading.block();
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
                        $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('type', 'submit');
                    }
                } else if (data.status == 0) {
                    $('#nama_produk').val('');
                    $('#produk').removeClass('is-valid');
                    $('#produk').addClass('is-invalid');
                    $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('type', 'button');
                }
            },
            error: function (data) {
                $('#nama_produk').val('');
                $('#produk').removeClass('is-valid');
                $('#produk').addClass('is-invalid');
                $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('type', 'button');
            }
        });
        loading.release();
    });
    // end validasi inputan kode produk

    // validasi dealer
    $('#dealer').on('change', function () {
        loading.block();
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
                        $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('type', 'submit');
                    }
                } else if (data.status == 0) {
                    $('#nama_dealer').val('');
                    $('#dealer').removeClass('is-valid');
                    $('#dealer').addClass('is-invalid');
                    $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('type', 'button');
                }
            },
            error: function (data) {
                $('#nama_dealer').val('');
                $('#dealer').removeClass('is-valid');
                $('#dealer').addClass('is-invalid');
                $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('type', 'button');
            }
        });
        loading.release();
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

    // pagination tabel
    $('#kt_project_users_table_paginate > ul > li').on('click', function () {
        if ($(this).hasClass('disabled') === false) {
            gantiUrl($(this).find('a').data('page'));
        }
    });
    // end pagination

    // search
    $('#filterSearch').val(search);
    $('#filterSearch').on('change keydown', function (e) {
        if (e.keyCode == 13 || e.type == 'change') {
            gantiUrl(1);
        }
    });
    // end search

    // per_page
    $('#kt_project_users_table_length > label > select > option[value="' + per_page + '"]').prop('selected', true);
    $('#kt_project_users_table_length > label > select').on('change', function () {
        gantiUrl(1);
    });
    // end per_page

    // merubah url dengan parameter yang baru + reload
    function gantiUrl(page = current_page) {
        loading.block();
        window.location.href = window.location.origin + window.location.pathname + "?page=" + page + "&per_page=" + $('#kt_project_users_table_length > label > select').val() + "&search=" + $('#filterSearch').val();
    }
    // end pagination,search,per_page


    // saat tambah diskon di klik focus ke pruduk dan merubah tombol enter menjadi tab
    $('#staticBackdrop').on('shown.bs.modal', function () {
        $('#produk').focus();
        $('#staticBackdrop').find('input').on('keydown', function (e) {
            if (e.which == 13) {
                e.preventDefault();
                var index = $('#staticBackdrop').find('input').index(this) + 1;
                $('#staticBackdrop').find('input').eq(index).focus();
            }
        });
    });
    // end saat tambah diskon
});