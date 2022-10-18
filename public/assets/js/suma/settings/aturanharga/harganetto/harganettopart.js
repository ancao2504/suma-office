$(document).ready(function () {
    if (old.status != null) {
        $('#status option[value="' + old.status.trim() + '"]').prop('selected', true);
        $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('type', 'submit');
    }

    // jika terdapat submit pada form
    $('form').submit(function (e) {
        loading.block();
    });
    // end form

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

    // pagination,search,per_page
    const params = new URLSearchParams(window.location.search)
    for (const param of params) {
        var search = params.get('search');
        var per_page = params.get('per_page');
        var page = params.get('page');
        var menuview = params.get('data');
    }

    // per_page
    $('#kt_project_users_table_length > label > select > option[value="' + per_page + '"]').prop('selected', true);

    $('#kt_project_users_table_length > label > select').on('change', function () {
        gantiUrl(1, 'table');
    });
    // end per_page

    // search
    $('#filterSearch').val(search);
    $('#filterSearch').on('change keydown', function (e) {
        if (e.keyCode == 13 || e.type == 'change') {
            gantiUrl(1, menuview);
        }
    });
    // end search

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

    // merubah url dengan parameter yang baru + reload
    function gantiUrl(page = current_page, data = '') {
        loading.block();
        window.location.href = window.location.origin + window.location.pathname + "?page=" + page + "&per_page=" + $('#kt_project_users_table_length > label > select').val() + "&search=" + $('#filterSearch').val() + "&data=" + data;
    }
    // end pagination,search,per_page

    // validasi inputan kode produk
    $('#part_number').on('change', function () {
        loading.block();
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
                    $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('type', 'submit');
                } else if (data.status == 0) {
                    $('#nama_part').val('');
                    $('#part_number').removeClass('is-valid');
                    $('#part_number').addClass('is-invalid');
                    $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('type', 'button');
                }
            },
            error: function (data) {
                $('#nama_part').val('');
                $('#part_number').removeClass('is-valid');
                $('#part_number').addClass('is-invalid');
                $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('type', 'button');
            }
        });
        loading.release();
    });
    // end validasi inputan kode produk

    // delete data
    $('.btn-delete').on('click', function () {
        var data = $(this).data('array');
        $('#delet_model #part_number').val(data.part_number);
        $('#delet_model #status').val('T');
        $('#delet_model #harga').val(data.harga);

        $('#form > div.modal-body > div > p.ms-text').html('Apakah anda yakin ingin menghapus diskon<br> Part Number : <b>' + data.part_number + '</b> ?');
    });
    // end delete data

    // saat tambah diskon di klik focus ke pruduk dan merubah tombol enter menjadi tab
    $('#staticBackdrop').on('shown.bs.modal', function () {
        $('#part_number').focus();
        $('#staticBackdrop').find('input').on('keydown', function (e) {
            if (e.which == 13) {
                e.preventDefault();
                var index = $('#staticBackdrop').find('input').index(this) + 1;
                $('#staticBackdrop').find('input').eq(index).focus();
            }
        });
    });
    // end saat tambah diskon

    /* Tanpa Rupiah */
    var tanpa_rupiah = document.getElementById('harga');
    tanpa_rupiah.addEventListener('keyup', function (e) {
        // console.log(e.target.value);
        tanpa_rupiah.value = formatRupiah(this.value);
    });

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
});