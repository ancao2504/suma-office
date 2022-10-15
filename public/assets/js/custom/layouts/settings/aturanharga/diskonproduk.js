$(document).ready(function () {
    if (old.cabang != null) {
        $('#cabang option[value="' + old.cabang.trim() + '"]').prop('selected', true);
    }
    var targetmodal = document.querySelector("#staticBackdrop .modal-content");
    var loadingModal = new KTBlockUI(targetmodal, {
        message: '<div class="blockui-message">' +
            '<span class="spinner-border text-primary"></span> Loading...' +
            '</div>'
    });

    var targetcontent = document.querySelector("#kt_wrapper");
    var loadingContent = new KTBlockUI(targetcontent, {
        message: '<div class="blockui-message">' +
            '<span class="spinner-border text-primary"></span> Loading...' +
            '</div>'
    });

    // jika terdapat submit pada form
    $('form').submit(function (e) {
        loadingModal.block();
    });
    // end form

    // responsive ukuran layar
    if (screen.width < 576) {
        // $('#dataDiskon > div:nth-child(3) > div > div.d-flex.flex-column.py-2.w-100 > div:nth-child(3) > table')add class table table-borderless
        $('#dataDiskon table.tb-card').removeClass('table table-borderless');
        // #dataDiskon > div:nth-child(1) > div > div.d-flex.flex-column.py-2.w-100 > div:nth-child(3) > table
    }
    // $(window).resize(function() {
    //     (screen.width < 576)? viewCard():'';
    // });

    function viewCard() {
        $('#kt_content_container > div.d-flex.flex-wrap.flex-stack.pb-7 > div:nth-child(2) > ul > li:nth-child(1) > a')[0].click();
        $('#kt_content_container > div.d-flex.flex-wrap.flex-stack.pb-7 > div:nth-child(2) > ul > li:nth-child(2) > a').removeClass('active');
    }
    function viewTabel() {
        $('#kt_content_container > div.d-flex.flex-wrap.flex-stack.pb-7 > div:nth-child(2) > ul > li:nth-child(2) > a')[0].click();
        $('#kt_content_container > div.d-flex.flex-wrap.flex-stack.pb-7 > div:nth-child(2) > ul > li:nth-child(1) > a').removeClass('active');
    }
    // end responsive ukuran layar

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
        loadingContent.block();
        window.location.href = window.location.origin + window.location.pathname + "?page=" + page + "&per_page=" + $('#kt_project_users_table_length > label > select').val() + "&search=" + $('#filterSearch').val() + "&data=" + data;
    }
    // end pagination,search,per_page

    // validasi inputan kode produk
    $('#produk').on('change', function () {
        loadingModal.block();
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
                    $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('type', 'submit');
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
        loadingModal.release();
    });
    // end validasi inputan kode produk

    // validasi data produk
    $('#produk, #cabang').on('change', function () {
        if ($('#produk').val() != '' && $('#cabang').val() != '') {
            loadingModal.block();
            $.ajax({
                url: base_url + '/setting/diskonproduk/cekproduk',
                type: "POST",
                data: {
                    _token: $('input[name="_token"]').val(),
                    kd_produk: $('#produk').val(),
                    cabang: $('#cabang').val()
                },
                success: function (data) {
                    console.log(data);
                    if (data.status == 1) {
                        loadingModal.release();
                        Swal.fire({
                            title: 'Informasi',
                            text: data.message,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Edit!',
                            cancelButtonText: 'Tidak !',
                            customClass: {
                                confirmButton: 'btn btn-primary',
                                cancelButton: 'btn btn-danger'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#staticBackdropLabel').html('Edit Diskon Produk');
                                editData(data.data);
                            } else {
                                $('#produk').val('');
                                $('#nama_produk').val('');
                                $('#produk').removeClass('is-valid');
                                $('#produk').removeClass('is-invalid');
                                $('#cabang option[value=""]').prop('selected', true);
                                $('#staticBackdrop > div > div > form').trigger('reset');
                            }
                        });
                    } else if (data.status == 0) {
                        loadingModal.release();
                    }
                },
                error: function (data) {
                    loadingModal.release();
                }
            });
        }
    });
    // end validasi data produk


    // delete data
    $('.btn-delete').on('click', function () {
        $('#delet_model #produk').val($(this).data('p').trim());
        $('#delet_model #cabang').val($(this).data('c').trim());

        $('#form > div.modal-body > div > p.ms-text').html('Apakah anda yakin ingin menghapus diskon produk<br> Produk : <b>' + $(this).data('p') + '</b>, pada cabang : <b>' + $(this).data('c') + '</b> ?');
    });
    // end delete data

    // edit data
    $('.btn-edit').on('click', function () {
        $('#staticBackdropLabel').html('Edit Diskon Produk');
        var data = $(this).data('array');
        editData(data);
    });

    function editData(data) {
        console.log(data);
        $('#produk').val(data.kode_produk.trim());
        $('#produk').attr('readonly', true);
        $('#produk').addClass('bg-secondary');
        $('#nama_produk').val(data.nama_produk);
        $('#cabang option[value="' + data.cabang.trim() + '"]').prop('selected', true);
        $('#cabang option:not(:selected)').attr('disabled', true);
        $('#cabang').addClass('bg-secondary');
        $('#umur_faktur').val(data.umur_faktur);
        $('#disc_normal').val(data.disc_normal == '.00' ? 0 : data.disc_normal);
        $('#disc_max').val(data.disc_max == '.00' ? 0 : data.disc_max);
        $('#disc_plus_normal').val(data.disc_plus_normal == '.00' ? 0 : data.disc_plus_normal);
        $('#disc_plus_max').val(data.disc_plus_max == '.00' ? 0 : data.disc_plus_max);
        $('#staticBackdrop').modal('show');
        $('#staticBackdrop > div > div > form > div.modal-footer > button.btn.btn-primary').attr('type', 'submit');
    }
    // end edit data

    //  add data hanya menganti label di modal dan mengosongkan inputan
    $('#btn-adddiskonproduk').on('click', function () {
        $('#staticBackdropLabel').html('Tambah Diskon Produk');
        $('#staticBackdrop > div > div > form').trigger('reset');
        $('#produk').removeAttr('readonly');
        $('#produk').removeClass('bg-secondary');
        $('#produk').removeClass('is-valid');
        $('#produk').removeClass('is-invalid');
        $('#cabang option').removeAttr('disabled');
        $('#cabang').removeClass('bg-secondary');
        $('#cabang option[value=""]').prop('selected', true);
    });
    // end add data

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