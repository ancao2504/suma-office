// dokumen ready
$(document).ready(function () {

    // agar tombol menu aktif
    function viewCard() {
        $('#kt_content_container > div.d-flex.flex-wrap.flex-stack.pb-7 > div:nth-child(2) > ul > li:nth-child(1) > a')[0].click();
        $('#kt_content_container > div.d-flex.flex-wrap.flex-stack.pb-7 > div:nth-child(2) > ul > li:nth-child(2) > a').removeClass('active');
    }
    function viewTabel() {
        $('#kt_content_container > div.d-flex.flex-wrap.flex-stack.pb-7 > div:nth-child(2) > ul > li:nth-child(2) > a')[0].click();
        $('#kt_content_container > div.d-flex.flex-wrap.flex-stack.pb-7 > div:nth-child(2) > ul > li:nth-child(1) > a').removeClass('active');
    }
    // end tombol menu aktif

    // jika terdapat submit pada form
    $('form').submit(function (e) {
        loading.block();
    });
    // end form

    // saat tambah diskon di klik focus ke pruduk dan merubah tombol enter menjadi tab
    $('#staticBackdrop').on('shown.bs.modal', function () {
        $('#dealer').focus();
        $('#staticBackdrop').find('input').on('keydown', function (e) {
            if (e.which == 13) {
                e.preventDefault();
                var index = $('#staticBackdrop').find('input').index(this) + 1;
                $('#staticBackdrop').find('input').eq(index).focus();
            }
        });
    });
    // end saat tambah diskon

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

    // search di url
    const params = new URLSearchParams(window.location.search)
    for (const param of params) {
        var search = params.get('search');
        var per_page = params.get('per_page');
        var page = params.get('page');
        var menuview = params.get('data');
    }
    //  end search di url

    // merubah url dengan parameter yang baru + reload
    function gantiUrl(page = current_page, data = '') {
        loading.block();
        window.location.href = window.location.origin + window.location.pathname + "?page=" + page + "&per_page=" + $('#kt_project_users_table_length > label > select').val() + "&search=" + $('#filterSearch').val() + "&data=" + data;
    }
    // end pagination,search,per_page

    // search
    $('#filterSearch').val(search);
    $('#filterSearch').on('change keydown', function (e) {
        if (e.keyCode == 13 || e.type == 'change') {
            gantiUrl(1, menuview);
        }
    });
    // end search

    // per_page
    $('#kt_project_users_table_length > label > select > option[value="' + per_page + '"]').prop('selected', true);
    $('#kt_project_users_table_length > label > select').on('change', function () {
        gantiUrl(1, 'tabel');
    });
    // end per_page

    // pagination, card & tabel
    $('#kt_project_users_table_paginate > ul > li').on('click', function () {
        if ($(this).hasClass('disabled') === false) {
            gantiUrl($(this).find('a').data('page'), 'tabel');
        }
    });
    $('#kt_project_users_card_pane > div.d-flex.flex-stack.flex-wrap.pt-10 > ul > li').on('click', function () {
        if ($(this).hasClass('disabled') === false) {
            gantiUrl($(this).find('a').data('page'), 'card');
        }
    });
    if (params.has('data')) {
        if (params.get('data') == 'tabel') {
            viewTabel();
        } else if (params.get('data') == 'card') {
            viewCard();
        }
    }
    // end pagination


    // delete data
    $('.btn-delete').on('click', function () {
        let data = $(this).data('array')
        $('#delet_model #dealer').val(data.kode_dealer.trim())

        $('#form > div.modal-body > div > p.ms-text').html('Apakah anda yakin ingin menghapus diskon pada <br>Dealer : <b>' + data.kode_dealer.trim() + '</b> ?');
    });
    // end delete data


    //  add data hanya menganti label di modal dan mengosongkan inputan
    $('#btn-adddiskonproduk').on('click', function () {
        $('#staticBackdropLabel').html('Tambah Diskon Dealer');
        $('#staticBackdrop > div > div > form').trigger('reset');
        $('#dealer').removeAttr('readonly');
        $('#dealer').removeClass('bg-secondary');
        $('#dealer').removeClass('is-valid');
        $('#dealer').removeClass('is-invalid');
    });
    // end add data

    // edit data
    $('.btn-edit').on('click', function () {
        $('#staticBackdropLabel').html('Edit Diskon Dealer');
        var data = $(this).data('array');
        editData(data);
    });

    function editData(data) {
        console.log(data);
        $('#dealer').val(data.kode_dealer);
        $('#dealer').attr('readonly', true);
        $('#dealer').addClass('bg-secondary');
        $('#dealer').trigger('change');

        // $('#nama_dealer').val();
        $('#disc_default').val(data.disc_default == '.00' ? 0 : data.disc_default);
        $('#disc_plus').val(data.disc_plus == '.00' ? 0 : data.disc_plus);
        $('#umur_faktur').val(data.umur_faktur == '.00' ? 0 : data.umur_faktur);

        $('#staticBackdrop').modal('show');
        $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('type', 'submit');
    }
    // end edit data
});